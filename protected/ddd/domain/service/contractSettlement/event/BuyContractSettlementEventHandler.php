<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 17:25
 * Describe：
 */

namespace ddd\domain\service\contractSettlement\event;

use ddd\domain\entity\contractSettlement\SettlementStatus;
use ddd\domain\iRepository\stock\IStockInRepository;
use ddd\infrastructure\DIService;

class BuyContractSettlementEventHandler
{
    public $event;

    public function __construct($event = null)
    {
        if (!empty($event))
        {
            $this->event = $event;
        }
    }

    public function updateStockInSettledStatus()
    {
        $stockIns = DIService::getRepository(IStockInRepository::class)->findAllByContractId($this->event->sender->contract_id);
        if (\Utility::isNotEmpty($stockIns))
        {
            foreach ($stockIns as $stockIn)
            {
                if ($stockIn->status == \StockIn::STATUS_PASS && $this->event->sender->status == SettlementStatus::STATUS_PASS)
                {
                    $stockIn->setSettledAndSave();
                }
            }
        }
    }
}