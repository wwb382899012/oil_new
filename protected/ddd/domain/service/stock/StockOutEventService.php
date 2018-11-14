<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/4/19 0019
 * Time: 16:00
 */

namespace ddd\domain\service\stock;


use ddd\Common\Domain\BaseService;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementEvent;
use ddd\domain\event\contractSettlement\SaleContractSettlementEvent;
use ddd\domain\service\contractSettlement\event\DeliveryOrderSettlementEventHandler;
use ddd\domain\service\contractSettlement\event\SellContractSettlementEventHandler;

class StockOutEventService extends BaseService
{
    /**
     * @desc 发货单结算审批通过，更新所有出库单状态
     * @param DeliveryOrderSettlementEvent $event
     */
    public function onDeliveryOrderSettlementPass(DeliveryOrderSettlementEvent $event)
    {
        $handler = new DeliveryOrderSettlementEventHandler($event);
        $handler->updateStockOutSettledStatus();
    }

    /**
     * @desc 销售合同结算审批通过，更新所有出库单状态
     * @param SaleContractSettlementEvent $event
     */
    public function onSellContractSettlementPass(SaleContractSettlementEvent $event)
    {
        $handler = new SellContractSettlementEventHandler($event);
        $handler->updateStockOutSettledStatus();
    }
}