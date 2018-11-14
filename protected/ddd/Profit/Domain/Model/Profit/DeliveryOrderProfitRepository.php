<?php
/**
 * Desc: 发货单利润仓储trait
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Profit\Domain\Model\Profit;

use ddd\infrastructure\DIService;


trait DeliveryOrderProfitRepository
{
    /**
     * @var IDeliveryOrderProfitRepository
     */
    protected $deliveryOrderProfitRepository;

    /**
     * @desc 获取发货单利润仓储
     * @return IDeliveryOrderProfitRepository
     * @throws \Exception
     */
    protected function getDeliveryOrderProfitRepository()
    {
        if(empty($this->deliveryOrderProfitRepository)) {
            $this->deliveryOrderProfitRepository = DIService::getRepository(IDeliveryOrderProfitRepository::class);
        }

        return $this->deliveryOrderProfitRepository;
    }
}