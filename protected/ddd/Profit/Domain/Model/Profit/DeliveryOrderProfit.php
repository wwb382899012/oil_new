<?php
/**
 * Desc: 发货单利润
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 15:39
 */

namespace ddd\Profit\Domain\Model\Profit;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\Quantity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\Attachment;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\Utility;
use ddd\Profit\Domain\Model\Profit\BaseProfit;
use ddd\Profit\Domain\Model\Profit\DeliveryOrderProfitRepository;
use ddd\Profit\Domain\Model\Stock\BuyGoodsCost;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderRepository;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderDetailRepository;
use ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository;
use ddd\Profit\Domain\Model\Stock\DeliveryOrder;
use ddd\domain\iRepository\contract\IContractRepository;

use ddd\Profit\Domain\Service\ProfitService;

class DeliveryOrderProfit extends BaseProfit
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
     * 销售利润
     * @var      SellProfit
     */
    public $sell_profit;

    /**
     * 采购成本
     * @var      BuyCost
     */
    public $buy_cost;


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
    use DeliveryOrderProfitRepository;

    /**
     * 创建对象
     * @param    Contract $contract
     * @param    array $stockBills
     * @return   static
     * @throws   \Exception
     */
    public static function create(DeliveryOrder $deliveryOrder)
    {
        if (empty($deliveryOrder))
        {
            ExceptionService::throwArgumentNullException("DeliveryOrder对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        $entity = new static();
        $entity->order_id = $deliveryOrder->order_id;
        $entity->contract_id = $deliveryOrder->contract_id;
        $entity->project_id = $deliveryOrder->project_id;
        $entity->corporation_id = $deliveryOrder->corporation_id;

        return $entity;
    }


}
