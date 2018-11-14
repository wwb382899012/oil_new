<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/11 17:17
 * Describe：
 */

namespace ddd\domain\entity\contract;


use ddd\Common\BaseEnum;

class ContractType extends BaseEnum
{
    /**
     * 采购合同
     */
    const BUY_CONTRACT=1;

    /**
     * 销售合同
     */
    const SELL_CONTRACT=2;
}