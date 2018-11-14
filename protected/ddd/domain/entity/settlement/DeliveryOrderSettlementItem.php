<?php
/**
 * Created by vector.
 * DateTime: 2018/3/23 17:26
 * Describe：发货单结算明细
 */

namespace ddd\domain\entity\settlement;

use ddd\domain\entity\BaseEntity;
use ddd\infrastructure\error\ZException;

class DeliveryOrderSettlementItem extends BaseEntity
{
    
    /**
    * @var      bigint
    */
    public $item_id;
    
    /**
    * @var      int
    */
    public $goods_id;

    /**
    * @var      bigint
    */
    public $order_id;
    
    /**
    * @var      Quantity
    */
    public $out_quantity;
    public $out_quantity_sub;
    
    /**
    * @var      Quantity
    */
    public $settle_quantity;
    public $settle_quantity_sub;
    
    /**
    * @var      Quantity
    */
    public $loss_quantity;
    public $loss_quantity_sub;
    
    /**
    * @var      int
    */
    public $settle_price;
    
    /**
    * @var      int
    */
    public $settle_amount;
    
    /**
    * @var      float
    */
    public $exchange_rate;
    
    /**
    * @var      int
    */
    public $settle_amount_cny;
    
    /**
    * @var      int
    */
    public $settle_price_cny;

    public $remark;

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
