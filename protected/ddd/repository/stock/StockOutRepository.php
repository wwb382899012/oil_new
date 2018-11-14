<?php
/**
 * Created by vector.
 * DateTime: 2018/3/29 18:04
 * Describe：出库单仓储
 */

namespace ddd\repository\stock;


use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\stock\StockOut;
use ddd\domain\entity\stock\StockOutItem;
use ddd\domain\entity\value\Quantity;
use ddd\repository\EntityFile;

class StockOutRepository extends EntityRepository
{

    use EntityFile;

    public function init()
    {
        $this->with = array("attachments","deliveryOrder");
    }
    
    public function getActiveRecordClassName()
    {
        return "StockOutOrder";
    }
    
    public function getNewEntity()
    {
        return new StockOut();
    }
    

    
    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return Project|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = new StockOut();
        $entity->setAttributes($model->getAttributes(), false);
        
        if (is_array($model->details))
        {
            foreach ($model->details as $d)
            {
                $item = StockOutItem::create();
                $item->goods_id = $d->goods_id;
                $item->quantity = new Quantity($d->quantity,$d->contractGoods->unit);
                $item->delivery_quantity =  new Quantity($d->stockDeliveryDetail->quantity,$d->contractGoods->unit);
                $item->stock_in_code =  $d->stockDeliveryDetail->stock->stockIn->code;
                $item->remark = $d->remark;
                $entity->addGoods($item);
            }
        }
        if (is_array($model->attachments))
        {
            foreach ($model->attachments as $a)
            {
                $attachments = \ddd\domain\entity\Attachment::create($a->id);
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
     * 查询发货单下所有的出库单
     * @param orderId
     * @return StockOut
     */
    public function findAllByOrderId($orderId)
    {
        $condition = "t.order_id=" . $orderId;
        return $this->findAll($condition);
    }

    /**
     * 查询合同下所有的出库单
     * @param orderId
     * @return StockOut
     */
    public function findAllByContractId($contractId)
    {
        $condition = "deliveryOrder.contract_id=" . $contractId;
        return $this->findAll($condition);
    }

    /**
     * 更新出库单状态
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    public function updateStatus(StockOut $entity)
    {
        if(empty($entity))
            throw new ZException("StockOut对象不存在");

        $model=\StockOutOrder::model()->findByPk($entity->out_order_id);
        if(empty($model))
            throw new ZModelNotExistsException($entity->out_order_id, "StockOut");

        if($model->status != $entity->status)
        {
            $model->status = $entity->status;
            $res = $model->save();
            if(!$res)
                throw new ZModelSaveFalseException($model);
        }

        return true;
    }

    /**
     * 设置已结算
     * @param StockOut $stockOut
     * @throws \Exception
     */
    public function setSettled(StockOut $stockOut)
    {
        $stockOut->status = \StockOutOrder::STATUS_SETTLED;
        $this->updateStatus($stockOut);
    }
}