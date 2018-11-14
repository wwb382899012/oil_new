<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/5 11:33
 * Describe：
 */
abstract class Check
{

    /**
     * 完成审核
     */
    const CHECK_DONE = 1;
    /**
     * 驳回
     */
    const CHECK_BACK = -1;
    /**
     * 拒绝
     */
    const CHECK_REJECT = 0;

    public $objId=0;

    public $businessId=0;

    public $userId=0;

    public $checkDetailId=0;
    public $checkLogId=0;

    /**
     * 被审核对象的实例
     * @var null
     */
    public $checkObjectModel=null;

    public $businessConfig=null;


    /**
     * 审核状态
     * @var int
     */
    //public $checkStatus=0;


    function __construct() {
        $this->init();
        $this->businessConfig=FlowService::$business[$this->businessId];
    }

    public function init()
    {

    }

    /**
     * 开始审核流程时更新当前对象的状态，不同审核对象重写该方法
     */
    abstract function checkStart();

    /**
     * 完成审核时更新审核对象的相关状态，不同的审核对象重写该方法
     */
    abstract function checkDone();

    /**
     * 审核拒绝时更新当前对象的状态，不同审核对象重写该方法
     */
    abstract function checkReject();


    /**
     * 审核驳回时更新当前对象的状态，不同审核对象重写该方法
     */
    abstract function checkBack();


    /**
     * 其它状态的审核处理
     * @param $checkStatus
     */
    public function checkElse($checkStatus)
    {

    }

    /**
     * 获取被审核对象
     * @param $objId
     * @return mixed
     */
    public function getCheckObjectModel($objId)
    {
        if(empty($this->checkObjectModel))
        {
            $model=$this->businessConfig["model"];
            if(empty($model))
                return null;
            $this->checkObjectModel=$model::model()->findByPk($this->objId);
        }
        return $this->checkObjectModel;
    }

    /**
     * 获取审核对象的交易主体Id
     * @param $objId
     * @return int
     */
    public function getCheckObjectCorpId($objId)
    {
        $this->getCheckObjectModel($objId);
        if(empty($this->checkObjectModel))
            return 0;
        if($this->checkObjectModel->hasAttribute('corporation_id'))
            return $this->checkObjectModel["corporation_id"];
        else
            return 0;
    }

    /**
     * 多审核对象时更新任务状态，不同审核对象重写该方法
     * @param $checkDetail
     * @param int $roleId
     * @param int $userId
     */
    public function updateTask($checkDetail,$roleId=0,$userId=0)
    {
        $actionId=$this->businessConfig["action_id"];
        TaskService::doneTask($checkDetail->obj_id,$actionId,$roleId,$userId);
    }

    /**
     * 销毁任务
     * @param $checkDetail
     * @throws Exception
     */
    protected function trashTask($checkDetail){
        TaskService::trashTask($checkDetail->obj_id, $this->businessConfig["action_id"]);
    }

    /**
     * 增加下次审核任务
     * @param $checkItem
     */
    public function addNextCheckTask($checkItem)
    {
        // debug(get_class($this));
        $corId=$this->getCheckObjectCorpId($checkItem->obj_id);
        TaskService::addCheckTasks($checkItem->obj_id,$checkItem->check_id,$this->businessConfig["action_id"],$corId);
    }


    /**
     * 开始流程信息
     * @param $objId
     * @param int $userId
     * @param string $checkUserIds
     * @return int|string
     */
    public function startCheck($objId,$checkUserIds,$userId)
    {
        $this->objId=$objId;
        $this->userId=$userId;
        
        $obj = CheckItem::model()->find("business_id=:businessId and obj_id=:objId and node_id>0",
                                        array("businessId" => $this->businessId, "objId" => $this->objId));
        if (!empty($obj->check_id)) {
            Mod::log("业务为" . $this->businessId . "的Id为" . $this->objId . "的对象已经在流程中，请不要重复提交", "error");
            return "当前对象已经在流程中，请不要重复提交";
        }
        $flowId = FlowService::getFlowId($this->businessId);
        if (empty($flowId)) {
            Mod::log("Id为" . $this->businessId . "的业务没有审核流程信息", "error");
            return "当前业务没有审核流程信息";
        }

        $checkNode=FlowService::getNextCheckNode($flowId,0);
        if (empty($checkNode)) {
            Mod::log("Id为" . $flowId . "的流程取不到首个节点信息", "error");
            return "Id为" . $flowId . "的流程取不到首个节点信息";
        }
        $obj = new CheckItem();
        $obj->business_id = $this->businessId;
        $obj->flow_id = $flowId;
        $obj->obj_id=$this->objId;
        $obj->node_id=$checkNode["node_id"];
        $obj->next_node_id=$checkNode["next_id"];

        $obj->status=1;
        $obj->create_time = date("Y-m-d H:i:s");
        $obj->create_user_id =$userId;
        $obj->update_time = date("Y-m-d H:i:s");
        $obj->update_user_id =$userId;


        // $db=Mod::app()->db;
        // $trans = $db->beginTransaction();
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }

        try{
            $obj->saveCheck($checkNode["role_ids"],$checkUserIds);
            $this->checkStart();
            $this->addNextCheckTask($obj);
            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            //$trans->commit();
            return 1;
        }
        catch(Exception $e)
        {
            if(!$isInDbTrans)
            {
                try{$trans->rollback();}catch (Exception $ee){}
            }
            Mod::log("业务为" . $this->businessId . "的Id为" . $this->objId . "的对象发起审核流程出错：".$e->getMessage(),"error");
            return "业务为" . $this->businessId . "的Id为" . $this->objId . "的对象发起审核流程出错：".$e->getMessage();
        }
    }

    /**
     * 进行审核
     * @param $checkItem
     * @param $checkStatus
     * @param $roleId
     * @param $remark
     * @param $userId
     * @param $checkUserIds "1,2,3"
     * @return int|string
     * @throws Exception
     */
    public function doCheck($checkItem,$checkStatus,$roleId,$remark,$userId,$checkUserIds)
    {
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $trans=Utility::beginTransaction();
        }
        $this->objId=$checkItem->obj_id;
        $this->userId=$userId;
        $map= Map::$v;
        $actionInfo=$map["node_id_map_action_id"];

        try {
            $flowNode = FlowNode::model()->with("nextNode")->findByPk($checkItem->node_id);
            if (empty($flowNode->node_id)){
                return "审核节点不存在！";
            }
            $checkDetail=CheckDetail::model()->find("check_id=".$checkItem->check_id." and status=".CheckDetail::STATUS_NEW."
                                                  and (role_id=".$roleId." or check_user_id=".$this->userId.")");
            if(empty($checkDetail->detail_id))
            {
                return "当前用户没有需要审核的信息！";
            }
            $this->checkDetailId=$checkDetail->detail_id;

            //更新当前审核用户的审核明细信息
            $checkDetail->check_user_id=$this->userId;
            $checkDetail->check_status=$checkStatus;
            $checkDetail->status=CheckDetail::STATUS_CHECKED;
            $checkDetail->update_time=date("Y-m-d H:i:s");
            $checkDetail->update_user_id =$this->userId;
            $checkDetail->save();


            $log=new CheckLog();
            $log->business_id=$this->businessId;
            $log->check_id=$checkItem->check_id;
            $log->detail_id=$checkDetail->detail_id;
            $log->user_id=$this->userId;
            $log->obj_id=$this->objId;
            $log->flow_id=$checkItem->flow_id;
            $log->node_id=$checkItem->node_id;
            $log->check_status=$checkStatus;
            $log->check_time=date("Y-m-d H:i:s");
            $log->is_countersign=$flowNode->is_countersign;
            $log->status=1;
            $log->remark=$remark;
            $log->create_time = date("Y-m-d H:i:s");
            $log->create_user_id =$this->userId;
            $log->update_time = date("Y-m-d H:i:s");
            $log->update_user_id =$this->userId;
            $log->save();
            $this->checkLogId=$log->id;

            if ($flowNode->is_countersign == 1)//会签处理
            {
                //$this->doneCheckDetail($checkItem,$roleId);//会签，只更新当前角色的审核明细
                
                $this->updateTask($checkDetail,$checkDetail->role_id,$checkDetail->check_user_id);
                
                $r=$this->countersignCheck($checkItem,$flowNode->countersign_rate);
                if($r===0)
                {
                    if(!$isInDbTrans)
                    {
                        $trans->commit();
                    }
                    return 1;
                }

                if($r==1){
                    $checkStatus=1;
                }
                else
                    $checkStatus=0;
            }
            else//非会签更新所有审核明细
            {
                $this->ignoreCheckDetail($checkItem);
                //$this->doneCheckDetail($checkItem);
                $this->updateTask($checkDetail,$checkDetail->role_id);
            }
            switch ($checkStatus)
            {
                case self::CHECK_DONE:
                    $nextNodeId=$this->getNextNodeId($checkItem,$flowNode);
                    //if ($checkItem->next_node_id > 0)
                    if($nextNodeId>0)
                    {
                        $this->goToNextNode($checkItem,$checkUserIds,$flowNode,$nextNodeId);
                        $this->addNextCheckTask($checkItem);
                        //TaskService::addCheckTasks($checkItem->business_id,$checkItem->check_id,$checkItem->obj_id,$this->getCheckObjectModel($checkItem->obj_id));
                    }
                    else
                    {
                        $this->completeCheck($checkItem);
                    }
                    break;

                case self::CHECK_BACK:
                    $this->backCheck($checkItem);
                    break;
                case self::CHECK_REJECT:
                    $this->rejectCheck($checkItem);
                    break;
                default:
                    $this->elseCheck($checkItem,$checkStatus);
                    break;
            }

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return 1;
        } catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                Mod::log("业务为" . $checkItem->business_id . "的Id为" . $checkItem->obj_id . "的对象的审核拒绝时出错：" . $e->getMessage(), "error");
                return "业务为" . $checkItem->business_id . "的Id为" . $checkItem->obj_id . "的对象的审核拒绝时出错：" . $e->getMessage();
                //return $e->getMessage();
            }
            else
                throw $e;
        }

    }

    /**
     * 撤销审核
     * @param $checkItem
     * @param $roleId
     * @param $remark
     * @param $userId
     * @param $checkUserIds
     * @return int|string
     * @throws Exception
     */
    public function revocationCheck($checkItem,$roleId,$remark,$userId,$checkUserIds){
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans){
            $trans=Utility::beginTransaction();
        }

        $this->objId=$checkItem->obj_id;
        $this->userId=$userId;

        try {
            $flowNode = FlowNode::model()->with("nextNode")->findByPk($checkItem->node_id);
            if (empty($flowNode->node_id)){
                return "审核节点不存在！";
            }

            $checkDetail=CheckDetail::model()->find("check_id=".$checkItem->check_id." and status=".CheckDetail::STATUS_NEW);
            if(empty($checkDetail->detail_id)){
                return "当前审核的信息不存在！";
            }

            $this->checkDetailId=$checkDetail->detail_id;

            //更新当前审核明细信息
            $checkDetail->check_user_id = $this->userId;
            $checkDetail->check_status = Check::CHECK_REJECT;
            $checkDetail->status = CheckDetail::STATUS_IGNORED;
            $checkDetail->update_time=date("Y-m-d H:i:s");
            $checkDetail->update_user_id =$this->userId;
            $checkDetail->save();

            //更新审核子项
            $checkItem->node_id = -1;
            $checkItem->next_node_id = 0;
            $checkItem->update_time = date("Y-m-d H:i:s");
            $checkItem->update_user_id = $this->userId;
            $checkItem->save();

            //销毁任务
            $this->trashTask($checkDetail);

            $log=new CheckLog();
            $log->business_id=$this->businessId;
            $log->check_id=$checkItem->check_id;
            $log->detail_id=$checkDetail->detail_id;
            $log->user_id=$this->userId;
            $log->obj_id=$this->objId;
            $log->flow_id=$checkItem->flow_id;
            $log->node_id=$checkItem->node_id;
            $log->check_status= Check::CHECK_REJECT;
            $log->check_time=date("Y-m-d H:i:s");
            $log->is_countersign=$flowNode->is_countersign;
            $log->status=1;
            $log->remark=$remark;
            $log->create_time = date("Y-m-d H:i:s");
            $log->create_user_id =$this->userId;
            $log->update_time = date("Y-m-d H:i:s");
            $log->update_user_id =$this->userId;
            $log->save();
            $this->checkLogId=$log->id;

            if(!$isInDbTrans){
                $trans->commit();
            }

            return 1;
        } catch (Exception $e) {
            if(!$isInDbTrans){
                try { $trans->rollback(); }catch(Exception $ee){}
                Mod::log("业务为" . $checkItem->business_id . "的Id为" . $checkItem->obj_id . "的对象的审核撤销时出错：" . $e->getMessage(), "error");
                return "业务为" . $checkItem->business_id . "的Id为" . $checkItem->obj_id . "的对象的审核撤销时出错：" . $e->getMessage();
                //return $e->getMessage();
            }
            else
                throw $e;
        }
    }


    /**
     * 完成审核明细，更新状态为2，表示无需审核
     * @param $checkItem
     * @param int $roleId
     */
    protected function doneCheckDetail($checkItem,$roleId=0)
    {
        $sql="update t_check_detail set status=1,update_time=now(),update_user_id=".$this->userId." where check_id=".$checkItem->check_id." and status=0";
        if(!empty($roleId))
            $sql.=" and role_id=".$roleId;
        Utility::executeSql($sql);
    }

    /**
     * 忽略其他审核明细，更新状态为2，表示无需审核
     * @param $checkItem
     */
    protected function ignoreCheckDetail($checkItem)
    {
        CheckDetail::model()->updateAll(
            array("status"=>CheckDetail::STATUS_IGNORED,"update_time"=>new CDbExpression("now()"),"update_user_id"=>$this->userId),
            "check_id=".$checkItem->check_id." and status=".CheckDetail::STATUS_NEW
        );
       /* $sql="update t_check_detail set status=1,update_time=now(),update_user_id=".$this->userId." where check_id=".$checkItem->check_id." and status=0";
        if(!empty($roleId))
            $sql.=" and role_id=".$roleId;
        Utility::executeSql($sql);*/
    }

    /**
     * 进入下级审核
     * @param $checkItem
     * @param string $checkUserIds "1,2,3"
     * @param null $flowNode
     * @param int $next_node_id
     * @return mixed
     * @throws Exception
     */
    protected function goToNextNode($checkItem,$checkUserIds,$flowNode=null,$next_node_id=0)
    {
        if(empty($flowNode))
            $flowNode=FlowNode::model()->with("nextNode")->findByPk($checkItem->node_id);

        if(empty($flowNode["node_id"]))
        {
            Mod::log("获取节点".$checkItem->node_id."信息出错","error");
            throw new Exception("获取节点".$checkItem->node_id."信息出错");
        }
        if(empty($next_node_id))
            $next_node_id=$this->getNextNodeId($checkItem,$flowNode);

        if($next_node_id==$flowNode->next_id)
        {
            $nextNode=$flowNode->nextNode;
        }
        else
        {
            $nextNode=FlowNode::model()->findByPk($next_node_id);
            if(empty($nextNode["node_id"]))
            {
                Mod::log("获取节点".$checkItem->node_id."的信息出错","error");
                throw new Exception("获取节点".$checkItem->node_id."的信息出错");
            }
        }

        $checkItem->node_id=$nextNode["node_id"];
        $checkItem->next_node_id=$nextNode["next_id"];
        $checkItem->update_time = date("Y-m-d H:i:s");
        $checkItem->update_user_id =$this->userId;

        return $checkItem->saveCheck($nextNode["role_ids"],$checkUserIds);
    }

    /**
     * 获取当前审核节点的下级审核节点
     *  配置样例
     * $config=array(
     *    array(
     *    "next_node_id"=>12,
     *    "field"=>array("key1"=>"amount","key2"=>"extra['items']['key1']",),
     *    "condition"=>"(#key1# > 1 || #key2#==1)"
     *    ),
     * @param $checkItem
     * @param $flowNode
     * @return mixed
     * @throws Exception
     */
    protected function getNextNodeId($checkItem,$flowNode)
    {
        $next_node_id=$flowNode["next_id"];
        if(!empty($flowNode->next_condition))
        {
            /*
             * 配置样例
             * $config=array(
                array(
                    "next_node_id"=>12,
                    "field"=>array("key1"=>"amount","key2"=>"extra['items']['key1']",),
                    "condition"=>"(#key1# > 1 || #key2#==1)"
                ),
            );*/
            $conditions=json_decode($flowNode->next_condition,true);
            if(is_array($conditions))
            {
                $model=$this->getCheckObjectModel($checkItem->obj_id);
                if(empty($model))
                    throw new Exception("有条件流程设置， 但无法获取到审核对象实例");
                foreach ($conditions as $c)
                {
                    $res=false;
                    $condition=$c["condition"];
                    foreach ($c["field"] as $k=>$v)
                    {
                        $names=explode(".",$v);
                        $name=implode("->",$names);

                        /*foreach ($names as $n)
                        {
                            $name.="->".$n."";
                        }*/
                        $value="0";
                        eval('$value=$model->'.$name.';');
                        //$value=$model->$name;
                        $condition=str_replace("#".$k."#",$value,$condition);
                    }
                    eval('$res='.$condition.';');
                    if($res)
                    {
                        $next_node_id=$c["next_node_id"];
                        break;
                    }

                }
            }
        }

        return $next_node_id;
    }

    /**
     * 审核完成的相关操作
     * @param $checkItem
     * @return int|string
     */
    protected function completeCheck($checkItem)
    {
        $checkItem->node_id = 0;
        $checkItem->next_node_id = 0;
        $checkItem->update_time = date("Y-m-d H:i:s");
        $checkItem->update_user_id = $this->userId;
        $checkItem->save();
        $this->checkDone();
    }

    /**
     * 审核拒绝的相关操作
     * @param $checkItem
     * @return int|string
     */
    protected function rejectCheck($checkItem)
    {
        $checkItem->node_id = -1;
        $checkItem->next_node_id = 0;
        $checkItem->update_time = date("Y-m-d H:i:s");
        $checkItem->update_user_id = $this->userId;

        $checkItem->save();
        $this->checkReject();

    }

    protected function elseCheck($checkItem,$checkStatus)
    {
        $checkItem->node_id = -1*$checkStatus;
        $checkItem->next_node_id = 0;
        $checkItem->update_time = date("Y-m-d H:i:s");
        $checkItem->update_user_id = $this->userId;

        $checkItem->save();
        $this->checkElse($checkStatus);

    }

    /**
     * 驳回操作
     * @param $checkItem
     */
    protected function backCheck($checkItem)
    {
        $checkItem->node_id = -1;
        $checkItem->next_node_id = 0;
        $checkItem->update_time = date("Y-m-d H:i:s");
        $checkItem->update_user_id = $this->userId;

        $checkItem->save();
        $this->checkBack();

    }

    /**
     * 驳回后删除没有审核记录
     * @param $checkItem
     *
     */
    protected function delDetail($checkItem)
    {
        $sql="delete from t_check_detail where check_id=".$checkItem->check_id." and obj_id=".$checkItem->obj_id." and check_status=0 and status=0";
        Utility::executeSql($sql);
    }

    /**
     * 判断会签状态
     * @param $checkItem
     * @param $rate
     * @return int
     */
    protected function countersignCheck($checkItem,$rate)
    {
        $sql="select * from t_check_detail where check_id=".$checkItem->check_id." order by status asc";
        $data=Utility::query($sql);

        /*foreach ($data as $key => $value) {
            if($value["check_status"]==-1){
                return -2;
            }
        }*/

        $d=array("0"=>0,"1"=>0);

        foreach($data as $v)
        {
            if($v["status"]==0)
                return 0;

            $d[$v["check_status"]]+=1;
        }

        if(empty($d["0"]))
        {
            return 1;
        }
        else if(empty($d["1"]))
        {
            return -1;
        }
        else
        {
            $r=$d["1"]/($d["1"]+$d["0"]);
            if($r>=$rate)
                return 1;
            else
                return -1;
        }
    }


}