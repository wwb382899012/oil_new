<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/12/10
 * Time: 9:51
 * Describe：
 */
class SystemRole extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_system_role';
    }


    /**
     * 保存
     * @return mixed
     */
    public function save($runValidation=true,$attributes=null)
    {
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {

            $this->right_codes=strtolower($this->right_codes);
            $this->update_time=date("Y-m-d H:i:s");

            parent::save();

            $trans->commit();
            return 1;
        } catch (Exception $e) {
            try {
                $trans->rollback();
            }catch(Exception $ee){}
            return $e->getMessage();
        }
    }


    /**
     * 删除成功返回1，否则返回错误信息或0
     * @return int|string
     */
    public static function del($id)
    {
        if(empty($id))
            return "id不能为空！";
        if(!Utility::isIntString($id))
            return "非法Id";

        $sql="delete from t_system_role where role_id=".$id." ";
        $res=Utility::execute($sql);
        if($res==1)
        {
            return 1;
        }
        else
            return "操作失败！";
    }

    public static function getFormattedRightCodes($rightCodes)
    {
        //$redis = Mod::app()->redis;
        /*if($redis->hExists(SystemUser::RedisSystemRightKey,$userId))
            return json_decode($redis->hGet(SystemUser::RedisSystemRightKey,$userId));*/

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
                $rightData[strtolower($arr[0])]=array("id"=>0,"code"=>$arr[0],"actions"=>$arr[1],"actionsList"=>$actions);
                $r=$r."'".$arr[0]."',";
            }
        }
        $ids="0";
        $moduleData=array();
        if($r!="")
        {
            $r = trim($r, ",");
            $sql = "select * from t_system_module where status=1 and code in (" . $r . ") and system_id=".Utility::getSystemId()." order by parent_id asc,order_index asc,id asc";
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

        //$redis->hSet(SystemUser::RedisSystemRightKey,$userId,json_encode($data));
        return $data;
    }

    public static function getActiveRoles()
    {
        $sql="select * from t_system_role where status=1 order by order_index asc,role_id asc";
        return Utility::query($sql);
    }

    /**
     * 获取角色权限的并集
     * @param $roleIds
     * @return string
     */
    public static function getRightCodesWithRoles($roleIds)
    {
        $sql="select * from t_system_role where status=1 and role_id in(".$roleIds.")";
        $data= Utility::query($sql);
        $r=array();
        foreach($data as $v)
        {
            $rightCodes=strtolower(str_replace("##","#",$v["right_codes"]));
            $codes=explode("#",$rightCodes);
            foreach($codes as $c)
            {
                $arr=explode("|",$c);
                if(!empty($arr[0]))
                {
                    $actionList=array();
                    if(array_key_exists($arr[0],$r))
                    {
                        $actionList=$r[$arr[0]];
                    }

                    $actions=explode(",",$arr[1]);
                    foreach($actions as $a)
                    {
                        if(!in_array($a,$actionList))
                            $actionList[]=$a;
                    }
                    $r[$arr[0]]=$actionList;

                }
            }
        }
        $rightCodes="";
        //var_dump($r);
        foreach($r as $k=>$v)
        {
            $rightCodes.="#".$k."|";

            foreach($v as $a)
            {
                $rightCodes.=$a.",";
            }
            $rightCodes=trim($rightCodes,",");
            $rightCodes.="#";
        }

        return $rightCodes;
    }

    /**
     * 更新当前角色下用户的权限信息，只更新跟随角色变更权限的用户
     * @param $roleId
     */
    public static function updateUserRightCode($roleId)
    {
        $sql="select * from t_system_user where is_right_role=1 and user_id in (select user_id from t_user_role_relation where role_id=".$roleId.")";
        $data=Utility::query($sql);
        $sqls=array();
        foreach($data as $v)
        {
            $right_codes=strtolower(SystemRole::getRightCodesWithRoles($v["role_ids"]));
            $sqls[]="update t_system_user set right_codes='".$right_codes."' where user_id=".$v["user_id"]."";
        }
        Utility::execute($sqls);
        SystemUser::clearCache();
    }
}