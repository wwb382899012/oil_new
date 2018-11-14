<?php
/**
 * Created by vector.
 * DateTime: 2018/4/10 12:24
 * Describe：
 */

namespace ddd\domain\event\contractSettlement;


use ddd\Common\Domain\BaseEvent;

class BuyContractSettlementSubmitEvent extends ContractSettlementSubmitEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '采购合同结算提交';
    }
}