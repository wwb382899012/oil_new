<?php

/**
 * Created by PhpStorm.
 * User: Don
 * Date: 2016/11/9
 * Time: 19:52
 */
class CorporationController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="";
        $this->rightCode="corporation";
    }

    public function actionIndex(){
        $attr=$_GET[search];
        $sql="select {col} from t_corporation ".$this->getWhereSql($attr)." order by corporation_id desc {limit}";
        $data=$this->queryTablesByPage($sql,"*");
        $this->render("index",$data);
    }

    public function actionAdd(){
        $this->pageTitle="添加公司主体信息";
        $this->render("edit");
    }

    public function actionSave(){
        $params=$_POST["data"];
        $user=$this->getUser();
        if(!empty($params["corporation_id"])){
            $data=Corporation::model()->findByPk($params["corporation_id"]);
        }
        if(empty($data->corporation_id)){
            $data=new Corporation();
            $data->create_user_id=$user["user_id"];
            $data->create_time=date('Y-m-d H:i:s');
        }
        
        //$obj=Corporation::model()->findByPk("name=".$params["name"]." and code=".$params["code"]);
        $obj=Corporation::model()->find("name='".$params["name"]."'");
        if(!empty($obj->corporation_id) && $obj->corporation_id!=$data->corporation_id){
            $this->returnError("当前企业名称的公司主体已经存在，不能重复添加！");
        }

        /*if(!empty($params['bank_account'])){
            $params['bank_account'] = str_replace(' ', '', $params['bank_account']);
        }*/

        $data->setAttributes($params,false);

        $data->update_user_id=$user["user_id"];
        $data->update_time=date('Y-m-d H:i:s');
        $logRemark = ActionLog::getEditRemark($data->isNewRecord, '公司主体');
        $res=$data->save();
        if($res===true){
            //BaseCacheActiveRecord::clearCache('cor_'.$data->corporation_id);
            Utility::addActionLog(json_encode($data->oldAttributes), $logRemark, "Corporaion", $data->corporation_id);
            $this->returnSuccess($data->corporation_id);
        }else{
            $this->returnError("保存失败！".$res);
        }
    }

    public function actionEdit(){
        $id=Mod::app()->request->getParam("id");
        if(empty($id)){
            $this->returnError("信息异常！","/corporation/");
        }
        $data=Utility::query("select * from t_corporation where corporation_id=$id");
        if(Utility::isEmpty($data)){
            $this->render("当前信息不存在！","/corporation/");
        }
        $this->pageTitle="修改公司主体信息";
        $this->render("edit",array(
            "data"=>$data[0]
        ));
    }

    public function actionDel(){
        $id=Mod::app()->request->getParam("id");
        if(empty($id)){
            $this->returnError("信息异常！");
        }
        $projectArr=Project::model()->findAllToArray('corporation_id='.$id);
        if(count($projectArr)>0){
            $this->returnError("当前主体公司已有项目在使用，不能删除！");
        }
        $res=Corporation::del($id);
        if($res){
            Utility::addActionLog(null, "删除公司主体", "Corporaion", $id);
            $this->returnSuccess();
        }else{
            $this->returnError($res);
        }
    }

    public function actionDetail(){
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/corporation/");
        }

        $data=Utility::query("select * from t_corporation where corporation_id=".$id."");
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/corporation/");
        }

        $this->pageTitle="查看公司主体详情";
        $this->render('detail',array("data"=>$data[0]));
    }
}