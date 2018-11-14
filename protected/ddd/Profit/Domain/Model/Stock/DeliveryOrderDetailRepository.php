<?php
/**
 * Desc: 发货明细仓储trait
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Profit\Domain\Model\Stock;

use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderDetailRepository;
trait DeliveryOrderDetailRepository
{
    /**
     * @var $buyGoodsCostRepository
     */
    protected $deliveryOrderDetailRepository;

    /**
     * @desc 获取商品成本仓储
     * @return IBuyGoodsCostRepository
     * @throws \Exception
     */
    protected function getBuyGoodsCostRepository()
    {
        if(empty($this->deliveryOrderDetailRepository)) {
            $this->deliveryOrderDetailRepository = DIService::getRepository(IDeliveryOrderDetailRepository::class);
        }

        return $this->deliveryOrderDetailRepository;
    }
}