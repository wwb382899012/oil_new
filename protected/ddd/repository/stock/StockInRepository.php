<?php
/**
 * Desc: 入库仓储
 * User: susiehuang
 * Date: 2018/3/16 0016
 * Time: 9:56
 */

namespace ddd\repository\stock;


use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\stock\StockIn;
use ddd\domain\entity\stock\StockInItem;
use ddd\domain\entity\value\Quantity;
use ddd\repository\EntityFile;

class StockInRepository extends EntityRepository
{

    use EntityFile;

    public function init()
    {
        $this->with = array("details","files");
    }

    public function getActiveRecordClassName()
    {
        return "StockIn";
    }

    public function getNewEntity()
    {
        return new StockIn();
    }



    protected function getFieldsMap()
    {
        return [
                "lading_bill_id"=>"batch_id",
                //"entry_date"=>"entry_date",
            ];
    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return \ddd\domain\entity\BaseEntity|StockIn
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = new StockIn();
        $attributes=$model->getAttributes();
        $entity->setAttributes($attributes, false);
        $this->setEntityValue($entity,$model);
        if (is_array($model->details))
        {
            foreach ($model->details as $d)
            {
                $item = StockInItem::create();
                $item->goods_id = $d->goods_id;
                $item->quantity = new Quantity($d->quantity, $d->unit);
                $item->quantity_sub = new Quantity($d->sub->quantity, $d->sub->unit);
                $item->unit_rate = !empty($d->sub)&&!empty($d->unit_rate)?$d->unit_rate:'';
                $item->remark =$d->remark;
                $entity->addItem($item);
            }
        }

        if (is_array($model->files))
        {
            foreach ($model->files as $f)
            {
                $attachments = \ddd\domain\entity\Attachment::create($f->id);
                if (!empty($attachments))
                {
                    $attachments->setAttributes($f->getAttributes(), false);
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
     * 查询提单（入库通知单）下所有的入库单
     * @param batchId
     * @return StockIn
     */
    public function findAllByBatchId($batchId)
    {
        $condition = "t.batch_id=" . $batchId;

        return $this->findAll($condition);
    }

    /**
     * 查询合同下所有的入库单
     * @param batchId
     * @return StockIn
     */
    public function findAllByContractId($contractId)
    {
        $condition = "t.contract_id=" . $contractId;

        return $this->findAll($condition);
    }

    /**
     * 更新入库单状态
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    public function updateStatus(StockIn $entity)
    {
        if(empty($entity))
            throw new ZException("StockIn对象不存在");

        $model=\StockIn::model()->findByPk($entity->stock_in_id);
        if(empty($model))
            throw new ZModelNotExistsException($entity->stock_in_id, "StockIn");

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
     * @param StockIn $stockIn
     * @throws \Exception
     */
    public function setSettled(StockIn $stockIn)
    {
        $stockIn->status = \StockIn::STATUS_SETTLED;
        $this->updateStatus($stockIn);
    }
}