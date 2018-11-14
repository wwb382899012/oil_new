<?php
/**
 * Describe：
 *  风控审核
 */
class Check2 extends Check
{
    // 需要添加表t_flow, t_flow_business, t_flow_node添加数据
    public function init()
    {
        $this->businessId=2;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart()
    {
        RiskManagementService::riskManagementStart($this->objId);
    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone()
    {  
        RiskManagementService::doneRiskManagement($this->objId);
        $contract = Contract::model()->with('project')->findByPk($this->objId);
        $contractType = Map::$v['contract_category'][$contract->category];
        $taskParams = array('projectCode'=>$contract->project->project_code, 'contractType' => $contract->getContractType(), 'isMain'=>'');
        TaskService::addTasks(Action::ACTION_12, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_12), 0, $contract->corporation_id, $taskParams);
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
     * @throws Exception
     */
    public function checkBack()
    {
        $contract =\ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\contract\IContractRepository::class)->findByPk($this->objId);
        if (empty($contract->contract_id))
        {
            throw new \ddd\infrastructure\error\ZEntityNotExistsException($this->objId,\ddd\domain\entity\contract\Contract::class);
        }
        $res = \ddd\application\contract\ContractService::service()->riskRejectContract($this->objId,$contract);
        if ($res !== true) {
            throw new Exception($res);
        }

        //RiskManagementService::rollbackRiskManagement($this->objId);
        //$contract = Contract::model()->with("project")->findByPk($this->objId);
        $project=\ddd\infrastructure\DIService::getRepository(\ddd\Contract\Domain\Model\Project\IProjectRepository::class)->findByPk($contract->project_id);
        $taskParams=array(
            "project_id"=>$contract->project_id,
            "contract_id"=>$contract->contract_id,
            'title'=>$project->project_code." 风控审核驳回"
        );
        TaskService::addTasks(Action::ACTION_10, $contract->project_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $contract->corporation_id, $taskParams);

        //调整合作方额度
        /*$contractEntity = \ddd\repository\contract\ContractRepository::repository()->findByPk($this->objId);
        if (empty($contractEntity->contract_id))
        {
            BusinessException::throw_exception(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $this->objId));
        }
        $contractDTO = new \ddd\application\dto\contract\ContractDTO();
        $contractDTO->fromEntity($contractEntity);
        $contractService = new \ddd\application\contract\ContractService();
        $res = $contractService->riskRejectContract($contractDTO);
        if($res !== true) {
            throw new Exception($res);
        }*/

        /*if($contract->is_main==1)
            TaskService::addTasks(Action::ACTION_10, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $contract->corporation_id, array("project_id"=>$contract->project_id,"contract_id"=>$contract->contract_id));
        else
            TaskService::addTasks(Action::ACTION_10, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $contract->corporation_id);*/
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