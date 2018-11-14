<?php
/**
 * Desc: 商品成本仓储接口
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Profit\Domain\Model\Stock;


use ddd\Common\Domain\IRepository;

interface IBuyGoodsCostRepository extends IRepository
{
    function findByOrderId($orderId);
}