<?php
/**
 * @Name            商品采购结算单价
 * @DateTime        2018年8月28日 15:08:05
 * @Author          Administrator
 */

namespace ddd\Profit\Domain\Price;

use ddd\Common\Domain\Value\Money;
use ddd\Profit\Domain\Model\Settlement\LadingBillSettlement;

use ddd\infrastructure\error\ZException;

class BuySettledPrice extends ProjectPrice
{

    #region property
    
    /**
     * 入库通知单id 
     * @var   int
     */
    public $bill_id;    

    #endregion
    

    /**
     * 创建工厂方法
     */
    public static function create(LadingBillSettlement $ladingSettlement)
    {
        if(empty($ladingSettlement))
            throw new ZException("LadingBillSettlement对象不存在");
        
        $entity = new static();
        $entity->contract_id = $ladingSettlement->contract_id;
        $entity->project_id  = $ladingSettlement->project_id;
        $entity->bill_id     = $ladingSettlement->bill_id;
        $entity->is_settled  = 1;
        $entity->type        = 1;
        
        if(is_array($ladingSettlement->settle_items) && !empty($ladingSettlement->settle_items)) {
            foreach ($ladingSettlement->settle_items as $settle)
            {
                $item = new GoodsPriceItem();
                $item->goods_id      = $settle->goods_id;
                $item->price         = $settle->price;
                $item->exchange_rate = $settle->exchange_rate;
                $item->price_cny     = $settle->price_cny;
                $unit_convert_rate = \Utility::query('select unit_convert_rate from t_contract_goods where contract_id='.$ladingSettlement->contract_id.' and goods_id='.$settle->goods_id);
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