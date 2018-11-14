<?php
/**
 * Created by vector.
 * DateTime: 2018/4/3 20:26
 * Describe：
 *  合同结算维度
 */

namespace ddd\domain\entity\settlement;


class SettlementMode
{
    const LADING_BILL_MODE_SETTLEMENT   	= 1;//提单方式结算
    const BUY_CONTRACT_MODE_SETTLEMENT 		= 2;//采购合同结算
    const DELIVERY_ORDER_MODE_SETTLEMENT   	= 3;//发货单方式结算
    const SALE_CONTRACT_MODE_SETTLEMENT 	= 4;//销售合同方式结算

}