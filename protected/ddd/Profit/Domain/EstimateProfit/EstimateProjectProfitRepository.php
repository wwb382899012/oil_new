<?php
/**
 * Desc: 预估项目利润仓储trait
 * User: vector
 * Date: 2018/8/28
 * Time: 17:02
 */

namespace ddd\Profit\Domain\EstimateProfit;

use ddd\infrastructure\DIService;


trait EstimateProjectProfitRepository
{
    /**
     * @var IEstimateProjectProfitRepository
     */
    protected $estimateProjectProfitRepository;

    /**
     * @desc 获取预估项目利润仓储
     * @return IEstimateProjectProfitRepository
     * @throws \Exception
     */
    protected function getEstimateProjectProfitRepository()
    {
        if(empty($this->estimateProjectProfitRepository)) {
            $this->estimateProjectProfitRepository = DIService::getRepository(IEstimateProjectProfitRepository::class);
        }

        return $this->estimateProjectProfitRepository;
    }
}