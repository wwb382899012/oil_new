<?php

/**
 * Created by vector.
 * DateTime: 2018/3/29 11:43
 * Describe：发货单服务
 */
namespace ddd\domain\service\stock;

use ddd\Common\Domain\BaseService;
use ddd\domain\entity\stock\DeliveryOrder;
use ddd\repository\stock\StockOutRepository;


class DeliveryOrderService extends BaseService
{
	/**
     * @desc 发货单是否可以结算
     * @param DeliveryOrder $deliveryOrder
     * @return boolean
     */
    public function isCanSettle(DeliveryOrder $deliveryOrder)
	{
        $isBool = $this->isStockOutFinish($deliveryOrder);

        if($isBool === true){
            if($deliveryOrder->status==\DeliveryOrder::STATUS_SETTLE_SUBMIT || $deliveryOrder->status<\DeliveryOrder::STATUS_PASS)
                $isBool = "当前状态下的发货单（".$deliveryOrder->code."）不能发起结算！";
        }
        

        return $isBool;
        
	}

    /**
     * @desc 所有出库单是否已经完成
     * @param $orderId
     * @return boolean
     */
    public function isStockOutFinish(DeliveryOrder $deliveryOrder)
    {
        if(empty($deliveryOrder))
            return "参数有误，不能发起结算";
        
        $stockOuts = StockOutRepository::repository()->findAllByOrderId($deliveryOrder->order_id);
        if (!empty($stockOuts)) {
            $num = 0;
            foreach ($stockOuts as $key => $row) {
                if($row->status!=\StockOutOrder::STATUS_SUBMITED) {
                    if($row->status==\StockOutOrder::STATUS_INVALIDITY)
                        $num += 1;
                    else
                        return "发货单（".$deliveryOrder->code."）下出库单（".$row->code."）未审核通过，不能发起结算！";
                }
            }

            if(count($stockOuts)==$num)
                return "发货单（".$deliveryOrder->code."）下所有出库单都是作废状态，不能发起结算！";

            return true;
        }

        return "发货单（".$deliveryOrder->code."）下没有出库单，不能发起结算！";
    }



}