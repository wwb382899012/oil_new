<?php
/**
 * Desc: 发货单仓储trait
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Profit\Domain\Model\Stock;

use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderRepository;
trait DeliveryOrderRepository
{
    /**
     * @var deliveryOrderRepository
     */
    protected $deliveryOrderRepository;

    /**
     * @desc 获取发货单仓储
     * @return IContractSplitApplyRepository
     * @throws \Exception
     */
    protected function getDeliveryOrderRepository()
    {
        if(empty($this->deliveryOrderRepository)) {
            $this->deliveryOrderRepository = DIService::getRepository(IDeliveryOrderRepository::class);
        }

        return $this->deliveryOrderRepository;
    }
}