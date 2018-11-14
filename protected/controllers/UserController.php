<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/10/22
 * Time: 15:37
 * Describe：
 */
class UserController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="";
        $this->rightCode = "systemUser";
    }

    public function actionIndex()
    {
        $attr = $_GET[search];
        $query = "";
        if(!empty($attr['corp_id'])){
            $corp_id = $attr['corp_id'];
            unset($attr['corp_id']);
            $query .= " and FIND_IN_SET(".$corp_id.",a.corp_ids)";
        }
        if(!empty($attr['role_id'])){
            $role_id = $attr['role_id'];
            unset($attr['role_id']);
            $query .= " and exists(select 1 from t_user_role_relation where user_id=a.user_id and role_id=".$role_id.")";
        }

        $sql="select {col}"
            ." from t_system_user a 
                left join t_system_role r on a.main_role_id=r.role_id"
            .$this->getWhereSql($attr);
        $sql .= $query;
        $sql .= " order by a.login_time desc,a.user_id desc {limit}";

        $data = $this->queryTablesByPage($sql,"a.*,r.role_name");

        if(!empty($corp_id))
            $attr["corp_id"]=$corp_id;
        if(!empty($role_id))
            $attr["role_id"]=$role_id;

        $this->render('index',$data);
    }


    public function actionAdd()
    {
        $this->pageTitle="添加系统用户";
        $this->render("add",array(
            "roles"=>SystemRole::getActiveRoles(),
            "corps"=>Corporation::getActiveCorporations(),
        ));

    }



    /**
     * 授权
     */
    public function actionRight()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
            $this->renderError("用户信息有误！","/user/" );
        $sql="select a.* from t_system_user a where a.user_id=".$id;
        $data=Utility::query($sql);
        if(Utility::isEmpty($data))
            $this->renderError("用户不存在！","/user/" );

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
        //$data=SystemModule::getActiveTreeTable();
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
        $obj=SystemUser::model()->findByPk($id);
        if(empty($obj->user_id))
        {
            $this->returnError("用户信息有误！");
        }
        $oldData=$obj->attributes;
        $data=json_decode($s);
        $r="#";
        $moduleDic=SystemModule::getModuleDic();
        foreach($data as $k=>$v)
        {
            $r.=$moduleDic[$k]["code"]."|".trim($v,",")."#";
        }
        $user = $this->getUser();
        $obj->right_codes=$r;
        try{
            $obj->update_time = date("Y-m-d H:i:s");
            $obj->update_user_id = $user["user_id"];
            $obj->is_right_role=0;
            $res=$obj->save();

            if($res===1)
            {
                Utility::addActionLog(json_encode($obj->oldAttributes),"修改用户权限","SystemUser",$obj->user_id);
                //SystemUser::setFormattedRightCodes($obj->user_id,$obj->right_codes);
                $this->returnSuccess();
            }
            else
                $this->returnError("保存失败：".$res);
        }
        catch(Exception $e){
            $this->returnError("保存失败：".$e->getMessage());
        }

    }

    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
            $this->renderError("用户信息有误！","/user/" );
        $sql="select a.*,b.role_name from t_system_user a left join t_system_role b on a.main_role_id=b.role_id where a.user_id=".$id;
        $data=Utility::query($sql);
        if(Utility::isEmpty($data))
            $this->renderError("用户不存在！","/user/" );

        $rightData=SystemUser::getFormattedRightCodes($data[0]["user_id"],$data[0]["right_codes"]);
        $sql = "select * from t_system_module where id in (" . $rightData["ids"] . ") and system_id=".Utility::getSystemId()." order by parent_id asc,order_index asc,id asc";
        $d=Utility::query($sql);

        $selectedObject=array();
        foreach($d as $v)
        {
            $selectedObject[$v["id"]]=$rightData["items"][strtolower($v["code"])]["actionsList"];
        }

        $this->render('detail',array("data"=>$data[0],"modules"=>$d,"selectedObject"=>$selectedObject));
    }

    public function actionDel()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
            $this->returnError("信息有误！");
        $res=SystemUser::del($id);
        if($res==1)
        {
            Utility::addActionLog("","删除用户","SystemUser",$id);
            $this->returnSuccess();
        }
        else
            $this->returnError($res);
    }

    public function actionEdit()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/user/");
        }

        $sql="select user_id,user_name,identity,weixin,name,status,remark,corp_ids,main_role_id,phone,email,role_ids,is_right_role from t_system_user a where a.user_id=".$id;
        $data=Utility::query($sql);
        if(Utility::isEmpty($data))
            $this->renderError("用户不存在！","/user/" );

        //echo SystemRole::getRightCodesWithRoles("2,3");

        $this->pageTitle="修改用户信息";
        $this->render("add",array(
            "data"=>$data[0],
            "roles"=>SystemRole::getActiveRoles(),
            "corps"=>Corporation::getActiveCorporations(),
        ));
    }

    public function actionSave()
    {
        $params = $_POST["obj"];
        $user = $this->getUser();
        if (!empty($params["userId"])) {
            $obj = SystemUser::model()->findByPk($params["userId"]);
        }

        if (empty($obj->user_id))
        {
            $obj = new SystemUser();
            $obj->create_time = date("Y-m-d H:i:s");
            $obj->create_user_id = $user["user_id"];
        }

        $obj->user_name=Utility::filterInject($params["user_name"]);
        if(!empty($params["password"]))
        {
            if($params["password"]!=$params["confirmPassword"])
                $this->returnError("密码与确认密码不一致，请重新输入！");
            $obj->password=Utility::getSecretPassword(md5($params["password"]));

            unset($params["password"]);
        }
        else
        {
            if(empty($obj->user_id))
                $this->returnError("新增用户，密码不得为空，请输入密码！");
        }

        $obj->name=Utility::filterInject($params["name"]);
        $obj->status=$params["status"];

        $obj->main_role_id=$params["main_role_id"];

        $obj->role_ids=trim($params["rIds"]);
        $obj->corp_ids=trim($params["cIds"]);
        if(empty($obj->role_ids))
            $obj->role_ids="0";
        if(empty($obj->corp_ids))
            $obj->corp_ids="0";
        if($params["isRoleRight"])
            $obj->is_right_role=1;
        else
            $obj->is_right_role=0;

        $obj->identity=trim($params["identity"]);
        $obj->weixin=trim($params["weixin"]);
        $obj->email=trim($params["email"]);
        $obj->phone=trim($params["phone"]);
        $obj->remark=trim($params["remark"]);

        $obj->update_time = date("Y-m-d H:i:s");
        $obj->update_user_id = $user["user_id"];

        $isNew=$obj->isNewRecord;

        $res=$obj->save();
        if($res===1)
        {
            if(!empty($obj->identity))
            {
                $oldIdentity=$obj->getOldAttribute("identity");
                if(empty($oldIdentity))
                    WeiXinService::createWeiXinUser($obj);
                else
                    WeiXinService::updateWeiXinUser($obj);
            }
            if($obj->is_right_role==1)
                SystemUser::setFormattedRightCodes($obj->user_id,$obj->right_codes);

            $logRemark = ActionLog::getEditRemark($isNew, "用户");
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark,"SystemUser",$obj->user_id);
            $this->returnSuccess();
        }
        else
            $this->returnError("保存失败:".$res);
    }


}