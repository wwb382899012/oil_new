<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:29
 * Describe：
 */

namespace ddd\application\stock;

use ddd\application\dto\stock\DeliveryOrderDTO;
use ddd\Common\Application\TransactionService;
use ddd\repository\stock\DeliveryOrderRepository;

class DeliveryOrderService extends TransactionService
{
    /**
     * 获取发货单
     * @param $order_id 发货单id
     * @return array|bool
     * @throws \Exception
     */
    public function getDeliveryOrder($order_id)
    {
        $deliveryOrderEntity = DeliveryOrderRepository::repository()->findByPk($order_id);
        if(!empty($deliveryOrderEntity)){
            $DeliveryOrderDTO = new DeliveryOrderDTO();
            $DeliveryOrderDTO->fromEntity($deliveryOrderEntity);
            return $DeliveryOrderDTO->getAttributes();
        }
        else 
          return array();
           
    }
    /**
     * 发货单是否可以结算
     * @param $order_id 发货单id
     * @return array|bool
     * @throws \Exception
     */
    public function isCanSettle($order_id)
    {
        $deliveryOrderEntity = DeliveryOrderRepository::repository()->findByPk($order_id);
        if(!empty($deliveryOrderEntity)){
            $service = new \ddd\domain\service\stock\DeliveryOrderService();
            $isCanSettle =  $service->isCanSettle($deliveryOrderEntity);
            if(is_bool(isCanSettle)&&$isCanSettle)
                return true;
            else
                return $isCanSettle;
        }
        else {
            return \BusinessError::outputError(\OilError::$DELIVERY_ORDER_NOT_EXIST, array('order_id' => $order_id));

        }

    }
}