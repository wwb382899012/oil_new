<?php

/**
 * Desc: 库存盘点审核
 * User: susiehuang
 * Date: 2017/11/17 0013
 * Time: 16:56
 */
class Check18 extends Check {
    public function init() {
        $this->businessId = FlowService::BUSINESS_STOCK_INVENTORY;
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
        $obj = StockInventory::model()->findByPk($this->objId);
        if (empty($obj)) {
            BusinessException::throw_exception(OilError::$STOCK_INVENTORY_NOT_EXIST, array('inventory_id' => $this->objId));
        }

        //库存盘点审批通过添加库存操作日志信息
        StockInventoryService::addStockLogAfterCheckPass($this->objId);

        $obj->setAttributes(array('status' => StockInventory::STATUS_PASS, 'status_time' => new CDbExpression('now()'), 'update_user_id' => Utility::getNowUserId(), 'update_time' => new CDbExpression('now()')));
        $obj->update(array('status', 'status_time', 'update_user_id', 'update_time'));
    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     * 当审核状态不为1或-1时都进入该项，可以在这里添加其他审核状态的处理
     */
    public function checkReject() {

    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack() {
        $obj = StockInventory::model()->findByPk($this->objId);
        if (empty($obj)) {
            BusinessException::throw_exception(OilError::$STOCK_INVENTORY_NOT_EXIST, array('inventory_id' => $this->objId));
        }

        $obj->setAttributes(array('status' => StockInventory::STATUS_BACK, 'status_time' => new CDbExpression('now()'), 'update_user_id' => Utility::getNowUserId(), 'update_time' => new CDbExpression('now()')));
        $obj->update(array('status', 'status_time', 'update_user_id', 'update_time'));

        $stockInventoryDetail = StockInventoryDetail::model()->findAllToArray('inventory_id = :inventoryId', array('inventoryId' => $this->objId));
        if (Utility::isNotEmpty($stockInventoryDetail)) {
            //根据损耗分摊修改库存
            StockInventoryDetailService::updateGoodsStock($stockInventoryDetail, 1);
        }

        TaskService::addTasks(Action::ACTION_STOCK_INVENTORY_BACK, $this->objId,
                              array(
                                  "userIds"=>$obj->create_user_id,
                                  "corpId"=>$obj->corporation_id,
                                  "code"=>$obj->inventory_id,
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
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', array('code'=>$this->checkObjectModel->inventory_id));
    }
}