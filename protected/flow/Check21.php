<?php
/**
 * Describe：
 *  采购合同结算审核
 */
class Check21 extends Check
{
    // 需要添加表t_flow, t_flow_business, t_flow_node添加数据
    public function init()
    {
        $this->businessId=21;
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
        $StockContractSettlementEntity = \ddd\repository\contractSettlement\BuyContractSettlementRepository::repository()->find('t.contract_id='.$this->objId);
        if(!empty($StockContractSettlementEntity)){
            $BuyContractSettlementService = new \ddd\application\contractSettlement\BuyContractSettlementService();
            $BuyContractSettlementService->checkDone($StockContractSettlementEntity);
        }else{
            throw new Exception("id为".$this->objId."的采购合同结算信息不存在");
        }
        //task代办
        TaskService::doneTask($this->objId, Action::ACTION_STOCK_CONTRACT_SETTLEMENT_CHECK);

        //结算利润报表
        \ddd\Profit\Application\ProfitEventService::service()->onBuyContractSettlePass($this->objId);

        //预估利润报表
        \ddd\Profit\Application\Estimate\EstimateProfitEventService::service()->onBuyContractSettlePass($this->objId);
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
        $StockContractSettlementEntity = \ddd\repository\contractSettlement\BuyContractSettlementRepository::repository()->find('t.contract_id='.$this->objId);
        if(!empty($StockContractSettlementEntity)){
            $BuyContractSettlementService = new \ddd\application\contractSettlement\BuyContractSettlementService();
            $BuyContractSettlementService->checkBack($StockContractSettlementEntity);
        }else{
            throw new Exception("id为".$this->objId."的采购合同结算信息不存在");
        }
        //task代办
        $ContractSettlement = ContractSettlement::model()->with('contract')->find('t.contract_id = :contract_id', array('contract_id' => $this->objId));
        TaskService::addTasks(Action::ACTION_STOCK_CONTRACT_SETTLEMENT_BACK, $this->objId,
            array(
                "userIds"=>$ContractSettlement->create_user_id,
                "corpId"=>$ContractSettlement->contract->corporation_id,
                "contractCode"=>$ContractSettlement->contract->contract_code,
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
        $ContractSettlement = ContractSettlement::model()->with('contract')->find('t.contract_id = :contract_id', array('contract_id' => $checkItem->obj_id));
        TaskService::addTasks(Action::ACTION_STOCK_CONTRACT_SETTLEMENT_CHECK, $checkItem->obj_id,
            array(
                "roleIds"=>$checkItem->node->role_ids,
                "corpId"=>$ContractSettlement->contract->corporation_id,
                "contractCode"=>$ContractSettlement->contract->contract_code,
            )
        ); 
    }

}