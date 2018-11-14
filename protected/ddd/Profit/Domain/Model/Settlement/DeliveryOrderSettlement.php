<?php
/**
 * Created by wwb.
 * DateTime: 2018/3/21 11:35
 * Describe：入库通知单结算对象
 */

namespace ddd\Profit\Domain\Model\Settlement;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Currency\Currency;
use ddd\Common\IAggregateRoot;
use ddd\Common\Domain\Value\Money;
use ddd\Profit\Domain\Model\Settlement\Settlement;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrder;

class DeliveryOrderSettlement extends Settlement
{
    /**
     * 发货单id
     * @var   bigint
     */
    public $bill_id;

    /**
     * 出库明细
     * @var   DeliveryItem
     */
    public $out_items;

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
     * @desc:  创建入库通知单结算对象
     * @param:* @param $batchId
     * @throw: * @throws ZException
     * @return:static
     */
    public static function create(DeliveryOrder $deliveryOrder)
    {
        if (empty($deliveryOrder))
        {
            ExceptionService::throwArgumentNullException("DeliveryOrder对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        $entity = new static();
        $entity->bill_id = $deliveryOrder->order_id;
        $entity->contract_id = $deliveryOrder->contract_id;
        $entity->project_id = $deliveryOrder->project_id;
        $entity->settle_items = $deliveryOrder->settle_items;
        $entity->out_items = $deliveryOrder->delivery_items;
        $entity->status =$deliveryOrder->settle_status;

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


    /**
     * @name:addDeliveryItem
     * @desc:  添加出库明细
     * @param:* @param DeliveryItem $items
     * @throw: * @throws ZException
     * @return:void
     */
    public function addDeliveryItem(DeliveryItem $items) {
        if (empty($items)) {
            throw new ZException("参数DeliveryItem对象为空");
        }

        $this->out_items[] = $items;
    }
}

