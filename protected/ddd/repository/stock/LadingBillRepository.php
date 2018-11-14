<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/27 10:04
 * Describe：
 */

namespace ddd\repository\stock;

use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\Attachment;
use ddd\domain\entity\stock\LadingBill;
use ddd\domain\entity\stock\LadingBillGoods;
use ddd\domain\entity\value\Quantity;
use ddd\infrastructure\Utility;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelNotExistsException;

class LadingBillRepository extends EntityRepository
{


    public function init()
    {
        $this->with=array("details","attachments");
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName()
    {
        // TODO: Implement getActiveRecordClassName() method.
        return "StockNotice";
    }

    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new LadingBill();
    }




    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return bool
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        $id=$entity->getId();
        if(!empty($id))
            $model = \StockNotice::model()->with("details")->findByPk($id);
        if (empty($model))
        {
            $model = new \StockNotice();
        }
        if ($model->contract_id != $entity->contract_id)
        {
            $contract = \Contract::model()->findByPk($entity->contract_id);
            if (empty($contract))
                \BusinessException::throwException(\OilError::$PROJECT_CONTRACT_NOT_EXIST, array("contract_id" => $entity->contract_id));
            $model->contract_id = $entity->contract_id;
            $model->corporation_id = $contract->corporation_id;
            $model->project_id = $contract->project_id;
        }

        //$model->type = $entity->type;
        $model->setAttributes($entity->getAttributes());
        $model->batch_date = $entity->lading_date;

        $isNew = $model->isNewRecord;

        $res = $model->save();
        if (!$res)
            ExceptionService::throwModelSaveFalseException($model);

        $items = $entity->items;
        if (!is_array($items))
            $items = array();
        if (!$isNew)
        {
            if (is_array($model->details))
            {
                foreach ($model->details as $d)
                {
                    if (isset($items[$d->goods_id]))
                    {
                        $item = $items[$d->goods_id];
                        $d->quantity = $item->quantity->quantity;
                        $d->quantity_sub = $item->quantitySub->quantity;
                        $d->unit = $item->quantity->unit;
                        $d->unit_sub = $item->quantitySub->unit;
                        $d->quantity_actual = $item->in_quantity->quantity;
                        $d->quantity_actual_sub = $item->in_quantity_sub->quantity;
                        $d->unit_rate = $item->unit_rate;
                        $res = $d->save();
                        if (!$res)
                            ExceptionService::throwModelSaveFalseException($d);
                        unset($items[$d->goods_id]);
                    }
                    else
                    {
                        $res = $d->delete();
                        if (!$res)
                            ExceptionService::throwModelDeleteFalseException($d);
                    }
                }
            }
        }


        if (is_array($items) && count($items) > 0)
        {
            foreach ($items as $item)
            {
                $d = new \StockNoticeDetail();
                $d->batch_id = $model->batch_id;
                $d->project_id = $model->project_id;
                $d->contract_id = $model->contract_id;
                $d->goods_id = $item->goods_id;
                $d->project_id = $model->project_id;
                $d->contract_id = $model->contract_id;
                $d->quantity = $item->quantity->quantity;
                $d->unit = $item->quantity->unit;
                $d->quantity_sub = $item->quantitySub->quantity;
                $d->unit_sub = $item->quantitySub->unit;
                $d->quantity_actual = $item->in_quantity->quantity;
                $d->quantity_actual_sub = $item->in_quantity_sub->quantity;
                $d->unit_rate = $item->unit_rate;
                $res = $d->save();
                if (!$res)
                    ExceptionService::throwModelSaveFalseException($d);
            }
        }


        return true;

    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return LadingBill|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity=$this->getNewEntity();
        $entity->setAttributes($model->getAttributes(),false);
        $entity->id=$model->batch_id;
        $entity->lading_date=$model->batch_date;
        $entity->currency = $model->contract->currency;
        if(is_array($model->details))
        {
            foreach ($model->details as $d)
            {
                $item=LadingBillGoods::create($d->goods_id);
                $item->remark = $d->remark;
                $item->store_id = $d->store_id;
                $item->goods_id=$d->goods_id;
                $item->quantity=new Quantity($d->quantity,$d->unit);
                $item->quantitySub=new Quantity($d->sub->quantity,$d->sub->unit);
                $item->in_quantity=new Quantity($d->quantity_actual,$d->unit);
                $item->in_quantity_sub=new Quantity($d->sub->quantity_actual,$d->sub->unit);
                $item->unit_rate = !empty($d->sub)&&!empty($d->unit_rate)?$d->unit_rate:'';
                $entity->addItem($item);
            }
        }

        /*if (is_array($model->stockBatchSettlement))
        {
            foreach ($model->stockBatchSettlement as $g)
            {
                $settleItem = LadingBillSettlementItem::create($g->goods_id);
                if (!empty($settleItem))
                {
                    $settleItem->setAttributes($g->getAttributes(), false);
                }
                $settleItem->settle_quantity = $g->quantity;
                $settleItem->settle_quantity_sub = $g->sub->quantity;
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
     * 查询合同下所有的提单（入库通知单）
     * @param contractId
     * @return LadingBill
     */
    public function findAllByContractId($contractId)
    {
        $condition = "t.contract_id=" . $contractId;

        return $this->findAll($condition);
    }


    /**
     * 更新提单状态
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    protected function updateStatus(LadingBill $entity)
    {
        if(empty($entity))
            throw new ZException("LadingBill对象不存在");

        $model=\StockNotice::model()->findByPk($entity->id);
        if(empty($model))
            throw new ZModelNotExistsException($entity->id, "StockNotice");

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
     * 获取提单下每个商品实际入库数量
     */
    public function getLadingGoodsInQuantity($batchId)
    {
        $qArr = array();
        $ladingBill =  $this->findByPk($batchId);
        if(is_array($ladingBill->items) && !empty($ladingBill->items)){
            foreach ($ladingBill->items as $goods_id=>$item){
                $in_quantity = !empty($item->in_quantity->quantity) ? $item->in_quantity->quantity : 0;
                $in_quantity_sub = !empty($item->in_quantity_sub->quantity) ? $item->in_quantity_sub->quantity : 0;

                $qArr[$goods_id]['in_quantity'] = $in_quantity;
                $qArr[$goods_id]['in_quantity_sub'] = $in_quantity_sub;
            }
        }

        return $qArr;
    }

    /**
     * 获取合同下每个商品实际入库数量
     */
    public  function getContractGoodsInQuantity($contractId)
    {
        $qArr = array();
        $ladingBills = $this->findAllByContractId($contractId);
        if(is_array($ladingBills) && !empty($ladingBills)){
            foreach ($ladingBills as $ladingBill){
                if(is_array($ladingBill->items) && !empty($ladingBill->items)){
                    foreach ($ladingBill->items as $goods_id=>$item){
                        $in_quantity = !empty($item->in_quantity->quantity) ? $item->in_quantity->quantity : 0;
                        $in_quantity_sub = !empty($item->in_quantity_sub->quantity) ? $item->in_quantity_sub->quantity : 0;

                        $qArr[$goods_id][$ladingBill->id]['in_quantity'] = $in_quantity;
                        $qArr[$goods_id][$ladingBill->id]['in_quantity_sub'] = $in_quantity_sub;

                        $qArr[$goods_id]['in_quantity'] += $in_quantity;
                        $qArr[$goods_id]['in_quantity_sub'] += $in_quantity_sub;
                    }
                }
            }
        }

        return $qArr;
    }


    /**
     * 保存提交
     * @param LadingBill $ladingBill
     * @throws \Exception
     */
    public function submit(LadingBill $ladingBill)
    {
        $this->updateStatus($ladingBill);
    }


    /**
     * 设置为结算驳回
     * @param LadingBill $ladingBill
     * @throws \Exception
     */
    public function setSettledBack(LadingBill $ladingBill)
    {
        $this->updateStatus($ladingBill);
    }

    /**
     * 设置为结算中
     * @param LadingBill $ladingBill
     * @throws \Exception
     */
    public function setOnSettling(LadingBill $ladingBill)
    {
        $this->updateStatus($ladingBill);
    }

    /**
     * 设置为结算完成
     * @param LadingBill $ladingBill
     * @throws \Exception
     */
    public function setSettled(LadingBill $ladingBill)
    {
        $this->updateStatus($ladingBill);
    }


}