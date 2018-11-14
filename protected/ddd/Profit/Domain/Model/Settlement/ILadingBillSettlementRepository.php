<?php
/**
 * Desc: 入库通知单结算仓储接口
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Profit\Domain\Model\Settlement;


use ddd\Common\Domain\IRepository;

interface ILadingBillSettlementRepository extends IRepository
{
    function findByBatchId($batch_id);

}