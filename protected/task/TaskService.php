<?php

/**
 * Created by youyi000.
 * DateTime: 2016/8/15 15:46
 * Describe：
 */
class TaskService
{

    public static $emailTemplate="";


    /**
     * 获取用户的任务汇总信息
     * @param $userId
     * @param int $roleId
     * @return array|null
     */
    public static function getUserActions($userId,$roleId=0)
    {
        if (empty($userId))
            return null;
        if (empty($roleId))
        {
            $roleId = UserService::getUserMainRoleId($userId);
            if (empty($roleId))
                $roleId = 0;
        }
        $user = SystemUser::getUser($userId);
        $corIds = empty($user["corp_ids"]) ? "0" : ("0," . $user["corp_ids"]);
        $sql = "select b.action_id,b.action_name,b.icon,b.list_url,b.order_index,a.num as n from
              (
                select action_id,count(*) num 
                from t_task 
                where status=0 and (user_id=" . $userId . " or role_id=" . $roleId . ") 
                and corporation_id in(" . $corIds .")
                group by action_id
              ) a
              left join t_action b on a.action_id=b.action_id             
              order by b.order_index asc,b.action_id asc";
        return Utility::query($sql);
    }

    public static function getUserAllRolesActions($userId,$roleId=0)
    {
        if(empty($userId))
            return null;
        if(empty($roleId))
        {
            $roleId=UserService::getUserMainRoleId($userId);
            if(empty($roleId))
                $roleId=0;
        }
        $user = SystemUser::getUser($userId);
          $map = Map::$v;
        $roleIds=$user["main_role_id"];
        $roleIds=empty($user["role_ids"])?$roleIds:($roleIds.",".$user["role_ids"]);
          $sql = "select c.*,sum(c.num) as n from (select b.*,a.num from
              (select corporation_id,action_id,count(*) num from t_task where status=0 and (user_id=".$userId." or role_id in(".$roleIds.")) group by action_id,corporation_id) a
              left join t_action b on a.action_id=b.action_id where 
              a.corporation_id = 0 or 
              FIND_IN_SET(a.corporation_id, '".$user['corp_ids']."') or FIND_IN_SET(b.action_id, '".implode(',', $map['not_corporation_map_action_id'])."')) c
              group by c.action_id
              order by c.order_index asc,c.action_id asc";
        return Utility::query($sql);
    }



    public static function getUserAllTasks($userId=0)
    {
        if(empty($userId))
            $userId=Utility::getNowUserId();

        $user=SystemUser::getUser($userId);
        $roleIds=$user["main_role_id"];
        $roleIds=empty($user["role_ids"])?$roleIds:($roleIds.",".$user["role_ids"]);
        $corIds=empty($user["corp_ids"])?"0":("0,".$user["corp_ids"]);
        $sql="select a.*,b.action_name,b.icon from t_task a,t_action b where a.action_id=b.action_id and a.status=0 
              and (a.user_id=".$userId." or role_id in(".$roleIds."))
              and a.corporation_id in(".$corIds.") order by a.task_id desc limit 20";
        $data=Utility::query($sql);
        return $data;
    }


    public static function getUserAllTasksUnlimit($userId=0)
    {
        if(empty($userId))
            $userId=Utility::getNowUserId();

        $user=SystemUser::getUser($userId);
        $roleIds=$user["main_role_id"];
        $roleIds=empty($user["role_ids"])?$roleIds:($roleIds.",".$user["role_ids"]);
        $corIds=empty($user["corp_ids"])?"0":("0,".$user["corp_ids"]);
        $sql="select a.*,b.action_name,b.icon from t_task a,t_action b where a.action_id=b.action_id and a.status=0 
              and (a.user_id=".$userId." or role_id in(".$roleIds."))
              and a.corporation_id in(".$corIds.") order by a.task_id desc";

        $data=Utility::query($sql);
        return $data;
    }


    public static function getUserActionModels($userId=0)
    {
        if(empty($userId))
            $userId=Utility::getNowUserId();

        $user=SystemUser::getUser($userId);
        $roleIds=$user["main_role_id"];
        $roleIds=empty($user["role_ids"])?$roleIds:($roleIds.",".$user["role_ids"]);
        $corIds=empty($user["corp_ids"])?"0":("0,".$user["corp_ids"]);

        $actions=Action::model()->with("tasks")->findAll("
                tasks.status=0 
                and (tasks.user_id=".$userId." or tasks.role_id in(".$roleIds."))
                and tasks.corporation_id in(".$corIds.")");

        return $actions;
    }


    /**
     * 添加合作方准入任务信息
     * @param $actionId
     * @param $keyValue
     * @param string|array $roleIds
     * @param string $userIds
     * @return int|string
     * @throws Exception
     */
    public static function addPartnerTasks($actionId,$keyValue,$roleIds="0",$userIds="0", $params = array())
    {
        // $params=array();
        if(is_array($roleIds))
        {
            $params=$roleIds;

            if(isset($params["roleIds"]))
                $roleIds=$params["roleIds"];
            if(isset($params["userIds"]))
                $userIds=$params["userIds"];
        }

        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }
        try {
            $action=ActionService::getAction($actionId);
            if(empty($action))
                return "当前Action不存在！";

            $params['keyValue'] = $keyValue;

            $title =self::getTaskTitle($action,$params);

            /*if($actionId==7 || $actionId==8){
              $storehouse = Storehouse::model()->findByPk($keyValue);
              $title = $storehouse->company_name."-".$storehouse->name;
            }else{
              $partner = PartnerApply::model()->findByPk($keyValue);
              $title = $partner->name;
            }*/

            $data=array();
            if(!empty($roleIds)){
                $roleArr=explode(",",$roleIds);
                foreach ($roleArr as $roleId)
                {
                  if(empty($roleId))
                      continue;
                  $data[]=array("roleId"=>$roleId,"userId"=>0);
                }
            }else if(!empty($userIds)){
                $userArr=explode(",",$userIds);
                foreach ($userArr as $userId)
                {
                  if(empty($userId))
                      continue;
                  $data[]=array("roleId"=>0,"userId"=>$userId);
                }
            }

            foreach ($data as $v)
            {
                $obj = new Task();
                $obj->user_id = $v["userId"];
                $obj->role_id = $v["roleId"];
                $obj->action_id = $actionId;
                $obj->corporation_id= 0;
                $obj->key_value = $keyValue;

                $obj->title=$title;

                $obj->action_url =  static::getActionUrl($action["action_url"], $params);
                $obj->create_time = date("Y-m-d H:i:s");
                $obj->create_user_id = Utility::getNowUserId();
                $obj->update_time = date("Y-m-d H:i:s");
                $obj->update_user_id = $obj->create_user_id;
                $obj->status = 0;
                $obj->status_time = date("Y-m-d H:i:s");
                $obj->save();
                self::sendReminder($obj->task_id);
            }
      
            if(!$isInDbTrans)
            {
                $trans->commit();
            }
           return 1;
    
        }catch (Exception $e) {
          if(!$isInDbTrans)
          {
            try { $trans->rollback(); }catch(Exception $ee){}
                Mod::log("添加任务出错：".$e->getMessage(),"error"); 
                return $e->getMessage();
          }
          else 
            throw $e;
        }
    }


    /**
     * 添加任务信息
     * @param $actionId
     * @param $keyValue
     * @param string|array $roleIds
     * @param string $userIds
     * @param array $params
     * @return int|string
     * @throws Exception
     */
    public static function addTasks($actionId,$keyValue,$roleIds="0",$userIds="0",$corpId="0",$params=array())
    {
        if(is_array($roleIds))
        {
            $params=$roleIds;
            $roleIds="0";
            if(isset($params["roleIds"]))
                $roleIds=$params["roleIds"];
            if(isset($params["userIds"]))
                $userIds=$params["userIds"];
            if(isset($params["corpId"]))
                $corpId=$params["corpId"];
        }


        $isInDbTrans = Utility::isInDbTrans();
        if (!$isInDbTrans)
        {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }
        try
        {
            $action = ActionService::getAction($actionId);
            if (empty($action))
                return "当前Action不存在！";

            $params['keyValue'] = $keyValue;
            $action_url = static::getActionUrl($action["action_url"], $params);

            $data = array();
            if (!empty($userIds))
            {
                $userArr = explode(",", $userIds);
                foreach ($userArr as $userId)
                {
                    if (empty($userId))
                        continue;
                    $data[] = array("roleId" => 0, "userId" => $userId);
                }
            }
            else if (!empty($roleIds))
            {
                $roleArr = explode(",", $roleIds);
                foreach ($roleArr as $roleId)
                {
                    if (empty($roleId))
                        continue;
                    $data[] = array("roleId" => $roleId, "userId" => 0);
                }
            }


            $title=self::getTaskTitle($action,$params);

            foreach ($data as $v)
            {
                $obj = new Task();
                $obj->user_id = $v["userId"];
                $obj->role_id = $v["roleId"];
                $obj->action_id = $actionId;
                $obj->corporation_id = $corpId;//!empty($corpId) ? $corpId : $project->corporation_id;
                $obj->key_value = $keyValue;
                $obj->title = $title;

                $obj->action_url = $action_url;
                $obj->create_time = date("Y-m-d H:i:s");
                $obj->create_user_id = Utility::getNowUserId();
                $obj->update_time = date("Y-m-d H:i:s");
                $obj->update_user_id = $obj->create_user_id;
                $obj->status = 0;
                $obj->status_time = date("Y-m-d H:i:s");
                $obj->save();

                self::sendReminder($obj->task_id);
            }

            if (!$isInDbTrans)
            {
                $trans->commit();
            }
            return 1;

        }
        catch (Exception $e)
        {
            if (!$isInDbTrans)
            {
                try
                {
                    $trans->rollback();
                }
                catch (Exception $ee)
                {
                }
                Mod::log("添加任务出错：" . $e->getMessage(), "error");
                return $e->getMessage();
            }
            else
                throw $e;
        }
    }

    /**
     * 获取提醒标题
     * @param $action
     * @param $params
     * @return mixed|string
     */
    protected static function getTaskTitle($action,$params)
    {
        if (!empty($params["title"]))
            $title = $params["title"];
        else
        {
            $title = static::getActionTitle($action["title"], $params);
            if(empty($title))
                $title = $action["action_name"] . " " . $params["keyValue"];
        }
        return $title;
    }

    /**
     * 添加审核相关的任务
     * @param $keyValue
     * @param $checkId
     * @param $actionId
     * @param int|array $corpId
     * @param string $title
     * @param array $params
     * @return bool
     */
    public static function addCheckTasks($keyValue,$checkId,$actionId,$corpId=0,$title="",$params=array())
    {
        if(is_array($corpId))
        {
            $params=$corpId;
            $corpId=0;
            if(isset($params["corpId"]))
                $corpId=$params["corpId"];
            if(isset($params["title"]))
                $title=$params["title"];
        }

        $action=ActionService::getAction($actionId);
        if(empty($action))
            return false;//"当前Action不存在！";
        // debug($actionId);
        $checkDetails=CheckDetail::model()->with("checkNode")->findAll("t.status=0 and t.check_id=".$checkId);
        // 这里应该是checkId
        $params["check_id"]=$checkId;
        $params['keyValue'] = $keyValue;

        if(empty($title))
            $title=self::getTaskTitle($action,$params);

        if(is_array($checkDetails) && count($checkDetails)>0)
        {
            try
            {
                foreach ($checkDetails as $check)
                {
                    $params["detail_id"] = $check->detail_id;
                    $obj = new Task();
                    $obj->user_id = $check['check_user_id'];
                    $obj->role_id = $check['role_id'];
                    $obj->corporation_id = $corpId;
                    $obj->action_id = $action["action_id"];
                    $obj->key_value = $keyValue;
                    $obj->title = $title;
                    $obj->action_url = static::getActionUrl($action["action_url"], $params);
                    $obj->create_time = date("Y-m-d H:i:s");
                    $obj->create_user_id = Utility::getNowUserId();
                    $obj->update_time = date("Y-m-d H:i:s");
                    $obj->update_user_id = $obj->create_user_id;
                    $obj->status = 0;
                    $obj->status_time = date("Y-m-d H:i:s");
                    $obj->save();
                    self::sendReminder($obj->task_id);
                }
            }
            catch(Exception $e)
            {
                Mod::log("添加任务出错：".$e->getMessage(),"error");
                return false;//$e->getMessage();
            }
        }

        return true;
    }

    protected static function getActionUrl($urlTemplate,$params)
    {
        $url=$urlTemplate;
        foreach ($params as $k=>$v)
        {
            $url=str_replace("#".$k."#", $v, $url);
        }
        return $url;
    }

    protected static function getActionTitle($titleTemplate,$params)
    {
        $title=$titleTemplate;
        foreach ($params as $k=>$v)
        {
            $title=str_replace("#".$k."#", $v, $title);
        }
        return $title;
    }


    /**
     * 完成任务
     * @param $keyValue
     * @param $actionId
     * @param int $roleId
     * @param int $userId
     * @return array|int|void
     * @throws Exception
     */
    public static function doneTask($keyValue,$actionId,$roleId=0,$userId=0)
    {
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }
        try {
            if(empty($keyValue) || empty($actionId))
                return;
            $sql="update t_task set status=1,status_time=now() where status=0 and key_value=".$keyValue." and action_id=".$actionId."";
            if(!empty($roleId))
            {
                $sql.=" and role_id=".$roleId."";
            }
            if(!empty($userId))
            {
                $sql.=" and user_id=".$userId."";
            }

            $res=Utility::execute($sql);
            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            if($res!=-1)
                return 1;
            else
                return $res;
        } catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return $e->getMessage();
            }
            else
                throw $e;
        }
    }



    /**
     * 删除作废任务
     * @param $keyValue
     * @param $actionId
     * @param int $roleId
     * @param int $userId
     * @return array|int|void
     * @throws Exception
     */
    public static function trashTask($keyValue,$actionId,$roleId=0,$userId=0)
    {
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }
        try {
            if(empty($keyValue) || empty($actionId))
                return;
            $sql="update t_task set status=-1,status_time=now() where status=0 and key_value=".$keyValue." and action_id=".$actionId."";
            if(!empty($roleId))
            {
                $sql.=" and role_id=".$roleId."";
            }
            if(!empty($userId))
            {
                $sql.=" and user_id=".$userId."";
            }
            $res=Utility::execute($sql);
            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            if($res!=-1)
                return 1;
            else
                return $res;
        } catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return $e->getMessage();
            }
            else
                throw $e;
        }
    }

    /**
     * 发布提醒事件
     * @param $taskId
     */
    public static function sendReminder($taskId)
    {
        // debug_print_backtrace(3);die;
        $params=array(
            "task_id"=>$taskId
        );

        Mod::log(__METHOD__ . " 添加task到消息队列:".json_encode($params));
        AMQPService::publishReminder($params);
    }

    /**
     * 添加邮件提醒
     * @param $actionId
     * @param $corpId
     * @param $title
     * @param $userId
     * @param $roleId
     * @return void
     */
    /*public static function sendEmailRemind($actionId,$corpId,$title,$userId=0,$roleId=0)
    {


        try
        {
            if (empty($actionId))
                return;
            if (empty($userId) && empty($roleId))
                return;
            $action = ActionService::getAction($actionId);
            $urlHost = Mod::app()->params["url_host"];
            if (!empty($userId))
            {
                $userName = UserService::getUsernameById($userId);
                AMQPService::publishEmail($userId, $action['action_name'] . '--' . $title, $userName . ',您好！<br/>&emsp;&emsp;您有一个新任务：<span style="color:#FF0000;font-weight:bold;">' . $action['action_name'] . '--' . $title . '</span>，请及时处理！<a href="http://' . $urlHost . '">点击进入石油系统</a>');
            }
            else if (!empty($roleId))
            {
                $uArr = UserService::getUserByRoleId($roleId, $corpId);
                if (Utility::isNotEmpty($uArr))
                {
                    foreach ($uArr as $key => $value)
                    {
                        AMQPService::publishEmail($value["user_id"], $action['action_name'] . '--' . $title, $value['user_name'] . ',您好！<br/>&emsp;&emsp;您有一个新任务：<span style="color:#FF0000;font-weight:bold;">' . $action['action_name'] . '--' . $title . '</span>，请及时处理！<a href="http://' . $urlHost . '">点击进入石油系统</a>');
                    }
                }
            }
        }
        catch (Exception $ee)
        {

        }
    }*/

    /**
     * 发送微信提醒
     * @param $taskId
     */
    public static function sendWeixinReminder($taskId)
    {
        try
        {
            if (empty($taskId))
                return;
            $task=Task::model()->with("action")->findByPk($taskId);
            if(empty($task))
                return;

            $userIds=array();
            if(!empty($task->user_id))
            {
                $user=SystemUser::getUser($task->user_id);
                $usersArr[]=array(
                    'id' => $user["identity"],
                    'name' => $user['name']
                );
                $userIds[] = $user['identity'];
            }
            else
            {
                $users=UserService::getUserByRoleId($task->role_id,$task->corporation_id);
                foreach ($users as $u)
                {
                    $usersArr[] = array(
                        'id' => $u["identity"],
                        'name' => $u['name']
                    );
                    $userIds[] = $u['identity'];
                }
            }

            //$user = SystemUser::getUser($task->user_id);
            //$msg=$user['name'] . '，您好！您有一个新提醒：' . $task->title . '，请登录石油系统及时处理！谢谢！';
            //$title=$task->action->action_name." " . $task->title ;
            $title=empty($task->title)?$task->action->action_name." ".$task->key_value:$task->title;
            //$msg='您有一个新的'.$task->action->action_name.'待办提醒：'.$title. '，请登录石油系统及时处理！谢谢！';

            if (in_array($task->action_id, array(Action::ACTION_11, Action::ACTION_13, Action::ACTION_21))) {
                $url = Mod::app()->params->weiXin_url . '/site/task?id=' . $taskId;
                foreach ($usersArr as $user) {
                    $msg = $user['name'] . ', 您有一个新任务:' . $task->action->action_name . '（' . $title . '），请尽快登录系统处理';
                    $msg .= "\n\n点击进入石油系统";
                    AMQPService::publishWinxinSingleNewsReminder($user['id'], $task->action->action_name, $msg, $url);
                    //WeiXinService::sendSingleNews($user['id'], $task->action->action_name, $msg, $url);
                }
            } else {
                $msg='您有一个新任务:'.$task->action->action_name.'（'.$title. '），请尽快登录系统处理';
                AMQPService::publishWinxinReminder($userIds,$msg);
                //WeiXinService::send($userIds,$msg);
            }
        }
        catch (Exception $e)
        {
            Mod::log("Send Weixin error: ".$e->getMessage(),"error");
        }
    }

    /**
     * 发送邮件提醒
     * @param $taskId
     */
    public static function sendEmailReminder($taskId)
    {
        try
        {
            if (empty($taskId))
                return;
            $task=Task::model()->with("action")->findByPk($taskId);
            if(empty($task))
                return;

            $urlHost = Mod::app()->params["url_host"];
            //$title=$task->action->action_name." " . $task->title;
            $title="石油系统待办提醒 ".$task->action->action_name;
            $content=empty($task->title)?$task->action->action_name." ".$task->key_value:$task->title;

            $msg='您有一个新任务:'.$task->action->action_name.'（'.$content. '），请尽快登录系统处理，<a href="http://' . $urlHost . '/site/task?id='.$task->task_id.'">点击进入石油系统</a>';
            //$msg='您好！<br/>&emsp;&emsp;您有一个新的提醒：<span style="color:#FF0000;font-weight:bold;">' .$content . '</span>，请及时处理！<a href="http://' . $urlHost . '/site/task?id='.$task->task_id.'">点击进入石油系统</a>';

            if(!empty($task->user_id))
            {
                AMQPService::publishEmail($task->user_id, $title, $msg);
            }
            else
            {
                $users=UserService::getUserByRoleId($task->role_id,$task->corporation_id);
                foreach ($users as $u)
                {
                    AMQPService::publishEmail($u["user_id"], $title, $msg);
                }
            }
        }
        catch (Exception $e)
        {
            Mod::log("Send Email Reminder error: ".$e->getMessage(),"error");
        }
    }

    /**
     * 添加微信提醒
     * @param $actionId
     * @param $corpId
     * @param $title
     * @param $userId
     * @param $roleId
     * @return void
     */
    /*public static function sendWeixRemind($actionId,$corpId,$title,$userId=0,$roleId=0)
    {
        return;
        try
        {
            if (empty($actionId))
                return;
            if (empty($userId) && empty($roleId))
                return;

            $action = ActionService::getAction($actionId);
            $msgArr = array();
            if (!empty($userId))
            {
                $user = SystemUser::getUser($userId);
                if (!empty($user['identity']))
                {
                    $msg = $user['user_name'] . ',您好！您有一个新任务：' . $action['action_name'] . '--' . $title . '，请登录石油系统及时处理！谢谢！';
                    $msgArr[] = array('user_account' => $user['identity'], 'msg' => $msg);
                }
            }
            else
            {
                $uArr = UserService::getUserByRoleId($roleId, $corpId);
                if (Utility::isNotEmpty($uArr))
                {
                    foreach ($uArr as $key => $value)
                    {
                        if (!empty($value['identity']))
                        {
                            $msg = $value['user_name'] . ',您好！您有一个新任务：' . $action['action_name'] . '--' . $title . '，请登录石油系统及时处理！谢谢！';
                            $msgArr[] = array('user_account' => $value['identity'], 'msg' => $msg);
                        }
                    }
                }
            }
            if (!empty($msgArr) && count($msgArr) > 0)
                WeiXin::postBatchMessage($msgArr);
            //self::curl($msgArr);
        }
        catch (Exception $ee)
        {

        }
    }*/

   /* public function curl($messages)
    {
      // $url      = "http://10.4.35.232:8092"; //微信提醒线上地址
      $url      = "http://172.16.1.16:9998"; //微信提醒测试环境地址
      $curl     = Mod::app()->curl;
      $cmdNo    = 24010001;

      $agentId  = Mod::app()->params["wx_agent_id"];
      $array    = array('cmd'=>$cmdNo,'data'=>$messages,'agent_id'=>$agentId);
      try{
          $res = $curl->post($url,json_encode($array));
          Mod::log("Alarm Log, params is ".json_encode($array).", and result is ".$res);
      }
      catch(Exception $e)
      {
          Mod::log("Alarm error, params is ".json_encode($array).", and error message is ".$e->getMessage(),"error");
          return array("code"=>-1,"msg"=>$e->getMessage());
      }
    }*/



    /*public static function getProjectByAction($actionId,$keyValue)
    {
        $projectId=0;
        switch ($actionId)
        {
            case 10:
                $model = Project::model()->findByPk($keyValue);
                if(empty($model->project_id)){
                  $model = Contract::model()->findByPk($keyValue);
                }
                $projectId = $model->project_id;
                break;

            case 11:
            case 12:
            case 13:
                $model=Contract::model()->findByPk($keyValue);
                $projectId= $model->project_id;
                break;

            default:
                $projectId=$keyValue;
                break;
        }

        $p = Project::model()->with('base')->findByPk($projectId);
        return $p;
    }*/

    /*public static function sendTips($actionId,$keyValue,$roleIds="0",$userIds="0")
    {
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }
        try {
            $action=ActionService::getAction($actionId);
            if(empty($action))
                return "当前Action不存在！";

            $title = "";
            $corporation_id = 0;
            $map = Map::$v;
            if($actionId==Action::ACTION_14){
              $contract = Contract::model()->with('project')->findByPk($keyValue);
              $corporation_id = $contract->corporation_id;
              $title = "项目编号:".$contract->project->project_code.",合同编码:".$contract->contract_code."-".$map['contract_file_categories'][$contract->type][$contract->category]['name'];
            }else{
              $contractFile = ContractFile::model()->with('contract','project')->findByPk($keyValue);
              $corporation_id = $contractFile->contract->corporation_id;
              $title = "项目编号:".$contractFile->project->project_code.",合同编码:".$contractFile->contract->contract_code."-".$map['contract_file_categories'][$contractFile->contract->type][$contractFile->category]['name'];
            }
            
            $data=array();
            if(!empty($roleIds)){
                $roleArr=explode(",",$roleIds);
                foreach ($roleArr as $roleId)
                {
                  if(empty($roleId))
                      continue;
                  $data[]=array("roleId"=>$roleId,"userId"=>0);
                }
            }else if(!empty($userIds)){
                $userArr=explode(",",$userIds);
                foreach ($userArr as $userId)
                {
                  if(empty($userId))
                      continue;
                  $data[]=array("roleId"=>0,"userId"=>$userId);
                }
            }

            foreach ($data as $v)
            {
                self::sendEmailRemind($actionId,$corporation_id,$title,$v["userId"],$v["roleId"]);
                self::sendWeixRemind($actionId,$corporation_id,$title,$v["userId"],$v["roleId"]);
            }
      
            if(!$isInDbTrans)
            {
                $trans->commit();
            }
           return 1;
    
        }catch (Exception $e) {
          if(!$isInDbTrans)
          {
            try { $trans->rollback(); }catch(Exception $ee){}
                Mod::log("发送提醒出错：".$e->getMessage(),"error"); 
                return $e->getMessage();
          }
          else 
            throw $e;
        }
    }

    public static function addTaskOrTip($actionId, $projectId, $objId, $corpId)
    {
      $task = Task::model()->find("key_value='".$projectId."' and action_id=".$actionId." and status=0");
      if(empty($task->task_id))
          TaskService::addTasks($actionId, $projectId, ActionService::getActionRoleIds($actionId), 0, $corpId);
      else
          TaskService::sendTips($actionId, $objId, ActionService::getActionRoleIds($actionId), 0);
    }*/

    /**
     * @desc 检查task是否存在
     * @param int $actionId
     * @param int $keyValue
     * @param int $roleIds
     * @param int $userIds
     * @return bool
     */
    public static function checkTaskExist($actionId,$keyValue,$roleIds=0,$userIds=0) {
        if(empty($keyValue) || empty($actionId))
            return;
        $sql = 'select * from t_task where action_id = '.$actionId.' and key_value = '.$keyValue.' and status = 0';
        if(!empty($roleId))
        {
            $sql.=" and role_id=".$roleId."";
        }
        if(!empty($userId))
        {
            $sql.=" and user_id=".$userId."";
        }
        $res = Utility::query($sql);
        if(Utility::isNotEmpty($res)) {
            return true;
        }
        return false;
    }
}