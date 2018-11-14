<?php
/**
 * Desc: 发货单利润仓储接口
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Profit\Domain\Model\Profit;


use ddd\Common\Domain\IRepository;

interface IDeliveryOrderProfitRepository extends IRepository
{


    function findByOrderId($order_id);

    function findByContractId($contract_id);

    function findByProjectId($project_id);

    function findByCorporationId($corporation_id);

}