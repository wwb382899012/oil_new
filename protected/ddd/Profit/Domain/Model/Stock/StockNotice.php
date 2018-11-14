<?php
/**
 * Desc: 入库通知单
 * User: wwb
 * Date: 2018/8/2
 * Time: 15:39
 */

namespace ddd\Profit\Domain\Model\Stock;

use ddd\Common\Domain\BaseEntity;
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
use ddd\Profit\domain\Model\Stock\DeliveryOrderDetail;
use ddd\Profit\Domain\Service\GoodsPriceService;
use ddd\Profit\Domain\Model\Profit\StockOutPassEvent;
use ddd\Profit\Domain\Model\Stock\StockNoticeItem;


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
     * 结算状态
     * @var      int
     */
    public $settle_status;
    /**
     * 计价方式
     * @var      int
     */
    public $price_type;

    /**
     * 商品明细
     * @var      StockNoticeItem
     */
    public $items;


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

    }

    /**
     * @name:addItem
     * @desc: 添加商品明细
     * @param:* @param \ddd\Profit\Domain\Model\Stock\StockNoticeItem $items
     * @throw: * @throws \ZException
     * @return:void
     */
    public function addItem(StockNoticeItem $items) {
        if (empty($items)) {
            throw new \ZException("参数items对象为空");
        }
        if (isset($this->items[$items->goods_id])) {
            throw new \ZException(BusinessError::Settle_Goods_Is_Exists, ["batch_id" => $items->batch_id, "goods_id" => $items->goods_id]);
        }
        $this->items[$items->goods_id] = $items;
    }

}
