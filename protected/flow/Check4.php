<?php
/**
 * Describe：合同审核
 */
class Check4 extends Check
{
    public function init()
    {
        $this->businessId=4;
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
        $obj = ContractFile::model()->updateByPk($this->objId,
        array(
            'status' => ContractFile::STATUS_CHECK_PASS,
            'update_user_id'=> Utility::getNowUserId(),
            'update_time'=> new CDbExpression('now()')
            )
        );

        ContractFileService::insertSignFileByFileId($this->objId);
        ContractFileService::updateContractStatusByFileId($this->objId); //更新合同状态

        $file = ContractFile::model()->findByPk($this->objId);

        $taskEfileExist = TaskService::checkTaskExist(Action::ACTION_16, $file->project_id, ActionService::getActionRoleIds(Action::ACTION_16));
        if(!$taskEfileExist) {
            $contractFile = ContractFile::model()->findByPk($this->objId);
            $contract = Contract::model()->with('project')->findByPk($contractFile->contract_id);
            $taskParams = array('projectCode'=>$contract->project->project_code, 'contractCode'=>$contract->contract_code, 'contractType'=>$contract->getContractType());
            TaskService::addTasks(Action::ACTION_16, $file->project_id, ActionService::getActionRoleIds(Action::ACTION_16), 0, $file->contract->corporation_id, $taskParams);
        }

        $user       = SystemUser::model()->findbyPk($file->project->create_user_id);
        $attachArr[]  = ContractService::getContractAttachment($file);
        AMQPService::publishEmail($file->project->create_user_id,'合同审核通过',$user->name.'，您好！<br/>&emsp;&emsp;这是经过法务审核通过后的合同附件，请查收！',$attachArr);
    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkReject()
    {
        $obj = ContractFile::model()->updateByPk($this->objId,
        array(
            'status' => ContractFile::STATUS_BACK,
            'update_user_id'=> Utility::getNowUserId(),
            'update_time'=> new CDbExpression('now()')
            )
        );
        ContractFileService::updateContractStatusByFileId($this->objId); //更新合同状态
    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack()
    {
        $obj = ContractFile::model()->updateByPk($this->objId,
        array(
            'status' => ContractFile::STATUS_BACK,
            'update_user_id'=> Utility::getNowUserId(),
            'update_time'=> new CDbExpression('now()')
            )
        );
        ContractFileService::updateContractStatusByFileId($this->objId); //更新合同状态
        $file = ContractFile::model()->with('contract',"project")->findByPk($this->objId);
        $contract = Contract::model()->findByPk($file->contract_id);
        TaskService::addTasks(Action::ACTION_31, $this->objId,
                              array(
                                  "userIds"=>$file->create_user_id,
                                  "corpId"=>$file->contract->corporation_id,
                                  "projectCode"=>$file->project->project_code,
                                  "contractCode"=>$file->contract->contract_code,
                                  'contractType' =>$contract->getContractType(),
                                  'projectId' => $file->project_id
                              )
                           );
    }


    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        $contractFile = ContractFile::model()->findByPk($checkItem->obj_id);
        $contract = Contract::model()->with('project')->findByPk($contractFile->contract_id);
        $taskParams = array('projectCode'=>$contract->project->project_code, 'contractCode'=>$contract->contract_code, 'contractType'=>$contract->getContractType(), 'projectId'=>$contract->project->project_id);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$contract->corporation_id,"",$taskParams);
    }

    /**
     * 多审核对象时更新任务状态，不同审核对象重写该方法
     * @param $checkDetail
     * @param int $roleId
     * @param int $userId
     */
    public function updateTask($checkDetail,$roleId=0,$userId=0)
    {
//        $actionId=$this->businessConfig["action_id"];
//        $contractFile = ContractFile::model()->findByPk($this->objId);
        TaskService::doneTask($this->objId,$this->businessConfig["action_id"],$roleId,$userId);
    }

}