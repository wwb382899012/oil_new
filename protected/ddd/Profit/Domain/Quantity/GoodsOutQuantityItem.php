<?php
/**
 * @Name            商品出库数量明细
 * @DateTime        2018年8月29日 17:53:30
 * @Author          Administrator
 */
namespace ddd\Profit\Domain\Quantity;


use ddd\Common\Domain\BaseEntity;

class GoodsOutQuantityItem extends BaseEntity
{
    #region property
    
    /**
     * 入库单id 
     * @var   bigint
     */
    public $stock_in_id;
    
    /**
     * 采购合同id 
     * @var   bigint
     */
    public $contract_id;
    
    /**
     * 商品id 
     * @var   int
     */
    public $goods_id;
    
    /**
     * 结算出库数量 
     * @var   Quantity
     */
    public $out_quantity;    

    #endregion
}

?>