<?php

/**
 * Created by vector.
 * DateTime: 2018/3/22 17:54
 * Describe：货款明细
 */

namespace ddd\domain\entity\settlement;

use ddd\domain\entity\BaseEntity;
use ddd\domain\entity\value\Currency;

class GoodsExpenseItem extends BaseEntity
{

    #region property

    /**
     * 计价币种
     * @var   Currency
     */
    public $currency;

    /**
     * 计价币种货款金额
     * @var   int
     */
    public $amount;

    /**
     * 汇率
     * @var   float
     */
    public $exchange_rate;

    /**
     * 人民币货款总额
     * @var   int
     */
    public $amount_cny;

    /**
     * 货款单价
     * @var   int
     */
    public $price;

    /**
     * 计税汇率
     * @var   float
     */
    public $tax_exchange_rate;

    /**
     * 计税人民币货款总额
     * @var   int
     */
    public $tax_amount_cny;

    #endregion

    /**
     * 创建对象
     * @return GoodsExpenseItem
     * @throws \Exception
     */
    public static function create()
    {
        $entity = new GoodsExpenseItem();
        return $entity;
    }
}