<?php

/**
 * Desc: 保理信息审核
 * User: susiehuang
 * Date: 2017/10/24 0009
 * Time: 10:03
 */
class Check14 extends Check {
    public function init() {
        $this->businessId = FlowService::BUSINESS_FACTORING;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart() {

    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone() {
        FactorDetail::model()->updateByPk($this->objId, array('status' => FactorDetail::STATUS_PASS, 'update_user_id' => Utility::getNowUserId(), 'update_time' => new CDbExpression('now()')));
    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkReject() {

    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack() {
        $factorDetail = FactorDetail::model()->findByPk($this->objId);
        TaskService::addTasks(Action::ACTION_35, $this->objId,
                              array(
                                  "userIds"=>$factorDetail->create_user_id,
                                  "corpId"=>$factorDetail->corporation_id,
                                  "applyId"=>$factorDetail->apply_id,
                              )
            );
        //驳回检查是否存在保理申请代办，不存在则添加
        $taskObj = Task::model()->find('action_id=:actionId and key_value=:keyValue and status=:status', array('actionId'=>Action::ACTION_FACTOR_APPLY, 'keyValue' => $factorDetail->factor_id, 'status' => 0));
        if(empty($taskObj)) {
            TaskService::addTasks(Action::ACTION_FACTOR_APPLY, $factorDetail->factor_id, ActionService::getActionRoleIds(Action::ACTION_FACTOR_APPLY), 0, $factorDetail->factor->corporation_id, array('contractCode' => $factorDetail->factor->contract->contract_code, 'applyId' => $factorDetail->factor->apply_id, 'factorCode' => $factorDetail->factor->contract_code));
        }
        FactorDetail::model()->updateByPk($this->objId, array('status' => FactorDetail::STATUS_BACK,
                                                        'update_user_id' => Utility::getNowUserId(),
                                                        'status_time'=>new CDbExpression('now()'),
                                                        'update_time' => new CDbExpression('now()')));
    }



    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        $obj = $this->checkObjectModel->with('payApply')->findByPk($checkItem->obj_id);
        $taskParams = array('checkId'=>$checkItem->check_id, 'code'=>$obj->contract_code);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', $taskParams);
    }
}