<?php
/**
 * Created by wwb.
 * DateTime: 2018/3/20 18:56
 * Describe：销售利润 值对象
 */

namespace ddd\Profit\Domain\Model\Profit;


use ddd\Common\Domain\BaseValue;
use ddd\Common\Domain\IValue;
use ddd\domain\entity\value\Price;
use ddd\domain\entity\value\Quantity;

class BuyCost extends BaseValue
{

    /**
     * 采购单价
    * @var      Price
    */
    public $buy_price;
    
    /**
     * 采购金额
    * @var      Price
    */
    public $buy_amount;
    /**
     * 出库数量
     * @var      Quantity
     */
    public $out_quantity;

    public function __construct()
    {

    }







}