<?php

/**
 * @Name            商品销售单价
 * @DateTime        2018年8月22日 10:02:50
 * @Author          Administrator
 */

namespace ddd\Profit\Domain\Price;

use ddd\Common\Domain\Value\Money;
use ddd\Profit\Domain\Contract\Contract;

use ddd\infrastructure\error\ZException;

class SellPrice extends ProjectPrice
{


    /**
     * getSellPriceRepository 获取仓储
     * @param
     * @throw
     * @return mixed
     */
    public function save(){
        return $this->getSellPriceRepository();
    }

    /**
     * 创建工厂方法
     */
    public static function create(Contract $contract)
    {
        if(empty($contract))
            throw new ZException("Contract对象不存在");
        
        $entity = new static();
        $entity->contract_id = $contract->contract_id;
        $entity->project_id  = $contract->project_id;
        $entity->is_settled  = 0;
        $entity->type        = 2;

        $goods_items = $contract->getGoodsItems();
        if(is_array($goods_items) && !empty($goods_items)) {
            foreach ($goods_items as $goods)
            {
                $item = new GoodsPriceItem();
				$item->goods_id      = $goods->goods_id;
				$item->price         = $goods->price; // 死价直接取合同价格，活价由第三方服务提供获取
				$item->exchange_rate = $goods->t_exchange_rate;
				$item->price_cny     = new Money(round($goods->price->amount * $contract->exchange_rate * $goods->t_exchange_rate));

                $entity->addPriceItem($item);
            }
        }

        return $entity;
    }
}