<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/6 16:59
 * Describe：
 *  实付事件
 */

namespace ddd\domain\event\payment;


use ddd\Common\Domain\BaseEvent;

class PayEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '付款实付提交';
    }
}