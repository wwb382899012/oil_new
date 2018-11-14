<?php

/**
 * Created by youyi000.
 * DateTime: 2016/6/27 17:18
 * Describe：
 */
class UserService
{
    /**
     * 判断指定权限码的action是否有操作权限
     * @param $rightCode
     * @param $action
     * @param int $userId
     * @return bool
     */
    public static function checkActionRight($rightCode,$action,$userId=0)
    {
        $actions=self::getRightActionKeys($userId);
        $action=strtolower($action);
        return isset($actions[strtolower($rightCode)]["actionKeys"][$action]);
    }

    /**
     * 获取用户的所有授权模块
     * @param int $userId
     * @return array
     */
    public static function getRightActionKeys($userId=0)
    {
        if(empty($userId))
            $userId=Utility::getNowUserId();

        $rightData=SystemUser::getFormattedRightCodes($userId);
        $modules=$rightData["items"];
        if(empty($modules))
            return array();

        return $modules;

    }

    /**
     * 获取用户指定权限码的action操作权限组合字符串
     * @param $rightCode
     * @param int $userId
     * @return bool|string
     */
    public static function getRightActions($rightCode,$userId=0)
    {
        if(empty($rightCode))
            return true;

        if(empty($userId))
            $userId=Utility::getNowUserId();

        $rightData=SystemUser::getFormattedRightCodes($userId);

        $module=$rightData["items"][strtolower($rightCode)];
        if(empty($module))
            return "";

        $actions="#,".strtolower($module["actions"]).",";
        return $actions;
    }


    /**
     * 获取项目管理人员
     * @return array
     */
    public static function getProjectManageUsers()
    {
        $sql="select DISTINCT a.user_id,a.user_name,a.name from  t_system_user a ,t_user_role_relation b where a.user_id=b.user_id and b.role_id in(3) and a.status=1 order by user_id desc";
        $data=Utility::query($sql);

        return $data;
    }

    /**
     * 获取合作方会议评审人员
     * @return array
     */
    public static function getPartnerReviewUsers()
    {
        $sql="select user_id,user_name,name from t_system_user where status=1 order by user_id asc ";//b.role_id in(2,3,4,5,6,7,8) and 
        $data=Utility::query($sql);

        return $data;
    }

    /**
     * 获取系统全部角色
     * @return array
     */
    public static function getAllRoles()
    {
        $sql="select role_id,role_name from  t_system_role where status=1 order by role_id asc";
        $data=Utility::query($sql);

        return $data;
    }

    public static function getRiskManageUsers()
    {
        $sql="select a.user_id,a.user_name,a.name from  t_system_user a ,t_user_role_relation b where a.user_id=b.user_id and b.role_id in(5,6,11) and a.status=1 order by user_id desc";
        $data=Utility::query($sql);

        return $data;
    }

    public static function getReviewManageUsers()
    {
        $sql="select DISTINCT(a.user_id),a.user_name,a.name from  t_system_user a ,t_user_role_relation b where a.user_id=b.user_id and b.role_id in(3,4,5,6,8,9,10,11,12) and a.status=1 order by user_id desc";
        $data=Utility::query($sql);

        return $data;
    }

    /*public static function getManageUsers()
    {
        $sql="select DISTINCT(a.user_id),a.user_name,a.name from  t_system_user a ,t_user_role_relation b where a.user_id=b.user_id and a.status=1 order by user_id desc"; //b.role_id in(3,4,5,6,8,9,10,11,12) and 
        $data=Utility::query($sql);

        return $data;
    }*/

    /**
     * @desc 获取业务主管
     */
	public static function getBusinessDirectors()
	{
		$sql = "select DISTINCT a.user_id,a.user_name,a.name,a.corp_ids from  t_system_user a ,t_user_role_relation b where a.user_id=b.user_id and b.role_id in(3) and a.status=1 order by user_id desc";
//		$sql = "select DISTINCT user_id,user_name,name from t_system_user where main_role_id=3 and status=1 order by user_id desc";
		$data=Utility::query($sql);

		return $data;
	}

    /**
     * @desc 获取业务主管
     */
    public static function getBusinessDirectorsByCorp($corpId)
    {
        $sql = "select DISTINCT a.user_id,a.user_name,a.name,a.corp_ids from  t_system_user a ,t_user_role_relation b where a.user_id=b.user_id and b.role_id in(3) and a.status=1 and find_in_set('".$corpId."',a.corp_ids) order by user_id desc";
//		$sql = "select DISTINCT user_id,user_name,name from t_system_user where main_role_id=3 and status=1 order by user_id desc";
        $data=Utility::query($sql);

        return $data;
    }

	/**
	 * @desc 获取风控人员
	 */
	public static function getRiskUsers()
	{
		$sql = "select DISTINCT a.user_id,a.user_name,a.name from  t_system_user a ,t_user_role_relation b where a.user_id=b.user_id and b.role_id in(6) and a.status=1 order by user_id desc";
		$data=Utility::query($sql);
        return Utility::sortByFields($data,"name asc",true);
	}

    /**
     * 获取用户名
     * @param $id
     * @return array
     */
    public static function getUsername()
    {
        $sql="select user_id,name from t_system_user order by user_id asc";
        $data=Utility::query($sql);
        $users=array();
        foreach($data as $v)
        {
            $users[$v["user_id"]]=$v;
        }
        return $users;
    }

    /**
     * 获取用户的所有角色
     * @param int $userId
     * @return mixed
     */
    public static function getUserRoles($userId=0)
    {
        if(empty($userId))
            $userId=Utility::getNowUserId();
        return SystemUser::getRoles($userId);
    }


    /**
     * 获取角色名称
     * @param $roleIds
     * @param string $separator
     * @return null|string
     */
    public static function getRoleNames($roleIds,$separator=" ")
    {
        if(empty($roleIds))
            return null;
        $sql="select role_id,role_name from t_system_role where role_id in(".$roleIds.")";
        $data=Utility::query($sql);
        $names="";
        foreach($data as $v)
        {
            if(empty($names))
                $names=$v["role_name"];
            else
                $names.=$separator.$v["role_name"];
        }
        return $names;
    }

    /**
     * 获取主体公司名称
     * @param $corpIds
     * @param string $separator
     * @return null|string
     */
    public static function getCorpNames($corpIds,$separator=" ")
    {
        if(empty($corpIds))
            return null;
        $sql="select corporation_id,name from t_corporation where corporation_id in(".$corpIds.")";
        $data=Utility::query($sql);
        $names="";
        foreach($data as $v)
        {
            if(empty($names))
                $names=$v["name"];
            else
                $names.=$separator.$v["name"];
        }
        return $names;
    }

    /**
     * 获取当前用户可选的公司主体
     * @return array
     */
    public static function getUserSelectedCorporations()
    {
        $user = SystemUser::getUser(Utility::getNowUserId());
        $sql="select corporation_id,name,code from t_corporation where status=1 and corporation_id in(".$user['corp_ids'].") order by corporation_id desc";
        return Utility::query($sql);
    }

    /**
     * 获取用户名
     * @param $userId
     * @return null
     */
    public static function getUsernameById($userId)
    {
        if (empty($userId))
            return null;
        $user = SystemUser::getUser($userId);
        return $user["user_name"];
    }

    /**
     * 获取用户的主体公司id组合
     * @param $userId
     * @return null
     */
    public static function getUserCorpIds($userId)
    {
        if (empty($userId))
            return null;
        $user = SystemUser::getUser(Utility::getNowUserId());
        if(empty($user["corp_ids"]))
            return "0";
        else
            return $user["corp_ids"];
    }

    /**
     * 获取用户姓名
     * @param $userId
     * @return null
     */
    public static function getNameById($userId)
    {
        if(empty($userId))
            return null;
        $user=SystemUser::getUser($userId);
        return $user["name"];

    }


    /**
     * 获取用户主角色
     * @param $id
     * @return int
     */
    public static function getUserMainRoleId($id)
    {
        return SystemUser::getMainRoleId($id);
        /*$user=SystemUser::getUser($id);
        return $user["main_role_id"];*/
        /*$sql="select main_role_id from t_system_user where user_id=".$id."";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
            return $data[0]["main_role_id"];
        else
            return 0;*/
    }

    /**
     * 获取当前用户的主角色ID
     * @return int
     */
    public static function getNowUserMainRoleId()
    {
        return UserService::getUserMainRoleId(Utility::getNowUserId());
    }

    /**
     * 根据角色ID获取用户名
     */
    public static function getUserByRoleId($roleId,$corpId=0)
    {
        $query = "";
        if(empty($roleId))
            return array();
       /* if(!empty($corpId))
            $query = " and FIND_IN_SET(".$corpId.",a.corp_ids) ";*/
       /* $sql = "select a.user_id,a.user_name,a.name,a.identity,b.role_id,b.role_name
                from t_system_user a ,t_system_role b 
                where FIND_IN_SET(".$roleId.",a.role_ids) ".$query." and b.role_id=".$roleId;*/
        $sql="select a.user_id,a.user_name,a.name,a.identity,a.weixin,b.role_id,b.role_name 
              from t_system_user a,t_user_role_relation r,t_system_role b 
              where a.user_id=r.user_id and b.role_id=r.role_id
              and r.role_id=".$roleId;
        if(!empty($corpId))
        {
            $sql.=" and FIND_IN_SET(".$corpId.",a.corp_ids)";
            //$sql.=" and a.corporation_id=".$corpId." ";
        }
        return Utility::query($sql);


    }

    /*public static function getMyCheckUrl()
    {
        $roldId=self::getNowUserMainRoleId();
        $url="";
        switch($roldId)
        {
            case 4:
            case 10:
            case 11:
                $url="/check1/";
                break;
            case 8:
                $url="/check2/";
                break;
            case 8:
                $url="/check2/";
                break;
        }
    }*/


    /**
     * 获取项目人员角色ID
     * @return int
     */
    public static function getProjectRoleId()
    {
        return 3;
    }

    /**
     * 获取项目主管角色ID
     * @return int
     */
    public static function getProjectChiefRoleId()
    {
        return 4;
    }

    /**
     * 获取初审财务角色ID
     * @return int
     */
    public static function getAccountantCheckRoleId()
    {
        return 10;
    }

    /**
     * 获取初审风控角色ID
     * @return int
     */
    public static function getRiskCheckRoleId()
    {
        return 11;
    }

    /**
     * 获取财务角色ID
     * @return int
     */
    public static function getFinanceRoleId()
    {
        return 6;
    }

    /**
     * 获取法务角色ID
     * @return int
     */
    public static function getLawRoleId()
    {
        return 8;
    }

    /**
     * 获取商务支持角色ID
     * @return int
     */
    public static function getBusinessRoleId()
    {
        return 4;
    }

    /**
     * 获取风控角色ID
     * @return int
     */
    public static function getRiskRoleId()
    {
        return 7;
    }

	/**
	 * 获取风控主管角色ID
	 * @return int
	 */
	public static function getRiskManagerRoleId() {
		return 9;
    }

    /**
     * 获取业务助理角色ID
     * @return int
     */
    public static function getAssistantsRoleId()
    {
        return 2;
    }

	/**
	 * @desc 获取管理员角色ID
	 * @return int
	 */
	public static function getAdminRoleId() {
		return 1;
    }


    public static function setMainRoleId($roleId,$userId=0)
    {
        if(empty($userId))
            $userId=Utility::getNowUserId();
        SystemUser::setMainRoleId($roleId,$userId);
    }

    public static function getMainRoleId($userId=0)
    {
        if(empty($userId))
            $userId=Utility::getNowUserId();
        return SystemUser::getMainRoleId($userId);
    }
}