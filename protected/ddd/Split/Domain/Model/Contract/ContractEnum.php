<?php
/**
 * Created by: yu.li
 * Date: 2018/5/31
 * Time: 10:59
 * Desc: ContractStatus
 */

namespace ddd\Split\Domain\Model\Contract;


use ddd\Common\BaseEnum;

class ContractEnum extends BaseEnum
{
    /**
     * 合同拆分状态
     */
    const SPLIT_TYPE_NOT_SPLIT = 0;//合同未拆分
    const SPLIT_TYPE_SPLIT = 1;//合同已拆分


    /**
     * 合同类型
     */
    const BUY_CONTRACT = 1;//采购合同
    const SELL_CONTRACT = 2;//销售合同
}