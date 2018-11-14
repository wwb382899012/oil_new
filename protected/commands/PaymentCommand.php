<?php
/**
 * Desc: 付款相关队列监听信息
 * User: susiehuang
 * Date: 2018/7/16 0016
 * Time: 17:26
 */

class PaymentCommand extends AMQPCommand
{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        "new.oil.do.payment" => array( //自动实付队列
            "fn" => "doPayment",
            "exchange" => "new.oil.direct",
            "routingKey" => "new.oil.do.payment",
        ),
        "new.oil.auto.payment" => array( //自动实付队列
            "fn" => "doAutoPayment",
            "exchange" => "new.oil.direct",
            "routingKey" => "new.oil.auto.payment",
        ),
        "new.oil.query.auto.pay.status" => array( //查询自动实付状态队列
            "fn" => "queryAutoPaymentStatus",
            "exchange" => "new.oil.direct",
            "routingKey" => "new.oil.query.auto.pay.status",
        ),
        "new.oil.for.auto.payment" => array( //自动实付请求队列
            "fn" => "autoPayment",
            "exchange" => "new.oil.direct",
            "routingKey" => "new.oil.for.auto.payment",
        ),
    );

    public function init()
    {
        $this->sleepTime = 1;
        parent::init();
    }

    /**
     * 付款操作处理
     * @param $applyId
     * @throws Exception
     */
    public function doPayment($applyId)
    {
        if (Utility::checkQueryId($applyId) && $applyId > 0)
        {
            $apply = PayApplication::model()->findByPk($applyId);
            if (empty($apply))
            {
                return;
            }
            $canAutoPayment = AutoPaymentService::checkIsCanAutoPayment($applyId);
            if ($canAutoPayment['code'] == - 2)
            {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ', 该付款申请校验是否可自动实付失败：' . json_encode($canAutoPayment), CLogger::LEVEL_ERROR);

                return;
            }
            if ($canAutoPayment['code'] == 0)
            { //可进行自动付款
                $apply->setAttributes(array(
                    'status' => PayApplication::STATUS_IN_AUTO_PAYMENT,
                    'status_time' => new CDbExpression('now()'),
                    'update_user_id' => Utility::getNowUserId(),
                    'update_time' => new CDbExpression('now()'))
                );
                $apply->update(array('status', 'status_time', 'update_user_id', 'update_time'));

                //取消实付代办
                TaskService::doneTask($applyId, Action::ACTION_ACTUAL_PAY, ActionService::getActionRoleIds(Action::ACTION_ACTUAL_PAY));
                //发出付款实付消息，#预估利润报表#
                AMQPService::publishPayConfirm($apply->project_id);
                //发送自动实付消息
                AMQPService::publishForAutoPaymentToDelayQueue(['apply_id' => $applyId], 1);
            } else {
                if ($apply->status == PayApplication::STATUS_CHECKED) { //付款实付代办
                    TaskService::addTasks(Action::ACTION_ACTUAL_PAY, $applyId, ActionService::getActionRoleIds(Action::ACTION_ACTUAL_PAY), 0, $apply->corporation_id, array('code' => $applyId));
                }
            }
        }
    }

    /**
     * @desc 自动实付操作
     * @param $msg
     * @throws Exception
     */
    public function doAutoPayment($msg)
    {
        $params = json_decode($msg, true);
        AutoPaymentService::doAutoPayment($params);
    }

    public function queryAutoPaymentStatus($msg)
    {
        $params = json_decode($msg, true);
        AutoPaymentService::queryAutoPaymentStatus($params);
    }

    public function autoPayment($msg)
    {
        $params = json_decode($msg, true);
        if (Utility::checkQueryId($params['apply_id'])) {
            AutoPaymentService::autoPayment($params['apply_id']);
        }
    }
}