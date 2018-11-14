<?php
/**
 * Created by vector.
 * DateTime: 2018/3/29 17:01
 * Describe：发货单仓储
 */

namespace ddd\repository\stock;

use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\Attachment;
use ddd\domain\entity\stock\DeliveryOrder;
use ddd\domain\entity\stock\DeliveryOrderGoods;
use ddd\domain\entity\value\Quantity;
use ddd\infrastructure\Utility;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\repository\contract\TradeGoodsRepository;

class DeliveryOrderRepository extends EntityRepository
{


    public function init()
    {
        $this->with = array("details","attachments");
    }
    
    public function getActiveRecordClassName()
    {
        return "DeliveryOrder";
    }
    
    public function getNewEntity()
    {
        return new DeliveryOrder();
    }
    

    
    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return DeliveryOrder|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        
        if (is_array($model->details))
        {
            foreach ($model->details as $d)
            {
                $tradeGoods= TradeGoodsRepository::repository()->findByContractIdAndGoodsId($d->contract_id, $d->goods_id);

                $item = DeliveryOrderGoods::create($d->goods_id);
                $item->detail_id = $d->detail_id;
                $item->goods_id = $d->goods_id;

                $item->quantity = new Quantity($d->quantity,$tradeGoods->unit);
                $item->out_quantity = new Quantity($d->quantity_actual,$tradeGoods->unit);
                $item->remark = $d->remark;
                $entity->addGoods($item);
            }
        }
       
        /*  if (is_array($model->settlementDetails))
         {
         foreach ($model->settlementDetails as $g)
         {
         $settleItem = DeliveryOrderSettlementItem::create($g->goods_id);
         if (!empty($settleItem))
         {
         $settleItem->setAttributes($g->getAttributes(), false);
         }
         $settleItem->out_quantity = $g->quantity;
         $settleItem->out_quantity_sub = $g->sub->quantity;
         $settleItem->settle_quantity = $g->quantity_settle;
         $settleItem->settle_quantity_sub = $g->sub->quantity_settle;
         $settleItem->loss_quantity = $g->quantity_loss;
         $settleItem->loss_quantity_sub = $g->sub->quantity_loss;
         $settleItem->settle_price = $g->price;
         $settleItem->settle_amount = $g->amount;
         $settleItem->settle_amount_cny = $g->amount_cny;
         $entity->addSettleItems($settleItem);
         }
         } */
        
        if (is_array($model->attachments))
        {
            foreach ($model->attachments as $a)
            {
                $attachments = Attachment::create($a->id);
                if (!empty($attachments))
                {
                    $attachments->setAttributes($a->getAttributes(), false);
                }
                $entity->addFilesItems($attachments);
            }
        }
        return $entity;
    }
    
    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return bool
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        
    }

    /**
     * 查询合同下所有的发货单
     * @param contractId
     * @return DeliveryOrder
     */
    public function findAllByContractId($contractId)
    {
        $condition = "t.contract_id=" . $contractId;

        return $this->findAll($condition);
    }


    /**
     * 更新发货单状态
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    protected function updateStatus(DeliveryOrder $entity)
    {
        if(empty($entity))
            throw new ZException("DeliveryOrder对象不存在");

        $model=\DeliveryOrder::model()->findByPk($entity->order_id);
        if(empty($model))
            throw new ZModelNotExistsException($entity->order_id, "DeliveryOrder");

        if($model->status != $entity->status)
        {
            $model->status = $entity->status;
            $model->status_time = Utility::getDateTime();
            $res = $model->save();
            if(!$res)
                throw new ZModelSaveFalseException($model);
        }

        return true;
    }


    /**
     * 查询发货单下每个商品实际入库数量
     */
    public  function getDeliveryGoodsInQuantity($orderId)
    {
        $qArr = array();
        $deliveryOrder =  $this->findByPk($orderId);
        if(is_array($deliveryOrder->items) && !empty($deliveryOrder->items)){
            foreach ($deliveryOrder->items as $goods_id=>$item){
                $out_quantity = !empty($item->out_quantity->quantity) ? $item->out_quantity->quantity : 0;
                $qArr[$goods_id]['out_quantity'] = $out_quantity ;
            }
        }

        return $qArr;
    }

    /**
     * 查询合同下每个商品实际入库数量
     */
    public  function getContractGoodsInQuantity($contractId)
    {
        $qArr = array();
        $deliveryOrders = $this->findAllByContractId($contractId);
        if(is_array($deliveryOrders) && !empty($deliveryOrders)){
            foreach ($deliveryOrders as $deliveryOrder){
                if(is_array($deliveryOrder->items) && !empty($deliveryOrder->items)){
                    foreach ($deliveryOrder->items as $goods_id=>$item){
                        $out_quantity = !empty($item->out_quantity->quantity) ? $item->out_quantity->quantity : 0;
                        $qArr[$goods_id][$deliveryOrder->order_id]['out_quantity'] = $out_quantity;
                        $qArr[$goods_id]['out_quantity'] += $out_quantity;

                    }
                }
            }
        }

        return $qArr;
    }


    /**
     * 保存提交
     * @param DeliveryOrder $deliveryOrder
     * @throws \Exception
     */
    public function submit(DeliveryOrder $deliveryOrder)
    {
        $this->updateStatus($deliveryOrder);
    }

    /**
     * 审核驳回
     * @param DeliveryOrder $deliveryOrder
     * @throws \Exception
     */
    public function back(DeliveryOrder $deliveryOrder)
    {
        $this->updateStatus($deliveryOrder);
    }

    /**
     * 审核通过
     * @param DeliveryOrder $deliveryOrder
     * @throws \Exception
     */
    public function pass(DeliveryOrder $deliveryOrder)
    {
        $this->updateStatus($deliveryOrder);
    }


    /**
     * 设置为结算驳回
     * @param DeliveryOrder $deliveryOrder
     * @throws \Exception
     */
    public function setSettledBack(DeliveryOrder $deliveryOrder)
    {
        $this->updateStatus($deliveryOrder);
    }

    /**
     * 设置为结算中
     * @param DeliveryOrder $deliveryOrder
     * @throws \Exception
     */
    public function setOnSettling(DeliveryOrder $deliveryOrder)
    {
        $this->updateStatus($deliveryOrder);
    }

    /**
     * 设置为结算完成
     * @param DeliveryOrder $deliveryOrder
     * @throws \Exception
     */
    public function setSettled(DeliveryOrder $deliveryOrder)
    {
        $this->updateStatus($deliveryOrder);
    }


}