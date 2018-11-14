<?php
/**
 * Describe：
 *  发货单结算审核
 * 
 */
class Check10 extends Check
{
    public function init()
    {
        $this->businessId=10;
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
        $DeliverOrderSettlementEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\contractSettlement\IDeliveryOrderSettlementRepository::class)->find('t.order_id='.$this->objId);
        if(!empty($DeliverOrderSettlementEntity)){
            $DeliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
            $DeliveryOrderSettlementService->checkDone($DeliverOrderSettlementEntity);
        }else{
            throw new Exception("id为".$this->objId."的发货单结算信息不存在");
        }
        //task代办
        TaskService::doneTask($this->objId, Action::ACTION_47);
        //结算利润报表
        \ddd\Profit\Application\ProfitEventService::service()->onDeliverySettlePass($this->objId);
        //预估利润报表
        \ddd\Profit\Application\Estimate\EstimateProfitEventService::service()->onDeliverySettlePass($this->objId);
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
        $DeliverOrderSettlementEntity = \ddd\repository\contractSettlement\DeliveryOrderSettlementRepository::repository()->find('t.order_id='.$this->objId);
        if(!empty($DeliverOrderSettlementEntity)){
            $DeliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
            $DeliveryOrderSettlementService->checkBack($DeliverOrderSettlementEntity);
        }else{
            throw new Exception("id为".$this->objId."的发货单结算信息不存在");
        }
        //task代办
        $DeliverySettlement = DeliverySettlement::model()->with('deliveryOrder')->find('t.order_id = :order_id', array('order_id' => $this->objId));
        TaskService::addTasks(Action::ACTION_48,$this->objId,0,$DeliverySettlement->create_user_id,$DeliverySettlement->deliveryOrder->contract->corporation_id, array('code'=>$DeliverySettlement->deliveryOrder->code));

    }


    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        //task代办
        $DeliverySettlement = DeliverySettlement::model()->with('deliveryOrder')->find('t.order_id = :order_id', array('order_id' => $checkItem->obj_id));
        TaskService::addTasks(Action::ACTION_47, $checkItem->obj_id,
            array(
                "corpId"=>$DeliverySettlement->deliveryOrder->contract->corporation_id,
                "code"=>$DeliverySettlement->deliveryOrder->code,
                "roleIds"=>$checkItem->node->role_ids,
            )
        );
    }

}