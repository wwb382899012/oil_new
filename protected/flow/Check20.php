<?php

/**
 * Desc: 出库单审核
 * User: phpdragon
 * Date: 2018/03/13 17:59
 * Time: 17:59
 */
class Check20 extends Check {

    public function init() {
        $this->businessId = FlowService::BUSINESS_STOCK_OUT_CHECK;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart() {

    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone() {
        //更新状态位已审核
        $updataResult = StockOutOrder::model()->updateByPk($this->objId, array(
            'status' => StockOutOrder::STATUS_SUBMITED,
            'update_user_id' => Utility::getNowUserId(),
            'update_time' => new CDbExpression('now()')
        ),'status=:status',array(':status'=> StockOutOrder::STATUS_SUBMIT));

        if(!$updataResult){
            throw new Exception("当前出库单审核失败");
        }

        $stockOutOrder = StockOutOrder::model()->with('details')->findByPk($this->objId,'`t`.status=:status',array(':status'=> StockOutOrder::STATUS_SUBMITED));
        if(empty($stockOutOrder) || empty($stockOutOrder['details'])){
            throw new Exception("当前出库单信息异常，审核失败");
        }



        //解除冻结库存
        $items = $stockOutOrder['details'];
        foreach($items as $detail){
            StockOutService::outStore($detail->stock_id,
                $detail->quantity,
                $detail->cross_detail_id,
                $detail->type,
                $detail->out_id);

            DeliveryOrderService::updateDeliveryOrderActualOutQuantity($detail->detail_id, $detail->quantity);
        }

        //调整合作方额度
        $stockOutEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\stock\IStockOutRepository::class)->findByPk($this->objId);
        if(empty($stockOutEntity->out_order_id)) {
            throw new \ddd\infrastructure\error\ZEntityNotExistsException($this->objId, \ddd\domain\entity\stock\StockOut::class);
        }

        $res = \ddd\application\stock\StockOutService::service()->passStockOut($this->objId, $stockOutEntity);
        if ($res !== true) {
            throw new Exception($res);
        }

    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkReject() {
        $stockOut = $this->unFreezeStockPatch();

        StockOutOrder::model()->updateByPk($this->objId, array(
            'status' => StockOutOrder::STATUS_BACK,
            'update_user_id' => Utility::getNowUserId(),
            'update_time' => new CDbExpression('now()')
        ),'status=:status',array(':status'=> StockOutOrder::STATUS_SUBMIT));
        //TODO: 应该也要通知被拒绝方
    }

    /**
     * 解冻出库单超出的库存
     * @return static
     */
    private function unFreezeStockPatch(){
        $stockOut = StockOutOrder::model()->with('details')->findByPk($this->objId,'t.status=:status',
            array(':status'=> StockOutOrder::STATUS_SUBMIT));

        //解冻库存补丁
        foreach($stockOut->details as $detail){
            StockOutService::unfreezeStockPatch($detail);
        }

        return $stockOut;
    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack() {
        $stockOut = $this->unFreezeStockPatch();

        StockOutOrder::model()->updateByPk($this->objId,
            array(
                'status' => StockOutOrder::STATUS_BACK,
                'status_time' => new CDbExpression('now()'),
                'update_user_id' => Utility::getNowUserId(),
                'update_time' => new CDbExpression('now()')
            ),'status=:status',array(':status'=> StockOutOrder::STATUS_SUBMIT));

        TaskService::addTasks(Action::ACTION_STOCK_OUT_CHECK_BACK,$this->objId,array(
            "userIds"=>$stockOut->create_user_id,
            "corpId"=>$stockOut->corporation_id,
            "code"=>$stockOut->code
        ));
    }

    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem){
        $stockOut = StockOutOrder::model()->findByPk($checkItem->obj_id);
        $taskParams = array("code"=>$stockOut->code);
        $corId = $stockOut->corporation_id;
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', $taskParams);
    }

}
