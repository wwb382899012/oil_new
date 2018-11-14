<?php
namespace ddd\Profit\Domain\EstimateProfit;

use ddd\Common\Domain\BaseEntity;
/**
 * @Name            预估采购商品明细
 * @DateTime        2018年8月27日 16:59:35
 * @Author          Administrator
 */
class EstimateBuyGoodsItem extends BaseEntity
{
    #region property
    
    /**
     * 商品id 
     * @var   int
     */
    public $goods_id;
    
    /**
     * 预估采购单价 
     * @var   Money
     */
    public $buy_price;
    
    /**
     * 预估销售单价 
     * @var   Money
     */
    public $sell_price;
    
    /**
     * 销售结算数量 
     * @var   Quantity
     */
    public $out_quantity;
    
    /**
     * 采购合同数量 
     * @var   Qauntity
     */
    public $buy_quantity;  

    #endregion
}
