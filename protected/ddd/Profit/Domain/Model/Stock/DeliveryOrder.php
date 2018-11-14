<?php
/**
 * Desc: 发货单
 * User: wwb
 * Date: 2018/8/2
 * Time: 15:39
 */

namespace ddd\Profit\Domain\Model\Stock;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\Quantity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\Attachment;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\Utility;
use ddd\Profit\Domain\Model\Stock\DeliverySettlementDetail;
use ddd\Profit\Domain\Model\Stock\DeliveryOrderSettleEvent;
use ddd\Profit\domain\Model\Stock\DeliveryOrderRepository;


class DeliveryOrder extends BaseEntity implements IAggregateRoot
{

    /**
     * 标识id
     * @var      int
     */
    public $id;

    /**
     * 发货单id
     * @var      int
     */
    public $order_id;

    /**
     * 合同id
     * @var      int
     */
    public $contract_id;

    /**
     * 项目id
     * @var      int
     */
    public $project_id;

    /**
     * 交易主体id
     * @var      int
     */
    public $corporation_id;

    /**
     * 结算出库数量
     * @var      Quantity
     */
    public $settle_quantity;

    /**
     * 结算金额
     * @var      Price
     */
    public $settle_amount;

    /**
     * 发货明细
     * @var      DeliveryOrderDetail
     */
    public $delivery_items;
    

    /**
     * 获取id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 设置id
     * @param $value
     */
    public function setId($value)
    {
        $this->id = $value;
    }


    /**
     * 创建对象
     * @param
     * @return   static
     * @throws   \Exception
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @name:addDeliveryItem
     * @desc: 添加结算明细
     * @param:* @param \ddd\Profit\Domain\Model\Stock\DeliverySettlementDetail $items
     * @throw: * @throws \ZException
     * @return:void
     */
    public function addDeliveryItem(DeliveryOrderDetail $items) {

        if (empty($items)) {
            throw new ZException("参数items对象为空");
        }
        if (isset($this->delivery_items[$items->out_id])) {
            throw new ZException(BusinessError::Settle_Goods_Is_Exists, ["order_id" => $items->order_id, "goods_id" => $items->goods_id]);
        }
        $this->delivery_items[$items->out_id] = $items;
    }

    /**
     * 返回以T计量的数量
     * @return Quantity
     * @throws \Exception
     */
    public function getQuantityWithT()
    {

        return Quantity::create(0);
    }


}
