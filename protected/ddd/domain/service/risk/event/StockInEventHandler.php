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

class StockInEventHandler implements IAmountEventHandler
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
        $stockInItems = $this->event->sender->items;
        if (\Utility::isNotEmpty($stockInItems))
        {
            foreach ($stockInItems as $goodsId => $entity)
            {
                $contractGoods = DIService::getRepository(ITradeGoodsRepository::class)->findByContractIdAndGoodsId($this->event->sender->contract_id, $goodsId);
                if ($contractGoods->unit == $entity->quantity->unit)
                {
                    $amount += $entity->quantity->quantity * $contractGoods->price * $this->contractEntity->exchange_rate;
                } elseif ($contractGoods->unit == $entity->quantity_sub->unit)
                {
                    $amount += $entity->quantity_sub->quantity * $contractGoods->price * $this->contractEntity->exchange_rate;
                }
            }
        }

        return $amount * - 1;
    }

    function getCategory()
    {
        return PartnerAmountSourceCategoryEnum::Stock_In;
    }

    function getRelationId()
    {
        return $this->event->sender->stock_in_id;
    }

    function getContractInfo()
    {
        return array(
            'contract_id' => $this->contractEntity->contract_id,
            'project_id' => $this->contractEntity->project_id,
            'corporation_id' => $this->contractEntity->corporation_id
        );
    }
}