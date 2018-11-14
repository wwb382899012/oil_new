<?php
/**
 * Created by: yu.li
 * Date: 2018/5/30
 * Time: 16:06
 * Desc: ContractTerminateEvent
 */

namespace ddd\Split\Domain\Model\Contract;


use ddd\Common\Domain\BaseEvent;

class ContractTerminatedEvent extends BaseEvent
{
    public function initEventName() {
        parent::initEventName();
        $this->eventName='合同终止审核通过';
    }
}