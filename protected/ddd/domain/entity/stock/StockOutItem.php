<?php
/**
 * Created by vector.
 * DateTime: 2018/3/29 18:13
 * Describe：出库单明细
 */

namespace ddd\domain\entity\stock;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Quantity;


class StockOutItem extends BaseEntity
{
    /**
     * @var      int
     */
    public $goods_id;

    /**
     * @var      bigint
     */
    public $contract_id;


    /**
     * @var      Quantity
     */
    public $quantity;
    /**
     * @var      Quantity  配货数量
     */
    public $delivery_quantity;
    public $stock_in_code;//配货入库单编号
    public $remark;

    /**
     * 创建出库单商品明细项
     * @param DeliveryOrderDistributeItem|null $item
     * @return StockInItem
     */
    public static function create(DeliveryOrderDistributeItem $item=null)
    {
        $entity = new StockOutItem();
        if(!empty($item))
        {
            //$stock = StockRepository::repository()->findByPk($item->stock_id);
            $entity->goods_id = $item->goods_id;
            $quantity   = $item->quantity - $item->quantity_actual;
            $quantity   = $quantity<0?0:$quantity;
            $entity->quantity     = $quantity;
        }

        return $entity;

    }

}