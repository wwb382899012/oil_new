<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/9 16:07
 * Describe：
 */

namespace ddd\domain\event\contract;


use ddd\Common\Domain\BaseEvent;

class ContractSubmitEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '商务确认提交';
    }
}