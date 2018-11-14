<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/24
 * Time: 9:51
 */

namespace ddd\domain\service\stock;


use ddd\domain\entity\contractSettlement\SettlementMode;
use ddd\domain\event\contractSettlement\BuyContractSettlementEvent;
use ddd\domain\event\contractSettlement\BuyContractSettlementRejectEvent;
use ddd\domain\event\contractSettlement\BuyContractSettlementSubmitEvent;
use ddd\domain\event\contractSettlement\LadingBillSettlementEvent;
use ddd\domain\event\contractSettlement\LadingBillSettlementRejectEvent;
use ddd\domain\event\contractSettlement\LadingBillSettlementSubmitEvent;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\repository\stock\LadingBillRepository;

class LadingBillEventHandlerService
{
    /**
     * @desc 当提单结算单提交时，处理相关的事件状态变更
     * @param LadingBillSettlementEvent $event
     * @throws \Exception
     */
    public function onLadingBillSettlementSubmit(LadingBillSettlementSubmitEvent $event)
    {
        $entity = LadingBillRepository::repository()->findByPk($event->sender->batch_id);
        if(empty($entity))
            throw new ZEntityNotExistsException($event->sender->batch_id,"LadingBill");

        $entity->setOnSettlingAndSave();
    }

    /**
     * @desc 当提单结算单驳回时，处理相关的事件状态变更
     * @param LadingBillSettlementRejectEvent $event
     * @throws \Exception
     */
    public function onLadingBillSettlementReject(LadingBillSettlementRejectEvent $event)
    {
        $entity = LadingBillRepository::repository()->findByPk($event->sender->batch_id);
        if(empty($entity))
            throw new ZEntityNotExistsException($event->sender->batch_id,"LadingBill");

        $entity->setSettledBackAndSave();
    }

    /**
     * @desc 当提单结算单审核通过时，处理相关的事件状态变更
     * @param LadingBillSettlementEvent $event
     * @throws \Exception
     */
    public function onLadingBillSettlementPass(LadingBillSettlementEvent $event)
    {
        $entity = LadingBillRepository::repository()->findByPk($event->sender->batch_id);
        if(empty($entity))
            throw new ZEntityNotExistsException($event->sender->batch_id,"LadingBill");

        $entity->setSettledAndSave();
    }

    #region buy contract

    /**
     * @desc 当采购合同结算单提交时，处理相关的事件状态变更
     * @param BuyContractSettlementSubmitEvent $event
     * @throws \Exception
     */
    public function onBuyContractSettlementSubmit(BuyContractSettlementSubmitEvent $event)
    {
        if($event->sender->settle_type==SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT){
            $ladingBills = LadingBillRepository::repository()->findAllByContractId($event->sender->contract_id);
            if(!empty($ladingBills)){
                foreach ($ladingBills as $entity){
                    $entity->setOnSettlingAndSave();
                }
            }
        }
    }



    /**
     * @desc 当采购合同结算单驳回时，处理相关的事件状态变更
     * @param BuyContractSettlementRejectEvent $event
     * @throws \Exception
     */
    public function onBuyContractSettlementReject(BuyContractSettlementRejectEvent $event)
    {
        if($event->sender->settle_type==SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT){
            $ladingBills = LadingBillRepository::repository()->findAllByContractId($event->sender->contract_id);
            if(!empty($ladingBills)){
                foreach ($ladingBills as $entity){
                    $entity->setSettledBackAndSave();
                }
            }
        }
    }

    /**
     * @desc 当采购结算单审核通过时，处理相关的事件状态变更
     * @param BuyContractSettlementEvent $event
     * @throws \Exception
     */
    public function onBuyContractSettlementPass(BuyContractSettlementEvent $event)
    {
        if($event->sender->settle_type==SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT){
            $ladingBills = LadingBillRepository::repository()->findAllByContractId($event->sender->contract_id);
            if(!empty($ladingBills)){
                foreach ($ladingBills as $entity){
                    $entity->setSettledAndSave();
                }
            }
        }
    }

    #endregion

}