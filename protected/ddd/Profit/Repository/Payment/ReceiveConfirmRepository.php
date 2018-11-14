<?php
/**
 * User: liyu
 * Date: 2018/8/13
 * Time: 19:31
 * Desc: ReceiveConfirmRepository.php
 */

namespace ddd\Profit\Repository\Payment;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\Profit\Domain\Model\Payment\IReceiveConfirmRepository;
use ddd\Profit\Domain\Model\Payment\ReceiveConfirm;

class ReceiveConfirmRepository extends EntityRepository implements IReceiveConfirmRepository
{

    function findByContract($contractId) {
        return $this->findAll('t.contract_id=' . $contractId);
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity
     */
    public function getNewEntity() {
        return ReceiveConfirm::create();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName() {
        return 'ReceiveConfirm';
    }
}