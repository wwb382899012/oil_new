<?php
/**
 * User: liyu
 * Date: 2018/8/14
 * Time: 15:00
 * Desc: InvoiceApplicationDetail.php
 */

namespace ddd\Profit\Domain\Model\Invoice;


use ddd\Common\Domain\BaseEntity;

class InvoiceApplicationDetail extends BaseEntity
{
    /**
     * @var 标识
     */
    public $detail_id;

    /**
     * @var 申请ID
     */
    public $apply_id;

    /**
     * @var 金额
     */
    public $amount;

    public static function create() {
        $entity = new static();
        return $entity;
    }
}