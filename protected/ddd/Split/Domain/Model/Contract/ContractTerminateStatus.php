<?php
/**
 * Created by: yu.li
 * Date: 2018/5/30
 * Time: 16:28
 * Desc: ContractTerminateStatus
 */

namespace ddd\Split\Domain\Model\Contract;


use ddd\Common\BaseEnum;

class ContractTerminateStatus extends BaseEnum
{
    const STATUS_NOT_EDIT = -10;//未编辑
    const STATUS_BACK = -1; //审核驳回
    const STATUS_NEW = 0; //保存
    const STATUS_SUBMIT = 1; //提交待审核
    const STATUS_PASS = 10; //审核通过
}