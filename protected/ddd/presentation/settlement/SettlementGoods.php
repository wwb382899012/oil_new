<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/3 11:29
 * Describe：
 */

namespace ddd\presentation\settlement;

use ddd\presentation\BaseObject;

class SettlementGoods extends BaseObject
{
    public $item_id;
    /**
     * @var      int  商品id
     */
    public $goods_id;

    /**
     * @var      varchar  商品名称
     */
    public $goods_name;
    /**
     * @var      int   结算id
     */
    public $bill_id;
    /**
     * @var      int   结算单编号
     */
    public $bill_code;

    /**
     * @var      int   结算数量
     */
    public $quantity;

    /**
     * @var      int   结算数量  子单位
     */
    public $quantity_sub;

    /**
     * @var      int   损耗量
     */
    public $quantity_loss;
    /**
     * @var      int   损耗量 子单位
     */
    public $quantity_loss_sub;

    /**
     * @var      float  结算单价
     */
    public $price;

    /**
     * @var      float  结算金额
     */
    public $amount;

    /**
     * @var      object  入库单数量或者出库单数量
     */
    public $bill_quantity;
    /**
     * @var      object  入库单数量或者出库单数量  子单位
     */
    public $bill_quantity_sub;

    /**
     * @var      float  人民币结算单价
     */
    public $price_cny;

    /**
     * @var      float  人民币结算金额
     */
    public $amount_cny;

    /**
     * @var      float  结算汇率
     */
    public $unit_rate;
    /**
     * @var      boolean 是否有明细录入
     */
    public $hasDetail = false;
    /**
     * @var      string 备注
     */
    public $remark;
    /**
     * @var      array   入库通知单列表 或者 发货单列表
     */
    public $bill_items;
    /**
     * @var      array   结算单附件
     */
    public $settleFiles;
    /**
     * @var      array   其他附件
     */
    public $goodsOtherFiles;
    /**
     * @var      array   结算明细
     */
    public $settlementGoodsDetail;


}