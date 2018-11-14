<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 17:52
 * Describe：
 */

namespace ddd\Profit\Application\Price;


use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;
use ddd\domain\entity\settlement\SettlementStatus;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZException;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\Contract\IContractRepository;
use ddd\Profit\Domain\Contract\ContractRepository;
use ddd\Profit\Domain\Model\Settlement\ILadingBillSettlementRepository;
use ddd\Profit\Domain\Model\Settlement\LadingBillSettlementRepository;
use ddd\Profit\Domain\Model\Settlement\LadingBillSettlement;
use ddd\Profit\Domain\Price\BuyPrice;
use ddd\Profit\Domain\Price\BuySettledPrice;
use ddd\Profit\Domain\Price\BuySettledPriceService;
use ddd\Profit\Domain\Price\PriceService;
use ddd\Profit\Domain\Service\EventService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\Profit\Repository\Settlement\StockNoticeRepository;

class BuyPriceService extends BaseService
{

    use Transaction;
    use ContractRepository;
    use LadingBillSettlementRepository;
    /**
     * @var IContractRepository
     */
    protected $contractRepository;

    /**
     * @var [ILadingBillSettlementRepository]
     */
    protected $ladingbillSettlementRepository;

    public function __construct()
    {
        parent::__construct();

    }


    /**
     * 当采购合同已确认
     * @param $contractId
     * @throws \Exception
     */
    public function onBuyContractConfirmed($contractId)
    {
        $contract=$this->getContractRepository()->findByContractId($contractId);
        if(empty($contract))
            throw new ZEntityNotExistsException($contractId,Contract::class);

        try
        {

            $this->beginTransaction();

            $result = \ddd\Profit\Domain\Price\BuyPriceService::service()->createBuyPrice($contract,true);
            if($result) {

                //mq事件
                \AMQPService::publishBuyContractPrice($contract->contract_id);

                EventService::service()->store($contract->contract_id, \Event::BUY_CONTRACT_PRICE_EVENT, \Event::BuyContractPriceEvent);
            }

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
     * [onBuyContractPriceChange 采购合同单价变更]
     * @param
     * @param  [bigint] $contractId [合同id]
     * @return [bool]
     */
    public function onBuyContractPriceChange($contractId)
    {
        $contract=$this->getContractRepository()->findByContractId($contractId);
        if(empty($contract))
            throw new ZEntityNotExistsException($contractId,Contract::class);

        try
        {

            $this->beginTransaction();

            PriceService::service()->updateBuyPriceByContract($contract);

            $this->commitTransaction();
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * [onLadingSettledConfirmed 入库通知单已结算]
     * @param
     * @param  [bigint] $billId [入库通知单id]
     * @return [bool]
     */
    public function onLadingSettledConfirmed($billId)
    {
        $ladingSettlement=$this->getLadingBillSettlementRepository()->findByBatchId($billId);

        if(empty($ladingSettlement)){
            $stockNotice = StockNoticeRepository::repository()->findByPk($billId);
            if($stockNotice->settle_status == SettlementStatus::STATUS_PASS)
                $ladingSettlement = LadingBillSettlement::create($stockNotice);
            else
                throw new ZEntityNotExistsException($billId,LadingBillSettlement::class);

        }else{
            if($ladingSettlement->status!=SettlementStatus::STATUS_PASS)
                throw new ZException('入库通知单id：'.$ladingSettlement->bill_id.'未结算通过');
        }

        try
        {

            $this->beginTransaction();

            $result = BuySettledPriceService::service()->createBuySettledPrice($ladingSettlement,true);
            if($result) {

                //mq事件
                \AMQPService::publishBuySettledPrice($ladingSettlement->bill_id);

                EventService::service()->store($ladingSettlement->bill_id, \Event::BUY_SETTLED_PRICE_EVENT, \Event::BuySettledPriceEvent);
            }
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
     * [onBuySettledPriceChange 采购结算单价变更]
     * @param
     * @param  [bigint] $billId [入库通知单id]
     * @return [bool]
     */
    public function onBuySettledPriceChange($billId)
    {
        $ladingSettlement = $this->getLadingBillSettlementRepository()->findByBatchId($billId);
        if(empty($ladingSettlement)){
            $stockNotice = StockNoticeRepository::repository()->findByPk($billId);
            if($stockNotice->settle_status == SettlementStatus::STATUS_PASS)
                $ladingSettlement = LadingBillSettlement::create($stockNotice);
            else
                throw new ZEntityNotExistsException($billId,LadingBillSettlement::class);
        }else{
            if($ladingSettlement->status!=SettlementStatus::STATUS_PASS)
                throw new ZException('入库通知单id：'.$ladingSettlement->bill_id.'未结算通过');
        }


        try
        {

            $this->beginTransaction();

            PriceService::service()->updateBuyPriceByLadingSettlement($ladingSettlement);

            $this->commitTransaction();
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw $e;
        }
    }

}