<?php

namespace ddd\Profit\Application\Estimate;

use ddd\Common\Application\TransactionService;
use ddd\Common\Domain\Value\Quantity;
use ddd\domain\entity\contractSettlement\SettlementMode;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZInvalidArgumentException;
use ddd\infrastructure\Utility;
use ddd\Profit\Domain\Model\Stock\StockNoticeRepository;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderRepository;
use ddd\Profit\Domain\Contract\IContractRepository;

use ddd\Profit\Application\Estimate\EstimateProfitService;

/**
 * 预估利润  事件服务
 * Class ProfitEventService
 * @package ddd\Profit\Application
 */
class EstimateProfitEventService extends TransactionService
{
    use StockNoticeRepository;

    public function __construct()
    {

    }

    /**
     * @name:onDeliverySettlePass
     * @desc: 发货单结算完成，触发事件
     * @param:* @param $orderId 发货单id 
     * @throw: * @throws \Exception
     * @return:string
     */
    public function onDeliverySettlePass($orderId){
        if(empty($orderId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);

        \AMQPService::publishDeliveryOrderSettlementCheckPass($orderId);


    }
    /**
     * @name:onDeliverySettlePass
     * @desc: 销售合同结算完成，触发事件
     * @param:* @param $contractId 合同id
     * @throw: * @throws \Exception
     * @return:string
     */
    public function onContractSettlePass($contractId){
        if(empty($contractId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);

        $contract = DIService::getRepository(IContractRepository::class)->findByContractId($contractId);
        if($contract->settle_type == SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT){

            $orderList = DIService::getRepository(IDeliveryOrderRepository::class)->findAllByContractId($contractId);
            if(!empty($orderList)) {
                foreach ($orderList as $key => $value) {
                    \AMQPService::publishDeliveryOrderSettlementCheckPass($value['order_id']);
                }
            }
        }



    }


    /**
     * @name:onBatchSettlePass
     * @desc: 入库通知单结算完成 事件
     * @param:* @param $batchId
     * @throw: * @throws \Exception
     * @return:string|void
     */
    public function onBatchSettlePass($batchId){
        if(empty($batchId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);

        \AMQPService::publishLadingBillSettlementCheckPass($batchId);

    }

    /**
     * @name:onBuyContractSettlePass
     * @desc: 采购合同结算完成 事件
     * @param:* @param $batchId
     * @throw: * @throws \Exception
     * @return:string|void
     */
    public function onBuyContractSettlePass($contractId){
        if(empty($contractId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);

        $contract = DIService::getRepository(IContractRepository::class)->findByContractId($contractId);
        if($contract->settle_type == SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT) {

            $stockNoticeList = $this->getStockNoticeRepository()->findByContractId($contractId);
            if (!empty($stockNoticeList)) {
                foreach ($stockNoticeList as $key => $value) {
                    \AMQPService::publishLadingBillSettlementCheckPass($value['batch_id']);
                }
            }
        }
    }


}