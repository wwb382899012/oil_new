<?php
/**
 * Desc: 合同平移审批
 * User: susiehuang
 * Date: 2018/6/11 0011
 * Time: 10:33
 */

use ddd\infrastructure\DIService;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitApplyRepository;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApply;
use ddd\Split\Application\ContractSplitService;
use ddd\infrastructure\error\ZEntityNotExistsException;

class Check23 extends Check{

    public function init(){
        $this->businessId = FlowService::BUSINESS_CONTRACT_SPLIT_CHECK;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart(){

    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone(){
        $entity = DIService::getRepository(IContractSplitApplyRepository::class)->findByPk($this->objId);
        if(empty($entity)) {
            throw new ZEntityNotExistsException($this->objId, ContractSplitApply::class);
        }

       $res = ContractSplitService::service()->checkPass($entity);
        if(true !== $res){
            throw new Exception($res);
        }
    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkReject(){

    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack(){
        $entity = DIService::getRepository(IContractSplitApplyRepository::class)->findByPk($this->objId);
        if(empty($entity)) {
            throw new ZEntityNotExistsException($this->objId, ContractSplitApply::class);
        }

        $res = ContractSplitService::service()->checkBack($entity);
        if(true !== $res){
            throw new Exception($res);
        }
    }

    /**
     * 增加下次审核任务
     * @param $checkItem
     * @throws CException
     */
    public function addNextCheckTask($checkItem){
        $contractSplitApply = \ContractSplitApply::model()->findByPk($checkItem->obj_id);
        $corId = $contractSplitApply->contract->corporation_id;

        $origin_contract_model = \Contract::model()->findByPk($contractSplitApply->contract_id);
        $contract_type_name = \Map::getStatusName('goods_contract_type',$origin_contract_model->type);

        TaskService::addCheckTasks($checkItem->obj_id, $checkItem->check_id, $this->businessConfig["action_id"], $corId, '', [
            "contract_id" => $contractSplitApply->contract_id,
            'contract_type' => $origin_contract_model->type,
            'check_id'=> $checkItem->check_id,
            'contractType' => $contract_type_name,
            'contractCode'=> $origin_contract_model->contract_code,
            'projectCode'=> $origin_contract_model->project->project_code,
            'partnerName' => $origin_contract_model->partner->name,
        ]);
    }

}
