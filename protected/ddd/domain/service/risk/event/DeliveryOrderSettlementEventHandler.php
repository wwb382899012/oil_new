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
use ddd\repository\contract\TradeGoodsRepository;

class DeliveryOrderSettlementEventHandler implements IAmountEventHandler
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
                    $amount += ($entity->settle_amount_cny - $entity->out_quantity->quantity * $tradeGoods->price * $this->contractEntity->exchange_rate);
                } elseif ($tradeGoods->unit == $entity->settle_quantity_sub->unit)
                {
                    $amount += (($entity->settle_quantity_sub->quantity * $entity->settle_price - $entity->out_quantity_sub->quantity * $tradeGoods->price) * $this->contractEntity->exchange_rate);
                }
            }
        }

        return $amount;
    }

    function getCategory()
    {
        return PartnerAmountSourceCategoryEnum::Stock_Out_Settle;
    }

    function getRelationId()
    {
        return $this->event->sender->order_id;
    }

    function getContractInfo()
    {
        return array(
            'contract_id' => $this->event->sender->contract_id,
            'corporation_id' => $this->contractEntity->corporation_id,
            'project_id' => $this->contractEntity->project_id,
        );
    }
}