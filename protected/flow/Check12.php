<?php
/**
 * Describe：
 *  入库通知单审核
 */
class Check12 extends Check
{
    // 需要添加表t_flow, t_flow_business, t_flow_node添加数据
    public function init()
    {
        $this->businessId=12;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart()
    {
        $obj = CrossOrder::model()->updateByPk($this->objId, 
            array(
                'status' => CrossOrder::STATUS_CHECKING, 
                'update_user_id'=> Utility::getNowUserId(),
                'update_time'=> new CDbExpression('now()')
                )
            );
    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone()
    {
        $cross=CrossOrder::model()->findByPk($this->objId);
        if(empty($cross))
        {
            throw new Exception("id为".$this->objId."的调货单不存在,请重试");
        }
        $cross->setAttributes(array(
                'status' => CrossOrder::STATUS_PASS,
                'status_time' => new CDbExpression('now()'),
                'update_user_id'=> Utility::getNowUserId(),
                'update_time'=> new CDbExpression('now()')
            ));
        $cross->update(array('status','status_time', 'update_user_id','update_time' ));
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
       /* $obj = CrossOrder::model()->updateByPk($this->objId,
            array(
                'status' => CrossOrder::STATUS_BACK, 
                'update_user_id'=> Utility::getNowUserId(),
                'update_time'=> new CDbExpression('now()')
                )
            );

        $crossDetail = CrossDetail::model()->findAllToArray("cross_id=".$this->objId);
        if(Utility::isNotEmpty($crossDetail)){
            foreach ($crossDetail as $key => $value) {
                StockService::unFreeze($value['stock_id'], $value['quantity']);
            }
        }*/

        $obj=CrossOrder::model()->with("borrowContractDetail","crossDetail")->findByPk($this->objId);
        $obj->saveAttributes(array(
                                 'status' => CrossOrder::STATUS_BACK,
                                 'status_time' => new CDbExpression('now()'),
                                 'update_user_id'=> Utility::getNowUserId(),
                                 'update_time'=> new CDbExpression('now()')
                             ));
        if(is_array($obj->crossDetail))
        {
            foreach ($obj->crossDetail as $key => $value) {
                StockService::unFreeze($value['stock_id'], $value['quantity']);
            }
        }

        
        TaskService::addTasks(Action::ACTION_CROSS_RETURN_CHECK_BACK,$obj->borrowContractDetail->cross_id,
                              array(
                                  "userIds"=>$obj->create_user_id,
                                  "code"=>$obj->cross_code,
                                  //"title"=>ActionService::getActionName(Action::ACTION_CROSS_RETURN_CHECK_BACK)." ".$obj->cross_code
                                  'contractId'=>$obj->contract_id
                              )
        );

    }

    /**
     * 其它状态的审核处理
     * @param $checkStatus
     */
    public function checkElse($checkStatus)
    {
        
    }



    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        // debug(get_class($this));
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', array('checkId'=>$checkItem->check_id,'code'=>$this->checkObjectModel->cross_code));
    }
}