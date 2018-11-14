<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 17:25
 * Describe：
 */

namespace ddd\domain\service\risk\event;


use ddd\domain\iRepository\contract\IContractRepository;
use ddd\domain\iRepository\contract\ITradeGoodsRepository;
use ddd\domain\service\risk\IAmountEventHandler;
use ddd\domain\service\risk\PartnerAmountSourceCategoryEnum;
use ddd\infrastructure\DIService;

class BuyContractSettlementEventHandler implements IAmountEventHandler
{
    public $event;
    public $contractEntity;

    public function __construct($event = null)
    {
        if (!empty($event))
        {
            $this->event = $event;
        }

        $this->contractEntity = DIService::getRepository(IContractRepository::class)->findByPk($this->event->sender->contract_id);
    }

    function getPartnerId()
    {
        return $this->contractEntity->partner_id;
    }

    function getAmount()
    {
        $amount = 0;
        $settleItems = $this->event->sender->goods_expense;
        if (\Utility::isNotEmpty($settleItems))
        {
            foreach ($settleItems as $goodsId => $entity)
            {
                $tradeGoods = DIService::getRepository(ITradeGoodsRepository::class)->findByContractIdAndGoodsId($this->event->sender->contract_id, $goodsId);
                if ($tradeGoods->unit == $entity->settle_quantity->unit)
                {
                    $amount += ($entity->in_quantity->quantity * $tradeGoods->price * $this->contractEntity->exchange_rate - $entity->settle_amount_cny);
                } elseif ($tradeGoods->unit == $entity->settle_quantity_sub->unit)
                {
                    $amount += (($entity->in_quantity_sub->quantity * $tradeGoods->price - $entity->settle_quantity_sub->quantity * $entity->settle_price) * $this->contractEntity->exchange_rate);
                }
            }
        }

        return $amount;
    }

    function getCategory()
    {
        return PartnerAmountSourceCategoryEnum::Buy_Contract_Settle;
    }

    function getRelationId()
    {
        return $this->event->sender->settle_id;
    }

    function getContractInfo()
    {
        return array(
            'contract_id' => $this->event->sender->contract_id,
            'project_id' => $this->contractEntity->project_id,
            'corporation_id' => $this->contractEntity->corporation_id
        );
    }
}