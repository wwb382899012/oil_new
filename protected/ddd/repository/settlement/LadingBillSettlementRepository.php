<?php

/**
 * Created by vector.
 * DateTime: 2018/3/26 11:33
 * Describe：提单结算单仓储
 */

namespace ddd\repository\settlement;

use ddd\domain\entity\settlement\LadingBillSettlement;

use ddd\domain\IAggregateRoot;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\infrastructure\error\BusinessError;
use ddd\domain\entity\value\Currency;
use ddd\domain\entity\settlement\SettlementMode;


class LadingBillSettlementRepository extends SettlementRepository
{
    

	public function init()
    {
        $this->with=array("contractSettlementGoods","contractSettlementGoods.ladingSettlement","contractSettlementGoods.settleGoods","contractSettlementGoods.settleGoods.goodsItems","contractSettlementGoods.ladingAttachments");
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName()
    {
        return "LadingSettlement";
    }

    public function getNewEntity()
    {
        return new LadingBillSettlement();
    }

    

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return LadingBillSettlement|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(),false);
        $entity->settle_currency = Currency::getCurrency($model->currency);
        $entity->batch_id = $model->lading_id;
        $entity->goods_amount = $model->amount_goods;

        $this->addGoodsEntity($model, $entity, SettlementMode::LADING_BILL_MODE_SETTLEMENT);

        return $entity;
    }



    /**
     * 把对象持久化到数据库
     * @param IAggregateRoot $entity
     * @return bool
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        if(empty($entity))
            throw new ZException("LadingBillSettlement对象不存在");

        $contract = \Contract::model()->findByPk($entity->contract_id);
        if(empty($contract))
            throw new ZModelNotExistsException($entity->contract_id, "Contract");
        if(empty($contract->settle_type)){
            $contract->settle_type = SettlementMode::LADING_BILL_MODE_SETTLEMENT;
            $res = $contract->save();
            if (!$res)
                throw new ZModelSaveFalseException($contract);
        }else{
            if($contract->settle_type != SettlementMode::LADING_BILL_MODE_SETTLEMENT)
                throw new ZException(BusinessError::Now_Settle_Mode_Is_Buy_Contract_Settle,array("contract_code"=>$contract->contract_code));
        }
        
        $contractSettlement = \ContractSettlement::model()->find("contract_id=".$contract->contract_id);
        $this->saveContractSettlement($contractSettlement, $entity, $contract, SettlementMode::LADING_BILL_MODE_SETTLEMENT);
        
        $model = array();
        $id=$entity->getId();
        if(!empty($id))
            $model = \LadingSettlement::model()->with($this->with)->findByPk($id);

        $this->saveBillSettlement($model, $entity, $contract, SettlementMode::LADING_BILL_MODE_SETTLEMENT);

        $this->saveSettlementDetail($model, $entity, $contract,SettlementMode::LADING_BILL_MODE_SETTLEMENT);

        return true;

    }


    /**
     * 查询合同下所有的提单结算单
     * @param contractId
     * @return LadingBillSettlement
     */
    public function findAllByContractId($contractId)
    {
        $condition = "t.contract_id=" . $contractId;
        
        return $this->findAll($condition);
    }


    /**
     * 更新提单结算单状态
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    public function updateStatus(LadingBillSettlement $entity)
    {
        if(empty($entity))
            throw new ZException("LadingBillSettlement对象不存在");

        $model=\LadingSettlement::model()->findByPk($entity->settle_id);
        if(empty($model))
            throw new ZModelNotExistsException($entity->settle_id, "LadingSettlement");

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
     * 保存提交
     * @param LadingBillSettlement $settlement
     * @throws \Exception
     */
    public function submit(LadingBillSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 作废
     * @param LadingBillSettlement $settlement
     * @throws \Exception
     */
    public function trash(LadingBillSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 驳回
     * @param LadingBillSettlement $settlement
     * @throws \Exception
     */
    public function back(LadingBillSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 设置为结算完成
     * @param LadingBillSettlement $settlement
     * @throws \Exception
     */
    public function setSettled(LadingBillSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

}

