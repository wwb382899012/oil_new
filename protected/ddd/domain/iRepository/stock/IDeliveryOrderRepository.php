<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/24
 * Time: 11:03
 */

namespace ddd\domain\iRepository\stock;


use ddd\domain\entity\stock\DeliveryOrder;
use ddd\Common\Domain\IRepository;

interface IDeliveryOrderRepository  extends IRepository
{
    function submit(DeliveryOrder $deliveryOrder);

    function back(DeliveryOrder $deliveryOrder);

    function pass(DeliveryOrder $deliveryOrder);

    function setSettledBack(DeliveryOrder $deliveryOrder);

    function setOnSettling(DeliveryOrder $deliveryOrder);

    function setSettled(DeliveryOrder $deliveryOrder);

}