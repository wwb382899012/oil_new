<?php
/**
 * Desc: 交易主体利润仓储接口
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Profit\Domain\Model\Profit;


use ddd\Common\Domain\IRepository;

interface ICorporationProfitRepository extends IRepository
{
    function findByCorporationId($project_id);
}