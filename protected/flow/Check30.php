<?php
/**
 * Describe：
 */
class Check30 extends Check
{
    public function init()
    {
        $this->businessId=30;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart()
    {
        // PartnerService::updateApplyStatus($this->objId,PartnerApply::STATUS_SUBMIT);
    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone()
    {
        PartnerService::updateApplyStatus($this->objId,PartnerApply::STATUS_PASS);
        PartnerService::updatePartnerInfo($this->objId);

    }

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     * 当审核状态不为1或-1时都进入该项，可以在这里添加其他审核状态的处理
     */
    public function checkReject()
    {
        PartnerService::updateApplyStatus($this->objId,PartnerApply::STATUS_REJECT);
    }

    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkBack()
    {
        PartnerService::updateApplyStatus($this->objId,PartnerApply::STATUS_BACK);

        $partner = PartnerApply::model()->findByPk($this->objId);
        // TaskService::addPartnerTasks(Action::ACTION_1, $this->objId, 0, $partner->create_user_id);
        // action 1 目前没有消息
        $param = $this->getPartnerInfos($this->objId);
        TaskService::addPartnerTasks(Action::ACTION_44, $this->objId, ActionService::getActionRoleIds(Action::ACTION_44), "0", $param);
    }

    /**
     * 其它状态的审核处理
     * @param $checkStatus
     */
    public function checkElse($checkStatus)
    {
        switch ($checkStatus)
        {
            case 5://需要现场风控
                PartnerService::updateApplyStatus($this->objId,PartnerApply::STATUS_ON_RISK);
                $param = $this->getPartnerInfos($this->objId);
                TaskService::addPartnerTasks(Action::ACTION_3, $this->objId, ActionService::getActionRoleIds(Action::ACTION_3), "0", $param);
                break;
            case 6://需评审
                PartnerService::updateApplyStatus($this->objId,PartnerApply::STATUS_REVIEW);
                $param = $this->getPartnerInfos($this->objId);
                TaskService::addPartnerTasks(Action::ACTION_4, $this->objId, ActionService::getActionRoleIds(Action::ACTION_4), "0", $param);
                break;
        }
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
        $partner = PartnerApply::model()->findByPk($checkItem->obj_id);
        $typeStr = array();
        $types = explode(',', $partner->type);
        foreach ($types as $thisType) {
            $typeStr[] = Map::$v['partner_type'][$thisType];
        }
        $typeStr = implode(',', $typeStr);
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId, '', array('checkId'=>$checkItem->check_id, 'name'=>$partner->name, 'typeName'=>$typeStr));
    }

    private function getPartnerInfos($partner_id) {
        $partner = PartnerApply::model()->findByPk($partner_id);
        $typeStr = array();
        $types = explode(',', $partner->type);
        foreach ($types as $thisType) {
            $typeStr[] = Map::$v['partner_type'][$thisType];
        }
        $typeStr = implode(',', $typeStr);
        return array('name'=>$partner->name, 'typeName'=>$typeStr);
    } 
}