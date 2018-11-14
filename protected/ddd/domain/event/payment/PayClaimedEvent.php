<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/6 17:00
 * Describe：
 *  付款认领事件
 */

namespace ddd\domain\event\payment;


use ddd\Common\Domain\BaseEvent;

class PayClaimedEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '付款认领提交';
    }
}