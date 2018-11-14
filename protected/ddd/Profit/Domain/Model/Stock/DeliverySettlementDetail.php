<?php
/**
 * Desc: 发货单结算明细
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


class DeliverySettlementDetail extends BaseEntity implements IAggregateRoot
{


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
     * 结算数量(吨)
     * @var      Quantity
     */
    public $settle_quantity;

    /**
     * 结算金额（人民币）
     * @var      Price
     */
    public $settle_amount;



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

    }


}
