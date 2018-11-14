<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/16 10:59
 * Describe：
 */

namespace ddd\domain\event\project;


use ddd\Common\Domain\BaseEvent;

class ProjectSubmitEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '项目提交';
    }
}