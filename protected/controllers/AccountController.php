<?php

/**
 * Created by PhpStorm.
 * User: vector
 * Date: 2016/11/9
 * Time: 19:52
 */
class AccountController extends Controller
{
    public function pageInit()
    {
        //$this->filterActions="";
        $this->rightCode="account";
        //$this->publicActions=array("add");
    }

    public function actionIndex(){
        /*$this->renderNewWeb();
		return;*/
		$attr=$_GET[search];
        $user = Utility::getNowUser();
        $sql="select {col} from t_account a left join t_corporation b on a.corporation_id=b.corporation_id ".$this->getWhereSql($attr)." and ".AuthorizeService::getUserDataConditionString("a")." order by account_id desc {limit}";
        if(!empty($user['corp_ids'])) {
            $data=$this->queryTablesByPage($sql,'a.*,b.name as corporation_name');
        }else{
            $data = array();
        }
        $this->render("index",$data);
    }

    public function actionAdd(){
        $this->pageTitle="添加信息";
        $this->render("edit");
    }

    public function actionSave(){
        $params=$_POST["obj"];
        $user=$this->getUser();
        if(empty($params['account_no']) || empty($params['bank_name'])){
            $this->returnError("信息异常！");
        }

        $params['account_no'] = str_replace(' ', '', $params['account_no']);
        
        if (!empty($params["account_id"])) {
            $obj=Account::model()->findByPk($params["account_id"]);
        }

        if(empty($obj->account_id)){
            $obj=new Account;
            $obj->create_user_id=$user["user_id"];
            $obj->create_time=date('Y-m-d H:i:s');
        }

        $data=Account::model()->find("account_no='".$params["account_no"]."'");
        if(!empty($data->account_id) && $obj->account_id!=$data->account_id){
            $this->returnError("当前银行账号已经存在，不能重复添加！");
        }

        $co = Corporation::model()->findByPk($params['corporation_id']);
        $params['account_name'] = $co->name;
        
        unset($params['account_id']);
        $obj->setAttributes($params,false);

        $obj->update_user_id=$user["user_id"];
        $obj->update_time=date('Y-m-d H:i:s');
        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "公司账户");
        $res=$obj->save();
        if($res===true){
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "Account", $obj->account_id);
            $this->returnSuccess($obj->account_id);
        }else{
            $this->returnError("保存失败！".$res);
        }
    }

    public function actionEdit(){
        $id=Mod::app()->request->getParam("id");
        if(empty($id)){
            $this->renderError("信息异常！","/account/");
        }
        $data=Utility::query("select * from t_account where account_id=$id");
        if(Utility::isEmpty($data)){
            $this->renderError("当前信息不存在！","/account/");
        }
        $this->pageTitle="修改公司账户信息";
        $this->render("edit",array(
            "data"=>$data[0]
        ));
    }

    public function actionDel(){
        $id=Mod::app()->request->getParam("id");
        if(empty($id)){
            $this->returnError("信息异常！");
        }
        $res=Account::del($id);
        if($res){
            Utility::addActionLog(null, "删除公司账户", "Account", $id);
            $this->returnSuccess();
        }else{
            $this->returnError($res);
        }
    }

    public function actionDetail(){
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/account/");
        }

        $data=Utility::query("select * from t_account where account_id=".$id);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/account/");
        }

        $this->pageTitle="查看公司账户详情";
        $this->render('detail',array("data"=>$data[0]));
    }
}