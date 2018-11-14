<?php

namespace ddd\Profit\Application;

use ddd\Common\Application\TransactionService;
use ddd\Common\Domain\Value\Quantity;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZInvalidArgumentException;
use ddd\infrastructure\Utility;
use ddd\Profit\Domain\Model\Profit\DeliveryOrderProfit;
use ddd\Profit\Domain\Model\Profit\IDeliveryOrderProfitRepository;
use ddd\Profit\Domain\Model\Stock\BuyGoodsCost;
use ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderRepository;
use ddd\Profit\Domain\Model\Profit\ISellContractProfitRepository;
use ddd\domain\iRepository\contract\IContractRepository;
use ddd\Profit\Domain\Model\Profit\SellContractProfit;
use ddd\domain\iRepository\stock\IStockOutRepository;
use ddd\Profit\Domain\Model\Stock\IStockNoticeCostRepository;
use ddd\Profit\Domain\Model\Stock\StockNoticeCost;
use ddd\Profit\Domain\Model\Stock\IStockNoticeRepository;
use ddd\Profit\Domain\Model\Stock\StockNoticeRepository;

/**
 * 结算利润事件 服务
 * Class ProfitEventService
 * @package ddd\Profit\Application
 */
class ProfitEventService extends TransactionService
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

        //采购商品明细 初始化
        $deliveryOrder = DIService::getRepository(IDeliveryOrderRepository::class)->findByPk($orderId);
        $buyGoodsCost = new BuyGoodsCost();
        $buyGoodsCost = $buyGoodsCost->create($deliveryOrder);
        if (!empty($buyGoodsCost)) {
            foreach ($buyGoodsCost as & $item) {
                DIService::getRepository(IBuyGoodsCostRepository::class)->store($item);
            }
        }
        //发货单利润
        ProfitService::service()->createDeliveryOrderProfit($orderId);
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
        $orderList = DIService::getRepository(IDeliveryOrderRepository::class)->findAllByContractId($contractId);
        if(!empty($orderList)){
            foreach($orderList as $key=>$value){

                //采购商品明细 初始化
                $deliveryOrder = DIService::getRepository(IDeliveryOrderRepository::class)->findByPk($value['order_id']);
                $buyGoodsCost = new BuyGoodsCost();
                $buyGoodsCost = $buyGoodsCost->create($deliveryOrder);
                if (!empty($buyGoodsCost)) {
                    foreach ($buyGoodsCost as & $item) {
                        DIService::getRepository(IBuyGoodsCostRepository::class)->store($item);
                    }
                }
                //发货单利润
                ProfitService::service()->createDeliveryOrderProfit($value['order_id']);
            }
        }
    }

    /**
     * @name:onStockOutPass
     * @desc: 出库单审核完成 事件
     * @param:* @param $outOrderId
     * @throw: * @throws \Exception
     * @return:string|void
     */
   /* public function onStockOutPass($outOrderId){
        if(empty($outOrderId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $stockOut = DIService::getRepository(IStockOutRepository::class)->findByPk($outOrderId);
        if(empty($stockOut))
            return \BusinessError::outputError(\OilError::$STOCK_OUT_NOT_EXIST,array('out_order_id'=>$stockOut->out_order_id));

        //发货单利润
        ProfitService::service()->createDeliveryOrderProfit($stockOut->order_id);
    }*/


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
        $re = ProfitService::service()->createStockNoticeCost($batchId);

        \AMQPService::publishBuyGoodsCost($batchId,null); //发起事件
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
        $stockNoticeList = $this->getStockNoticeRepository()->findByContractId($contractId);
        $re=false;
        if(!empty($stockNoticeList)){
            foreach($stockNoticeList as & $item){
                $re = ProfitService::service()->createStockNoticeCost($item->batch_id);
            }
        }

        \AMQPService::publishBuyGoodsCost(null,$contractId); //发起事件
    }


}