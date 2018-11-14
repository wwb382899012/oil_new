<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/12/10
 * Time: 9:37
 * Describe：
 */
class RoleController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="";
        $this->rightCode = "systemRole";
    }

    public function actionIndex()
    {
        $attr = $_GET[search];

        $sql="select {col}"
            ." from t_system_role"
            .$this->getWhereSql($attr);
        $sql .= " order by role_id desc {limit}";

        $data = $this->queryTablesByPage($sql,"*");

        $this->render('index',$data);
    }

    public function actionAdd()
    {
        $this->pageTitle="添加角色";
        $this->render("edit");
    }

    public function actionSave()
    {
        $params = $_POST["obj"];
        $user = $this->getUser();
        if (!empty($params["role_id"])) {
            $obj = SystemRole::model()->findByPk($params["role_id"]);
        }

        if (empty($obj->role_id))
        {
            $obj = new SystemRole();
            $obj->create_time = date("Y-m-d H:i:s");
            $obj->create_user_id = $user["user_id"];
        }

        $obj2=SystemRole::model()->find("role_name='".$params["role_name"]."'");
        if(!empty($obj2->role_id) && $obj2->role_id!=$obj->role_id)
        {
            $this->returnError("当前角色已经存在，不能重复添加！".$obj->role_id);
        }

        unset($params["role_id"]);
        $obj->setAttributes($params,false);

        $obj->update_time = date("Y-m-d H:i:s");
        $obj->update_user_id = $user["user_id"];
        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "角色");
        $res=$obj->save();
        if($res===1)
        {
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "SystemRole", $obj->role_id);
            $this->returnSuccess($obj->role_id);
        }
        else
            $this->returnError("保存失败:".$res);

    }

    public function actionEdit()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/role/");
        }

        $data=Utility::query("select role_id,role_name,order_index,remark,status from t_system_role where role_id=".$id."");
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/role/");
        }

        $this->pageTitle="修改角色";
        $this->render("edit",array(
            "data"=>$data[0]
        ));
    }

    public function actionDel()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
            $this->returnError("信息有误！");
        $res=SystemRole::del($id);
        if($res==1) {
            Utility::addActionLog(null, ActionLog::TYPE_DEL."角色", "SystemRole", $id);
            $this->returnSuccess();
        }
        else
            $this->returnError($res);
    }

    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/role/");
        }

        $data=Utility::query("select a.* from t_system_role a where a.role_id=".$id."");
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/role/");
        }


        $rightData=SystemRole::getFormattedRightCodes($data[0]["right_codes"]);

        $sql = "select * from t_system_module where id in (" . $rightData["ids"] . ") and status=1 and system_id=".Utility::getSystemId()." order by parent_id asc,order_index asc,id asc";
        $d=Utility::query($sql);

        $selectedObject=array();
        foreach($d as $v)
        {
            $selectedObject[$v["id"]]=$rightData["items"][strtolower($v["code"])]["actionsList"];
        }

        $this->pageTitle="查看角色详情";
        $this->render('detail',array("data"=>$data[0],"modules"=>$d,"selectedObject"=>$selectedObject));

    }


    /**
     * 授权
     */
    public function actionRight()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
            $this->renderError("信息有误！","/role/" );
        $sql="select * from t_system_role where role_id=".$id;
        $data=Utility::query($sql);
        if(Utility::isEmpty($data))
            $this->renderError("角色不存在！","/role/" );

        $rightCodes=str_replace("##","#",strtolower($data[0]["right_codes"]));
        $codes=explode("#",$rightCodes);
        $rightData=array();
        $r="";
        foreach($codes as $v)
        {
            $arr=explode("|",$v);
            if(!empty($arr[0]))
            {
                $actions=explode(",",$arr[1]);
                $rightData[strtolower($arr[0])]=array("id"=>0,"code"=>$arr[0],"actions"=>$actions);
                $r=$r."'".$arr[0]."',";
            }
        }
        $selectedObject=array();
        if($r!="")
        {
            $r = trim($r, ",");
            $sql = "select * from t_system_module where code in (" . $r . ")";
            $d=Utility::query($sql);
            foreach($d as $v)
            {
                $rightData[strtolower($v["code"])]["id"]=$v["id"];
                $selectedObject[$v["id"]]=$rightData[strtolower($v["code"])]["actions"];
            }
        }

        $this->render('right',array("data"=>$data[0],"selectedObject"=>$selectedObject));
    }

    public function actionSaveRight()
    {
        $id=Mod::app()->request->getParam("id");
        $s=Mod::app()->request->getParam("data");
        if(empty($id))
        {
            $this->returnError("用户信息有误！");
        }
        $obj=SystemRole::model()->findByPk($id);
        if(empty($obj->role_id))
        {
            $this->returnError("角色信息有误！");
        }
        $oldRight = $obj->right_codes;
        $data=json_decode($s);
        $r="#";
        $moduleDic=SystemModule::getModuleDic();
        foreach($data as $k=>$v)
        {
            $r.=$moduleDic[$k]["code"]."|".trim($v,",")."#";
        }
        $user = $this->getUser();
        $obj->right_codes=$r;
        $obj->update_time = date("Y-m-d H:i:s");
        $obj->update_user_id = $user["user_id"];
        $res=$obj->save();
        if($res===1)
        {
            //$this->endRequestSuccess();
            SystemRole::updateUserRightCode($obj->role_id);
            Utility::addActionLog($oldRight, "更新权限", "SystemUser", $obj->role_id);
            $this->returnSuccess();
        }
        else
            $this->returnError("保存失败:".$res);
    }


}