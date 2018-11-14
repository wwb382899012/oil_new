<?php
/**
 * Desc: 采购合同平移申请提交之后
 * User: susiehuang
 * Date: 2018/5/31 0031
 * Time: 14:52
 */

namespace ddd\Split\Domain\Model\ContractSplit;


use ddd\Common\Domain\BaseEvent;

class ContractSplitApplySubmittedEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '合同平移申请提交之后';
    }
}