<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/3/19 0019
 * Time: 14:05
 */

namespace ddd\domain\entity\receipt;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;

class BankFlow extends BaseEntity implements IAggregateRoot
{

    /**
     * @var      bigint
     */
    public $id;

    /**
     * @var      string
     */
    public $code;

    /**
     * @var      int
     */
    public $corporation_id;

    /**
     * 收款流水对应付款账户信息
     * array(CashFlowPayer)
     * @var      array
     */
    public $payer;

    /**
     * 收款流水对应收款账户信息
     * array(CashFlowReceiver)
     * @var      array
     */
    public $receiver;

    /**
     * @var      date
     */
    public $receive_date;

    /**
     * @var      float
     */
    public $amount;

    /**
     * @var      int
     */
    public $currency;

    /**
     * @var      float
     */
    public $amount_claim;

    /**
     * @var      int
     */
    public $status;

    public function getId()
    {
        return $this->flow_id;
    }

    public function getIdName()
    {
        return "flow_id";
    }

    function setId($value)
    {
        $this->flow_id=$value;
    }

    /**
     */
    public function generateId()
    {
        // TODO: implement
    }

    /**
     */
    public function generateCode()
    {
        // TODO: implement
    }

    /**
     */
    public static function create()
    {
        // TODO: implement
        return new BankFlow();
    }

    /**
     * @return   boolean
     */
    public function isCanWithdraw()
    {
        // TODO: implement
    }

    /**
     */
    public function withdraw()
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

    /**
     * @return   boolean
     */
    public function isCanClaim()
    {
        // TODO: implement
    }

    /**
     */
    public function getBalanceClaimAmount()
    {
        // TODO: implement
    }

    /**
     */
    public function getClaimedAmount()
    {
        // TODO: implement
    }

    /**
     */
    public function updateBankFlowClaimAmount()
    {
        // TODO: implement
    }
}