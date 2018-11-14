<?php
/**
 * Describe：
 */
class Check31 extends Check
{
    public function init()
    {
        $this->businessId=31;
    }


    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    public function checkStart()
    {
        
    }

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    public function checkDone()
    {
        $obj = PartnerReview::model()->findByPk($this->objId);
        $obj->status            = PartnerReview::STATUS_INFO_PASS;
        $obj->update_user_id    = $this->userId;
        $obj->update_time       = date("Y-m-d H:i:s");
        $res = $obj->save();
        if($res===true) {
            $partner = PartnerApply::model()->findByPk($obj->partner_id);
            if($partner->status==PartnerApply::STATUS_ADD_INFO_NOT_REVIEW){
                $result = PartnerService::updateApplyPartnerStatus($obj->partner_id,PartnerApply::STATUS_PASS);
                if($result==1){
                    PartnerService::updatePartnerInfo($obj->partner_id);
                }
            }else{
                $param = $this->getPartnerInfos($obj->partner_id);
                TaskService::addPartnerTasks(Action::ACTION_4, $obj->partner_id, ActionService::getActionRoleIds(Action::ACTION_4), 0, $param);
            }
            
        }
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
        $obj = PartnerReview::model()->findByPk($this->objId);
        $obj->status            = PartnerReview::STATUS_INFO_BACK;
        $obj->update_user_id    = $this->userId;
        $obj->update_time       = date("Y-m-d H:i:s");
        $obj->save();

        $partner = PartnerApply::model()->findByPk($obj->partner_id);
        TaskService::addPartnerTasks(Action::ACTION_5, $obj->partner_id, 0, $partner->create_user_id);

    }

    /**
     * 其它状态的审核处理
     * @param $checkStatus
     */
    public function checkElse($checkStatus)
    {
        
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
        $review= PartnerReview::model()->with('partner')->findByPk($checkItem->obj_id);
        $typeStr = array();
        $types = explode(',', $review->partner->type);
        foreach ($types as $thisType) {
            $typeStr[] = Map::$v['partner_type'][$thisType];
        }
        $typeStr = implode(',', $typeStr);
        $taskParams = array('name'=>$review->partner->name, 'typeName'=>$typeStr);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId,'', $taskParams);
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