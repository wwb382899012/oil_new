<?php
/**
 * Created by vector.
 * DateTime: 2018/8/28 17:01
 * Describe：
 */

namespace ddd\Profit\Domain\EstimateProfit;


interface IEstimateProjectProfitRepository
{
    /**
     * 根据项目id查找对象
     * @param $contractId
     * @return Contract
     */
    public function findByProjectId($projectId);

    public function findByCorporationId($corporationId);

}