<?php
/**
 * Describe：调货单审核
 */
class Check11 extends Check
{
    public function init()
    {
        $this->businessId=11;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart()
    {
        // Contract::updateContractStatus($this->objId,Contract::STATUS_CONTRACT_CHECKING);
    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone()
    {
        $obj = CrossOrder::model()->updateByPk($this->objId, 
        array(
            'status' => CrossOrder::STATUS_PASS,
            'status_time' => new CDbExpression('now()'),
            'update_user_id'=> Utility::getNowUserId(),
            'update_time'=> new CDbExpression('now()')
            )
        );

        CrossOrderService::updateCrossContractDetail($this->objId);
    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkReject()
    {
    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack()
    {


        /*$obj = CrossOrder::model()->updateByPk($this->objId,
        array(
            'status' => CrossOrder::STATUS_BACK, 
            'update_user_id'=> Utility::getNowUserId(),
            'update_time'=> new CDbExpression('now()')
            )
        );*/
        $obj=CrossOrder::model()->with("crossDetail")->findByPk($this->objId);
        $obj->saveAttributes(array(
                                 'status' => CrossOrder::STATUS_BACK,
                                 'status_time' => new CDbExpression('now()'),
                                 'update_user_id'=> Utility::getNowUserId(),
                                 'update_time'=> new CDbExpression('now()')
                             ));
        if(is_array($obj->crossDetail))
        {
            foreach ($obj->crossDetail as $key => $value) {
                StockService::unFreeze($value['stock_id'], $value['quantity']);
            }
        }

        $detailId=ContractService::getContractGoodsDetailId($obj->contract_id,$obj->goods_id);

        TaskService::addTasks(Action::ACTION_CROSS_CHECK_BACK,$detailId,
                              array(
                                  "userIds"=>$obj->create_user_id,
                                  "code"=>$obj->cross_code,
                                  //"title"=>ActionService::getActionName(Action::ACTION_CROSS_CHECK_BACK)." ".$obj->cross_code
                              )

        );
    }

    /**
     * 多审核对象时更新任务状态，不同审核对象重写该方法
     * @param $checkDetail
     * @param int $roleId
     * @param int $userId
     */
    /*public function updateTask($checkDetail,$roleId=0,$userId=0)
    {
        $actionId=$this->businessConfig["action_id"];
        TaskService::doneTask($checkDetail->detail_id,$actionId,$roleId,$userId);
    }*/

    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        $taskParams = array('checkId'=>$checkItem->check_id, 'code'=>$this->checkObjectModel->cross_code);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', $taskParams);
    }
}