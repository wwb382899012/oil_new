<?php

/**
 * Created by vector.
 * DateTime: 2018/4/12 11:50
 * Describe：销售合同结算单事件服务
 */
namespace ddd\domain\service\contractSettlement;

use ddd\domain\event\contractSettlement\DeliveryOrderSettlementEvent;
use ddd\repository\contractSettlement\SaleContractSettlementRepository;


class SaleContractSettlementEventService
{
    /**
     * @desc 当发货单结算单审核通过时，触发销售合同货款金额增加事件
     * @param DeliveryOrderSettlementEvent $event
     * @throws \Exception
     */
    public function onDeliveryOrderSettlementPass(DeliveryOrderSettlementEvent $event)
    {
        $entity = SaleContractSettlementRepository::repository()->find("t.contract_id=".$event->sender->contract_id);
        if(empty($entity))
            throw new ZException("SaleContractSettlement对象不存在");

        $entity->addAndSaveGoodsAmount($event->sender->goods_amount);
    }

}