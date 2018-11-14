<?php
/**
 * Desc: 发货单仓储接口
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Profit\Domain\Model\Stock;


use ddd\Common\Domain\IRepository;

interface IDeliveryOrderRepository extends IRepository
{
    function findByOrderId($orderId);
    function findByBatchId($batchId);

}