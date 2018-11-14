<?php
/**
 * Desc: 商品成本
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
use ddd\Profit\Domain\Model\Stock\StockNotice;
use ddd\Profit\Domain\Model\Stock\StockNoticeCostItem;

class StockNoticeCost extends BaseEntity implements IAggregateRoot
{
    /**
     * 标识id
     * @var      int
     */
    public $id;
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
     * 成本明细
     * @var      StockNoticeCostItem
     */
    public $items;



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
     * @name:create
     * @desc: 创建对象
     * @param:* @param \ddd\Profit\Domain\Model\Stock\StockNotice $stockNotice
     * @throw: * @throws \Exception
     * @return:static
     */
    public static function create(StockNotice $stockNotice)
    {
        if (empty($stockNotice))
        {
            ExceptionService::throwArgumentNullException("StockNotice", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        $entity = new static();
        $items=array();
        $entity->setAttributes($stockNotice->getAttributes());

        if(!empty($stockNotice->items)){
            foreach($stockNotice->items as $key=>$value){
                $stockNoticeCostItem = new StockNoticeCostItem();
                $stockNoticeCostItem->setAttributes($value->getAttributes());
                $stockNoticeCostItem->settle_price=$value->settle_price;
                $stockNoticeCostItem->contract_price=$value->contract_price;
                $items[$key]=$stockNoticeCostItem;
            }
        }
        $entity->items=$items;
        return $entity;
    }

    /**
     * @name:addItem
     * @desc: 添加成本明细
     * @param:* @param \ddd\Profit\Domain\Model\Stock\StockNoticeCostItem $items
     * @throw: * @throws \ZException
     * @return:void
     */
    public function addItem(StockNoticeCostItem $items) {
        if (empty($items)) {
            throw new \ZException("参数items对象为空");
        }
        if (isset($this->items[$items->goods_id])) {
            throw new \ZException(BusinessError::Settle_Goods_Is_Exists, ["batch_id" => $items->batch_id, "goods_id" => $items->goods_id]);
        }
        $this->items[$items->goods_id] = $items;
    }


}
