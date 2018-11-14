<?php 
/**
 * Created by vector.
 * DateTime: 2018/9/3 10:21
 * Describe：
 */

namespace ddd\Profit\Application;


use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;
use ddd\domain\entity\contractSettlement\SettlementStatus;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlement;
use ddd\Profit\Domain\Model\Settlement\IDeliveryOrderSettlementRepository;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlementRepository;
use ddd\Profit\Domain\Quantity\ISellOutQuantityRepository;
use ddd\Profit\Domain\Quantity\SellOutQuantityRepository;
use ddd\Profit\Domain\Quantity\SellOutQuantity;
use ddd\Profit\Domain\Quantity\SellOutQuantityService;
use ddd\Profit\Domain\Service\EventService;
use ddd\Profit\Repository\Settlement\DeliveryOrderRepository;

class QuantityService extends BaseService
{
	use Transaction;
    use DeliveryOrderSettlementRepository;
    use SellOutQuantityRepository;


    public function __construct()
    {
        parent::__construct();

    }


    /**
     * [onDeliveryOrderSettled 发货单已结算]
     * @param
     * @param  [bigint] $billId [发货单id]
     * @return [bool]
     */
    public function onDeliveryOrderSettled($billId)
    {
        $deliverySettlement = $this->getDeliveryOrderSettlementRepository()->findByOrderId($billId);
        if(empty($deliverySettlement)){

            $deliveryOrder = DeliveryOrderRepository::repository()->findByPk($billId);

            if($deliveryOrder->settle_status == SettlementStatus::STATUS_PASS)
                $deliverySettlement = DeliveryOrderSettlement::create($deliveryOrder);
            else
                throw new ZEntityNotExistsException($billId,DeliveryOrderSettlement::class);
        }


        try
        {

            $this->beginTransaction();

            SellOutQuantityService::service()->createSellOutQuantity($deliverySettlement,true);

            //mq事件
            \AMQPService::publishSellOutQuantity($deliverySettlement->bill_id);

            EventService::service()->store($deliverySettlement->bill_id, \Event::Sell_OUT_QUANTITY_EVENT, \Event::SellOutQuantityEvent);

            $this->commitTransaction();

            return true;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * [onSellQuantityChange 销售数量变更]
     * @param
     * @param  [bigint] $billId [发货单id]
     * @return [bool]
     */
    public function onSellQuantityChange($billId)
    {
        $sellOutQuantity = $this->getSellOutQuantityRepository()->findByBillId($billId);

        if(empty($sellOutQuantity))
            throw new ZEntityNotExistsException($billId,DeliveryOrderSettlement::class);

        try
        {

            $this->beginTransaction();

            SellOutQuantityService::service()->updateEstimateSellQuantity($sellOutQuantity);

            EventService::service()->store($billId, \Event::ESTIMATE_CONTRACT_PROFIT_EVENT_BY_QUANTITY, \Event::EstimateContractProfitEventByQuantity);
            $this->commitTransaction();

            return true;
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw $e;
        }
    }


}