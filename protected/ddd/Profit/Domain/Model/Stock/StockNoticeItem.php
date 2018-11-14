<?php
/**
 * Desc: 入库通知单商品明细
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


class StockNoticeItem extends BaseEntity implements IAggregateRoot
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
    public $goods_id;

    /**
     * 结算价格
     * @var      Price
     */
    public $settle_price;
    /**
     * 合同价格
     * @var      Price
     */
    public $contract_price;


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

}
