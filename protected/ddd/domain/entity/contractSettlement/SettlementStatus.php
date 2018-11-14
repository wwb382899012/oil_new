<?php
/**
 * Created by vector.
 * DateTime: 2018/3/26 9:54
 * Describe：
 *  合同结算状态
 */

namespace ddd\domain\entity\contractSettlement;


class SettlementStatus
{
    const STATUS_STOP   = -9;//合同结算作废
    const STATUS_RECALL = -2;//合同结算撤回
    const STATUS_BACK   = -1;//合同结算审核驳回
    const STATUS_NEW 	= 0; //合同结算新创建
    const STATUS_TEMP_SAVE = 1;//合同结算已暂存
    const STATUS_SAVED  = 2; //合同结算已保存
    

    const STATUS_SUBMIT = 10; //合同结算已提交
    const STATUS_PASS   = 20; //合同结算审核通过
}