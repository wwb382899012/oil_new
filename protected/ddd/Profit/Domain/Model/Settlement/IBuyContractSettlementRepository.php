<?php
/**
 * Desc: 采购合同结算仓储接口
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Profit\Domain\Model\Settlement;


use ddd\Common\Domain\IRepository;

interface IBuyContractSettlementRepository extends IRepository
{
    function findByContractId($contract_id);

}