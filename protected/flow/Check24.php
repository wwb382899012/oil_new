<?php

use ddd\infrastructure\DIService;
use ddd\Split\Application\StockSplitService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\Split\Domain\Model\StockSplit\IStockSplitApplyRepository;
use ddd\Split\Domain\Model\StockSplit\StockSplitApply;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;

/**
 * 出入库平移审批
 * Class Check24
 */
class Check24 extends Check{

    public function init(){
        $this->businessId = FlowService::BUSINESS_STOCK_SPLIT_CHECK;
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
        $entity = DIService::getRepository(IStockSplitApplyRepository::class)->findByPk($this->objId);
        if(empty($entity)){
            throw new ZEntityNotExistsException($this->objId, StockSplitApply::class);
        }

        $service = new StockSplitService();
        $res = $service->checkPass($entity);

        if ($res !== true) {
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
        $entity = DIService::getRepository(IStockSplitApplyRepository::class)->findByPk($this->objId);
        if(empty($entity)){
            throw new ZEntityNotExistsException($this->objId, StockSplitApply::class);
        }

        $service = new StockSplitService();
        $res = $service->checkBack($entity);

        if ($res !== true) {
            throw new Exception($res);
        }
    }

    /**
     * 增加下次审核任务
     * @param $checkItem
     * @throws CDbException
     * @throws CException
     */
    public function addNextCheckTask($checkItem){
        $model = \StockSplitApply::model()->findByPk($checkItem->obj_id);
        $corId = $model->contract->corporation_id;
        $taskParams = [
            'check_id'=> $checkItem->check_id,
            'contract_id'=> $model->contract_id,
            'contract_type'=> $model->type
        ];
        //
        if(StockSplitEnum::TYPE_STOCK_IN == $model->type){
            $action_id = \Action::ACTION_STOCK_IN_SPLIT_CHECK;
            $taskParams['billCode'] = $model->stockIn->code;
            $taskParams['billTypeName'] = "入";
        }else{
            $action_id = \Action::ACTION_STOCK_OUT_SPLIT_CHECK;
            $taskParams['billCode'] = $model->stockOut->code;
            $taskParams['billTypeName'] = "出";
        }

        TaskService::addCheckTasks($checkItem->obj_id, $checkItem->check_id, $action_id, $corId, '', $taskParams);
    }

}
