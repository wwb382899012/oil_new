<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/26 11:54
 * Describe：
 *      付款审核
 */

class Check13 extends Check
{
    // 需要添加表t_flow, t_flow_business, t_flow_node添加数据
    public function init()
    {
        $this->businessId=FlowService::BUSINESS_PAY_APPLICATION;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart()
    {

    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone()
    {
        $apply=PayApplication::model()->findByPk($this->objId);
        if(empty($apply))
            throw new Exception("id为".$this->objId."的付款申请不存在");

        $apply->setAttributes(array(
            'status' =>PayApplication::STATUS_CHECKED,
            'status_time'=> new CDbExpression('now()'),
            'update_user_id'=> Utility::getNowUserId(),
            'update_time'=> new CDbExpression('now()')
        ));
        $apply->update(array('status','status_time', 'update_user_id','update_time' ));

        AMQPService::publishForPayment($this->objId);
    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     * 当审核状态不为1或-1时都进入该项，可以在这里添加其他审核状态的处理
     */
    public function checkReject()
    {

    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack()
    {
        $apply=PayApplication::model()->findByPk($this->objId);
        if(empty($apply))
            throw new Exception("id为".$this->objId."的付款申请不存在");

        $apply->setAttributes(array(
                                  'status' =>PayApplication::STATUS_BACK,
                                  'status_time'=> new CDbExpression('now()'),
                                  'update_user_id'=> Utility::getNowUserId(),
                                  'update_time'=> new CDbExpression('now()')
                              ));
        $apply->update(array('status','status_time', 'update_user_id','update_time' ));


        if($apply->type == PayApplication::TYPE_CONTRACT || $apply->type ==PayApplication::TYPE_SELL_CONTRACT)
        {
            //更新合同付款计划中的付款金额
            $details=PayApplicationDetail::model()->findAll("apply_id=".$apply->apply_id);
            if(is_array($details))
            {
                foreach ($details as $d)
                {
                    if(empty($d["plan_id"]))
                        continue;
                    PaymentPlanService::updatePaidAmount($d["plan_id"],-1*$d["amount"]);
                }
            }
        }

        $currency_ico = Map::$v["currency"][$apply->currency]['ico'];
        $taskParams = array('keyValue'=>$apply->apply_id ,'payee'=>$apply->payee, 'amount'=>$currency_ico . number_format($apply->amount / 100, 2));
        TaskService::addTasks(Action::ACTION_PAY_APPLICATION_BACK,$this->objId,0,$apply->create_user_id,$apply->corporation_id, $taskParams);

        if($apply->is_factoring) {
            $factor = Factor::model()->find('apply_id=:applyId', array('applyId'=>$apply->apply_id));
            $factor->updateByPk($factor->factor_id,array(
                "status"=>Factor::STATUS_CONFIRM_BACK,
                "status_time"=>new CDbExpression("now()"),
                "update_time"=>new CDbExpression("now()"),
                "update_user_id"=>Utility::getNowUserId()
            ),"status<=".Factor::STATUS_SUBMIT);
            TaskService::doneTask($factor->factor_id, Action::ACTION_FACTOR_AMOUNT_CONFIRM, ActionService::getActionRoleIds(Action::ACTION_FACTOR_AMOUNT_CONFIRM));
        }
    }

    /**
     * 其它状态的审核处理
     * @param $checkStatus
     */
    public function checkElse($checkStatus)
    {

    }


    /**
     * 获取被审核对象，主要是条件流转时需要，有条件流转的子类必须重写该方法
     * @param $objId
     * @return mixed
     */
    public function getCheckObjectModel($objId)
    {
        if(empty($this->checkObjectModel))
            $this->checkObjectModel=PayApplication::model()->with("extra")->findByPk($this->objId);
        /*if(empty($apply))
            throw new Exception("id为".$this->objId."的付款申请不存在");*/
        return $this->checkObjectModel;
    }


    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', array('checkId'=>$checkItem->check_id, 'keyValue'=>$this->checkObjectModel->apply_id));
    }

}