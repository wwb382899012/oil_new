<?php

/**
 * Desc: 发货单审核
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class Check9 extends Check {
    public function init() {
        $this->businessId = 9;
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
        DeliveryOrder::model()->updateByPk($this->objId, array('status' => DeliveryOrder::STATUS_PASS, 'update_user_id' => Utility::getNowUserId(), 'update_time' => new CDbExpression('now()')));
        $deliveryOrder = DeliveryOrder::model()->with('details')->findByPk($this->objId);
        //直调发货单审批通过生成配货明细出库单
        if ($deliveryOrder->type == ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER) {
            if(is_array($deliveryOrder->details) && !empty($deliveryOrder->details)){
                foreach ($deliveryOrder->details as $detail) {
                    DeliveryOrderDetail::model()->updateByPk($detail->detail_id, array("quantity_actual"=>$detail->quantity));
                }
            }

            //生成审核通过的直调出库单
            $outOrderId = StockOutService::addDirectTransferDeliveryDetail($deliveryOrder->order_id);

            //调整合作方额度
            $stockOutEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\stock\IStockOutRepository::class)->findByPk($outOrderId);
            if(empty($stockOutEntity->out_order_id)) {
                throw new \ddd\infrastructure\error\ZEntityNotExistsException($outOrderId, \ddd\domain\entity\stock\StockOut::class);
            }

            $res = \ddd\application\stock\StockOutService::service()->passStockOut($outOrderId, $stockOutEntity);
            if ($res !== true) {
                throw new Exception($res);
            }
        }
    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkReject() {
        DeliveryOrder::model()->updateByPk($this->objId, array('status' => DeliveryOrder::STATUS_BACK, 'update_user_id' => Utility::getNowUserId(), 'update_time' => new CDbExpression('now()')));
    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack() {
        DeliveryOrder::model()->updateByPk($this->objId, array('status' => DeliveryOrder::STATUS_BACK, 'update_user_id' => Utility::getNowUserId(), 'update_time' => new CDbExpression('now()')));

        //解冻库存
        StockDeliveryDetailService::unfreezeStockByOrderId($this->objId);

        $deliveryOrderModel = DeliveryOrder::model()->findByPk($this->objId);

        //配货状态调整
        StockDeliveryDetail::model()->updateAll(array('status' => StockDeliveryDetail::STATUS_NOT_USE), 'order_id=:orderId', array('orderId' => $this->objId));
        TaskService::addTasks(Action::ACTION_33, $this->objId,
                              array(
                                  "userIds"=>$deliveryOrderModel->create_user_id,
                                  "corpId"=>$deliveryOrderModel->corporation_id,
                                  "code"=>$deliveryOrderModel->code,
                              )
            );
    }

    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        $order = DeliveryOrder::model()->findByPk($checkItem->obj_id);
        $taskParams = array('code'=>$order->code);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId,'',$taskParams);
    }
}