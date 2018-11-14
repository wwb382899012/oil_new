<?php

/**
 * @Name            商品销售结算单价
 * @DateTime        2018年8月28日 15:07:47
 * @Author          Administrator
 */

namespace ddd\Profit\Domain\Price;

use ddd\Common\Domain\Value\Money;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlement;
use ddd\infrastructure\error\ZException;

class SellSettledPrice extends ProjectPrice
{
    #region property
    
    /**
     * 发货单id 
     * @var   bigint
     */
    public $bill_id;    

    #endregion
    
    /**
     * 商品结算单价变更事件
     */
    public function 商品结算单价变更事件()
    {
       // TODO: implement
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
        $entity->project_id  = $deliverySettlement->project_id;
        $entity->bill_id     = $deliverySettlement->bill_id;
        $entity->is_settled  = 1;
        $entity->type        = 2;
        
        if(is_array($deliverySettlement->settle_items) && !empty($deliverySettlement->settle_items)) {
            foreach ($deliverySettlement->settle_items as $settle)
            {
                $item = new GoodsPriceItem();
                $item->goods_id      = $settle->goods_id;
                $item->price         = $settle->price;
                $item->exchange_rate = $settle->exchange_rate;
                $item->price_cny     = $settle->price_cny;

                $unit_convert_rate = \Utility::query('select unit_convert_rate from t_contract_goods where contract_id='.$deliverySettlement->contract_id.' and goods_id='.$settle->goods_id);
                if(!empty($unit_convert_rate)){
                    $item->price_cny     = new Money($settle->price_cny->amount * $unit_convert_rate[0]['unit_convert_rate'],$settle->price_cny->currency);

                }

                $entity->addPriceItem($item);
            }
        }

        return $entity;
    }
}

?>