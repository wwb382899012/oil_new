<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/6 14:52
 * Describe：
 */

namespace ddd\domain\event\contractSettlement;


use ddd\Common\Domain\BaseEvent;

class DeliveryOrderSettlementEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '发货单结算审核通过';
    }
}