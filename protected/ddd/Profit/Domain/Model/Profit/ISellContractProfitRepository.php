<?php
/**
 * Desc: 销售合同利润仓储接口
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Profit\Domain\Model\Profit;


use ddd\Common\Domain\IRepository;

interface ISellContractProfitRepository extends IRepository
{
    function findByContractId($order_id);
    function findByProjectId($project_id);

}