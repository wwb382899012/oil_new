<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/6 16:55
 * Describe：
 */

namespace ddd\domain\event\stock;


use ddd\Common\Domain\BaseEvent;

class StockOutEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '出库单审核通过';
    }
}