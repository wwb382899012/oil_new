<?php
/**
 * Desc: 合同平移仓储接口
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Split\Domain\Model\ContractSplit;


use ddd\Common\Domain\IRepository;
use ddd\Common\IAggregateRoot;

interface IContractSplitRepository extends IRepository{
    public function store(IAggregateRoot $entity);

    public function findByNewContractId($newContractId);
}