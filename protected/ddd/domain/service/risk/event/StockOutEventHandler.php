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
use ddd\infrastructure\error\ExceptionService;
use ddd\repository\contract\TradeGoodsRepository;
use ddd\repository\contract\ContractRepository;
use ddd\repository\stock\DeliveryOrderRepository;

class StockOutEventHandler implements IAmountEventHandler
{
    public $event;
    public $contractEntity;

    public function __construct($event = null)
    {
        if (!empty($event))
        {
            $this->event = $event;
        }
        $deliveryOrderEntity = DeliveryOrderRepository::repository()->findByPk($this->event->sender->order_id);
        if (empty($deliveryOrderEntity))
        {
            ExceptionService::throwModelDataNotExistsException($this->event->sender->order_id, 'DeliveryOrder');
        }
        $contractEntity = ContractRepository::repository()->findByPk($deliveryOrderEntity->contract_id);
        if (empty($contractEntity))
        {
            ExceptionService::throwModelDataNotExistsException($deliveryOrderEntity->contract_id, 'Contract');
        }
        $this->contractEntity = $contractEntity;
    }

    function getPartnerId()
    {
        return $this->event->sender->partner_id;
    }

    function getAmount()
    {
        $amount = 0;
        $stockOutItems = $this->event->sender->items;
        if (\Utility::isNotEmpty($stockOutItems))
        {
            foreach ($stockOutItems as $goodsId => $entity)
            {
                $contractGoods = DIService::getRepository(ITradeGoodsRepository::class)->findByContractIdAndGoodsId($this->contractEntity->contract_id, $goodsId);
                $amount += $entity->quantity->quantity * $contractGoods->price * $this->contractEntity->exchange_rate;
            }
        }

        return $amount;
    }

    function getCategory()
    {
        return PartnerAmountSourceCategoryEnum::Stock_Out;
    }

    function getRelationId()
    {
        return $this->event->sender->out_order_id;
    }

    function getContractInfo()
    {
        return array(
            'contract_id' => $this->contractEntity->contract_id,
            'corporation_id' => $this->contractEntity->corporation_id,
            'project_id' => $this->contractEntity->project_id,
        );
    }
}