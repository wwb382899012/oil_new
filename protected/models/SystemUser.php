<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/10/22
 * Time: 16:00
 * Describe：
 */
class SystemUser extends BaseActiveRecord
{

    const LoginSecretKey = '030da3d79hf3b03fc6cf14c0b1bd309d5';

    /**
     * 用户权限的Redis缓存名
     */
    const RedisSystemRightKey="NewOilSystemRight";

    /**
     * 用户信息的Redis缓存名
     */
    const RedisSystemUserKey="NewOilSystemUser";

    /**
     * session缓存的Redis值名
     */
    const RedisSessionKey="new_oil_system_session_user_id_map";

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 保存
     * @return mixed
     */
    public function save($runValidation=true,$attributes=null)
    {
        $obj=SystemUser::model()->find("user_name='".$this->user_name."'");
        if(!empty($obj->user_id) && $obj->user_id!=$this->user_id)
            return "当前用户名的用户已经存在，请重新填写用户名！";
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {

            if( $this->is_right_role==1)
                $this->right_codes=SystemRole::getRightCodesWithRoles($this->role_ids);
            $this->right_codes=strtolower($this->right_codes);
            $this->update_time=date("Y-m-d H:i:s");

            parent::save();

            $ids = $this->role_ids;
            if(empty($ids))
                $ids=$this->main_role_id;
            else
                $ids.=",".$this->main_role_id;
            //处理角色
            if (!empty($ids)) {
                $idArray = explode(',', $ids);

                $ids = "0";
                foreach ($idArray as $v) {
                    if (isset($v) && $v != 0) {
                        if (strpos($ids . ",", "," . $v . ",") > 0)
                            continue;
                        $obj = UserRoleRelation::model()->find("user_id='" . $this->user_id . "' and role_id='" . $v . "'");
                        if (!isset($obj->id)) {
                            $obj = new UserRoleRelation();
                            $obj->user_id = $this->user_id;
                            $obj->role_id = $v;
                            $obj->update_time = date("Y-m-d H:i:s");
                            $obj->save();
                        }
                        $ids = $ids . "," . $v;
                    }
                }
                $sql = "delete from t_user_role_relation where user_id='" . $this->user_id . "' and role_id not in(" . $ids . ")";
                Utility::execute($sql);
                $this->role_ids = $ids;
                /*if ($ids != "0") {
                    $this->role_ids = $ids;
                    //$this->save();
                }*/
            }

            $trans->commit();
            SystemUser::setFormattedRightCodes($this->user_id,$this->right_codes);
            self::clearUserCache($this->user_id);
            self::setUserCache($this->user_id);
            self::setMainRoleId($this->main_role_id,$this->user_id);
            return 1;
        } catch (Exception $e) {
            try {
                $trans->rollback();
            }catch(Exception $ee){}
            return $e->getMessage();
        }
    }

    public function tableName()
    {
        return 't_system_user';
    }

    /**
     * 清除用户权限相关的缓存
     * @param int $userId
     */
    public static function clearCache($userId=0)
    {
        $redis = Mod::app()->redis;
        if(empty($userId))
        {
            $redis->delete(SystemUser::RedisSystemRightKey);
        }
        else
        {
            $redis->hDel(SystemUser::RedisSystemRightKey,$userId);
            $redis->hDel(SystemUser::RedisSystemUserKey,"role_".$userId);
        }
    }

    /**
     * 清除用户缓存
     * @param int $userId
     */
    public static function clearUserCache($userId=0)
    {
        $redis = Mod::app()->redis;
        if(empty($userId))
        {
            $redis->delete(SystemUser::RedisSystemUserKey);
        }
        else
        {
            $redis->hDel(SystemUser::RedisSystemUserKey,$userId);
            $redis->hDel(SystemUser::RedisSystemUserKey,"role_".$userId);
        }
    }

    /**
     * 获取格式化的用户权限码信息，如果没有，则直接重新生成缓存
     *      优先从redis缓存获取，如果没有缓存，直接读取数据库获取
     * @param $userId
     * @param string $rightCodes
     * @return array|mixed
     */
    public static function getFormattedRightCodes($userId,$rightCodes="")
    {
        if(empty($userId))
            return array();

        $redis = Mod::app()->redis;
        if($redis->hExists(SystemUser::RedisSystemRightKey,$userId))
            return json_decode($redis->hGet(SystemUser::RedisSystemRightKey,$userId),true);

        $obj=SystemUser::model()->findByPk($userId);
        if(empty($obj->user_id))
            return array();
        return SystemUser::setFormattedRightCodes($userId,$obj->right_codes);
    }

    /**
     * 设置格式化后的用户权限码，存入Redis
     * @param $userId
     * @param $rightCodes
     * @return array
     */
    public static function setFormattedRightCodes($userId,$rightCodes)
    {
        if(empty($userId))
            return array();

        $redis = Mod::app()->redis;

        $rightCodes=str_replace("##","#",$rightCodes);
        $codes=explode("#",$rightCodes);
        $rightData=array();
        $r="";
        foreach($codes as $v)
        {
            $arr=explode("|",$v);
            if(!empty($arr[0]))
            {
                $actions=explode(",",$arr[1]);
                $rightData[strtolower($arr[0])]=array("id"=>0,"code"=>$arr[0],"actions"=>$arr[1],"actionsList"=>$actions,"actionKeys"=>array_flip($actions));
                $r=$r."'".$arr[0]."',";
            }
        }
        $ids="0";
        $moduleData=array();
        if($r!="")
        {
            $r = trim($r, ",");
            $sql = "select * from t_system_module where code in (" . $r . ") and status=1 and system_id=".Utility::getSystemId()." order by parent_id asc,order_index asc,id asc";

            $d=Utility::query($sql);
            foreach($d as $v)
            {
                $ids.=",".$v["id"];
                $rightData[strtolower($v["code"])]["id"]=$v["id"];
            }
            $moduleData=SystemModule::getTreeNode($d,0);
        }

        $data=array(
            "ids"=>$ids,
            "codes"=>$r,
            "tree"=>$moduleData,
            "items"=>$rightData
        );

        $redis->hSet(SystemUser::RedisSystemRightKey,$userId,json_encode($data));
        return $data;
    }

    public static function setCookies($user_account,$user_name,$user_id)
    {
        $prefix=Mod::app()->params['prefix'];
        Utility::setCookie($prefix."system_account",$user_account);
        Utility::setCookie($prefix."system_user_name",$user_name);
        Utility::setCookie($prefix."system_user_id",$user_id);
    }

    /**
     * 清除登陆的Cookies
     */
    public static function clearCookies()
    {
        $prefix=Mod::app()->params['prefix'];
        setcookie($prefix.'system_account',false,time()-3600,'/',Mod::app()->params['url_host']);
        setcookie($prefix.'system_user_name',false,time()-3600,'/',Mod::app()->params['url_host']);
        setcookie($prefix.'system_user_id',false,time()-3600,'/',Mod::app()->params['url_host']);
    }

    /**
     * 更新登陆信息
     * @param $userId
     */
    public static function updateLoginInfo($userId)
    {
        $sql="update t_system_user set login_time=now(),login_count=login_count+1 where user_id=".$userId."";
        Utility::execute($sql);
    }

    /**
     * 删除用户
     * @param $id
     * @return int|string
     */
    public static function del($id)
    {
        if(empty($id))
            return "id不能为空！";
        if(!Utility::isIntString($id))
            return "非法Id";

        /*$sql="delete from t_system_user where user_id=".$id."";
        $res=Utility::execute($sql);*/
        $res=SystemUser::model()->deleteByPk($id);
        if($res==1)
        {
        	UserExtra::model()->deleteByPk($id);
            self::clearUserCache($id);
            self::clearCache($id);
            return 1;
        }
        else
            return "操作失败！";
    }

    /**
     * 设置用户基本信息缓存
     * @param int $userId
     * @param null $data
     */
    public static function setUserCache($userId=0,$data=null)
    {
        $redis = Mod::app()->redis;

        if(!empty($userId) && !empty($data))
        {
            $redis->hSet(SystemUser::RedisSystemUserKey,$userId,json_encode($data));
        }
        else
        {
            $sql="select user_id,user_name,name,email,phone,main_role_id,role_ids,corp_ids,identity,weixin from t_system_user";
            if(!empty($userId))
            {
                $sql.=" where user_id=".$userId."";
            }
            $data=Utility::query($sql);
            foreach($data as $v)
            {
                $redis->hSet(SystemUser::RedisSystemUserKey,$v["user_id"],json_encode($v));
            }
        }
    }

    /**
     * 获取用户基本信息
     * @param $userId
     * @return mixed
     */
    public static function getUser($userId)
    {
        $redis = Mod::app()->redis;
        if(!$redis->hExists(SystemUser::RedisSystemUserKey,$userId))
        {
            self::setUserCache($userId);
        }

        return json_decode($redis->hGet(SystemUser::RedisSystemUserKey,$userId),true);
    }

    public static function getEmail($userId)
    {
        $user=self::getUser($userId);
        return $user["email"];
    }
	
	//根据用户id获取用户名
	public static function getUserNameById($userId)
	{
		$user=self::getUser($userId);
		return $user["user_name"];
	}

    /**
     * [getNameById 获取用户姓名]
     * @param
     * @param  [int] $userId [用户id]
     * @return [string]
     */
    public static function getNameById($userId)
    {
        $user=self::getUser($userId);
        return $user["name"];
    }

    /**
     * 获取用户的所有角色
     * @param $userId
     * @return mixed
     */
    public static function getRoles($userId)
    {
        $redis = Mod::app()->redis;
        $keyName="role_".$userId;
        if(!$redis->hExists(SystemUser::RedisSystemUserKey,$keyName))
        {
            $user=SystemUser::getUser($userId);
            $roleIds=empty($user["role_ids"])?"0":$user["role_ids"];
            if(!empty($user["main_role_id"]))
                $roleIds.=",".$user["main_role_id"];

            $sql="select role_id,role_name from t_system_role where role_id in(".$roleIds.")";
            $data=Utility::query($sql);
            $roles=array();
            foreach ($data as $v)
            {
                $roles[$v["role_id"]]=$v;
            }
            $redis->hSet(SystemUser::RedisSystemUserKey,$keyName,json_encode($roles));
        }

        return json_decode($redis->hGet(SystemUser::RedisSystemUserKey,$keyName),true);
    }

       public static function setMainRoleId($roleId,$userId)
    {
        $redis = Mod::app()->redis;
        $redis->hSet(SystemUser::RedisSystemUserKey,"main_role_".$userId,$roleId);
    }

    public static function getMainRoleId($userId)
    {
        $redis = Mod::app()->redis;
        $keyName="main_role_".$userId;
        if($redis->hExists(SystemUser::RedisSystemUserKey,$keyName))
        {
            return $redis->hGet(SystemUser::RedisSystemUserKey,$keyName);
        }
        else
        {
            $user=SystemUser::getUser($userId);
            return $user["main_role_id"];
        }

    }

    /**
     * 获取用户指定权限码的操作action数组
     * @param $rightCode
     * @param int $id
     * @return mixed
     */
    public static function getAuthorizedActions($rightCode,$id=0)
    {
        if(empty($id))
            $id=!empty(Mod::app()->user->id)?Mod::app()->user->id:0;
        if(empty($id) || empty($rightCode))
            return array();
        $rightData=self::getFormattedRightCodes($id);
        return $rightData["items"][strtolower($rightCode)]["actionsList"];

    }

	/**
	 * @desc 获取附件信息
	 * @param $id | int
	 * @param $attachType | string map中的attachment key
	 * @return array
	 */
	public static function getUserAttachments($id, $attachType) {
		if (empty($id)) {
			return array();
		}
		if ($attachType == Attachment::C_USER_EXTRA) {
			$sql = "select * from t_user_attachment where base_id=" . $id . " and status=1 and type>=4000 and type<4050 order by type asc";
		} else {
			if ($attachType == Attachment::C_USER_CREDIT) {
				$sql = "select * from t_user_attachment where base_id=" . $id . " and status=1 and type>=4050 and type<4099 order by type asc";
			}
		}
		$data = Utility::query($sql);
		if (Utility::isEmpty($data)) {
			return array();
		}
		$attachments = array();
		foreach ($data as $v) {
			$attachments[$v["type"]][] = $v;
		}

		return $attachments;
	}

	public static function getUsersByRoleIds(Array $roleIds) {
	    if (empty($roleIds))
	        return array();

	    $roleWhere = array();
	    foreach ($roleIds as $roleId) {
            $roleWhere[] = "FIND_IN_SET($roleId, role_ids)";
        }

        $sql = 'select * from t_system_user where (' . join($roleWhere, ' OR ') . ')';
        $data = Utility::query($sql);
        if (Utility::isEmpty($data))
            return array();

        return $data;
    }
}