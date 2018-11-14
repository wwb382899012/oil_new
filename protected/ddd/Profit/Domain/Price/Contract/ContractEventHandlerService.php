<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 17:33
 * Describe：
 */

namespace ddd\Profit\Domain\Price\Contract;


use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\Money;
use ddd\infrastructure\error\ZEntityNotExistsException;

class ContractEventHandlerService extends BaseService
{

    /**
     * @var IContractGoodPriceRepository
     */
    protected $contractGoodsPriceRepository;

    /**
     * 响应合同确认事件
     * @param ContractGoodsConfirmedEvent $event
     * @throws \Exception
     */
    public function onContractConfirmed(ContractGoodsConfirmedEvent $event)
    {
        $entity=ContractGoodsPrice::create();
        $entity->contract_id=$event->contract_id;
        $entity->goods_id=$event->goods_id;
        $entity->price=new Money($event->price);
        $this->contractGoodsPriceRepository->store($entity);
    }

    /**
     * 合同结算事件
     * @param ContractGoodsSettledEvent $event
     * @throws \Exception
     */
    public function onContractSettled(ContractGoodsSettledEvent $event)
    {
        $entity=$this->contractGoodsPriceRepository->findByContractIdAndGoodsId($event->contract_id,$event->goods_id);
        if(empty($entity))
        {
            throw new ZEntityNotExistsException($event->contract_id."_".$event->goods_id,ContractGoodsPrice::class);
        }
        $entity->price=new Money($event->price);
        $this->contractGoodsPriceRepository->store($entity);
    }

}