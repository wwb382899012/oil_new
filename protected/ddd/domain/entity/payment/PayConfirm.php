<?php

/**
 * Desc:付款实付
 * User: susiehuang
 * Date: 2018/3/19 0018
 * Time: 12:13
 */

namespace ddd\domain\entity\payment;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\event\payment\PayEvent;
use ddd\domain\event\subscribe\EventSubscribeService;
use ddd\Common\IAggregateRoot;

class PayConfirm extends BaseEntity implements IAggregateRoot
{

    /**
     * @var      int
     */
    public $payement_id;

    /**
     * @var      date
     */
    public $pay_date;

    /**
     * @var      float
     */
    public $amount;

    /**
     * @var      array
     */
    public $payer;

    /**
     * @var      string
     */
    public $bank_flow;

    public function getId()
    {
        return $this->payment_id;
    }

    public function getIdName()
    {
        return "payment_id";
    }

    function setId($value)
    {
        $this->payement_id=$value;
    }

    /**
     * 自定义的属性
     * attribute name => attribute value
     * @return array
     */
    public function customAttributes()
    {
        $fields = array('payment_id', 'apply_id', 'pay_date', 'payment_no', 'amount', 'currency', 'amount_cny', 'exchange_rate', 'account_id', 'status', 'remark', 'create_user_id', 'create_time', 'update_user_id', 'update_time');
        $attrs = array();
        foreach ($fields as $f)
        {
            $attrs[$f] = null;
        }

        return $attrs;
    }

    public function customAttributeNames()
    {
        return array('payment_id', 'apply_id', 'pay_date', 'payment_no', 'amount', 'currency', 'amount_cny', 'exchange_rate', 'account_id', 'status', 'remark', 'create_user_id', 'create_time', 'update_user_id', 'update_time');
    }


    public static function create(PayApplication $payApplication = null)
    {
        $entity = new PayConfirm();
        if (!empty($payApplication))
        {
            $entity->apply_id = $payApplication->getId();
            $entity->corporation_id = $payApplication->corporation_id;
            $entity->contract_id = $payApplication->contract_id;
            $entity->project_id = $payApplication->project_id;
        }

        return $entity;
    }

    /**
     * @desc 实付提交
     * @throws \CException
     */
    public function submit()
    {
        EventSubscribeService::bind($this,"onAfterSubmit", EventSubscribeService::PayConfirmSubmitEvent);
        $this->afterSubmit();
    }

    /**
     * 当实付提交后
     * @throws \CException
     */
    public function afterSubmit()
    {
        if($this->hasEventHandler('onAfterSubmit'))
            $this->onAfterSubmit(new PayEvent($this));
    }

    /**
     * 响应实付提交事件
     * @param $event
     * @throws \CException
     */
    public function onAfterSubmit($event)
    {
        $this->raiseEvent('onAfterSubmit', $event);
    }
}