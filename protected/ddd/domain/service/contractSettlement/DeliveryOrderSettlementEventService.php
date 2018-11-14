<?php

/**
 * Created by vector.
 * DateTime: 2018/3/29 11:43
 * Describe：发货单结算单事件服务
 */
namespace ddd\domain\service\contractSettlement;

use ddd\domain\entity\contractSettlement\SettlementStatus;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementEvent;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementRejectEvent;
use ddd\domain\event\contractSettlement\DeliveryOrderSettlementSubmitEvent;
use ddd\repository\contractSettlement\DeliveryOrderSettlementRepository;
use ddd\repository\stock\DeliveryOrderRepository;



class DeliveryOrderSettlementEventService
{

}