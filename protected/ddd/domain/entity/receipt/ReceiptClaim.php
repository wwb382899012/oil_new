<?php

/**
 * Desc: 收款认领
 * User: susiehuang
 * Date: 2018/3/19 0018
 * Time: 12:13
 */

namespace ddd\domain\entity\receipt;

use ddd\domain\event\receipt\ReceiptClaimedEvent;
use ddd\domain\event\subscribe\EventSubscribeService;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\FinanceConfirm;

class ReceiptClaim extends FinanceConfirm implements IAggregateRoot
{
    public function getId()
    {
        return $this->id;
    }

    public function getIdName()
    {
        return "receive_id";
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
        $fields = array('receive_id', 'flow_id', 'project_id', 'contract_id', 'sub_contract_type', 'sub_contract_code', 'receive_date', 'amount', 'account_id', 'pay_type', 'currency', 'exchange_rate', 'status', 'remark', 'create_user_id', 'create_time', 'update_user_id', 'update_time', 'subject');
        $attrs = array();
        foreach ($fields as $f)
        {
            $attrs[$f] = null;
        }

        return $attrs;
    }

    public function customAttributeNames()
    {
        return array('receive_id', 'flow_id', 'project_id', 'contract_id', 'sub_contract_type', 'sub_contract_code', 'receive_date', 'amount', 'account_id', 'pay_type', 'currency', 'exchange_rate', 'status', 'remark', 'create_user_id', 'create_time', 'update_user_id', 'update_time', 'subject');
    }

    public static function create(BankFlow $bankFlow = null)
    {
        $entity = new ReceiptClaim();
        if (!empty($bankFlow))
        {
            $entity->flow_id = $bankFlow->getId();
        }

        return $entity;
    }

    /**
     * @desc 银行流水认领提交
     * @throws \CException
     */
    public function submit()
    {
        EventSubscribeService::bind($this,"onAfterSubmit", EventSubscribeService::ReceiptClaimSubmitEvent);
        $this->afterSubmit();
    }

    /**
     * 当银行流水认领提交后
     * @throws \CException
     */
    public function afterSubmit()
    {
        if($this->hasEventHandler('onAfterSubmit'))
            $this->onAfterSubmit(new ReceiptClaimedEvent($this));
    }

    /**
     * 响应银行流水认领提交事件
     * @param $event
     * @throws \CException
     */
    public function onAfterSubmit($event)
    {
        $this->raiseEvent('onAfterSubmit', $event);
    }
}