<?php

use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\Split\Domain\Model\Contract\IContractTerminateRepository;
use ddd\Split\Domain\Model\Contract\ContractTerminate;

/**
 * User: liyu
 * Date: 2018/6/13
 * Time: 11:32
 * Desc: 合同终止审批
 */
class Check25 extends Check
{

    public function init() {
        $this->businessId = FlowService::BUSINESS_CONTRACT_TERMINATE_CHECK;
    }

    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    function checkStart() {
        // TODO: Implement checkStart() method.
    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    function checkDone() {
        $entity = DIService::getRepository(IContractTerminateRepository::class)->findByContractId($this->objId);
        if (empty($entity)) {
            throw new ZEntityNotExistsException($this->objId, ContractTerminate::class);
        }
        $res = \ddd\Split\Application\ContractTerminateService::service()->checkPass($entity);
        if ($res !== true) {
            throw new Exception($res);
        }
    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     */
    function checkReject() {
        // TODO: Implement checkReject() method.
    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    function checkBack() {
        $entity = DIService::getRepository(IContractTerminateRepository::class)->findByContractId($this->objId);
        if (empty($entity)) {
            throw new ZEntityNotExistsException($this->objId, ContractTerminate::class);
        }
        $res = \ddd\Split\Application\ContractTerminateService::service()->checkBack($entity);
        if ($res !== true) {
            throw new Exception($res);
        }
    }

    public function addNextCheckTask($checkItem) {
        $model = \ContractTerminate::model()->find('t.contract_id='.$checkItem->obj_id);
        $taskParams = ['contractCode' => $model->contract->contract_code, 'partnerName' => $model->contract->partner->name];//TODO
        $corId = $model->contract->corporation_id;
        TaskService::addCheckTasks($checkItem->obj_id, $checkItem->check_id, $this->businessConfig['action_id'], $corId, '', $taskParams);
    }
}