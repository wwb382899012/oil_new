<?php
/**
 * User: liyu
 * Date: 2018/8/9
 * Time: 16:36
 * Desc: ReceiveConfirmEvent.php
 */

namespace ddd\Profit\Domain\Model\Payment;


use ddd\Common\Domain\BaseEvent;

class ProjectPayConfirmEvent extends BaseEvent
{
    public function initEventName() {
        parent::initEventName(); // TODO: Change the autogenerated stub
        $this->eventName = '项目下付款实付成功';
    }
}