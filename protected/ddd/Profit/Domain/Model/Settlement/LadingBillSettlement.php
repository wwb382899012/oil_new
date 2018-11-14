<?php
/**
 * Created by wwb.
 * DateTime: 2018/3/21 11:35
 * Describe：发货单结算对象
 */

namespace ddd\Profit\Domain\Model\Settlement;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Currency\Currency;
use ddd\Common\IAggregateRoot;
use ddd\Common\Domain\Value\Money;
use ddd\Profit\Domain\Model\Settlement\Settlement;


class LadingBillSettlement extends Settlement
{
    /**
     * 入库通知单id
     * @var   bigint
     */
    public $bill_id;


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
     * @desc:  创建入库通知单结算
     * @param:* @param StockNotice $stockNotice
     * @throw:
     * @return:static
     */
    public static function create(StockNotice $stockNotice)
    {
        if (empty($stockNotice))
        {
            ExceptionService::throwArgumentNullException("StockNotice对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        $entity = new static();
        $entity->bill_id = $stockNotice->batch_id;
        $entity->contract_id = $stockNotice->contract_id;
        $entity->project_id = $stockNotice->project_id;
        $entity->settle_items = $stockNotice->settle_items;
        $entity->status =$stockNotice->settle_status;

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

