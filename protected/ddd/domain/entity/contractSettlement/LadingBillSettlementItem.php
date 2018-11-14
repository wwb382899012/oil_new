<?php
/**
 * Created by vector.
 * DateTime: 2018/3/23 17:26
 * Describe：提单结算明细
 */

namespace ddd\domain\entity\contractSettlement;

use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\error\ZException;

class LadingBillSettlementItem extends BaseEntity
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
    public $batch_id;
    
    /**
    * @var      Quantity
    */
    public $in_quantity;
    public $in_quantity_sub;
    
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
