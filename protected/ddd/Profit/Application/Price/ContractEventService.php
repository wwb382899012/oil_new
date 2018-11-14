<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 17:52
 * Describe：
 */

namespace ddd\Profit\Application\Price;


use ddd\Common\Application\BaseService;
use ddd\Common\Application\Transaction;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\Contract\ContractSettlement;
use ddd\Profit\Domain\Contract\IContractRepository;
use ddd\Profit\Domain\Contract\ContractRepository;
use ddd\Profit\Domain\Contract\IContractSettlementRepository;
use ddd\Profit\Domain\Price\Contract\ContractEventHandlerService;
use ddd\Profit\Domain\Price\Contract\ContractGoodsConfirmedEvent;
use ddd\Profit\Domain\Price\Contract\ContractGoodsSettledEvent;

class ContractEventService extends BaseService
{

    use Transaction;
    use ContractRepository;
    /**
     * @var IContractRepository
     */
    protected $contractRepository;

    /**
     * @var IContractSettlementRepository
     */
    protected $contractSettlementRepository;

    public function __construct()
    {
        parent::__construct();
        //$this->contractRepository=$contractRepository;
        //$this->contractSettlementRepository=$contractSettlementRepository;
    }


    /**
     * 当合同已确认
     * @param $contractId
     * @throws \Exception
     */
    public function onContractConfirmed($contractId)
    {
        $contract=$this->getContractRepository()->findByContractId($contractId);
        if(empty($contract))
            throw new ZEntityNotExistsException($contractId,Contract::class);
        $goodsItems=$contract->getGoodsItems();
        try
        {

            $this->beginTransaction();

            $service = new ContractEventHandlerService();
            foreach ($goodsItems as $goods)
            {
                $event = new ContractGoodsConfirmedEvent();
                $event->contract_id = $contract->contract_id;
                $event->goods_id = $goods->goods_id;
                $event->quantity = $goods->quantity;
                $event->price = $goods->price;

                $service->onContractConfirmed($event);
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
     * 当合同已结算
     * @param $contractId
     * @throws \Exception
     */
    public function onContractSettled($contractId)
    {
        $contract=$this->contractSettlementRepository->findByContractId($contractId);
        if(empty($contract))
            throw new ZEntityNotExistsException($contractId,ContractSettlement::class);
        $goodsItems=$contract->getGoodsItems();

        try
        {
            $this->beginTransaction();

            $service = new ContractEventHandlerService();
            foreach ($goodsItems as $goods)
            {
                $event = new ContractGoodsSettledEvent();
                $event->contract_id = $contract->contract_id;
                $event->goods_id = $goods->goods_id;
                $event->quantity = $goods->quantity;
                $event->price = $goods->price;

                $service->onContractSettled($event);
            }
            $this->commitTransaction();
        }
        catch (\Exception $e)
        {
            $this->rollbackTransaction();
            throw $e;
        }
    }
}