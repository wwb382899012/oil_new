<?php
/**
 * Created by vector.
 * DateTime: 2018/3/22 17:54
 * Describe：调整明细
 */

namespace ddd\domain\entity\contractSettlement;


use ddd\Common\Domain\BaseEntity;

class AdjustmentItem extends BaseEntity
{
    
    /**
    * @var      AdjustMode
    */
    public $adjust_type;

    /**
    * @var      int
    */
    public $adjust_amount;
    
    /**
    * @var      text
    */
    public $adjust_reason;
    
    /**
    * @var      Quantity
    */
    public $settle_quantity;
    public $settle_quantity_sub;
    
    /**
    * @var      int
    */
    public $settle_amount_cny;
    
    /**
    * @var      int
    */
    public $settle_price_cny;
    
    /**
    * @var      Quantity
    */
    public $confirm_quantity;
    public $confirm_quantity_sub;
    
    /**
    * @var      int
    */
    public $confirm_amount_cny;
    
    /**
    * @var      int
    */
    public $confirm_price_cny;


    /**
     * 创建对象
     * @return AdjustmentItem
     */
    public static function create()
    {
        return new AdjustmentItem();
    }
}