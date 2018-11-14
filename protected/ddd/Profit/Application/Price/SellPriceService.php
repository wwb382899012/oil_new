<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 17:52
 * Describe：
 */

namespace ddd\Profit\Application\Price;


use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;
use ddd\domain\entity\contractSettlement\SettlementStatus;
use ddd\infrastructure\error\ZException;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\Contract\IContractRepository;
use ddd\Profit\Domain\Contract\ContractRepository;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlement;
use ddd\Profit\Domain\Model\Settlement\IDeliveryOrderSettlementRepository;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlementRepository;
use ddd\Profit\Domain\Price\PriceService;
use ddd\Profit\Domain\Price\SellPrice;
use ddd\Profit\Domain\Price\SellSettledPrice;
use ddd\Profit\Domain\Price\SellSettledPriceService;
use ddd\Profit\Domain\Service\EventService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\Profit\Repository\Settlement\DeliveryOrderRepository;

class SellPriceService extends BaseService
{

    use Transaction;
    use ContractRepository;
    use DeliveryOrderSettlementRepository;


    public function __construct()
    {
        parent::__construct();

    }


    /**
     * 当销售合同已确认
     * @param $contractId
     * @throws \Exception
     */
    public function onSellContractConfirmed($contractId)
    {
        $contract=$this->getContractRepository()->findByContractId($contractId);
        if(empty($contract))
            throw new ZEntityNotExistsException($contractId,Contract::class);

        try
        {

            $this->beginTransaction();
            $result = \ddd\Profit\Domain\Price\SellPriceService::service()->createSellPrice($contract,true);

            if($result) {

                //mq事件
                \AMQPService::publishSellContractPrice($contract->contract_id);

                EventService::service()->store($contract->contract_id, \Event::SELL_CONTRACT_PRICE_EVENT, \Event::SellContractPriceEvent);
            }
            $this->commitTransaction();
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw $e;
        }

    }


    public function onDeliverySettledConfirmed($billId)
    {
        $deliverySettlement=$this->getDeliveryOrderSettlementRepository()->findByOrderId($billId);
        if(empty($deliverySettlement)){
            $deliveryOrder = DeliveryOrderRepository::repository()->findByPk($billId);

            if($deliveryOrder->settle_status == SettlementStatus::STATUS_PASS)
                $deliverySettlement = DeliveryOrderSettlement::create($deliveryOrder);
            else
                throw new ZEntityNotExistsException($billId,DeliveryOrderSettlement::class);
        }else{
            if($deliverySettlement->status!=SettlementStatus::STATUS_PASS)
                throw new ZException('发货单id：'.$deliverySettlement->bill_id.'未结算通过');
        }


        try
        {

            $this->beginTransaction();
            $result = SellSettledPriceService::service()->createSellSettledPrice($deliverySettlement,true);
            if($result) {
                
                //mq事件
                \AMQPService::publishSellSettledPrice($deliverySettlement->bill_id);

                EventService::service()->store($deliverySettlement->bill_id, \Event::SELL_SETTLED_PRICE_EVENT, \Event::SellSettledPriceEvent);
            }

            $this->commitTransaction();
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw $e;
        }

    }

    /**
     * [onSellContractPriceChange 销售合同单价变更]
     * @param
     * @param  [bigint] $contractId [合同id]
     * @return [bool]
     */
    public function onSellContractPriceChange($contractId)
    {
        $contract=$this->getContractRepository()->findByContractId($contractId);
        if(empty($contract))
            throw new ZEntityNotExistsException($contractId,Contract::class);

        try
        {

            $this->beginTransaction();

            PriceService::service()->updateSellPriceByContract($contract);

            $this->commitTransaction();
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * [onSellSettledPriceChange 销售结算单价变更]
     * @param
     * @param  [bigint] $billId [发货单id]
     * @return [bool]
     */
    public function onSellSettledPriceChange($billId)
    {
        $deliverySettlement=$this->getDeliveryOrderSettlementRepository()->findByOrderId($billId);
        if(empty($deliverySettlement)){
            $deliveryOrder = DeliveryOrderRepository::repository()->findByPk($billId);

            if($deliveryOrder->settle_status == SettlementStatus::STATUS_PASS)
                $deliverySettlement = DeliveryOrderSettlement::create($deliveryOrder);
            else
                throw new ZEntityNotExistsException($billId,DeliveryOrderSettlement::class);
        }else{
            if($deliverySettlement->status!=SettlementStatus::STATUS_PASS)
                throw new ZException('发货单id：'.$deliverySettlement->bill_id.'未结算通过');
        }


        try
        {

            $this->beginTransaction();

            PriceService::service()->updateSellPriceByDeliverySettlement($deliverySettlement);

            $this->commitTransaction();
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw $e;
        }
    }

}