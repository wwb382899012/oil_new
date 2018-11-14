<?php
/**
 * Desc: 出入库平移仓储接口
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Split\Domain\Model\ContractSplit;


use ddd\Common\Domain\IRepository;
use ddd\Common\IAggregateRoot;

interface IStockSplitDetailRepository extends IRepository{
    /**
     * 持久化到数据库
     * @param    IAggregateRoot $entity
     * @throws   \Exception
     */
    public function store(IAggregateRoot $entity);

    public function updateNewBillId($id,$newBillId);
}