<?php
/**
 * Created by vector.
 * DateTime: 2018/4/11 14:52
 * Describe：
 */

namespace ddd\domain\event\contractSettlement;


use ddd\Common\Domain\BaseEvent;

class DeliveryOrderSettlementSubmitEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '发货单结算提交';
    }
}