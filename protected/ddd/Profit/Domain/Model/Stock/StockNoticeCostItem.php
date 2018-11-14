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


class StockNoticeCostItem extends BaseEntity implements IAggregateRoot
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
    public $goods_id;

    /**
     * 结算单价
     * @var      Price
     */
    public $settle_price;
    /**
     * 合同单价
     * @var      Price
     */
    public $contract_price;


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
     * @param:* @param $deliveryOrderDetail
     * @throw: * @throws \Exception
     * @return:array
     */
    public static function create($deliveryOrderDetail)
    {
        if (empty($deliveryOrderDetail))
        {
            ExceptionService::throwArgumentNullException("DeliveryOrder", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        $entity = array();
        if(!empty($deliveryOrderDetail)){
            foreach ($deliveryOrderDetail as $key=>$value) {
                $BuyGoodsCost = new static();
                $BuyGoodsCost->setAttributes($value->getAttributes());
                $BuyGoodsCost->out_quantity = $value->out_quantity;
                $goods_price = GoodsPriceService::getGoodsPrice($BuyGoodsCost->goods_id,$BuyGoodsCost->batch_id);
                $BuyGoodsCost->goods_price = new Price($goods_price,\ConstantMap::CURRENCY_RMB);
                $entity[$key] = $BuyGoodsCost;
            }
        }
        return $entity;
    }

}
