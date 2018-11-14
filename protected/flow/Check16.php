<?php
/**
 * Describe：开票确认
 */
class Check16 extends Check
{
    public function init()
    {
        $this->businessId=16;
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
        $invoice=Invoice::model()->findByPk($this->objId);
        if(empty($invoice))
            throw new Exception("id为".$this->objId."的销项票开票信息不存在");

        $invoice->setAttributes(array(
                                  'status' =>Invoice::STATUS_PASS, 
                                  'status_time'=> new CDbExpression('now()'),
                                  'update_user_id'=> Utility::getNowUserId(),
                                  'update_time'=> new CDbExpression('now()')
                              ));
        $invoice->update(array('status','status_time', 'update_user_id','update_time' ));

        $apply   = InvoiceApplication::model()->updateByPk($invoice->apply_id,
            array(
            'amount_paid' => new CDbExpression("amount_paid+".$invoice->amount),
            'num'=>new CDbExpression("num+".$invoice->invoice_num),
            'update_user_id'=> Utility::getNowUserId(),
            'update_time'=> new CDbExpression('now()')
            )    
        );
        // CrossOrderService::updateCrossContractDetail($this->objId);
        //增加利润报表的 开票利润
        \ddd\Profit\Application\InvoiceEventService::service()->onInvoiceCheckPass($invoice->contract_id,$this->objId);
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
        $obj=Invoice::model()->with("application")->findByPk($this->objId);
        if(empty($obj))
            throw new Exception("id为".$this->objId."的开票信息不存在");

        $obj->setAttributes(array(
                                  'status' =>Invoice::STATUS_BACK,
                                  'status_time'=> new CDbExpression('now()'),
                                  'update_user_id'=> Utility::getNowUserId(),
                                  'update_time'=> new CDbExpression('now()')
                              ));
        $obj->update(array('status','status_time', 'update_user_id','update_time' ));

        TaskService::addTasks(Action::ACTION_29,$obj->application->apply_id,0,$obj->create_user_id,$obj->application->corporation_id, array('code'=>$obj->application->apply_code));
        
        //ProjectService::updateProjectStatus($this->objId,Project::STATUS_CONTRACT_BACK);
        //TaskService::addTasks(Action::ACTION_5,$this->objId,ActionService::getActionRoleIds(Action::ACTION_5));
    }


    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        $obj=Invoice::model()->with("application")->findByPk($checkItem->obj_id);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,Action::ACTION_26,$obj->application->corporation_id, '', array('code'=>$obj->application->apply_code));
    }
}