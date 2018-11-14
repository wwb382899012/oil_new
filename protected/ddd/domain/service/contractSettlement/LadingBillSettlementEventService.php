<?php

/**
 * Created by vector.
 * DateTime: 2018/3/26 11:33
 * Describe：提单结算单事件服务
 */
namespace ddd\domain\service\contractSettlement;


use ddd\domain\entity\contractSettlement\SettlementStatus;
use ddd\domain\event\contractSettlement\LadingBillSettlementEvent;
use ddd\domain\event\contractSettlement\LadingBillSettlementRejectEvent;
use ddd\domain\event\contractSettlement\LadingBillSettlementSubmitEvent;
use ddd\repository\contractSettlement\LadingBillSettlementRepository;
use ddd\repository\stock\LadingBillRepository;


class LadingBillSettlementEventService
{
}