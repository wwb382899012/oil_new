<?php
/**
 * Created by vector.
 * DateTime: 2018/8/28 17:01
 * Describe：
 */

namespace ddd\Profit\Domain\EstimateProfit;

use ddd\Common\Domain\IRepository;


interface IEstimateContractProfitRepository extends IRepository
{
    /**
     * 根据合同id查找对象
     * @param $contractId
     * @return EstimateContractProfit
     */
    public function findByContractId($contractId);

    /**
     * 根据项目id查找对象
     * @param $projectId
     * @return EstimateContractProfit[]
     */
    public function findAllByProjectId($projectId);


}