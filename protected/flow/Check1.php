<?php
/**
 * Describe：
 *  仓库审核
 */
class Check1 extends Check
{
    // 需要添加表t_flow, t_flow_business, t_flow_node添加数据
    public function init()
    {
        $this->businessId=1;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart()
    {
        $obj = Storehouse::model()->updateByPk($this->objId, 
            array(
                'status' => Storehouse::STATUS_IN_APPROVAL, 
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
        $store=Storehouse::model()->findByPk($this->objId);
        if(empty($store))
        {
            throw new Exception("id为".$this->objId."的仓库不存在");
        }
        $store->setAttributes(array(
                                  'status' => Storehouse::STATUS_PASS,
                                  'update_user_id'=> Utility::getNowUserId(),
                                  'update_time'=> new CDbExpression('now()')
                              ));
        $store->update(array('status', 'update_user_id','update_time' ));
        /*$obj = Storehouse::model()->updateByPk($this->objId,
            array(
                'status' => Storehouse::STATUS_PASS, 
                'update_user_id'=> Utility::getNowUserId(),
                'update_time'=> new CDbExpression('now()')
                )
            );*/
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
        $obj = Storehouse::model()->updateByPk($this->objId, 
            array(
                'status' => Storehouse::STATUS_BACK, 
                'update_user_id'=> Utility::getNowUserId(),
                'update_time'=> new CDbExpression('now()')
                )
            );
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        TaskService::addTasks(Action::ACTION_28,$this->objId,0,$this->checkObjectModel->create_user_id,$corId, array('name'=>$this->checkObjectModel->name));
    }

    /**
     * 其它状态的审核处理
     * @param $checkStatus
     */
    public function checkElse($checkStatus)
    {
        
    }

    // 添加下步发送消息
    public function addNextCheckTask($checkItem)
    {
        $storehouse = Storehouse::model()->findByPk($checkItem->obj_id);
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', array('name'=>$storehouse->name));
    }


}