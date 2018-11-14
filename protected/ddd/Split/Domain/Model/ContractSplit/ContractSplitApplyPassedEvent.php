<?php
/**
 * Desc: 合同平移申请通过之后
 * User: susiehuang
 * Date: 2018/5/31 0031
 * Time: 15:08
 */

namespace ddd\Split\Domain\Model\ContractSplit;


use ddd\Common\Domain\BaseEvent;

class ContractSplitApplyPassedEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '合同平移申请通过之后';
    }
}