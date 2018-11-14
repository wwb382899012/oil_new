<?php
/**
 * Created by: yu.li
 * Date: 2018/6/7
 * Time: 11:04
 * Desc: ContractTerminateEventHandlerService
 */

namespace ddd\Split\Domain\Service\Contract;


use ddd\Split\Domain\Model\Contract\ContractTerminate;
use ddd\Split\Domain\Model\Contract\ContractTerminatedEvent;
use ddd\Split\Domain\Model\Contract\ContractTerminateRejectEvent;
use ddd\Split\Domain\Model\Contract\ContractTerminateStatus;
use ddd\Split\Domain\Model\Contract\ContractTerminateSubmittedEvent;

class ContractTerminateEventHandlerService
{

    /**
     * 合同终止提交时,相应的事件处理
     * @param ContractTerminateSubmittedEvent $event
     */
    public function onContractTerminateSubmitted(ContractTerminateSubmittedEvent $event) {
        $entity = $event->sender;
        if ($entity->status == ContractTerminateStatus::STATUS_SUBMIT) {
            //开始合同终止审核流程
            \FlowService::startFlowForCheck25($entity->contract_id);

            //消代办 完成任务（审核驳回待修改任务）
            \TaskService::doneTask($entity->contract_id, \Action::ACTION_CONTRACT_TERMINATE_BACK);
        }
    }

    /**
     * 合同终止审核通过，相应的事件处理
     * @param ContractTerminatedEvent $event
     */
    public function onContractTerminatePassed(ContractTerminatedEvent $event) {
        //消task代办
        $entity = $event->sender;
        if ($entity->status == ContractTerminateStatus::STATUS_PASS) {
            \TaskService::doneTask($entity->contract_id, \Action::ACTION_CONTRACT_TERMINATE_CHECK);
        }
    }


    /**
     * 合同终止审核驳回，相应的事件处理
     * @param ContractTerminateRejectEvent $event
     */
    public function onContractTerminateReject(ContractTerminateRejectEvent $event) {
        $entity = $event->sender;
        if ($entity->status == ContractTerminateStatus::STATUS_BACK) {
            $model = \ContractTerminate::model()->findByPk($entity->getId());
//            $repository = $entity->getContractTerminateRepository();
            //添加代办
            \TaskService::addTasks(\Action::ACTION_CONTRACT_TERMINATE_BACK, $entity->contract_id,
                [
                    'userIds' => $model->create_user_id,
                    'corpId' => $model->contract->corporation_id,
                    'contractCode' => $model->contract->contract_code,
                    'partnerName' => $model->contract->partner->name
                ]
            );
        }
    }

}