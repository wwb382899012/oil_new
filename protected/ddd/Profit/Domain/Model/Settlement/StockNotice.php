<?php
/**
 * Desc: 入库通知单
 * User: wwb
 * Date: 2018/8/2
 * Time: 15:39
 */

namespace ddd\Profit\Domain\Model\Settlement;


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


class StockNotice extends BaseEntity implements IAggregateRoot
{

    /**
     * 入库通知单id
     * @var      int
     */
    public $batch_id;

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
     * 结算状态
     * @var      int
     */
    public $settle_status;


    /**
     * 结算明细
     * @var      SettlementItem
     */
    public $settle_items;
    

    /**
     * 获取id
     * @return int
     */
    public function getId()
    {
        return $this->batch_id;
    }

    /**
     * 设置id
     * @param $value
     */
    public function setId($value)
    {
        $this->batch_id = $value;
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
     * @name:addSettleItem
     * @desc: 添加结算明细
     * @param:* @param SettlementItem $items
     * @throw: * @throws ZException
     * @return:void
     */
    public function addSettleItem(SettlementItem $items) {

        if (empty($items)) {
            throw new ZException("参数items对象为空");
        }
        if (isset($this->settle_items[$items->goods_id])) {
            throw new ZException(BusinessError::Delivery_Settlement_Goods_Is_Exists, ["order_id" => $items->order_id, "goods_id" => $items->goods_id]);
        }
        $this->settle_items[$items->goods_id] = $items;
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
