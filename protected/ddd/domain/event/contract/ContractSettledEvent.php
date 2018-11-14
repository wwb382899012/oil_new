<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/17 10:32
 * Describe：
 */

namespace ddd\domain\event\contract;


use ddd\Common\Domain\BaseEvent;

class ContractSettledEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName(); // TODO: Change the autogenerated stub
        $this->eventName = '合同结算审核通过';
    }
}