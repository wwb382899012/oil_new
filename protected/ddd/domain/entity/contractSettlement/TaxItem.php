<?php


namespace ddd\domain\entity\contractSettlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Tax;

class TaxItem extends BaseEntity
{
    
    /**
    * @var      Tax
    */
    public $tax;
    
    /**
    * @var      float
    */
    public $tax_rate;
    
    /**
    * @var      int
    */
    public $tax_amount;
    
    /**
    * @var      int
    */
    public $tax_price;

    /**
    * @var      text
    */
    public $remark;


    /**
     * 创建对象
     * @return TaxItem
     */
    public static function create()
    {
        $entity = new TaxItem();
        return $entity;
    }
};