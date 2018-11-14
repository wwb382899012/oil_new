<?php
/**
 * Desc: 收付款确认/认领
 * User: susiehuang
 * Date: 2018/3/19 0019
 * Time: 9:26
 */

namespace ddd\domain\entity;


use ddd\Common\Domain\BaseEntity;

abstract class FinanceConfirm extends \ddd\Common\Domain\BaseEntity
{

    /**
     * @var      bigint
     */
    public $id;

    /**
     * @var      bigint
     */
    public $contract_id;

    /**
     * 收款确认对应收款合同信息
     * array(FinanceContractInfo)
     * @var      array
     */
    public $finance_contract_info;

    /**
     * 收款明细
     * array(planId=>Detail)
     * @var      array
     */
    public $detail;

    /**
     * @var      int
     */
    public $subject_id;

    /**
     * @var      float
     */
    public $amount;

    /**
     * @var      int
     */
    public $status;

    /**
     */
    public function generateId()
    {
        // TODO: implement
    }

    /**
     * @param    int $plan_id
     * @return   boolean
     */
    public function isDetailExists($plan_id)
    {
        // TODO: implement
    }

    /**
     * @param    FinanceDetail $detail
     * @return   boolean
     */
    public function addDetail($detail)
    {
        // TODO: implement
    }

    /**
     * @param    int $plan_id
     * @return   boolean
     */
    public function removeFinanceDetail($plan_id)
    {
        // TODO: implement
    }

    /**
     * @return   boolean
     */
    public function isCanSubmit()
    {
        // TODO: implement
    }

    /**
     */
    public function submit()
    {
        // TODO: implement
    }

    /**
     * @return   boolean
     */
    public function isCanEdit()
    {
        // TODO: implement
    }
}