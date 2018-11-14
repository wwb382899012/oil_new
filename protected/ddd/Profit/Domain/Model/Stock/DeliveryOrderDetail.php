<?php
/**
 * Desc: 发货明细
 * User: wwb
 * Date: 2018/8/2
 * Time: 15:39
 */

namespace ddd\Profit\Domain\Model\Stock;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\Quantity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\Attachment;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\Utility;
use ddd\Profit\Domain\Model\Stock\DeliverySettlementDetail;
use ddd\Profit\Domain\Model\Stock\DeliveryOrderSettleEvent;
use ddd\Profit\domain\Model\Stock\DeliveryOrderRepository;


class DeliveryOrderDetail extends BaseEntity implements IAggregateRoot
{

    /**
     * 出库单明细id
     * @var      int
     */
    public $out_id;

    /**
     * 出库单id
     * @var      int
     */
    public $out_order_id;
    /**
     * 发货单id
     * @var      int
     */
    public $order_id;

    /**
     * 商品id
     * @var      int
     */
    public $goods_id;
    /**
     * 入库单通知单id
     * @var      object
     */
    public $batch_id;
    /**
     * 出库数量
     * @var      Quantity
     */
    public $out_quantity;

    

    /**
     * 获取id
     * @return int
     */
    public function getId()
    {
        return $this->out_id;
    }

    /**
     * 设置id
     * @param $value
     */
    public function setId($value)
    {
        $this->out_id = $value;
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
