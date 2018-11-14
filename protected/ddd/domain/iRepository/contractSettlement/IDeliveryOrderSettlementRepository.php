<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/10 15:27
 * Describe：
 */

namespace ddd\domain\iRepository\contractSettlement;


use ddd\domain\entity\contractSettlement\DeliveryOrderSettlement;
use ddd\Common\Domain\IRepository;

interface IDeliveryOrderSettlementRepository extends IRepository
{
    function submit(DeliveryOrderSettlement $deliveryOrderSettlement);

    function back(DeliveryOrderSettlement $deliveryOrderSettlement);

    function trash(DeliveryOrderSettlement $deliveryOrderSettlement);

    function setSettled(DeliveryOrderSettlement $deliveryOrderSettlement);
}