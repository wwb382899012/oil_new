<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/24
 * Time: 12:19
 */

namespace ddd\domain\event\stock;


use ddd\Common\Domain\BaseEvent;

class DeliveryOrderSubmitEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '发货单提交';
    }
}