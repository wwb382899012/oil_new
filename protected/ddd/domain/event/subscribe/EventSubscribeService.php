<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/20 11:58
 * Describe：
 */

namespace ddd\domain\event\subscribe;


use ddd\domain\service\contract\ContractEventHandlerService;
use ddd\domain\service\contractSettlement\BuyContractSettlementEventService;
use ddd\domain\service\contractSettlement\SaleContractSettlementEventService;
use ddd\domain\service\risk\PartnerAmountEventService;
use ddd\domain\service\stock\DeliveryOrderEventHandlerService;
use ddd\domain\service\stock\DeliveryOrderEventService;
use ddd\domain\service\stock\DistributionOrderEventService;
use ddd\domain\service\stock\LadingBillEventHandlerService;
use ddd\domain\service\stock\StockInEventService;
use ddd\domain\service\stock\StockOutEventService;
use ddd\Profit\Domain\Model\Invoice\InputInvoiceCheckPassEvent;
use ddd\Profit\Domain\Model\Invoice\InvoiceCheckPassEvent;
use ddd\Profit\Domain\Model\Payment\PayClaimEvent;
use ddd\Profit\Domain\Model\Payment\PayConfirmEvent;
use ddd\Profit\Domain\Model\Payment\ProjectPayConfirmEvent;
use ddd\Profit\Domain\Model\Payment\ReceiveConfirmEvent;
use ddd\Profit\Domain\Model\Stock\BatchSettlePassEvent;
use ddd\Profit\Domain\Service\Invoice\InputInvoiceEventHandlerService;
use ddd\Profit\Domain\Service\Invoice\InvoiceEventHandlerService;
use ddd\Profit\Domain\Service\Payment\PayClaimEventHandlerService;
use ddd\Profit\Domain\Service\Payment\PayConfirmEventHandlerService;
use ddd\Profit\Domain\Service\Payment\ProjectPayConfirmEventHandlerService;
use ddd\Profit\Domain\Service\Payment\ReceiveConfirmEventHandlerService;
use ddd\Split\Domain\Model\Contract\ContractTerminatedEvent;
use ddd\Split\Domain\Model\Contract\ContractTerminateRejectEvent;
use ddd\Split\Domain\Model\Contract\ContractTerminateSubmittedEvent;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyPassedEvent;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyRejectedEvent;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplySubmittedEvent;
use ddd\Split\Domain\Model\ContractSplit\SellContractSplitApplySubmittedEvent;
use ddd\Split\Domain\Model\StockSplit\StockSplitCheckBackEvent;
use ddd\Split\Domain\Model\StockSplit\StockSplitCheckPassEvent;
use ddd\Split\Domain\Model\StockSplit\StockSplitSubmitEvent;
use ddd\Split\Domain\Service\Contract\ContractTerminateEventHandlerService;
use ddd\Split\Domain\Service\ContractSplit\ContractSplitApplyEventHandlerService;
use ddd\Split\Domain\Service\ContractSplit\SellContractSplitApplyEventHandlerService;
use ddd\Split\Domain\Service\StockSplit\StockSplitEventHandlerService;
use ddd\Profit\Domain\Model\Profit\DeliverySettlePassEvent;
use ddd\Profit\Domain\Model\Profit\StockOutPassEvent;



class EventSubscribeService
{
    const ProjectSubmitEvent = "projectSubmit";

    /**
     * 项目驳回事件
     */
    const ProjectRejectEvent = "projectReject";
    const ContractSubmitEvent = "contractSubmit";
    const ContractBackEvent = "contractBack";

    /**
     * 合同结算驳回事件
     */
    const ContractSettledBackEvent = "contractSettledBack";
    /**
     * 合同结算中的事件
     */
    const ContractSettlingEvent = "contractSettling";
    /**
     * 合同已结算的事件
     */
    const ContractSettledEvent = "contractSettled";
    /**
     * 合同完结
     */
    const ContractDoneEvent = "contractDone";

    const LadingBillSubmitEvent = "ladingBillSubmit";
    const LadingBillSettledBackEvent = "ladingBillSettledBack";
    const LadingBillSettlingEvent = "ladingBillSettling";
    const LadingBillSettledEvent = "ladingBillSettled";

    const DeliveryOrderSubmitEvent = "deliveryOrderSubmit";
    const DeliveryOrderBackEvent = "deliveryOrderBack";
    const DeliveryOrderPassEvent = "deliveryOrderPass";
    const DeliveryOrderSettledBackEvent = "deliveryOrderSettledBack";
    const DeliveryOrderSettlingEvent = "deliveryOrderSettling";
    const DeliveryOrderSettledEvent = "deliveryOrderSettled";

    const PayConfirmSubmitEvent = "payConfirmSubmit";
    const PayClaimSubmitEvent = "payClaimSubmit";
    const ReceiptClaimSubmitEvent = "receiptClaimSubmit";
    //
    const StockInRevocationEvent = "stockInRevocation";
    const StockInSubmitEvent = "stockInSubmit";
    const StockInBackEvent = "stockInBack";
    const StockInPassEvent = "stockInPass";
    //
    const StockOutPassEvent = "stockOutPass";
    const StockOutSubmitEvent = "stockOutSubmit";
    const StockOutBackEvent = "stockOutBack";
    const StockOutRevocationEvent = "stockOutRevocation";
    //
    const LadingBillSettlementSubmitEvent = "ladingBillSettlementSubmit";
    const LadingBillSettlementPassEvent = "ladingBillSettlementPass";
    const LadingBillSettlementBackEvent = "ladingBillSettlementBack";
    const DeliveryOrderSettlementSubmitEvent = "deliveryOrderSettlementSubmit";
    const DeliveryOrderSettlementPassEvent = "deliveryOrderSettlementPass";
    const DeliveryOrderSettlementBackEvent = "deliveryOrderSettlementBack";
    const BuyContractSettlementSubmitEvent = "buyContractSettlementSubmit";
    const BuyContractSettlementPassEvent = "buyContractSettlementPass";
    const BuyContractSettlementBackEvent = "buyContractSettlementBack";
    const SaleContractSettlementSubmitEvent = "saleContractSettlementSubmit";
    const SaleContractSettlementPassEvent = "saleContractSettlementPass";
    const SaleContractSettlementBackEvent = "saleContractSettlementBack";


    private static $_c;

    /**
     * 事件绑定配置，格式：key=>['类名','方法名','static'（可选，默认对象调用）]
     * @var array
     */
    /*public static $config=[
            self::ContractSubmitEvent=>[
                ['\ddd\domain\service\risk\PartnerAmountEventService','onContractSubmit'],
            ]
    ];*/

    /**
     * 返回配置信息
     * @return array
     */
    public static function getConfigs() {
        return [
            /*ProjectSubmittedEvent::class=>[
                [Test::class,'onProjectSubmitted',"static"]
            ],*/
            self::ContractSubmitEvent => [
                [new PartnerAmountEventService(), 'onContractSubmit']
            ],
            self::ContractBackEvent => [
                [new PartnerAmountEventService(), 'onContractRejectBack']
            ],
            self::PayConfirmSubmitEvent => [
                [new PartnerAmountEventService(), 'onPayConfirmSubmit']
            ],
            self::PayClaimSubmitEvent => [
                [new PartnerAmountEventService(), 'onPayClaimSubmit']
            ],
            self::ReceiptClaimSubmitEvent => [
                [new PartnerAmountEventService(), 'onReceiptClaimSubmit']
            ],

            self::StockInSubmitEvent => [
                [new StockInEventService(), 'onStockInSubmit']
            ],
            self::StockInRevocationEvent => [
                [new StockInEventService(), 'onStockInRevocation']
            ],
            self::StockInBackEvent => [
                [new StockInEventService(), 'onStockInCheckBack']
            ],
            self::StockInPassEvent => [
                [new PartnerAmountEventService(), 'onStockIn']
            ],
            self::StockOutSubmitEvent => [
                [new StockOutEventService(), 'onStockOutOrderSubmit']
            ],
            self::StockOutRevocationEvent => [
                [new StockOutEventService(), 'onStockOutOrderRevocation']
            ],
            self::StockOutBackEvent => [
                [new StockOutEventService(), 'onStockOutOrderCheckBack']
            ],
            self::StockOutPassEvent => [
                [new PartnerAmountEventService(), 'onStockOut']
            ],
            self::LadingBillSettlementSubmitEvent => [
                [new LadingBillEventHandlerService(), 'onLadingBillSettlementSubmit']
            ],
            self::LadingBillSettlementPassEvent => [
                [new BuyContractSettlementEventService(), 'onLadingBillSettlementPass'],
                [new LadingBillEventHandlerService(), 'onLadingBillSettlementPass'],
                [new StockInEventService(), 'onLadingBillSettlementPass'],
                [new PartnerAmountEventService(), 'onLadingBillSettlementPass'],

            ],
            self::LadingBillSettlementBackEvent => [
                [new LadingBillEventHandlerService(), 'onLadingBillSettlementReject']
            ],
            self::DeliveryOrderSettlementSubmitEvent => [
                [new DeliveryOrderEventHandlerService(), 'onDeliveryOrderSettlementSubmit']
            ],
            self::DeliveryOrderSettlementPassEvent => [
                [new SaleContractSettlementEventService(), 'onDeliveryOrderSettlementPass'],
                [new DeliveryOrderEventHandlerService(), 'onDeliveryOrderSettlementPass'],
                [new StockOutEventService(), 'onDeliveryOrderSettlementPass'],
                [new PartnerAmountEventService(), 'onDeliveryOrderSettlementFinish'],
            ],
            self::DeliveryOrderSettlementBackEvent => [
                [new DeliveryOrderEventHandlerService(), 'onDeliveryOrderSettlementReject']
            ],
            self::BuyContractSettlementSubmitEvent => [
                [new LadingBillEventHandlerService(), 'onBuyContractSettlementSubmit'],
                [new ContractEventHandlerService(), 'onAfterContractSettlementSubmit']
            ],
            self::BuyContractSettlementPassEvent => [
                [new LadingBillEventHandlerService(), 'onBuyContractSettlementPass'],
                [new ContractEventHandlerService(), 'onAfterContractSettled'],
                [new StockInEventService(), 'onBuyContractSettlementPass'],
                [new PartnerAmountEventService(), 'onBuyContractSettlementFinish'],
            ],
            self::BuyContractSettlementBackEvent => [
                [new LadingBillEventHandlerService(), 'onBuyContractSettlementReject'],
                [new ContractEventHandlerService(), 'onAfterContractSettlementReject']
            ],
            self::SaleContractSettlementSubmitEvent => [
                [new DeliveryOrderEventHandlerService(), 'onSaleContractSettlementSubmit'],
                [new ContractEventHandlerService(), 'onAfterContractSettlementSubmit']
            ],
            self::SaleContractSettlementPassEvent => [
                [new DeliveryOrderEventHandlerService(), 'onSaleContractSettlementPass'],
                [new ContractEventHandlerService(), 'onAfterContractSettled'],
                [new StockOutEventService(), 'onSellContractSettlementPass'],
                [new PartnerAmountEventService(), 'onSaleContractSettlementFinish'],
            ],
            self::SaleContractSettlementBackEvent => [
                [new DeliveryOrderEventHandlerService(), 'onSaleContractSettlementReject'],
                [new ContractEventHandlerService(), 'onAfterContractSettlementReject']
            ],
            //合同平移提交相关事件处理
            ContractSplitApplySubmittedEvent::class => [
                [new ContractSplitApplyEventHandlerService(), 'onContractSplitApplySubmitted'],
            ],
            //合同平移审核驳回相关事件处理
            ContractSplitApplyRejectedEvent::class => [
                [new ContractSplitApplyEventHandlerService(), 'onContractSplitApplyRejected'],
            ],
            //合同平移审核通过相关事件处理
            ContractSplitApplyPassedEvent::class => [
                [new ContractSplitApplyEventHandlerService(), 'onContractSplitApplyPassed'],
            ],
            //合同终止相关事件处理
            ContractTerminateSubmittedEvent::class => [
                [new ContractTerminateEventHandlerService, 'onContractTerminateSubmitted'],
                [new ContractEventHandlerService(), 'onAfterContractTerminateSubmitted']
            ],
            ContractTerminatedEvent::class => [
                [new ContractTerminateEventHandlerService(), 'onContractTerminatePassed'],
                [new ContractEventHandlerService(), 'onAfterContractTerminated']
            ],
            ContractTerminateRejectEvent::class => [
                [new ContractTerminateEventHandlerService(), 'onContractTerminateReject'],
                [new ContractEventHandlerService(), 'onAfterContractTerminateBack']
            ],
            //出入库平移提交相关事件处理
            StockSplitSubmitEvent::class => [
                [new StockSplitEventHandlerService(), 'onSubmitted'],
            ],
            //出入库平移审核驳回相关事件处理
            StockSplitCheckBackEvent::class => [
                [new StockSplitEventHandlerService(), 'onCheckBacked'],
            ],
            //出入库平移审核通过相关事件处理
            StockSplitCheckPassEvent::class => [
                [new StockSplitEventHandlerService(), 'onCheckPassed'],
            ],
            //利润报表 银行流水认领成功事件处理
            ReceiveConfirmEvent::class => [
                [new ReceiveConfirmEventHandlerService(), 'onReceiveConfirm'],
            ],
            //利润报表 合同下付款实付完成事件处理
            PayConfirmEvent::class => [
                [new PayConfirmEventHandlerService(), 'onPayConfirm'],
            ],
            //利润报表 后补项目合同付款认领事件处理
            PayClaimEvent::class => [
                [new PayClaimEventHandlerService(), 'onPayClaim'],
            ],
            //利润报表 进项票审核通过 事件
            InputInvoiceCheckPassEvent::class => [
                [new InputInvoiceEventHandlerService(), 'onInputInvoiceCheckPass'],
            ],
            //利润报表 销项票开票审核通过 事件
            InvoiceCheckPassEvent::class => [
                [new InvoiceEventHandlerService(), 'onInvoiceCheckPass'],
            ],
            //利润报表 项目下付款实付完成 事件
            ProjectPayConfirmEvent::class => [
                [new ProjectPayConfirmEventHandlerService(), 'onProjectPayConfirm'],
            ],
        ];
    }

    /**
     * 获取需要绑定的事件响应
     * @param $key
     * @return array|null
     */
    public static function getBinds($key) {
        if (empty($key))
            return null;

        if (self::$_c[$key])
            return self::$_c[$key];

        $config = static::getConfigs();
        self::$_c[$key] = $config[$key];
        return self::$_c[$key];

    }

    /**
     * 绑定事件处理到对象
     * @param $entity
     * @param $eventName
     * @param null $key 事件绑定的key，默认和事件名一致
     */
    public static function bind($entity, $eventName, $key = null) {
        if (empty($key))
            $key = $eventName;
        $binds = static::getBinds($key);
        if (!is_array($binds))
            return;
        foreach ($binds as $v) {
            $handler = [];
            if ($v[2] == "static")
                $handler = array($v[0], $v[1]);
            else {
                if (is_string($v[0]))
                    $handler = array(new $v[0], $v[1]);
                else
                    $handler = array($v[0], $v[1]);
            }

            $entity->attachEventHandler($eventName, $handler);
        }
    }
}