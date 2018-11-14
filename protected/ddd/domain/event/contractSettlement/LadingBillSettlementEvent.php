<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/6 14:52
 * Describe：
 */

namespace ddd\domain\event\contractSettlement;


use ddd\Common\Domain\BaseEvent;

class LadingBillSettlementEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '入库通知单结算审核通过';
    }
}