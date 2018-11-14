<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/24
 * Time: 9:52
 */

namespace ddd\domain\service\stock;


use ddd\domain\entity\contractSettlement\SettlementMode;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementEvent;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementRejectEvent;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementSubmitEvent;
use ddd\domain\event\contractSettlement\SaleContractSettlementEvent;
use ddd\domain\event\contractSettlement\SaleContractSettlementRejectEvent;
use ddd\domain\event\contractSettlement\SaleContractSettlementSubmitEvent;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\repository\stock\DeliveryOrderRepository;

class DeliveryOrderEventHandlerService
{
    /**
     * @desc 当发货单单结算单提交时，处理相关的事件状态变更
     * @param DeliveryOrderSettlementSubmitEvent $event
     * @throws \Exception
     */
    public function onDeliveryOrderSettlementSubmit(DeliveryOrderSettlementSubmitEvent $event)
    {
        $entity = DeliveryOrderRepository::repository()->findByPk($event->sender->order_id);
        if(empty($entity))
            throw new ZEntityNotExistsException($event->sender->order_id,"DeliveryOrder");

        $entity->setOnSettlingAndSave();
    }

    /**
     * @desc 当发货单结算单驳回时，处理相关的事件状态变更
     * @param DeliveryOrderSettlementRejectEvent $event
     * @throws \Exception
     */
    public function onDeliveryOrderSettlementReject(DeliveryOrderSettlementRejectEvent $event)
    {
        $entity = DeliveryOrderRepository::repository()->findByPk($event->sender->order_id);
        if(empty($entity))
            throw new ZEntityNotExistsException($event->sender->order_id,"DeliveryOrder");

        $entity->setSettledBackAndSave();
    }

    /**
     * @desc 当提单结算单审核通过时，处理相关的事件状态变更
     * @param DeliveryOrderSettlementEvent $event
     * @throws \Exception
     */
    public function onDeliveryOrderSettlementPass(DeliveryOrderSettlementEvent $event)
    {
        $entity = DeliveryOrderRepository::repository()->findByPk($event->sender->order_id);
        if(empty($entity))
            throw new ZEntityNotExistsException($event->sender->order_id,"DeliveryOrder");

        $entity->setSettledAndSave();
    }

    #region sale contract

    /**
     * @desc 当销售合同结算单提交时，处理相关的事件状态变更
     * @param SaleContractSettlementSubmitEvent $event
     * @throws \Exception
     */
    public function onSaleContractSettlementSubmit(SaleContractSettlementSubmitEvent $event)
    {
        if($event->sender->settle_type==SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT){
            $DeliveryOrders = DeliveryOrderRepository::repository()->findAllByContractId($event->sender->contract_id);
            if(!empty($DeliveryOrders)){
                foreach ($DeliveryOrders as $entity){
                    $entity->setOnSettlingAndSave();
                }
            }
        }
    }



    /**
     * @desc 当销售合同结算单驳回时，处理相关的事件状态变更
     * @param SaleContractSettlementRejectEvent $event
     * @throws \Exception
     */
    public function onSaleContractSettlementReject(SaleContractSettlementRejectEvent $event)
    {
        if($event->sender->settle_type==SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT){
            $DeliveryOrders = DeliveryOrderRepository::repository()->findAllByContractId($event->sender->contract_id);
            if(!empty($DeliveryOrders)){
                foreach ($DeliveryOrders as $entity){
                    $entity->setSettledBackAndSave();
                }
            }
        }
    }

    /**
     * @desc 当销售结算单审核通过时，处理相关的事件状态变更
     * @param SaleContractSettlementEvent $event
     * @throws \Exception
     */
    public function onSaleContractSettlementPass(SaleContractSettlementEvent $event)
    {
        if($event->sender->settle_type==SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT){
            $DeliveryOrders = DeliveryOrderRepository::repository()->findAllByContractId($event->sender->contract_id);
            if(!empty($DeliveryOrders)){
                foreach ($DeliveryOrders as $entity){
                    $entity->setSettledAndSave();
                }
            }
        }
    }

    #endregion
}