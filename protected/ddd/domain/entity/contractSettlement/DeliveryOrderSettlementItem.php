<?php
/**
 * Created by vector.
 * DateTime: 2018/3/23 17:26
 * Describe：发货单结算明细
 */

namespace ddd\domain\entity\contractSettlement;

use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\error\ZException;

class DeliveryOrderSettlementItem extends BaseEntity
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
     * 发货单id
     * @var   bigint
     */
    public $order_id;

    /**
     * 出库数量
     * @var   Quantity
     */
    public $out_quantity;

    /**
     * 第二单位出库数量
     * @var   Quantity
     */
    public $out_quantity_sub;

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
     * @return DeliveryOrderSettlementItem
     */
    public static function create($goodsId)
    {
        if(empty($goodsId))
            throw new ZException("发货单商品不存在");

       $entity = new DeliveryOrderSettlementItem();
       $entity->goods_id = $goodsId;

       return $entity;
    }



}
