<?php
/**
 * Desc: 销售合同利润仓储trait
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Profit\Domain\Model\Profit;

use ddd\infrastructure\DIService;


trait SellContractProfitRepository
{
    /**
     * @var IDeliveryOrderProfitRepository
     */
    protected $sellContractProfitRepository;

    /**
     * @desc 获取销售合同利润仓储
     * @return IDeliveryOrderProfitRepository
     * @throws \Exception
     */
    protected function getSellContratcProfitRepository()
    {
        if(empty($this->sellContractProfitRepository)) {
            $this->sellContractProfitRepository = DIService::getRepository(ISellContractProfitRepository::class);
        }

        return $this->sellContractProfitRepository;
    }
}