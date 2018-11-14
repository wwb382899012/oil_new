<?php
/**
 * Desc: 合同平移申请事件处理
 * User: susiehuang
 * Date: 2018/6/5 0005
 * Time: 17:20
 */

namespace ddd\Split\Domain\Service\ContractSplit;

use ddd\domain\iRepository\contract\IContractRepository;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyPassedEvent;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyRejectedEvent;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplySubmittedEvent;
use ddd\infrastructure\DIService;
use ddd\Split\Domain\Service\SplitService;

class ContractSplitApplyEventHandlerService{
    /**
     * @desc 当合同平移申请单提交时，处理相关的事件
     * @param ContractSplitApplySubmittedEvent $event
     * @throws \Exception
     */
    public function onContractSplitApplySubmitted(ContractSplitApplySubmittedEvent $event){
        $entity = $event->sender;

        if ($entity->status != ContractSplitApplyEnum::STATUS_SUBMIT){
            return;
        }

        foreach($entity->getEffectiveStockSplitBillIds() as $bill_id){
            $service = new SplitService();
            $service->setOriginalStockBillIsSplitting($entity->type,$bill_id);
        }

        //发起合同平移申请审核流程
        \FlowService::startFlowForCheck23($entity->apply_id);
        //完成任务（审核驳回待修改任务）
        \TaskService::doneTask($entity->apply_id, \Action::ACTION_CONTRACT_SPLIT_BACK);
    }

    /**
     * @desc 当合同平移申请单审核驳回时，处理相关的事件
     * @param ContractSplitApplyRejectedEvent $event
     * @throws \Exception
     */
    public function onContractSplitApplyRejected(ContractSplitApplyRejectedEvent $event){
        $entity = $event->sender;

        if ($entity->status != ContractSplitApplyEnum::STATUS_BACK){
            return;
        }

        foreach($entity->getEffectiveStockSplitBillIds() as $bill_id){
            $service = new SplitService();
            $service->cancelOriginalStockBillIsSplitting($entity->type,$bill_id);
        }

        //添加代办
        $origin_contract_model = \Contract::model()->findByPk($entity->contract_id);
        $contract_type_name = \Map::getStatusName('goods_contract_type',$entity->type);
        \TaskService::addTasks(\Action::ACTION_CONTRACT_SPLIT_BACK, $entity->apply_id,
            \ActionService::getActionRoleIds(\Action::ACTION_CONTRACT_SPLIT_BACK), 0, 0, [
                "contract_id" => $entity->contract_id,
                'contract_type' => $entity->type,
                'apply_id'=> $entity->apply_id,
                'contractType' => $contract_type_name,
                'contractCode'=> $entity->contract_code,
                'projectCode'=> $origin_contract_model->project->project_code,
                'partnerName' => $origin_contract_model->partner->name,
            ]);

        //完成任务
        \TaskService::doneTask($entity->apply_id, \Action::ACTION_CONTRACT_SPLIT_CHECK);
    }

    /**
     * @desc 当采购合同平移申请单审核通过时，处理相关的事件
     * @param ContractSplitApplyPassedEvent $event
     * @throws \Exception
     */
    public function onContractSplitApplyPassed(ContractSplitApplyPassedEvent $event){
        $entity = $event->sender;

        if ($entity->status != ContractSplitApplyEnum::STATUS_PASS){
            return;
        }

        $contractEntity = DIService::getRepository(IContractRepository::class)->findByPk($entity->contract_id);
        //生成合同

        SplitService::service()->handleContractSplitApplyAfterCheckPassed($contractEntity, $entity);

        //已结算/结算中，则结算作废
        if(in_array($contractEntity->status, array(\Contract::STATUS_SETTLED_SUBMIT, \Contract::STATUS_SETTLED))) {

        }

        //完成任务
        \TaskService::doneTask($entity->apply_id, \Action::ACTION_CONTRACT_SPLIT_CHECK);
    }
}