<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/24
 * Time: 11:00
 */

namespace ddd\domain\entity\stock;


class DeliveryOrderStatus
{
    const STATUS_BACK = -1;//审核驳回
    const STATUS_NEW = 0;//新增加
    const STATUS_SUBMIT = 10;//提交(待审核)
    const STATUS_PASS = 20;//审核通过

    const STATUS_SETTLE_INVALIDITY = 25;//发货单结算作废
    const STATUS_SETTLE_REVOCATION = 26; //发货单结算撤回
    const STATUS_SETTLE_SUBMIT = 30; // 提交发货单结算
    const STATUS_SETTLE_BACK = 40; // 结算打回
    const STATUS_SETTLE_PASS = 50; // 结算审核通过
}