<?php
/**
 * Created by: yu.li
 * Date: 2018/5/30
 * Time: 15:12
 * Desc: ContractTerminateSubmitedEvent
 */

namespace ddd\Split\Domain\Model\Contract;


use ddd\Common\Domain\BaseEvent;

class ContractTerminateSubmittedEvent extends BaseEvent
{
    public function initEventName() {
        parent::initEventName();
        $this->eventName = '合同终止提交';
    }
}