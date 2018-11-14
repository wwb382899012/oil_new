<?php

/**
 * Created by vector.
 * DateTime: 2018/3/22 17:54
 * Describe：货款明细
 */

namespace ddd\domain\entity\contractSettlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Currency;

class GoodsExpenseItem extends BaseEntity
{
    
    /**
    * @var      Currency
    */
    public $currency;
    
    /**
    * @var      int
    */
    public $amount;
    
    /**
    * @var      float
    */
    public $exchange_rate;
    
    /**
    * @var      int
    */
    public $amount_cny;
    
    /**
    * @var      int
    */
    public $price;
    
    /**
    * @var      float
    */
    public $tax_exchange_rate;
    
    /**
    * @var      int
    */
    public $tax_amount_cny;


    /**
     * 创建对象
     * @return GoodsExpenseItem
     * @throws \Exception
     */
    public static function create()
    {
        $entity = new GoodsExpenseItem();
        $entity->currency = Currency::getCurrency();
        return $entity;
    }
}