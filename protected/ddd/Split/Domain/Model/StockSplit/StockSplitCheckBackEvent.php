<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/5/31
 * Time: 21:20
 */

namespace ddd\Split\Domain\Model\StockSplit;

use ddd\Common\Domain\BaseEvent;

class StockSplitCheckBackEvent extends BaseEvent{
    function initEventName(){
        parent::initEventName();
        $this->eventName = '入库单平移审核驳回';
    }
}