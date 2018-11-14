<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 18:50
 * Describe：
 */

namespace ddd\Profit\Domain\Contract;


interface IContractSettlementRepository
{
    /**
     * 根据合同id查找对象
     * @param $contract_id
     * @return ContractSettlement
     */
    public function findByContractId($contract_id);

}