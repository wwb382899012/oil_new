<?php

/**
 * Created by vector.
 * DateTime: 2018/4/4 17:41
 * Describe：销售合同结算单仓储
 */

namespace ddd\repository\settlement;

use ddd\domain\entity\settlement\SaleContractSettlement;

use ddd\domain\IAggregateRoot;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\domain\entity\value\Currency;
use ddd\domain\entity\settlement\SettlementMode;
use ddd\infrastructure\Utility;


class SaleContractSettlementRepository extends SettlementRepository
{
    

	public function init()
    {
        $this->with=array("contractSettlementSubjectDetail","contractSettlementSubjectDetail.otherAttachments","contractSettlementGoods","contractSettlementGoods.orders","contractSettlementGoods.settleGoods","contractSettlementGoods.settleGoods.goodsItems","contractSettlementGoods.goodsAttachments");
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
        return new SaleContractSettlement();
    }

    

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return SaleContractSettlement|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(),false);
        $entity->settle_currency = Currency::getCurrency($model->currency);
        $entity->goods_amount    = $model->amount_goods;
        $entity->other_amount    = $model->amount_other;
        $entity->total_amount    = $model->amount;
        $entity->settle_type     = $model->type;

        $contract = \Contract::model()->findByPk($model->contract_id);
        if($contract->settle_type==SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT){
            $settlements = DeliveryOrderSettlementRepository::repository()->findAllByContractId($model->contract_id);
            if(empty($settlements))
                throw new ZException("DeliveryOrderSettlement对象不存在");
            
            $entity->bill_settlements = $settlements;
        }else{
            $this->addGoodsEntity($model, $entity, SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT);
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
            throw new ZException("SaleContractSettlement对象不存在");
        
        $contract = \Contract::model()->findByPk($entity->contract_id);
        if(empty($contract))
            throw new ZModelNotExistsException($entity->contract_id, "Contract");
        
        if(empty($contract->settle_type)){
            $contract->settle_type = SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT;
            $res = $contract->save();
            if (!$res)
                throw new ZModelSaveFalseException($contract);
        }

        $id=$entity->getId();
        if(!empty($id))
            $model = \ContractSettlement::model()->with($this->with)->findByPk($id);

        $this->saveContractSettlement($model, $entity, $contract, SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT);

        if($contract->settle_type == SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT){
            $this->saveSettlementDetail($model, $entity, $contract, SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT);
        }
        
        $this->saveOtherSettlement($model, $entity);

        return true;

    }


    /**
     * 查询销售合同结算单
     * @param contractId
     * @return SaleContractSettlement
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
     * @param SaleContractSettlement $settlement
     * @param $amount
     * @throws ZException
     */
    public function addAndSaveGoodsAmount(SaleContractSettlement $settlement,$amount)
    {
        try
        {
            $this->addAndSaveAmount($settlement->getId(),$amount);
        }
        catch (\Exception $e)
        {
            throw new ZException("增加销售合同结算单货款金额失败");
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
     * 更新销售合同结算单状态
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    public function updateStatus(SaleContractSettlement $entity)
    {
        if(empty($entity))
            throw new ZException("SaleContractSettlement对象不存在");

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
     * 保存提交
     * @param SaleContractSettlement $settlement
     * @throws \Exception
     */
    public function submit(SaleContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 作废
     * @param SaleContractSettlement $settlement
     * @throws \Exception
     */
    public function trash(SaleContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 驳回
     * @param BuyContractSettlement $settlement
     * @throws \Exception
     */
    public function back(SaleContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 设置为结算完成
     * @param SaleContractSettlement $settlement
     * @throws \Exception
     */
    public function setSettled(SaleContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }




}

