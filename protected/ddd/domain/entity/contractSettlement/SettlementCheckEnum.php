<?php
/**
 * Created by vector.
 * DateTime: 2018/4/12 16:20
 * Describe：
 *  结算审核类别枚举
 */

namespace ddd\domain\entity\contractSettlement;


class SettlementCheckEnum
{
	const BUSINESS_LOGISTICS_CHECK  = 1; //物流跟单审核
	const BUSINESS_BUSINESS_CHECK   = 2; //商务导入审核
	const BUSINESS_ACCOUNTANT_CHECK = 3; //财务会计审核
}