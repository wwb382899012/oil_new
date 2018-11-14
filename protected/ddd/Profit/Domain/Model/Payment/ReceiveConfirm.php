<?php
/**
 * User: liyu
 * Date: 2018/8/13
 * Time: 19:14
 * Desc: 合同实付实体
 */

namespace ddd\Profit\Domain\Model\Payment;


use ddd\Common\Domain\BaseEntity;

class ReceiveConfirm extends BaseEntity
{
    public $contract_id;
    public $amount;
    /**
     * @var 人名币金额
     */
    public $amount_cny;
    public $status;
    public $subject;

    public static function create($contractId = null) {
        $entity = new static();
        if ($contractId) {
            $entity->contract_id = $contractId;
        }
        return $entity;
    }

    public function isSubmitted() {
        return $this->status >= \ReceiveConfirm::STATUS_SUBMITED;
    }
}