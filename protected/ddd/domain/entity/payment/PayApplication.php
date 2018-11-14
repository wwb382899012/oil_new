<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/3/19 0019
 * Time: 9:40
 */

namespace ddd\domain\entity\payment;


use ddd\Common\IAggregateRoot;
use ddd\Common\Domain\BaseEntity;

class PayApplication extends BaseEntity implements IAggregateRoot
{

    /**
     * @var      bigint
     */
    public $apply_id;

    /**
     * @var      int
     */
    public $corporation_id;

    /**
     * array(Receiver)
     * @var      array
     */
    public $receiver;

    /**
     * @var      date
     */
    public $apply_date;

    /**
     * @var      int
     */
    public $amount;

    /**
     * 付款申请明细
     * array(planId=>RayApplicationDetail)
     * @var      array
     */
    public $detail;

    /**
     * @var      array
     */
    public $payConfirmDetail;

    /**
     * @var      array
     */
    public $payStopDetail;

    /**
     * @var      int
     */
    public $status;

    /**
     * @var      array
     */
    public $applicant;

    public function getId()
    {
        return $this->apply_id;
    }

    public function getIdName()
    {
        return "apply_id";
    }

    function setId($value)
    {
        $this->apply_id=$value;
    }

    /**
     */
    public function generateId()
    {
        // TODO: implement
    }

    /**
     */
    public function create()
    {
        // TODO: implement
        $obj = new PayApplication();

        return $obj;
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
     */
    public function addDetail()
    {
        // TODO: implement
    }

    /**
     * @return   boolean
     */
    public function planIsExists()
    {
        // TODO: implement
    }

    /**
     */
    public function removeDetail()
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

    /**
     */
    public function reject()
    {
        // TODO: implement
    }

    /**
     */
    public function confirm()
    {
        // TODO: implement
    }

    /**
     * @return   boolean
     */
    public function isCanTrash()
    {
        // TODO: implement
    }

    /**
     */
    public function trash()
    {
        // TODO: implement
    }

    /**
     * @return   boolean
     */
    public function isCanPayConfirm()
    {
        // TODO: implement
    }

    /**
     * @param    int $amount
     */
    public function payConfirm($amount)
    {
        // TODO: implement
    }

    /**
     * @return   boolean
     */
    public function isCanStopPayment()
    {
        // TODO: implement
    }

    /**
     */
    public function getStatus()
    {
        // TODO: implement
    }

    /**
     */
    public function getAmountPaid()
    {
        // TODO: implement
    }

    /**
     */
    public function isCanCliam()
    {
        // TODO: implement
    }

    /**
     */
    public function getAmountClaim()
    {
        // TODO: implement
    }

    /**
     */
    public function getAmountStop()
    {
        // TODO: implement
    }

    /**
     */
    public function isPayStop()
    {
        // TODO: implement
    }
}