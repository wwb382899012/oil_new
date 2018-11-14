<?php
/**
 * Created by wwb.
 * DateTime: 2018/3/21 11:35
 * Describe：销售合同结算对象
 */

namespace ddd\Profit\Domain\Model\Settlement;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Currency\Currency;
use ddd\Common\IAggregateRoot;
use ddd\Common\Domain\Value\Money;
use ddd\Profit\Domain\Model\Settlement\Settlement;

class SellContractSettlement extends Settlement
{

    /**
     * 获取id
     * @return int
     */
    public function getId()
    {
        return $this->settle_id;
    }

    /**
     * 设置id
     * @param $value
     */
    public function setId($value)
    {
        $this->settle_id = $value;
    }

    /**
     * @name:create
     * @desc: 创建销售合同结算对象
     * @param:* @param $contractId
     * @throw: * @throws ZException
     * @return:static
     */
    public static function create($contractId)
    {
        if (empty($contractId))
        {
            throw new ZException("contractId 参数为空");
        }

        $entity = new static();
        return $entity;
    }


    /**
     * @name:addSettleItem
     * @desc:  添加结算明细
     * @param:* @param SettlementItem $items
     * @throw: * @throws ZException
     * @return:void
     */
    public function addSettleItem(SettlementItem $items) {
        if (empty($items)) {
            throw new ZException("参数items对象为空");
        }
        if (isset($this->settle_items[$items->goods_id])) {
            throw new ZException('商品'.$items->goods_id.'已包含');
        }
        $this->settle_items[$items->goods_id] = $items;
    }

}

