<?php

namespace ddd\domain\entity\contractSettlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Expense;

class OtherExpenseItem extends BaseEntity
{
    
    /**
    * @var      Expense
    */
    public $expense;
    
    /**
    * @var      int
    */
    public $expense_amount;
    
    /**
    * @var      int
    */
    public $expense_price;

    /**
    * @var      text
    */
    public $remark;


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