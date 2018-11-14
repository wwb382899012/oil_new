<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/5/31 0031
 * Time: 17:39
 */

namespace ddd\Split\Repository\ContractSplit;

use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\infrastructure\Utility;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApply;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitApplyRepository;
use ddd\Split\Domain\Model\ContractSplit\StockSplit;
use ddd\Split\Repository\Contract\ContractRepository;

class ContractSplitApplyRepository extends EntityRepository implements IContractSplitApplyRepository{

    public function init(){
        $this->with = array('files', 'contract', 'contractSplits', 'stockSplits', 'contractSplits.goodsItems', 'stockSplits.splitDetails', 'stockSplits.splitDetails.items');
    }

    public function getNewEntity(){
        return new ContractSplitApply();
    }

    public function getActiveRecordClassName(){
        return 'ContractSplitApply';
    }

    public function dataToEntity($model){
        $entity = $this->getNewEntity();
        $values = $model->getAttributes();
        unset($values['apply_id']);
        $entity->setId($model->apply_id);
        $entity->setAttributes($values);

        $contractEntity = ContractRepository::repository()->findByPk($model->contract_id);
        if(empty($contractEntity)){
            throw new ZEntityNotExistsException($model->contract_id, 'Contract');
        }


        $entity->contract_code = $contractEntity->contract_code;
        $entity->type = $contractEntity->type;

        if(\Utility::isNotEmpty($contractEntity->goods_items)){
            $entity->initBalanceGoods($contractEntity);
        }
        if(\Utility::isNotEmpty($model->contractSplits)){
            foreach($model->contractSplits as $key => $contractSplit){
                $contractSplitEntity = ContractSplitRepository::repository()->dataToEntity($contractSplit);
                $entity->addContractSplit($contractSplitEntity, $contractSplitEntity->split_id, false);
            }
        }

        if(\Utility::isNotEmpty($model->stockSplits)){
            foreach($model->stockSplits as $key => $stockSplit){
                $stockSplitEntity = StockSplitRepository::repository()->dataToEntity($stockSplit);
                $entity->addStockSplit($stockSplitEntity);
            }
        }

        if(\Utility::isNotEmpty($model->files)){
            foreach($model->files as $file){
                $fileEntity = $this->getAttachmentEntity($file);
                $entity->addFile($fileEntity);
            }
        }

        return $entity;
    }

    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return int
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity){
        //保存合同平移申请
        $id = $entity->getId();
        if(!empty($id)){
            $model = $this->model()->with($this->with)->findByPk($id);
            if(empty($model)){
                ExceptionService::throwModelDataNotExistsException($id, $this->getActiveRecordClassName());
            }

            $this->deleteContractSplit($entity, $model);
            $this->deleteContractStockSplit($entity, $model);
        }else{
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
            $entity->apply_id = null;
        }

        $values = $entity->getAttributes();
        $values = \Utility::unsetCommonIgnoreAttributes($values);
        $model->setAttributes($values);
        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }

        //更新申请id
        $entity->apply_id = $model->apply_id;

        $contractSplits = $entity->getContractSplits();
        $stockSplits = $entity->getStockSplits();

        //保存合同平移信息
        if(\Utility::isNotEmpty($contractSplits)){
            foreach($contractSplits as & $split){
                if(!empty($split->split_id)){
                    $contractSplitModel = \ContractSplit::model()->findByPk($split->split_id);
                    if(empty($contractSplitModel)){
                        throw new ZModelNotExistsException($split->split_id, 'ContractSplit');
                    }
                }else{
                    $contractSplitModel = new \ContractSplit();
                }


                $splitValues['apply_id'] = $model->apply_id;
                $splitValues['contract_id'] = $model->contract_id;
                $splitValues['partner_id'] = $split->partner_id;
                $contractSplitModel->setAttributes($splitValues);

                if(!$contractSplitModel->save()){
                    throw new ZModelSaveFalseException($contractSplitModel);
                }

                //更新合同拆分明细id,下文
                $split->split_id = $contractSplitModel->split_id;

                //保存合同平移商品明细
                if(\Utility::isNotEmpty($split->goods_items)){
                    foreach($split->goods_items as $gi){
                        $item = \ContractSplitGoods::model()->find('split_id=:splitId and goods_id=:goodsId', array('splitId' => $contractSplitModel->split_id, 'goodsId' => $gi->goods_id));
                        if(empty($item)){
                            $item = new \ContractSplitGoods();
                            $item->split_id = $contractSplitModel->split_id;
                            $item->goods_id = $gi->goods_id;
                        }

                        $item->quantity = $gi->quantity->quantity;
                        $item->unit = $gi->quantity->unit;
                        if(!$item->save()){
                            throw new ZModelSaveFalseException($item);
                        }
                    }
                }
            }
        }

        //出入库平移信息
        if(\Utility::isNotEmpty($stockSplits)){
            $this->saveStockSplitData($entity);
        }

        //保存附件
        if(\Utility::isNotEmpty($entity->files)){
            foreach($entity->files as & $file){
                $file_model = \ContractSplitAttachment::model()->findByPk($file->id);
                if(empty($file_model)){
                    $file_model = new \ContractSplitAttachment();
                }

                $file_model->setAttributes($file->getAttributes());
                $file_model->status = 1;
                $file_model->base_id = $model->apply_id;

                if(!$file_model->save()){
                    throw new ZModelSaveFalseException($file_model);
                }
            }
        }

        return $model->apply_id;
    }

    private function deleteContractSplit(ContractSplitApply & $entity, \CActiveRecord & $model){
        $contract_split_ids = [];
        foreach($entity->getContractSplits() as & $contractSplit){
            if(!empty($contractSplit->split_id)){
                $contract_split_ids[$contractSplit->split_id] = $contractSplit->split_id;
            }
        }

        if(\Utility::isNotEmpty($model->contractSplits)){
            foreach($model->contractSplits as $contractSplitModel){
                if(isset($contract_split_ids[$contractSplitModel->split_id])){
                    continue;
                }

                //删除商品
                if(\Utility::isNotEmpty($contractSplitModel->goodsItems)){
                    foreach($contractSplitModel->goodsItems as & $contractSplitGoodsModel){
                        $contractSplitGoodsModel->delete();
                    }
                }


                //删除出入库拆分明细
                if(\Utility::isNotEmpty($contractSplitModel->stockSplitDetails)){
                    foreach($contractSplitModel->stockSplitDetails as & $stockSplitDetailModel){
                        //删除出入库拆分商品
                        if(\Utility::isNotEmpty($stockSplitDetailModel->items)){
                            foreach($stockSplitDetailModel->items as & $contractStockSplitGoodsModel){
                                $contractStockSplitGoodsModel->delete();
                            }
                        }

                        $stockSplitDetailModel->delete();
                    }
                }

                $contractSplitModel->delete();
            }
        }
    }

    private function deleteContractStockSplit(ContractSplitApply & $entity, \CActiveRecord & $model){
        $bill_id_code_map = $entity->getAllStockSplitBillIds();
        if(\Utility::isNotEmpty($model->stockSplits)){
            foreach($model->stockSplits as & $contractStockSplitModel){
                if(isset($bill_id_code_map[(string)$contractStockSplitModel->bill_id])){
                    continue;
                }

                if(\Utility::isNotEmpty($contractStockSplitModel->splitDetails)){
                    foreach($contractStockSplitModel->splitDetails as & $stockSplitDetailModel){
                        //删除出入库拆分商品
                        if(\Utility::isNotEmpty($stockSplitDetailModel->items)){
                            foreach($stockSplitDetailModel->items as & $contractStockSplitGoodsModel){
                                $contractStockSplitGoodsModel->delete();
                            }
                        }

                        $stockSplitDetailModel->delete();
                    }
                }

                $contractStockSplitModel->delete();
            }
        }
    }

    /**
     *
     * @param ContractSplitApply $applyEntity 合同平移申请实体
     * @throws ZModelSaveFalseException
     * @throws \CException
     */
    private function saveStockSplitData(ContractSplitApply & $applyEntity){
        $contractSplits = $applyEntity->getContractSplits();
        $stockSplits = $applyEntity->getStockSplits();

        foreach($stockSplits as & $stockSplitEntity){

            $stockSplitModel = \ContractStockSplit::model()->find('t.bill_id =:bill_id AND t.apply_id = :apply_id', [':bill_id' => $stockSplitEntity->bill_id, ':apply_id' => $applyEntity->apply_id,]);
            if(empty($stockSplitModel)){
                $stockSplitModel = new \ContractStockSplit();
            }

            $type = ($applyEntity->type == ContractSplitApplyEnum::CONTRACT_TYPE_SELL) ? ContractSplitApplyEnum::STOCK_TYPE_OUT : ContractSplitApplyEnum::STOCK_TYPE_IN;
            $stockSplitModel->setAttributes(['apply_id' => $applyEntity->apply_id, 'bill_id' => $stockSplitEntity->bill_id, 'type' => $type,]);

            //如果该出/入库单未勾选拆分，则该明细下的所有拆分数据都是0
            $is_effective_split = $stockSplitEntity->isEffective();
            $stockSplitModel->status = $is_effective_split ? \ContractStockSplit::STATUS_SPLIT : \ContractStockSplit::STATUS_UN_SPLIT;

            if(!$stockSplitModel->save()){
                throw new ZModelSaveFalseException($stockSplitModel);
            }

            $this->saveStockSplitDetails($is_effective_split, $stockSplitModel->stock_split_id, $contractSplits, $stockSplitEntity);
        }
    }

    /**
     * 保存出入库平移明细
     * @param bool $isEffactiveSplit 出/入库单是否勾选拆分
     * @param $stockSplitId
     * @param array $contractSplits
     * @param StockSplit $stockSplitEntity
     * @throws ZModelSaveFalseException
     * @throws \CException
     */
    private function saveStockSplitDetails(bool $isEffactiveSplit, $stockSplitId, array & $contractSplits, StockSplit & $stockSplitEntity){
        //保存出入库平移明细
        $details = $stockSplitEntity->getDetails();
        if(\Utility::isEmpty($details)){
            return;
        }

        foreach($details as $itemKey => & $di){
            $split_id = $contractSplits[$itemKey]->split_id;
            $stockDetail = \ContractStockSplitDetail::model()->find('t.split_id=:splitId and t.stock_split_id=:stockSplitId', ['splitId' => $split_id, 'stockSplitId' => $stockSplitId]);
            if(empty($stockDetail)){
                $stockDetail = new \ContractStockSplitDetail();
            }

            $stockDetail->stock_split_id = $stockSplitId;
            $stockDetail->split_id = $split_id;
            $stockDetail->bill_id = $stockSplitEntity->bill_id;
            $stockDetail->new_bill_id = 0;
            $stockDetail->remark = '';

            if(!$stockDetail->save()){
                throw new ZModelSaveFalseException($stockDetail);
            }

            //保存出入库平移明细商品信息
            if(\Utility::isNotEmpty($di->goods_items)){
                foreach($di->goods_items as $gt){
                    $goodsInfo = \ContractStockSplitGoods::model()->find('t.split_detail_id=:splitDetailId and t.goods_id=:goodsId', ['splitDetailId' => $stockDetail->split_detail_id, 'goodsId' => $gt->goods_id]);
                    if(empty($goodsInfo)){
                        $goodsInfo = new \ContractStockSplitGoods();
                        $goodsInfo->split_detail_id = $stockDetail->split_detail_id;
                        $goodsInfo->goods_id = $gt->goods_id;
                    }

                    //如果该出/入库单未勾选拆分，则该明细下的所有拆分数据都是0
                    $goodsInfo->quantity = $isEffactiveSplit ? $gt->quantity->quantity : 0;
                    $goodsInfo->unit = $gt->quantity->unit;

                    if(!$goodsInfo->save()){
                        throw new ZModelSaveFalseException($goodsInfo);
                    }
                }
            }
        }
    }

    /**
     * 提交
     * @param   ContractSplitApply $contractSplitApply
     * @throws  \Exception
     */
    function submit(ContractSplitApply $contractSplitApply){
        $this->updateStatus($contractSplitApply);
    }

    /**
     * @desc 更新状态
     * @param   ContractSplitApply $entity
     * @throws  \Exception
     */
    protected function updateStatus(ContractSplitApply $entity){
        if(empty($entity)){
            ExceptionService::throwArgumentNullException("ContractSplitApply对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        $model = \ContractSplitApply::model()->findByPk($entity->apply_id);
        if(empty($model)){
            throw new ZModelNotExistsException($entity->apply_id, "ContractSplitApply");
        }
        if($model->status != $entity->status){
            $model->status = $entity->status;
            $model->status_time = Utility::getNow();
            $res = $model->save();
            if($res !== true){
                throw new ZModelSaveFalseException($model);
            }
        }
    }

    /**
     * 审核驳回
     * @param   ContractSplitApply $contractSplitApply
     * @throws  \Exception
     */
    function reject(ContractSplitApply $contractSplitApply){
        $this->updateStatus($contractSplitApply);
    }

    /**
     * 审核通过
     * @param   ContractSplitApply $contractSplitApply
     * @throws  \Exception
     */
    function checkPass(ContractSplitApply $contractSplitApply){
        $this->updateStatus($contractSplitApply);
    }

    /**
     * 根据合同ID获取合同平移申请
     * @param $contractId
     * @return ContractSplitApply
     */
    function findByContractId($contractId){
        return $this->find('t.contract_id='.$contractId);
    }

    public function findAllByContractId($contractId):array{
        return $this->findAll("t.contract_id=" . $contractId);
    }
}