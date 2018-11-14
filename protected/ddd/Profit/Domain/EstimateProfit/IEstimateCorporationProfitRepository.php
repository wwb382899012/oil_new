<?php
/**
 * Created by vector.
 * DateTime: 2018/8/28 17:01
 * Describe：
 */

namespace ddd\Profit\Domain\EstimateProfit;


interface IEstimateCorporationProfitRepository
{
    public function findByCorporationId($corporationId);

}