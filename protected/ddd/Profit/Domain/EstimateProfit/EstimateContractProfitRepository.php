<?php
/**
 * Desc: 预估合同利润仓储trait
 * User: vector
 * Date: 2018/8/28
 * Time: 17:02
 */

namespace ddd\Profit\Domain\EstimateProfit;

use ddd\infrastructure\DIService;


trait EstimateContractProfitRepository
{
    /**
     * @var IEstimateContractProfitRepository
     */
    protected $estimateContractProfitRepository;

    /**
     * @desc 获取预估合同利润仓储
     * @return IEstimateContractProfitRepository
     * @throws \Exception
     */
    protected function getEstimateContractProfitRepository()
    {
        if(empty($this->estimateContractProfitRepository)) {
            $this->estimateContractProfitRepository = DIService::getRepository(IEstimateContractProfitRepository::class);
        }

        return $this->estimateContractProfitRepository;
    }
}