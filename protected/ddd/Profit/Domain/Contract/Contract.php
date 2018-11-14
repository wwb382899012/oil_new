<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 17:57
 * Describe：
 */

namespace ddd\Profit\Domain\Contract;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Profit\Domain\Price\Contract\ContractGoodsPrice;

class Contract extends BaseEntity implements IAggregateRoot
{

    /**
     * 合同id
     * @var int
     */
    public $contract_id;

    /**
     * 关联合同id
     * @var int
     */
    public $relation_contract_id;

    /**
     * 合同类型
     * @var int
     */
    public $type;

    /**
     * 结算类型
     * @var int
     */
    public $settle_type;

    /**
     * 项目id 
     * @var   project_id
     */
    public $project_id;

    /**
     * 交易主体 
     * @var   int
     */
    public $corporation_id;

    /**
     * 金额换算比 
     * @var   float
     */
    public $exchange_rate = 1.0;

    /**
     * 审核通过时间 
     * @var   datetime
     */
    public $check_pass_time;

    /**
     * 价格类型 
     * @var   int
     */
    public $price_type;

    /**
     * 商品明细
     * @var ContractGoodsPrice[]
     */
    protected $goods_items;

    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->contract_id;
    }

    public function setId($value)
    {
        // TODO: Implement setId() method.
        $this->contract_id=$value;
    }

    /**
     * 获取商品明细
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
     * 添加商品明细
     * @param ContractGoods $goodsItem
     */
    public function addGoods(ContractGoods $goodsItem)
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


}