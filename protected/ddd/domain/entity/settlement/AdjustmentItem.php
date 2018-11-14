<?php
/**
 * Created by vector.
 * DateTime: 2018/3/22 17:54
 * Describe：调整明细
 */

namespace ddd\domain\entity\settlement;


use ddd\domain\entity\BaseEntity;
use ddd\domain\entity\value\AdjustMode;
use ddd\domain\entity\value\Quantity;

class AdjustmentItem extends BaseEntity
{

    #region property

    /**
     * 调整方式
     * @var   AdjustMode
     */
    public $adjust_type;

    /**
     * 调整金额
     * @var   int
     */
    public $adjust_amount;

    /**
     * 调整原因
     * @var   text
     */
    public $adjust_reason;

    /**
     * 结算数量
     * @var   Quantity
     */
    public $settle_quantity;

    /**
     * 第二单位结算数量
     * @var   Quantity
     */
    public $settle_quantity_sub;

    /**
     * 人民币结算金额
     * @var   int
     */
    public $settle_amount_cny;

    /**
     * 人民币结算单价
     * @var   int
     */
    public $settle_price_cny;

    /**
     * 确定结算数量
     * @var   Quantity
     */
    public $confirm_quantity;

    /**
     * 第二单位确定结算数量
     * @var   Quantity
     */
    public $confirm_quantity_sub;

    /**
     * 确定人民币结算金额
     * @var   int
     */
    public $confirm_amount_cny;

    /**
     * 确定人民币结算单价
     * @var   int
     */
    public $confirm_price_cny;

    #endregion


    /**
     * 创建对象
     * @return AdjustmentItem
     */
    public static function create()
    {
        return new AdjustmentItem();
    }
}