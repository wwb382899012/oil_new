<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/5/31
 * Time: 21:37
 */

namespace ddd\Split\Domain\Service\StockSplit;

use ddd\Common\Domain\BaseService;
use ddd\Split\Domain\Model\Contract\IContractRepository;
use ddd\Split\Domain\Model\SplitEnum;
use ddd\Split\Domain\Model\StockSplit\StockSplitApply;
use ddd\Split\Domain\Model\StockSplit\StockSplitCheckBackEvent;
use ddd\Split\Domain\Model\StockSplit\StockSplitCheckPassEvent;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;
use ddd\Split\Domain\Model\StockSplit\StockSplitSubmitEvent;
use ddd\infrastructure\Utility;
use ddd\infrastructure\DIService;
use ddd\Split\Domain\Service\SplitService;
use StockInService;
use StockOutService;

/**
 * 出入库拆分领域事件服务
 * Class StockSplitEventHandlerService
 * @package ddd\Split\Domain\Service\StockSplitApply
 */
class StockSplitEventHandlerService extends BaseService{

    /**
     * @param StockSplitSubmitEvent $event
     * @throws \Exception
     */
    public function onSubmitted(StockSplitSubmitEvent $event){
        $entity = $event->sender;

        if ($entity->status != StockSplitEnum::STATUS_SUBMIT){
            return;
        }

        $service = new SplitService();
        $service->setOriginalStockBillIsSplitting($entity->type,$entity->bill_id);

        //开始审批流
        \FlowService::startFlow(\FlowService::BUSINESS_STOCK_SPLIT_CHECK,$entity->apply_id);
        //完成任务（审核驳回待修改任务）
        $action_id = $entity->isStockInSplit() ? \Action::ACTION_STOCK_IN_SPLIT_CHECK_BACK : \Action::ACTION_STOCK_OUT_SPLIT_CHECK_BACK;
        \TaskService::doneTask($entity->apply_id, $action_id);
    }

    /**
     * @param StockSplitCheckBackEvent $event
     * @throws \Exception
     */
    public function onCheckBacked(StockSplitCheckBackEvent $event){
        $entity = $event->sender;

        if ($entity->status != StockSplitEnum::STATUS_BACK){
            return;
        }

        $service = new SplitService();
        $service->cancelOriginalStockBillIsSplitting($entity->type,$entity->bill_id);

        //添加代办,出、入库平移审核驳回待修改
        $action_id = $entity->isStockInSplit() ? \Action::ACTION_STOCK_IN_SPLIT_CHECK_BACK : \Action::ACTION_STOCK_OUT_SPLIT_CHECK_BACK;
        \TaskService::addTasks($action_id, $entity->apply_id,
            \ActionService::getActionRoleIds($action_id), 0, 0, [
                'contract_id'=> $entity->contract_id,
                'contract_type'=> $entity->type,
                "billCode" => $entity->bill_code,
                "billTypeName" => ($entity->isStockInSplit() ? "入" : "出")
            ]);

        //完成任务
        $action_id = $entity->isStockInSplit() ? \Action::ACTION_STOCK_IN_SPLIT_CHECK : \Action::ACTION_STOCK_OUT_SPLIT_CHECK;
        \TaskService::doneTask($entity->apply_id, $action_id);
    }

    /**
     * @param StockSplitCheckPassEvent $event
     * @throws \Exception
     */
    public function onCheckPassed(StockSplitCheckPassEvent $event){
        $entity = $event->sender;

        if ($entity->status != StockSplitEnum::STATUS_PASS){
            return;
        }

        $service = new SplitService();
        $service->setOriginalStockBillHasBeenSplit($entity->type,$entity->bill_id);

        $service->handleStockSplitApplyAfterCheckPassed($entity);

        //完成任务
        $action_id = $entity->isStockInSplit() ? \Action::ACTION_STOCK_IN_SPLIT_CHECK : \Action::ACTION_STOCK_OUT_SPLIT_CHECK;
        \TaskService::doneTask($entity->apply_id, $action_id);
    }

}