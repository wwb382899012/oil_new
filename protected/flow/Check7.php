<?php

/**
 * Desc: 入库单审核
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class Check7 extends Check {
    public function init() {
        $this->businessId = FlowService::BUSINESS_STOCK_IN_CHECK;
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
        StockIn::model()->updateByPk($this->objId, array('status' => StockIn::STATUS_PASS, 'update_user_id' => Utility::getNowUserId(), 'update_time' => new CDbExpression('now()')));

        //将入库明细更新到合同商品库存中
        StockService::addStockAfterStockInPass($this->objId);

        $stockInModel = StockIn::model()->with('contract')->findByPk($this->objId);

        //更新入库通知单实际入库数量
        StockNoticeService::updateStockNoticeQuantityActual($stockInModel->batch_id);

        //调整合作方额度
        $stockInEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\stock\IStockInRepository::class)->findByPk($this->objId);
        if (empty($stockInEntity->stock_in_id)) {
            throw new \ddd\infrastructure\error\ZEntityNotExistsException($stockInEntity->stock_in_id, \ddd\domain\entity\stock\StockIn::class);
        }

        $res = \ddd\application\stock\StockInService::service()->passStockIn($this->objId, $stockInEntity);
        if ($res !== true) {
            throw new Exception($res);
        }
    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkReject() {
        StockIn::model()->updateByPk($this->objId, array('status' => StockIn::STATUS_BACK, 'update_user_id' => Utility::getNowUserId(), 'update_time' => new CDbExpression('now()')));
    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack() {
        StockIn::model()->updateByPk($this->objId, array('status' => StockIn::STATUS_BACK,
                                                         'status_time' => new CDbExpression('now()'),
                                                         'update_user_id' => Utility::getNowUserId(),
                                                         'update_time' => new CDbExpression('now()')));

        $stockIn = StockIn::model()->findByPk($this->objId);
        $corId=$this->getCheckObjectCorpId($this->objId);
        TaskService::addTasks(Action::ACTION_32, $this->objId,
                              array(
                                  "userIds"=>$stockIn->create_user_id,
                                  "corpId"=>$corId,
                                  "code"=>$stockIn->code,
                              )
            );
    }

    /**
     * 获取交易主体Id
     * @param $objId
     * @return mixed
     */
    public function getCheckObjectCorpId($objId)
    {
        $model = StockIn::model()->with("project")->findByPk($objId);
        return $model->project->corporation_id;
    }


    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {   
        $stockIn = StockIn::model()->findByPk($checkItem->obj_id);
        $taskParams = array("code"=>$stockIn->code);
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', $taskParams);
    }

}