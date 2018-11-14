<?php
/**
 * @Name            销售结算出库数量
 * @DateTime        2018年8月29日 17:52:36
 * @Author          Administrator
 */
namespace ddd\Profit\Domain\Quantity;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\Quantity;
use ddd\Common\IAggregateRoot;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlement;


class SellOutQuantity extends BaseEntity implements IAggregateRoot
{

    #region property
    
    /**
     * 标识id 
     * @var   bigint
     */
    public $id;
    
    /**
     * 发货单id 
     * @var   bigint
     */
    public $bill_id;
    
    /**
     * 合同id 
     * @var   bigint
     */
    public $contract_id;
    
    /**
     * 出库明细 
     * @var   GoodsOutQuantityItem[]
     */
    public $out_items;    

    #endregion
    
    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    public function setId($id)
    {
        // TODO: Implement setId() method.
        $this->id=$id;
    }

    /**
     * 获取商品出库数量明细
     * @return array
     */
    public function getOutItems()
    {
        if(!is_array($this->out_items))
            return [];
        else
            return $this->out_items;
    }

    /**
     * 添加商品出库数量明细
     * @param GoodsOutQuantityItem $outItem
     */
    public function addOutItem(GoodsOutQuantityItem $outItem)
    {
        $this->out_items[]=$outItem;
    }

    
    
    /**
     * 创建工厂方法
     */
    public static function create(DeliveryOrderSettlement $deliverySettlement)
    {
        if(empty($deliverySettlement))
            throw new ZException("DeliveryOrderSettlement对象不存在");
        
        $entity = new static();
        $entity->contract_id = $deliverySettlement->contract_id;
        $entity->bill_id     = $deliverySettlement->bill_id;
        
        if(is_array($deliverySettlement->out_items) && !empty($deliverySettlement->out_items)) {
            foreach ($deliverySettlement->out_items as $out)
            {
                $item = new GoodsOutQuantityItem();
                $item->stock_in_id  = $out->stock_in_id;
                $item->contract_id  = $out->contract_id;
                $item->goods_id     = $out->goods_id;
                $exchange_rate      = !empty($out->exchange_rate) ? $out->exchange_rate : 1.0;
                $item->out_quantity = new Quantity($out->out_quantity->quantity / $exchange_rate);

                $entity->addOutItem($item);
            }
        }

        return $entity;
    }
}

?>