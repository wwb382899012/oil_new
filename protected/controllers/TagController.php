<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/12/4
 * Time: 16:22
 * Describe：
 */
class TagController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="getAllChildren";
        $this->rightCode = "tag";
    }

    public function actionIndex()
    {
        $this->pageTitle="标签管理";
        $this->render('index');
    }

    public function actionGetAllChildren(){
        $id=Mod::app()->request->getParam("id");
        echo json_encode(Tag::getAllData($id));
    }

    public function actionSave()
    {
        $params = $_POST["obj"];
        $user = $this->getUser();
        if ($params["id"] != 0) {
            $obj = Tag::model()->findByPk($params["id"]);
        }

        if (!isset($obj) || !isset($obj->id)) {
            $obj = new Tag();
            $obj->create_time = date("Y-m-d H:i:s");
            $obj->create_user_id = $user["user_id"];
        }

        $obj->name = $params["name"];
        $obj->remark = $params["remark"];

        $obj->order_index = isset($params["orderIndex"]) ? $params["orderIndex"] : 0;
        $obj->parent_id = isset($params["parentId"]) ? $params["parentId"] : 0;
        $obj->status = isset($params["status"]) ? $params["status"] : 0;

        $obj->update_time = date("Y-m-d H:i:s");
        $obj->update_user_id = $user["user_id"];


        $res =$obj->save();
        if ($res === 1)
            $this->returnSuccess($obj->id);
        else
            $this->returnError($res);

    }

    public function actionDel(){
        $id=Mod::app()->request->getParam("id");

        $obj=Tag::model()->findByPk($id);
        if(!isset($obj->id))
        {
            $this->returnError("当前信息不存在！");
        }

        $res=Tag::model()->deleteByPk($id);
        if($res==1)
        {
            $this->returnSuccess("操作成功！");
        }
        else
            $this->returnError("删除失败！");
    }

}