<?php
/**
 * Describe：销项票发票申请审核
 */
class Check17 extends Check
{
    public function init()
    {
        $this->businessId=17;
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
        $obj = InvoiceApplication::model()->updateByPk($this->objId, 
        array(
            'status' => InvoiceApplication::STATUS_PASS, 
            'update_user_id'=> Utility::getNowUserId(),
            'update_time'=> new CDbExpression('now()')
            )
        );
        // todo 更新还款计划表已开票金额

        $payPlan = InvoicePayPlan::model()->findAllToArray("apply_id=".$this->objId);
        if(Utility::isNotEmpty($payPlan)){
            foreach ($payPlan as $key => $value) {
                $pay = PaymentPlan::model()->updateByPk($value['plan_id'],
                array(
                    'amount_invoice' => new CDbExpression("amount_invoice+".$value['amount']),
                    'update_user_id'=> Utility::getNowUserId(),
                    'update_time'=> new CDbExpression('now()')
                    )    
                );
            }
        }
        // CrossOrderService::updateCrossContractDetail($this->objId);
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
        $obj=InvoiceApplication::model()->findByPk($this->objId);
        if(empty($obj))
            throw new Exception("id为".$this->objId."的发票申请不存在");

        $obj->setAttributes(array(
                                  'status' =>InvoiceApplication::STATUS_BACK,
                                  'status_time'=> new CDbExpression('now()'),
                                  'update_user_id'=> Utility::getNowUserId(),
                                  'update_time'=> new CDbExpression('now()')
                              ));
        $obj->update(array('status','status_time', 'update_user_id','update_time' ));

        TaskService::addTasks(Action::ACTION_37,$this->objId,
                              array(
                                  "userIds"=>$obj->create_user_id,
                                  "corpId"=>$obj->corporation_id,
                                  "code"=>$obj->apply_code,
                              )
            );
    }

    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', array('code'=>$this->checkObjectModel->apply_code));
    }
}