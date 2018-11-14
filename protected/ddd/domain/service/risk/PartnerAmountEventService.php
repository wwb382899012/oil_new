<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 16:58
 * Describe：
 */

namespace ddd\domain\service\risk;


use ddd\domain\event\contract\ContractRejectEvent;
use ddd\domain\event\contract\ContractSubmitEvent;
use ddd\domain\event\contractSettlement\BuyContractSettlementEvent;
use ddd\domain\event\contractSettlement\SaleContractSettlementEvent;
use ddd\domain\event\payment\PayClaimedEvent;
use ddd\domain\event\payment\PayEvent;
use ddd\domain\event\receipt\ReceiptClaimedEvent;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementEvent;
use ddd\domain\event\contractSettlement\LadingBillSettlementEvent;
use ddd\domain\event\stock\StockInEvent;
use ddd\domain\event\stock\StockOutEvent;
use ddd\Common\Domain\BaseService;
use ddd\domain\service\risk\event\BuyContractSettlementEventHandler;
use ddd\domain\service\risk\event\ContractRejectEventHandler;
use ddd\domain\service\risk\event\ContractSubmitEventHandler;
use ddd\domain\service\risk\event\DeliveryOrderSettlementEventHandler;
use ddd\domain\service\risk\event\LadingBillSettlementEventHandler;
use ddd\domain\service\risk\event\PayClaimedEventHandler;
use ddd\domain\service\risk\event\PayEventHandler;
use ddd\domain\service\risk\event\ReceiptClaimedEventHandler;
use ddd\domain\service\risk\event\SellContractSettlementEventHandler;
use ddd\domain\service\risk\event\StockInEventHandler;
use ddd\domain\service\risk\event\StockOutEventHandler;

class PartnerAmountEventService extends BaseService
{

    /**
     * @desc 当合同提交时，处理相关的事件金额变更 - 增加合同额度
     * @param ContractSubmitEvent $event
     * @throws \Exception
     */
    public function onContractSubmit(ContractSubmitEvent $event)
    {
        $handler = new ContractSubmitEventHandler($event);
        $contractAmountService = new PartnerContractAmountService();
        $contractAmountService->eventHandler = $handler;
        $contractAmountService->updateAmount();
    }

    /**
     * @desc 当合同驳回时，处理相关的事件金额变更 - 减少合同额度
     * @param ContractRejectEvent $event
     * @throws \Exception
     */
    public function onContractRejectBack(ContractRejectEvent $event)
    {
        $handler = new ContractRejectEventHandler($event);
        $contractAmountService = new PartnerContractAmountService();
        $contractAmountService->eventHandler = $handler;
        $contractAmountService->updateAmount();
    }

    /**
     * @desc 当入库单审核通过时，处理相关的事件金额变更 - 减少合同额度和实际额度
     * @param StockInEvent $event
     * @throws \Exception
     */
    public function onStockIn(StockInEvent $event)
    {
        $handler = new StockInEventHandler($event);
        $contractAmountService = new PartnerContractAmountService();
        $contractAmountService->eventHandler = $handler;
        $contractAmountService->updateAmount();

        $usedAmountService = new PartnerUsedAmountService();
        $usedAmountService->eventHandler = $handler;
        $usedAmountService->updateAmount();
    }

    /**
     * @desc 当出库单审核通过时，处理相关的事件金额变更 - 增加实际额度
     * @param StockOutEvent $event
     * @throws \Exception
     */
    public function onStockOut(StockOutEvent $event)
    {
        $handler = new StockOutEventHandler($event);
        $usedAmountService = new PartnerUsedAmountService();
        $usedAmountService->eventHandler = $handler;
        $usedAmountService->updateAmount();
    }

    /**
     * @desc 当付款实付提交时，处理相关的事件金额变更 - 增加实际额度
     * @param PayEvent $event
     * @throws \Exception
     */
    public function onPayConfirmSubmit(PayEvent $event)
    {
        $handler = new PayEventHandler($event);
        $usedAmountService = new PartnerUsedAmountService();
        $usedAmountService->eventHandler = $handler;
        $usedAmountService->updateAmount();
    }

    /**
     * @desc 当付款认领提交时，处理相关的事件金额变更 - 增加实际额度
     * @param PayClaimedEvent $event
     * @throws \Exception
     */
    public function onPayClaimSubmit(PayClaimedEvent $event)
    {
        $handler = new PayClaimedEventHandler($event);
        $usedAmountService = new PartnerUsedAmountService();
        $usedAmountService->eventHandler = $handler;
        $usedAmountService->updateAmount();
    }

    /**
     * @desc 当收款认领提交时，处理相关的事件金额变更 - 减少合同额度和实际额度
     * @param ReceiptClaimedEvent $event
     * @throws \Exception
     */
    public function onReceiptClaimSubmit(ReceiptClaimedEvent $event)
    {
        $handler = new ReceiptClaimedEventHandler($event);
        $contractAmountService = new PartnerContractAmountService();
        $contractAmountService->eventHandler = $handler;
        $contractAmountService->updateAmount();

        $usedAmountService = new PartnerUsedAmountService();
        $usedAmountService->eventHandler = $handler;
        $usedAmountService->updateAmount();
    }

    /**
     * @desc 当提单结算审核通过时，处理相关的事件金额变更
     * @param LadingBillSettlementEvent $event
     * @throws \Exception
     */
    public function onLadingBillSettlementPass(LadingBillSettlementEvent $event)
    {
        $handler = new LadingBillSettlementEventHandler($event);
        $contractAmountService = new PartnerContractAmountService();
        $contractAmountService->eventHandler = $handler;
        $contractAmountService->updateAmount();

        $usedAmountService = new PartnerUsedAmountService();
        $usedAmountService->eventHandler = $handler;
        $usedAmountService->updateAmount();
    }

    /**
     * @desc 当发货单结算完时，处理相关的事件金额变更
     * @param DeliveryOrderSettlementEvent $event
     * @throws \Exception
     */
    public function onDeliveryOrderSettlementFinish(DeliveryOrderSettlementEvent $event)
    {
        $handler = new DeliveryOrderSettlementEventHandler($event);
        $usedAmountService = new PartnerUsedAmountService();
        $usedAmountService->eventHandler = $handler;
        $usedAmountService->updateAmount();
    }

    /**
     * @desc 当采购合同结算审核通过时，处理相关的事件金额变更
     * @param BuyContractSettlementEvent $event
     * @throws \Exception
     */
    public function onBuyContractSettlementFinish(BuyContractSettlementEvent $event)
    {
        $handler = new BuyContractSettlementEventHandler($event);
        $contractAmountService = new PartnerContractAmountService();
        $contractAmountService->eventHandler = $handler;
        $contractAmountService->updateAmount();

        $usedAmountService = new PartnerUsedAmountService();
        $usedAmountService->eventHandler = $handler;
        $usedAmountService->updateAmount();
    }

    /**
     * @desc 当销售合同结算审核通过时，处理相关的事件金额变更
     * @param SaleContractSettlementEvent $event
     * @throws \Exception
     */
    public function onSaleContractSettlementFinish(SaleContractSettlementEvent $event)
    {
        $handler = new SellContractSettlementEventHandler($event);
        $usedAmountService = new PartnerUsedAmountService();
        $usedAmountService->eventHandler = $handler;
        $usedAmountService->updateAmount();
    }
}