<?php
/**
 * User: liyu
 * Date: 2018/8/14
 * Time: 15:56
 * Desc: Invoice.php
 */

namespace ddd\Profit\Domain\Model\Invoice;


use ddd\Common\Domain\BaseEntity;

class Invoice extends BaseEntity
{

    /**
     * @var 标识
     */
    public $invoice_id;

    /**
     * @var 申请ID
     */
    public $apply_id;

    /**
     * @var 发票申请信息
     */
    public $invoice_application;

    /**
     * @var 状态
     */
    public $status;

    public static function create() {
        $entity = new static();
        return $entity;
    }

    public function isCheckPass(){
        return $this->status>=\Invoice::STATUS_PASS;
    }
}