<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/5/11
 * Time: 16:33
 */

namespace ddd\domain\entity\settlement;


use ddd\domain\entity\BaseEntity;
use ddd\infrastructure\error\ZException;

class BillSettlementItem extends BaseEntity
{
    #region property

    /**
     * 标识符
     * @var   biginit
     */
    public $item_id;

    /**
     * 商品
     * @var   int
     */
    public $goods_id;

    /**
     * 发货单/入库通知单id
     * @var   bigint
     */
    public $bill_id;

    /**
     * 出库数量
     * @var   Quantity
     */
    public $bill_quantity;

    /**
     * 第二单位出库数量
     * @var   Quantity
     */
    public $bill_quantity_sub;

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
     * @return BillSettlementItem
     */
    public static function create($goodsId)
    {
        if(empty($goodsId))
            throw new ZException("商品信息不存在");

        $entity = new BillSettlementItem();
        $entity->goods_id = $goodsId;

        return $entity;
    }
}