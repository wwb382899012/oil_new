<?php

namespace ddd\domain\entity\settlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Expense;

class OtherExpenseItem extends BaseEntity
{

    #region property

    /**
     * 费用科目
     * @var   Expense
     */
    public $expense;

    /**
     * 费用总额
     * @var   int
     */
    public $expense_amount;

    /**
     * 费用单价
     * @var   int
     */
    public $expense_price;

    /**
     * 备注
     * @var   text
     */
    public $remark;

    #endregion


    /**
     * 创建对象
     * @return OtherExpenseItem
     */
    public static function create()
    {
        $entity = new OtherExpenseItem();
        return $entity;
    }
}