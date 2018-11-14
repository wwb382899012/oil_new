<?php
/**
 * Created by vector.
 * DateTime: 2018/8/28 17:01
 * Describe：
 */

namespace ddd\Profit\Domain\Quantity;

use ddd\Common\Domain\IRepository;


interface ISellOutQuantityRepository extends IRepository
{
    /**
     * 根据发货单id查找对象
     * @param $bill_id
     * @return SellOutQuantity
     */
    public function findByBillId($billId);

}