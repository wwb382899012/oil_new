<?php
namespace ddd\Profit\Domain\EstimateProfit;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\Money;
use ddd\Common\Domain\Value\Quantity;
use ddd\Common\Domain\Value\UnitEnum;
use ddd\Common\IAggregateRoot;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\EstimateProfit\EstimateBuyGoodsItem;

use ddd\Profit\Repository\Contract\ContractRepository;
use ddd\infrastructure\Utility;
use ddd\infrastructure\error\ZException;

/**
 * @Name            预估合同利润
 * @DateTime        2018年8月27日 16:16:30
 * @Author          vector
 */
class EstimateContractProfit extends BaseEntity implements IAggregateRoot
{

    #region property
    
    /**
     * 标识id 
     * @var   bigint
     */
    public $id;

    /**
     * 采购合同id 
     * @var   bigint
     */  
    public $contract_id;
    
    /**
     * 项目id 
     * @var   bigint
     */
    public $project_id;

    /**
     * 交易主体 
     * @var   int
     */
    public $corporation_id;

    /**
     * 合同审核通过时间 
     * @var   datetime
     */
    public $check_pass_time;
    
    
    /**
     * 预估销售收入 
     * @var   EstimateCostInfo
     */
    public $sell_income;
    
    /**
     * 预估采购成本 
     * @var   EstimateCostInfo
     */
    public $buy_cost;

    /**
     * @var 已收票金额
     */
    public $invoice_amount;
    
    /**
     * 预估采购商品明细 
     * @var   EstimateBuyGoodsItem[]
     */
    public $goods_items;    

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
     * 获取预估采购商品明细
     * @return array
     */
    public function getGoodsItems()
    {
        if(!is_array($this->goods_items))
            return [];
        else
            return $this->goods_items;
    }

    /**
     * 添加预估采购商品明细
     * @param EstimateBuyGoodsItem $goodsItem
     */
    public function addGoods(EstimateBuyGoodsItem $goodsItem)
    {
        $this->goods_items[$goodsItem->goods_id]=$goodsItem;
    }

    /**
     * 根据商品id获取商品明细
     * @param $goodsId
     * @return ContractGoodsPrice
     */
    public function getGoodsItem($goodsId)
    {
        if(empty($goodsId))
            return null;
        return $this->goods_items[$goodsId];
    }

    
    
    /**
     * 创建工厂方法
     */
    public static function create(Contract $contract)
    {
        if(empty($contract))
            throw new ZException("Contract对象不存在");
        
        $entity = new static();
        $entity->contract_id     = $contract->contract_id;
        $entity->corporation_id  = $contract->corporation_id;
        $entity->project_id      = $contract->project_id;
        $entity->check_pass_time = $contract->check_pass_time;
        $goods_items = $contract->getGoodsItems();
        if(is_array($goods_items) && !empty($goods_items)) {
            foreach ($goods_items as $g)
            {
                $item = new EstimateBuyGoodsItem();
                $item->goods_id     = $g->goods_id;
                $item->buy_price = new Money($g->price_cny->amount*$g->t_exchange_rate,$g->price_cny->currency);
                $item->buy_quantity = new Quantity($g->quantity->quantity/$g->t_exchange_rate,UnitEnum::UNIT_T);
                $item->out_quantity = new Quantity();

                $entity->addGoods($item);
            }
        }


        return $entity;
    }
    
}

