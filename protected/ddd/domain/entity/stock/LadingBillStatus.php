<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/30 15:53
 * Describe：
 */

namespace ddd\domain\entity\stock;


class LadingBillStatus
{

    const STATUS_NEW = 0;//新增加
    const STATUS_SUBMIT = 10;//提交

    const STATUS_SETTLE_INVALIDITY = 13;//提单结算作废
    const STATUS_SETTLE_REVOCATION = 14; //提单结算撤回
    const STATUS_SETTLE_BACK = 15; // 打回入库结算
    const STATUS_SETTLE_SUBMIT = 20; // 入库通知结算审核中
    const STATUS_SETTLED = 30; // 入库通知已结算
}