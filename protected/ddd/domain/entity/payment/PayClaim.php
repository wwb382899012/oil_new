<?php

/**
 * Desc:付款认领
 * User: susiehuang
 * Date: 2018/3/19 0018
 * Time: 12:13
 */

namespace ddd\domain\entity\payment;

use ddd\domain\event\payment\PayClaimedEvent;
use ddd\domain\event\subscribe\EventSubscribeService;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\FinanceConfirm;

class PayClaim extends FinanceConfirm implements IAggregateRoot
{
    public function getId()
    {
        return $this->id;
    }

    public function getIdName()
    {
        return "claim_id";
    }

    function setId($value)
    {
        $this->id=$value;
    }

    /**
     * 自定义的属性
     * attribute name => attribute value
     * @return array
     */
    public function customAttributes()
    {
        $fields = array('claim_id', 'corporation_id', 'project_id', 'contract_id', 'apply_id', 'sub_contract_id', 'sub_contract_type', 'sub_contract_code', 'type', 'subject_id', 'pay_date', 'amount', 'amount_cny', 'currency', 'exchange_rate', 'status', 'status_time', 'remark', 'create_user_id', 'create_time', 'update_user_id', 'update_time');
        $attrs = array();
        foreach ($fields as $f)
        {
            $attrs[$f] = null;
        }

        return $attrs;
    }

    public function customAttributeNames()
    {
        return array('claim_id', 'corporation_id', 'project_id', 'contract_id', 'apply_id', 'sub_contract_id', 'sub_contract_type', 'sub_contract_code', 'type', 'subject_id', 'pay_date', 'amount', 'amount_cny', 'currency', 'exchange_rate', 'status', 'status_time', 'remark', 'create_user_id', 'create_time', 'update_user_id', 'update_time');
    }

    public static function create(PayApplication $payApplication = null)
    {
        $entity = new PayClaim();
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
     * @desc 付款认领提交
     * @throws \CException
     */
    public function submit()
    {
        EventSubscribeService::bind($this,"onAfterSubmit", EventSubscribeService::PayClaimSubmitEvent);
        $this->afterSubmit();
    }

    /**
     * 当付款认领提交后
     * @throws \CException
     */
    public function afterSubmit()
    {
        if($this->hasEventHandler('onAfterSubmit'))
            $this->onAfterSubmit(new PayClaimedEvent($this));
    }

    /**
     * 响应付款认领提交事件
     * @param $event
     * @throws \CException
     */
    public function onAfterSubmit($event)
    {
        $this->raiseEvent('onAfterSubmit', $event);
    }
}