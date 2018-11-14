<?php
/**
 * Desc: 项目利润仓储接口
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Profit\Domain\Model\Profit;


use ddd\Common\Domain\IRepository;

interface IProjectProfitRepository extends IRepository
{
    function findByProjectId($project_id);

    function findByCorporationId($corporation_id);
}