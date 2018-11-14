<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 18:00
 * Describe：
 */

namespace ddd\Profit\Domain\Contract;


interface IContractRepository
{
    /**
     * 根据合同id查找对象
     * @param $contract_id
     * @return Contract
     */
    public function findByContractId($contract_id);

}