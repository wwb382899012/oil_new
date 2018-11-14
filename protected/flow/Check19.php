<?php
/**
 * Describe：付款止付申请审核
 */
class Check19 extends Check
{
    public function init()
    {
        $this->businessId=19;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart()
    {
        // Contract::updateContractStatus($this->objId,Contract::STATUS_CONTRACT_CHECKING);
    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone()
    {
        /*$obj = PayApplication::model()->updateByPk($this->objId, 
        array(
            'status' => PayApplication::STATUS_STOP, 
            'update_user_id'=> Utility::getNowUserId(),
            'update_time'=> new CDbExpression('now()')
            )
        );*/
        $apply = PayApplication::model()->with('details')->findByPk($this->objId);
        $apply->status = PayApplication::STATUS_STOP;
        $apply->update_user_id = Utility::getNowUserId();
        $apply->update_time = new CDbExpression('now()');
        $apply->save();

        if($apply->type==PayApplication::TYPE_CONTRACT || $apply->type==PayApplication::TYPE_SELL_CONTRACT){
              if(!empty($apply->contract_id) && is_array($apply->details) && count($apply->details)>0){
                foreach ($apply->details as $detail) {
                  $res = PaymentPlanService::updatePaidAmount($detail->plan_id, -($detail->amount - $detail->amount_paid));
                  if(!$res)
                    throw new Exception("更新合同付款计划失败");
                }
              }
            }
        
        $extra = PayApplicationExtra::model()->find("apply_id=".$this->objId);
        if(!empty($extra->id)){
          $extra->status = PayApplicationExtra::STATUS_PASS;
          $extra->update_user_id = Utility::getNowUserId();
          $extra->update_time = new CDbExpression('now()');
          $extra->save();
        }
    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkReject()
    {
    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack()
    {
        $apply = PayApplication::model()->with('extra')->findByPk($this->objId);
        if(empty($apply) || empty($apply->extra->id))
            throw new Exception("id为".$this->objId."的止付信息不存在");

        $extra = $apply->extra;
        $extra->status = PayApplicationExtra::STATUS_BACK;
        $extra->update_user_id = Utility::getNowUserId();
        $extra->update_time = new CDbExpression('now()');
        $extra->save();


        TaskService::addTasks(Action::ACTION_STOP_BACK,$this->objId,
                              array(
                                  "userIds"=>$extra->create_user_id,
                                  "corpId"=>$apply->corporation_id,
                                  "stopCode"=>$extra->stop_code,
                                  "applyId"=>$apply->apply_id
                              )
            );

        TaskService::addTasks(Action::ACTION_ACTUAL_PAY, $this->objId, ActionService::getActionRoleIds(Action::ACTION_ACTUAL_PAY), 0, $apply->corporation_id, array('code' => $this->objId));
    }

    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        $corId = $this->getCheckObjectCorpId($checkItem->obj_id);
        $extra = PayApplicationExtra::model()->find("apply_id=".$checkItem->obj_id);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', array('applyId'=>$checkItem->obj_id,'stopCode'=>$extra->stop_code,'checkId'=>$checkItem->check_id));
    }
}