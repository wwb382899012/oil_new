<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/3/20 0020
 * Time: 11:24
 */

namespace ddd\domain\entity\stock;


use ddd\Common\Domain\BaseEntity;

class DeliveryOrderDistributeItem extends BaseEntity
{

    /**
     * @var      int
     */
    public $goods_id;

    /**
     * @var      Quantity
     */
    public $quantity;

    /**
     * @var      bigint
     */
    public $stock_id;


    public static function create(Stock $stock)
    {
        if(empty($stock))
            ExceptionService::throwArgumentNullException("Stock对象",array('class'=>get_class($self), 'function'=>__FUNCTION__));

        $entity = new DeliveryOrderDistributeItem();
        $entity->stock_id = $stock->stock_id;
        $entity->goods_id = $stock->goods_id;

        return $entity;
    }
}