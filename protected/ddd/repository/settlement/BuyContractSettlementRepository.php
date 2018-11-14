<?php

/**
 * Created by vector.
 * DateTime: 2018/4/4 17:41
 * Describe：采购合同结算单仓储
 */

namespace ddd\repository\settlement;

use ddd\domain\IAggregateRoot;
use ddd\domain\entity\settlement\BuyContractSettlement;
use ddd\domain\entity\settlement\SettlementMode;
use ddd\domain\entity\value\Currency;
use ddd\infrastructure\Utility;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;


class BuyContractSettlementRepository extends SettlementRepository
{

    public function init()
    {
        $this->with=array("contractSettlementSubjectDetail","contractSettlementSubjectDetail.otherAttachments","contractSettlementGoods","contractSettlementGoods.ladings","contractSettlementGoods.settleGoods","contractSettlementGoods.fees","contractSettlementGoods.goodsAttachments");
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName()
    {
        return "ContractSettlement";
    }

    public function getNewEntity()
    {
        return new BuyContractSettlement();
    }



    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return BuyContractSettlement|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(),false);
        $entity->settle_currency = Currency::getCurrency($model->currency);
        $entity->goods_amount = $model->amount_goods;
        $entity->other_amount = $model->amount_other;
        $entity->total_amount = $model->amount;
        $entity->settle_type = $model->type;

        if($entity->settle_type==SettlementMode::LADING_BILL_MODE_SETTLEMENT){
            $settlements = LadingBillSettlementRepository::repository()->findAllByContractId($model->contract_id);
            if(empty($settlements))
                throw new ZException("LadingBillSettlement对象不存在");

            $entity->bill_settlements = $settlements;
        }else{
            $this->addGoodsEntity($model, $entity, SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT);
        }


        $this->addOtherEntity($model, $entity);

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
            throw new ZException("BuyContractSettlement对象不存在");

        $contract = \Contract::model()->findByPk($entity->contract_id);
        if(empty($contract))
            throw new ZModelNotExistsException($entity->contract_id,'Contract');

        if(empty($contract->settle_type)){
            $contract->settle_type = SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT;
            $res = $contract->save();
            if (!$res)
                throw new ZModelSaveFalseException($contract);
        }

        $model = array();

        $id=$entity->getId();
        if(!empty($id))
            $model = \ContractSettlement::model()->with($this->with)->findByPk($id);

        $this->saveContractSettlement($model, $entity, $contract, SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT);

        if($contract->settle_type==SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT){
            $this->saveSettlementDetail($model, $entity, $contract, SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT);
        }
        
        $this->saveOtherSettlement($model, $entity);

        return true;

    }


    /**
     * 查询采购合同结算单
     * @param contractId
     * @return BuyContractSettlement
     */
    public function findContractSettlement($contractId)
    {
        $condition = "t.contract_id=" . $contractId;

        $model=$this->model()->find($condition);
        if(empty($model))
            return null;

        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(),false);
        $entity->settle_currency = Currency::getCurrency($model->currency);
        $entity->goods_amount = $model->amount_goods;
        $entity->total_amount = $model->amount;
        $entity->settle_date  = Utility::getDate();

        return $entity;
    }


    /**
     * 更新采购合同结算单状态
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    public function updateStatus(BuyContractSettlement $entity)
    {
        if(empty($entity))
            throw new ZException("BuyContractSettlement对象不存在");

        $model=\ContractSettlement::model()->findByPk($entity->settle_id);
        if(empty($model))
            throw new ZModelNotExistsException($entity->settle_id, "ContractSettlement");

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
     * @param BuyContractSettlement $settlement
     * @param $amount
     * @throws ZException
     */
    public function addAndSaveGoodsAmount(BuyContractSettlement $settlement,$amount)
    {
        try
        {
            $this->addAndSaveAmount($settlement->getId(),$amount);
        }
        catch (\Exception $e)
        {
            throw new ZException("增加采购合同结算单货款金额失败");
        }
    }

    /**
     * 更新指定金额
     * @param $id
     * @param $amount
     * @throws ZException
     */
    protected function addAndSaveAmount($id,$amount)
    {
        $rows=\ContractSettlement::model()->updateByPk($id
            ,array(
                "amount_goods"=>new \CDbExpression("amount_goods+".$amount),
                "amount"=>new \CDbExpression("amount+".$amount),
                "update_time"=>new \CDbExpression("now()")
            )
        );
        if($rows!==1)
            throw new ZException("更新金额失败");
    }


    /**
     * 保存提交
     * @param BuyContractSettlement $settlement
     * @throws \Exception
     */
    public function submit(BuyContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 作废
     * @param BuyContractSettlement $settlement
     * @throws \Exception
     */
    public function trash(BuyContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 驳回
     * @param BuyContractSettlement $settlement
     * @throws \Exception
     */
    public function back(BuyContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 设置为结算完成
     * @param BuyContractSettlement $settlement
     * @throws \Exception
     */
    public function setSettled(BuyContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }



}

