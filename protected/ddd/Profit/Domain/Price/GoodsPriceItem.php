<?php

/**
 * @Name            商品单价
 * @DateTime        2018年8月30日 11:08:26
 * @Author          Administrator
 */

namespace ddd\Profit\Domain\Price;

use ddd\Common\Domain\BaseEntity;


class GoodsPriceItem extends BaseEntity
{
    #region property
    
    /**
     * 商品id 
     * @var   int
     */
    public $goods_id;
    
    /**
     * 商品价格 
     * @var   Money
     */
    public $price;
    
    /**
     * 价格日期 
     * @var   date
     */
    public $price_date;
    
    /**
     * 币种换算比 
     * @var   float
     */
    public $exchange_rate = 1.0;
    
    /**
     * 人民币价格 
     * @var   Money
     */
    public $price_cny;
    
    /**
     * 备注 
     * @var   string
     */
    public $remark;    

    #endregion
    
    


    
}

?>