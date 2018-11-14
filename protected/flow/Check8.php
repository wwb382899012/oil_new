<?php
/**
 * Describe：
 *  入库通知单结算审核
 */
class Check8 extends Check
{
    // 需要添加表t_flow, t_flow_business, t_flow_node添加数据
    public function init()
    {
        $this->businessId=8;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart()
    {
      //todo
    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone()
    {
        $LadingBillSettlementEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\contractSettlement\ILadingBillSettlementRepository::class)->find('t.lading_id='.$this->objId);
        if(!empty($LadingBillSettlementEntity)){
            $StockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
            $StockBatchSettlementService->checkDone($LadingBillSettlementEntity);
        }else{
            throw new Exception("id为".$this->objId."的入库通知单结算信息不存在");
        }
        //task代办
        TaskService::doneTask($this->objId, Action::ACTION_45);
        //结算利润报表
        \ddd\Profit\Application\ProfitEventService::service()->onBatchSettlePass($this->objId);
        //预估利润报表
        \ddd\Profit\Application\Estimate\EstimateProfitEventService::service()->onBatchSettlePass($this->objId);
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
        $LadingBillSettlementEntity = \ddd\repository\contractSettlement\LadingBillSettlementRepository::repository()->find('t.lading_id='.$this->objId);
        
        if(!empty($LadingBillSettlementEntity)){
            $StockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
            $StockBatchSettlementService->checkBack($LadingBillSettlementEntity);
        }else{
            throw new Exception("id为".$this->objId."的入库通知单结算信息不存在");
        }
        //task代办
        $LadingSettlement = LadingSettlement::model()->with('stockBatch')->find('t.lading_id = :batchId', array('batchId' => $this->objId));
        TaskService::addTasks(Action::ACTION_STOCK_BATCH_SETTLE_BACK, $this->objId,
            array(
                "userIds"=>$LadingSettlement->create_user_id,
                "corpId"=>$LadingSettlement->stockBatch->contract->corporation_id,
                "code"=>$LadingSettlement->stockBatch->code,
            )
        );
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
    /*public function addNextCheckTask($checkItem)
    {
        $stockBatchSettlement = StockBatchSettlement::model()->with('stockBatch')->find('t.batch_id = :batchId', array('batchId' => $checkItem->obj_id));
        // $nextNode = CheckNode::model()->findByPk($checkItem->next_node_id);
        TaskService::addTasks(Action::ACTION_45, $checkItem->obj_id,
            array(
                "corpId"=>$stockBatchSettlement->stockBatch->contract->corporation_id,
                "code"=>$stockBatchSettlement->stockBatch->code,
                "roleIds"=>$checkItem->node->role_ids
            )
        );
    }*/
    public function addNextCheckTask($checkItem)
    {
        //task代办
        $LadingSettlement = LadingSettlement::model()->with('stockBatch')->find('t.lading_id = :batchId', array('batchId' => $checkItem->obj_id));
        TaskService::addTasks(Action::ACTION_45, $checkItem->obj_id,
            array(
                "roleIds"=>$checkItem->node->role_ids,
                "corpId"=>$LadingSettlement->stockBatch->contract->corporation_id,
                "code"=>$LadingSettlement->stockBatch->code,
            )
        );
    }

}