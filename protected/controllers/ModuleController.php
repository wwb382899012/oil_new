<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/10/21
 * Time: 16:41
 * Describe：
 *      系统模块
 */
class ModuleController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="getSelect,getActive";
        $this->rightCode = "systemModule";
        $this->pageTitle="系统模块管理";
    }

    public function actionIndex()
    {
        $data=SystemModule::getTreeTable();

        $this->render('index',array("data"=>$data));
    }

    public function actionAdd()
    {
        $this->render('add');
    }

    public function actionGetSelect()
    {
        $id = Mod::app()->request->getParam("id");
        $sql = "select * from t_system_module where id<>".$id." and system_id=".Utility::getSystemId()." and parent_ids not like '%," . $id . ",%'  order by parent_id asc,order_index asc,id asc";
        $data = Utility::query($sql);
        echo json_encode($data);
    }

    public function actionGetActive()
    {
        echo json_encode(SystemModule::getAllActiveData());
    }

    public function actionEdit()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("信息异常！", "/module/");
        }
        $obj=SystemModule::model()->findByPk($id);
        if(empty($obj->id))
            $this->renderError("当前信息不存在！", "/module/");

        $this->pageTitle="修改模块";
        $this->render("add",array(
            "data"=>$obj->attributes
        ));
    }

    public function actionSave()
    {
        $params = $_POST["obj"];
        $user = $this->getUser();

        if (!empty($params["id"]))
        {
            if(!Utility::checkQueryId($params["id"]))
                $this->returnError("id有误！");
            $obj = SystemModule::model()->findByPk($params["id"]);
        }



        if (empty($obj->id))
        {
            $obj = new SystemModule();
            $obj->create_time = date("Y-m-d H:i:s");
            $obj->create_user_id = $user["user_id"];
        }
        unset($params["id"]);
        $obj->setAttributes($params,false);

        $obj->update_time = date("Y-m-d H:i:s");
        $obj->update_user_id = $user["user_id"];

        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "模块");
        $res=$obj->save();
        if($res===1) {
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, 'SystemModule', $obj->id);
            $this->returnSuccess($obj->id);
        }
        else
            $this->returnError("保存失败:".$res);
    }

    public function actionDel()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->returnError("非法参数！");

        $res=SystemModule::del($id);
        if($res==1) {
            Utility::addActionLog(null, ActionLog::TYPE_DEL, "SystemModule", $id);
            $this->returnSuccess();
        }
        else
            $this->returnError($res);
    }
    
    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("信息异常！", "/module/");
        }
        $obj=SystemModule::model()->findByPk($id);
        if(empty($obj->id))
            $this->renderError("当前信息不存在！", "/module/");

        $this->pageTitle="模块详情";
        $this->render("detail",array(
            "data"=>$obj->attributes
        ));
    }
}