<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 17:25
 * Describe：
 */

namespace ddd\domain\service\contractSettlement\event;


use ddd\domain\entity\contractSettlement\SettlementStatus;
use ddd\domain\iRepository\stock\IStockOutRepository;
use ddd\infrastructure\DIService;

class DeliveryOrderSettlementEventHandler
{
    public $event;

    public function __construct($event = null)
    {
        if (!empty($event))
        {
            $this->event = $event;
        }
    }

    public function updateStockOutSettledStatus()
    {
        $stockOuts = DIService::getRepository(IStockOutRepository::class)->findAllByOrderId($this->event->sender->order_id);
        if (\Utility::isNotEmpty($stockOuts))
        {
            foreach ($stockOuts as $stockOut)
            {
                if ($stockOut->status == \StockOutOrder::STATUS_SUBMITED && $this->event->sender->status == SettlementStatus::STATUS_PASS)
                {
                    $stockOut->setSettledAndSave();
                }
            }
        }
    }
}