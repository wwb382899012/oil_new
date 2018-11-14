<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/26 14:47
 * Describe：
 *  公司主体
 */

namespace ddd\domain\entity\stock;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Quantity;

class StockInItem extends BaseEntity
{
    /**
     * @var      bigint
     */
    public $unit_rate;

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
    public $quantity_sub;
    
    public $remark;

    /**
     * 创建入库单商品明细项
     * @param LadingBillGoods|null $ladingItem
     * @return StockInItem
     */
    /*public static function create(LadingBillGoods $ladingItem=null)
    {
        $entity=new StockInItem();
        if(!empty($ladingItem))
        {
            $entity->goods_id=$ladingItem->goods_id;
            $entity->quantity=$ladingItem->quantity;
            $entity->unit_rate=$ladingItem->unit_rate;
            $entity->remark=$ladingItem->remark;
        }
        return $entity;

    }*/

    /**
     * 创建入库单商品明细项
     * @param $goodsId
     * @return StockInItem
     */
    public static function create($goodsId=0)
    {
        return new StockInItem('',array("goods_id"=>$goodsId));
    }
}