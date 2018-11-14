<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/4/19 0019
 * Time: 16:00
 */

namespace ddd\domain\service\stock;


use ddd\Common\Domain\BaseService;
use ddd\domain\event\contractSettlement\BuyContractSettlementEvent;
use ddd\domain\event\contractSettlement\LadingBillSettlementEvent;
use ddd\domain\service\contractSettlement\event\BuyContractSettlementEventHandler;
use ddd\domain\service\contractSettlement\event\LadingBillSettlementEventHandler;

class StockInEventService extends BaseService
{
    /**
     * @desc 入库通知单结算审批通过，更新所有入库单状态
     * @param LadingBillSettlementEvent $event
     */
    public function onLadingBillSettlementPass(LadingBillSettlementEvent $event)
    {
        $handler = new LadingBillSettlementEventHandler($event);
        $handler->updateStockInSettledStatus();
    }

    /**
     * @desc 采购合同结算审批通过，更新所有入库单状态
     * @param BuyContractSettlementEvent $event
     */
    public function onBuyContractSettlementPass(BuyContractSettlementEvent $event)
    {
        $handler = new BuyContractSettlementEventHandler($event);
        $handler->updateStockInSettledStatus();
    }
}