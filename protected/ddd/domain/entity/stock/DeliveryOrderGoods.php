<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/26 15:28
 * Describe：
 *  发货单商品明细
 */

namespace ddd\domain\entity\stock;


use ddd\Common\Domain\BaseEntity;

class DeliveryOrderGoods extends BaseEntity
{
    /**
     * @var      int
     */
    public $detail_id;
    /**
    * @var      int
    */
    public $goods_id;
     
    /**
    * @var      Quantity  发货数量
    */
    public $quantity;
    public $quantity_sub;


     /**
     * @var      Quantity  总出库数量
     */
    public $out_quantity;
    public $out_quantity_sub;
    
    /**
    * @var      int
    */
    public $remark;

    public static function create($goodsId=0)
    {
        return new DeliveryOrderGoods('',array("goods_id"=>$goodsId));
    }



}