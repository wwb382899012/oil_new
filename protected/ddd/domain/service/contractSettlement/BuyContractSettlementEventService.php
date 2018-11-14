<?php

/**
 * Created by vector.
 * DateTime: 2018/4/12 11:50
 * Describe：采购合同结算单事件服务
 */
namespace ddd\domain\service\contractSettlement;

use ddd\domain\event\contractSettlement\LadingBillSettlementEvent;
use ddd\infrastructure\error\ZException;
use ddd\repository\contractSettlement\BuyContractSettlementRepository;


class BuyContractSettlementEventService 
{

    /**
     * @desc 当提单结算单审核通过时，触发采购合同货款金额增加事件
     * @param LadingBillSettlementEvent $event
     * @throws \Exception
     */
    public function onLadingBillSettlementPass(LadingBillSettlementEvent $event)
    {
        $entity = BuyContractSettlementRepository::repository()->find("t.contract_id=".$event->sender->contract_id);
        if(empty($entity))
            throw new ZException("BuyContractSettlement对象不存在");

        $entity->addAndSaveGoodsAmount($event->sender->goods_amount);
    }

}