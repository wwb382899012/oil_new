<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/26 15:28
 * Describe：
 *  提单商品明细
 */

namespace ddd\domain\entity\stock;


use ddd\Common\Domain\BaseEntity;

class LadingBillGoods extends BaseEntity
{
    /**
     * @var      int
     */
    public $id;
    /**
     * @var      int
     */
    public $goods_id;

    /**
     * @var      Quantity
     */
    public $quantity;

    /**
     * @var      Quantity
     */
    public $quantitySub;

    /**
     * @var      Quantity
     */
    public $in_quantity;
    public $in_quantity_sub;



    public $remark;
    public $store_id;
    public $unit_rate=1;

    public static function create($goodsId=0)
    {
        return new LadingBillGoods('',array("goods_id"=>$goodsId));
    }

    /*public function customAttributeNames()
    {
        return \StockNoticeDetail::model()->attributeNames();
    }*/


}