<?php
/**
 * Created by vector.
 * DateTime: 2018/4/10 12:24
 * Describe：
 */

namespace ddd\domain\event\contractSettlement;



class SaleContractSettlementSubmitEvent extends ContractSettlementSubmitEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '销售合同结算提交';
    }
}