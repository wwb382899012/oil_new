<?php
/**
 * Desc: 预估交易主体利润仓储trait
 * User: vector
 * Date: 2018/8/28
 * Time: 17:02
 */

namespace ddd\Profit\Domain\EstimateProfit;

use ddd\infrastructure\DIService;


trait EstimateCorporationProfitRepository
{
    /**
     * @var IEstimateCorporationProfitRepository
     */
    protected $estimateCorporationProfitRepository;

    /**
     * @desc 获取预估交易主体利润仓储
     * @return IEstimateCorporationProfitRepository
     * @throws \Exception
     */
    protected function getEstimateCorporationProfitRepository()
    {
        if(empty($this->estimateCorporationProfitRepository)) {
            $this->estimateCorporationProfitRepository = DIService::getRepository(IEstimateCorporationProfitRepository::class);
        }

        return $this->estimateCorporationProfitRepository;
    }
}