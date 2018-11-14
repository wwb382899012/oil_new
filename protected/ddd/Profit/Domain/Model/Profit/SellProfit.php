<?php
/**
 * Created by wwb.
 * DateTime: 2018/3/20 18:56
 * Describe：销售利润 值对象
 */

namespace ddd\Profit\Domain\Model\Profit;


use ddd\Common\Domain\BaseValue;
use ddd\Common\Domain\IValue;
use ddd\Common\Domain\Value\Quantity;
use ddd\domain\entity\value\Price;

class SellProfit extends BaseValue
{

    /**
     * 销售单价
    * @var      Price
    */
    public $sell_price;
    
    /**
     * 结算出库数量
    * @var      Quantity
    */
    public $settle_quantity;

    /**
     * 销售金额
     * @var      Price
     */
    public $settle_amount;

    public function __construct()
    {


    }





}