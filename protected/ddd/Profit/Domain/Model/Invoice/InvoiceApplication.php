<?php
/**
 * User: liyu
 * Date: 2018/8/14
 * Time: 14:49
 * Desc: InvoiceApplication.php
 */

namespace ddd\Profit\Domain\Model\Invoice;


use ddd\Common\Domain\BaseEntity;

class InvoiceApplication extends BaseEntity
{
    /**
     * @var 发票申请ID
     */
    public $apply_id;

    /**
     * 状态
     */

    public $status;

    /**
     * @var 发票类型
     */
    public $type_sub;


    /**
     * @var 进项票/销项票
     */
    public $type;

    /**
     * @var array 发票详情
     */
    public $application_details = [];


    public static function create() {
        $entity = new static();
        return $entity;
    }

    public function addApplicationDetail(InvoiceApplicationDetail $detail) {
        $this->application_details[$detail->detail_id] = $detail;
    }

    /**
     * @desc 是否审核通过
     * @return bool
     */
    public function isCheckPass() {
        return $this->status >= \InvoiceApplication::STATUS_PASS;
    }

    /**
     * @desc 是否货款类
     * @return bool
     */
    public function isSubTypeGoods() {
        return $this->type_sub == \InvoiceApplication::SUB_TYPE_GOODS;
    }

    /**
     * 是否是进项票
     * @return bool
     */
    public function isTypeBuy() {
        return $this->type == \InvoiceApplication::TYPE_BUY;
    }
}