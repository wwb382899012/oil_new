<?php
/**
 * Created by vector.
 * DateTime: 2018/4/11 18:52
 * Describe：
 */

namespace ddd\domain\event\contractSettlement;


use ddd\Common\Domain\BaseEvent;

class LadingBillSettlementSubmitEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '入库通知单结算提交';
    }
}