<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/6 16:52
 * Describe：
 *  商品入库事件
 */

namespace ddd\domain\event\stock;


use ddd\Common\Domain\BaseEvent;

class StockInEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName();
        $this->eventName = '入库单审核通过';
    }
}