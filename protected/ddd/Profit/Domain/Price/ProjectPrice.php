<?php
/**
 * @Name            项目单价
 * @DateTime        2018年8月30日 14:57:25
 * @Author          Administrator
 */

namespace ddd\Profit\Domain\Price;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;

abstract class ProjectPrice extends BaseEntity implements IAggregateRoot
{
    #region property
    
    /**
     * 标识id 
     * @var   bigint
     */
    public $price_id;
    
    /**
     * 合同id 
     * @var   bigint
     */
    public $contract_id;
    
    /**
     * 项目id 
     * @var   bigint
     */
    public $project_id;

    /**
     * 合同类型 
     * @var   int
     */
    public $type;
    
    /**
     * 是否结算 
     * @var   int
     */
    public $is_settled;
    
    /**
     * 商品单价详情 
     * @var   GoodsPriceItem[]
     */
    public $price_items;    

    #endregion
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id=$id;
    }

    /**
     * 获取商品价格明细
     * @return array
     */
    public function getPriceItems()
    {
        if(!is_array($this->price_items))
            return [];
        else
            return $this->price_items;
    }

    /**
     * 添加商品价格明细
     * @param GoodsPriceItem $priceItem
     */
    public function addPriceItem(GoodsPriceItem $priceItem)
    {
        $this->price_items[]=$priceItem;
    }
}

?>