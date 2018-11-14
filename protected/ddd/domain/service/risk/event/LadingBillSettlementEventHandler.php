<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 17:25
 * Describe：
 */

namespace ddd\domain\service\risk\event;


use ddd\domain\iRepository\contract\ITradeGoodsRepository;
use ddd\domain\service\risk\IAmountEventHandler;
use ddd\domain\service\risk\PartnerAmountSourceCategoryEnum;
use ddd\infrastructure\DIService;
use ddd\repository\contract\TradeGoodsRepository;
use ddd\repository\contract\ContractRepository;

class LadingBillSettlementEventHandler implements IAmountEventHandler
{
    public $contractEntity;
    public $event;

    public function __construct($event = null)
    {
        if (!empty($event))
        {
            $this->event = $event;
        }
        $this->contractEntity = ContractRepository::repository()->findByPk($this->event->sender->contract_id);
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
        return PartnerAmountSourceCategoryEnum::Stock_In_Settle;
    }

    function getRelationId()
    {
        return $this->event->sender->batch_id;
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