<?php

namespace ddd\Split\Repository\StockSplit;

use BaseActiveRecord;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\Attachment;
use ddd\domain\entity\value\Quantity;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\repository\stock\StockInRepository;
use ddd\repository\stock\StockOutRepository;
use ddd\Split\Domain\Model\StockSplit\IStockSplitApplyRepository;
use ddd\Split\Domain\Model\StockSplit\StockSplitApply;
use ddd\Split\Domain\Model\StockSplit\StockSplitDetail;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;
use ddd\Split\Domain\Model\TradeGoods;

class StockSplitApplyRepository extends EntityRepository implements IStockSplitApplyRepository{

    /**
     * 获取新的实体对象
     * @return BaseEntity
     * @throws \Exception
     */
    public function getNewEntity(){
        return new StockSplitApply();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return 'StockSplitApply';
    }

    /**
     * 数据模型转换成业务对象
     * @param BaseActiveRecord $model
     * @return BaseEntity|void
     * @return BaseEntity|void
     * @throws \Exception
     */
    public function dataToEntity($model){
        $is_stock_in_split = (StockSplitEnum::TYPE_STOCK_IN == $model->type);
        $entity = $this->getNewEntity();
        if($is_stock_in_split){
            $stockEntity = StockInRepository::repository()->findByPk($model->bill_id);
        }else{
            $stockEntity = StockOutRepository::repository()->findByPk($model->bill_id);
        }

        $entity->bill_code = $stockEntity->code;
        $entity->setAttributes($model->getAttributes());
        $entity->initBalanceGoods($stockEntity);
        foreach($model->details as & $detail){
            $stockSplitDetailEntity = StockSplitApplyDetailRepository::repository()->dataToEntity($detail);
            $entity->addSplitDetail($stockSplitDetailEntity,false);
        }

        if (\Utility::isNotEmpty($model->files)){
            foreach ($model->files as & $file){
                $fileEntity = new Attachment();
                $fileEntity->setAttributes($file->getAttributes());
                $entity->addFile($fileEntity);
            }
        }

        return $entity;
    }

    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return StockSplitApply
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity):StockSplitApply{
        if(!empty($entity->getId())){
            $model = $this->model()->findByPk($entity->getId());
            if(empty($model)){
                throw new ZModelNotExistsException($entity->getId(),\StockSplitApply::class);
            }
        }else{
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
            $entity->setId(null);
        }

        $model->setAttributes($entity->getAttributes());

        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }
        $entity->setId($model->getPrimaryKey());

        //保存拆分明细
        foreach($entity->getDetails() as & $stockSplitDetailEntity){
            if(!empty($stockSplitDetailEntity->detail_id)){
                $detail_model = \StockSplitApplyDetail::model()->findByPk($stockSplitDetailEntity->detail_id);
                if(empty($detail_model)){
                    throw new ZModelNotExistsException($stockSplitDetailEntity->detail_id,\StockSplitApplyDetail::class);
                }
            }else{
                $detail_model = new \StockSplitApplyDetail();
                $stockSplitDetailEntity->detail_id = null;
            }

            $detail_model->setAttributes($model->getAttributes());
            $detail_model->setAttributes($stockSplitDetailEntity->getAttributes());
            $detail_model->type = $model->type;

            if(!$detail_model->save()){
                throw new ZModelSaveFalseException($detail_model);
            }

            //保存商品明细
            foreach($stockSplitDetailEntity->getGoodsItems() as & $tradeGoodsEntity){
                $detail_goods_model = \StockSplitDetailGoods::model()->find("t.detail_id = :detail_id AND t.goods_id = :goods_id",[
                    ':detail_id' => $stockSplitDetailEntity->detail_id,
                    ':goods_id' => $tradeGoodsEntity->goods_id,
                ]);
                if(empty($detail_goods_model)){
                    $detail_goods_model = new \StockSplitDetailGoods();
                }

                $detail_goods_model->setAttributes($tradeGoodsEntity->getAttributes());
                $detail_goods_model->detail_id = $detail_model->getPrimaryKey();
                $detail_goods_model->quantity = $tradeGoodsEntity->quantity->quantity;
                $detail_goods_model->unit = $tradeGoodsEntity->quantity->unit->id;

                if(!$detail_goods_model->save()){
                    throw new ZModelSaveFalseException($detail_goods_model);
                }
            }
        }


        //保存附件
        if (\Utility::isNotEmpty($entity->getFiles())){
            foreach ($entity->getFiles() as & $file){
                if(!empty($file->id)){
                    $file_model = \StockSplitAttachment::model()->findByPk($file->id);
                    if(empty($file_model)){
                        throw new ZModelNotExistsException($file->id,\StockSplitAttachment::class);
                    }
                }else{
                    $file_model = new \StockSplitAttachment();
                }

                $file_model->base_id = $entity->getId();
                $file_model->status = 1;
                $file_model->update_time = \Utility::getDateTime();
                $file_model->remark = "";

                if(!$file_model->save()){
                    throw new ZModelSaveFalseException($file_model);
                }
            }
        }

        return $entity;
    }

    /**
     * 更新出库单状态
     * @param $stockSplitApply
     * @return bool
     * @throws \Exception
     */
    protected function updateStatus(StockSplitApply $stockSplitApply){
        if(empty($entity)){
            throw new ZException("StockSplit对象不存在");
        }

        $model = $this->model()->findByPk($stockSplitApply->getId());
        if(empty($model)){
            throw new ZModelNotExistsException($stockSplitApply->getId(), $this->getActiveRecordClassName());
        }

        if($model->status != $entity->status){
            $model->status = $entity->status;
            if(!$model->save()){
                throw new ZModelSaveFalseException($model);
            }
        }

        return true;
    }

    public function submit(StockSplitApply $stockSplitApply){
        return $this->updateStatus($stockSplitApply);
    }

    public function checkBack(StockSplitApply $stockSplitApply){
        return $this->updateStatus($stockSplitApply);
    }

    public function checkPass(StockSplitApply $stockSplitApply){
        return $this->updateStatus($stockSplitApply);
    }

    public function findByBillId($billId) {
        return $this->find('t.bill_id = :bill_id ',[':bill_id'=>$billId]);
    }

    public function findAllByContractId($contractId){
        return $this->findAll('t.contract_id = :contract_id ',[':contract_id'=>$contractId]);
    }

    public function findByApplyId($applyId) {
        return $this->find('t.apply_id = :apply_id ',[':apply_id'=>$applyId]);
    }
}