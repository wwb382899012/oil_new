<?php

/**
 * Desc: 收款
 * User: susiehuang
 * Date: 2018/3/15 0015
 * Time: 14:47
 */

namespace ddd\domain\event\receipt;


use ddd\Common\Domain\BaseEvent;

class ReceiptClaimedEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '银行流水认领提交';
    }
}