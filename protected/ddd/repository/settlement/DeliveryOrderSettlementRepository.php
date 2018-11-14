<?php

/**
 * Created by vector.
 * DateTime: 2018/3/26 11:33
 * Describe：提单结算单仓储
 */

namespace ddd\repository\settlement;

use ddd\domain\entity\settlement\DeliveryOrderSettlement;

use ddd\domain\IAggregateRoot;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\domain\entity\value\Currency;
use ddd\domain\entity\settlement\SettlementMode;


class DeliveryOrderSettlementRepository extends SettlementRepository
{
    
	public function init()
    {
        $this->with=array("contractSettlementGoods","contractSettlementGoods.orderSettlement","contractSettlementGoods.settleGoods","contractSettlementGoods.settleGoods.goodsItems","contractSettlementGoods.deliveryAttachments");
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName()
    {
        return "DeliverySettlement";
    }

    public function getNewEntity()
    {
        return new DeliveryOrderSettlement();
    }

    

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return DeliveryOrderSettlement|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(),false);
        $entity->settle_currency = Currency::getCurrency($model->currency);
        $entity->order_id = $model->order_id;
        $entity->goods_amount = $model->amount_goods;

        $this->addGoodsEntity($model, $entity, SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT);

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
            throw new ZException("DeliveryOrderSettlement对象不存在");
        
        $contract = \Contract::model()->findByPk($entity->contract_id);
        if(empty($contract))
            throw new ZModelNotExistsException($entity->contract_id, "Contract");

        if(empty($contract->settle_type)){
            $contract->settle_type = SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT;
            $res = $contract->save();
            if (!$res)
                throw new ZModelSaveFalseException($contract);
        }else{
            if($contract->settle_type != SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT)
                throw new ZException(BusinessError::Buy_Contract_Cannot_Settle,array("contract_code"=>$contract->contract_code));
        }

        $contractSettlement = \ContractSettlement::model()->find("contract_id=".$contract->contract_id);
        $this->saveContractSettlement($contractSettlement, $entity, $contract, SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT);

        $model = array();
        $id=$entity->getId();
        if(!empty($id))
            $model = \DeliverySettlement::model()->with($this->with)->findByPk($id);

        $this->saveBillSettlement($model, $entity, $contract, SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT);

        $this->saveSettlementDetail($model, $entity, $contract,SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT);

        return true;

    }


    /**
     * 查询合同下所有的发货结算单
     * @param contractId
     * @return DeliveryOrderSettlement
     */
    public function findAllByContractId($contractId)
    {
        $condition = "t.contract_id=" . $contractId;

        return $this->findAll($condition);
    }


    /**
     * 更新发货单结算单状态
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    protected function updateStatus(DeliveryOrderSettlement $entity)
    {
        if(empty($entity))
            throw new ZException("DeliveryOrderSettlement对象不存在");

        $model=\DeliverySettlement::model()->findByPk($entity->settle_id);
        if(empty($model))
            throw new ZModelNotExistsException($entity->settle_id, "DeliverySettlement");

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
     * 更新销售合同结算单金额
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    public function updateContractSettlementAmount(DeliveryOrderSettlement $entity)
    {
        if(empty($entity))
            throw new ZException("DeliveryOrderSettlement对象不存在");

        $model=\ContractSettlement::model()->find("contract_id=".$entity->contract_id);
        if(empty($model))
            throw new ZException("ContractSettlement对象不存在");

        $model->amount_goods += empty($entity->goods_amount) ? 0 : $entity->goods_amount;
        $model->amount = $model->amount_goods;
        $res = $model->save();
        if(!$res)
            throw new ZModelSaveFalseException($model);

        return true;
    }


    /**
     * 保存提交
     * @param DeliveryOrderSettlement $settlement
     * @throws \Exception
     */
    public function submit(DeliveryOrderSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 作废
     * @param DeliveryOrderSettlement $settlement
     * @throws \Exception
     */
    public function trash(DeliveryOrderSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 驳回
     * @param DeliveryOrderSettlement $settlement
     * @throws \Exception
     */
    public function back(DeliveryOrderSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 设置为结算完成
     * @param DeliveryOrderSettlement $settlement
     * @throws \Exception
     */
    public function setSettled(DeliveryOrderSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

}

