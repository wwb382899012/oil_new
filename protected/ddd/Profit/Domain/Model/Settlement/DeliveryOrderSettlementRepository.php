<?php
/**
 * Desc: 发货单结算仓储trait
 * User: vector
 * Date: 2018/8/28
 * Time: 11:13
 */

namespace ddd\Profit\Domain\Model\Settlement;

use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Settlement\IDeliveryOrderSettlementRepository;


trait DeliveryOrderSettlementRepository
{
    /**
     * @var $deliveryOrderSettlementRepository
     */
    protected $deliveryOrderSettlementRepository;

    /**
     * @desc 获取发货单结算仓储
     * @return $deliveryOrderSettlementRepository
     * @throws \Exception
     */
    protected function getDeliveryOrderSettlementRepository()
    {
        if(empty($this->deliveryOrderSettlementRepository)) {
            $this->deliveryOrderSettlementRepository = DIService::getRepository(IDeliveryOrderSettlementRepository::class);
        }

        return $this->deliveryOrderSettlementRepository;
    }
}