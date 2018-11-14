<?php

use ddd\domain\enum\MainEnum;

use ddd\Split\Domain\Service\SplitService;

/**
 * Describe：
 *  业务审核
 */
class Check3 extends Check
{
    // 需要添加表t_flow, t_flow_business, t_flow_node添加数据
    public function init()
    {
        $this->businessId=3;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart()
    {
        TransectionAuditService::beginTransectionAudit($this->objId);
    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone()
    {
        TransectionAuditService::doneTransectionAduit($this->objId);

        $contract = Contract::model()->with('project')->findByPk($this->objId);
        if($contract->is_main)
            $contract->findRelative();

        ContractFileService::insertMainContractFile($contract->contract_id);
        if(!empty($contract->relation_contract_id)) {
            ContractFileService::insertMainContractFile($contract->relation_contract_id);
        }
        if(!TaskService::checkTaskExist(Action::ACTION_14, $contract->project_id, ActionService::getActionRoleIds(Action::ACTION_14))){
            $contractType = Map::$v['contract_category'][$contract->category];
            $taskParams = array('projectCode'=>$contract->project->project_code, 'contractCode' => $contract->contract_code);
            if(!empty($contract->relative))
                $taskParams["contractCode"].="和".$contract->relative->contract_code;
            TaskService::addTasks(Action::ACTION_14, $contract->project_id, ActionService::getActionRoleIds(Action::ACTION_14), 0, $contract->corporation_id, $taskParams);
        }

        if($contract->type==ConstantMap::BUY_TYPE){
            AMQPService::publishBuyContractBusinessCheckPass($contract->contract_id);

        }else{
            if($contract->relation_contract_id>0)
                AMQPService::publishBuyContractBusinessCheckPass($contract->relation_contract_id);
            else
                AMQPService::publishSellContractBusinessCheckPass($contract->contract_id);
        }

        AMQPService::publishContractBlock($contract->contract_id);
        if($contract->relation_contract_id>0)
            AMQPService::publishContractBlock($contract->relation_contract_id);

        /*$task = Task::model()->find("key_value=".$contract->project_id." and status=0");
        if(empty($task->task_id))
            TaskService::addTasks(Action::ACTION_14, $contract->project_id, ActionService::getActionRoleIds(Action::ACTION_14), 0, $contract->corporation_id);
        else
            TaskService::sendTips(Action::ACTION_14, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_14), 0);*/

        //生成虚拟出入库单
        SplitService::service()->handleSplitContractAfterCheckPassed($contract);
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
        $contractEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\contract\IContractRepository::class)->findByPk($this->objId);
        if (empty($contractEntity->contract_id))
        {
            throw new \ddd\infrastructure\error\ZEntityNotExistsException($this->objId, \ddd\domain\entity\contract\Contract::class);
        }

        $res = \ddd\application\contract\ContractService::service()->businessRejectContract($this->objId, $contractEntity);

        if ($res !== true) {
            throw new Exception($res);
        }

        //额度作废  临时解决
        $this->cancelQuotas($contractEntity);

        /*TransectionAuditService::rollbackTransectionAduit($this->objId);
        $contract = Contract::model()->with("project")->findByPk($this->objId);*/
        $project = \ddd\infrastructure\DIService::getRepository(\ddd\Contract\Domain\Model\Project\IProjectRepository::class)->findByPk($contractEntity->project_id);

        $taskParams=array(
            "project_id"=>$contractEntity->project_id,
            "contract_id"=>$contractEntity->contract_id,
            'title'=>$project->project_code." 业务审核驳回"
        );
        TaskService::addTasks(Action::ACTION_10, $contractEntity->project_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $contractEntity->corporation_id, $taskParams);

        //调整合作方额度
        /*$contractEntity = \ddd\repository\contract\ContractRepository::repository()->findByPk($this->objId);
        if (empty($contractEntity->contract_id))
        {
            BusinessException::throw_exception(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $this->objId));
        }
        $contractDTO = new \ddd\application\dto\contract\ContractDTO();
        $contractDTO->fromEntity($contractEntity);
        $contractService = new \ddd\application\contract\ContractService();
        $res = $contractService->businessRejectContract($contractDTO);
        if($res !== true) {
            throw new Exception($res);
        }*/
        /*if($contract->is_main==1)
            TaskService::addTasks(Action::ACTION_10, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $contract->corporation_id, array("project_id"=>$contract->project_id,"contract_id"=>$contract->contract_id));
        else
            TaskService::addTasks(Action::ACTION_10, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $contract->corporation_id);*/
        /*$contract = Contract::model()->findByPk($this->objId);
        TaskService::addTasks(Action::ACTION_12, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_12), 0, $contract->corporation_id);*/
    }

    /**
     * 把所有的额度数据废弃
     * @param $contractEntity
     */
    private function cancelQuotas($contractEntity) {
        if (!empty($contractEntity) && $contractEntity->is_main == MainEnum::IS_MAIN) {
            ProjectCreditDetail::model()->updateAll(array(
                'status' => ProjectCreditDetail::STATUS_DELETE,
                'update_time' => new CDbExpression('now()'),
                'update_user_id' => Utility::getNowUserId()
            ), "project_id=:project_id", array(
                'project_id' => $contractEntity->project_id
            ));
        } else if (!empty($contractEntity) && $contractEntity->is_main != MainEnum::IS_MAIN) {
            ProjectCreditDetail::model()->updateAll(array(
                'status' => ProjectCreditDetail::STATUS_DELETE,
                'update_time' => new CDbExpression('now()'),
                'update_user_id' => Utility::getNowUserId()
            ), "contract_id=:contract_id", array(
                'contract_id' => $contractEntity->contract_id
            ));
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
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        $contract = Contract::model()->with('project')->findByPk($checkItem->obj_id);
        $contractType = Map::$v['contract_category'][$contract->category];
        $taskParams = array('projectCode'=>$contract->project->project_code, 'contractType' => $contract->getContractType(), 'isMain'=>'');
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', $taskParams);
    }

}