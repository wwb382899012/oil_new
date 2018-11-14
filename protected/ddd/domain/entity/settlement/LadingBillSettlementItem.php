<?php
/**
 * Created by vector.
 * DateTime: 2018/3/23 17:26
 * Describe：提单结算明细
 */

namespace ddd\domain\entity\settlement;

use ddd\domain\entity\BaseEntity;

class LadingBillSettlementItem extends BaseEntity
{

    #region property

    /**
     * 商品
     * @var   int
     */
    public $goods_id;

    /**
     * 入库通知单id
     * @var   bigint
     */
    public $batch_id;

    /**
     * 入库数量
     * @var   Quantity
     */
    public $in_quantity;

    /**
     * 第二单位入库数量
     * @var   Quantity
     */
    public $in_quantity_sub;

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
     * 损耗量
     * @var   Quantity
     */
    public $loss_quantity;

    /**
     * 第二单位损耗量
     * @var   Quantity
     */
    public $loss_quantity_sub;

    /**
     * 结算单价
     * @var   int
     */
    public $settle_price;

    /**
     * 结算金额
     * @var   int
     */
    public $settle_amount;

    /**
     * 结算汇率
     * @var   float
     */
    public $exchange_rate;

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
     * 备注
     * @var   text
     */
    public $remark;

    #endregion

    /**
     * 创建对象
     * @param int $goodsId
     * @return LadingBillSettlementItem
     */
    public static function create($goodsId)
    {
        if(empty($goodsId))
            throw new ZException("入库通知单商品不存在");

       $entity = new LadingBillSettlementItem();
       $entity->goods_id = $goodsId;

       return $entity;
    }

}
