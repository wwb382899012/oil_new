<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/24
 * Time: 10:18
 */

namespace ddd\domain\event\stock;


use ddd\Common\Domain\BaseEvent;

class LadingBillSettledRejectEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '入库通知单结算驳回';
    }
}