<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/11 11:01
 * Describe：
 */
class CreditApplyController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="";
        $this->rightCode="credit_apply";
    }

    public function actionIndex(){
        $attr=$_GET[search];
        $sql="select {col} from t_project a 
              left join t_project_credit b on a.project_id=b.project_id 
              
              left join t_partner u on a.up_partner_id=u.partner_id
            left join t_partner d on a.down_partner_id=d.partner_id
              
              ".$this->getWhereSql($attr)." and ".AuthorizeService::getUserDataConditionString('a')." order by a.project_id desc {limit}";
        $user = Utility::getNowUser();
        if(!empty($user)) {
            $data=$this->queryTablesByPage($sql,'a.*,b.partner_amount,b.user_amount,b.other_amount
        ,b.partner_amount-b.partner_amount_free as  partner_used_amount
        ,b.user_amount-b.user_amount_free+b.other_amount-b.other_amount_free as user_used_amount
        ,ifNull((select status from t_project_credit_apply where project_id=a.project_id order by apply_id desc limit 1),-2) as apply_status
        ,u.name as up_name,d.name as down_name
        ');
        }else{
            $data = array();
        }
        $this->render("index",$data);
    }


    public function actionEdit(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！","/".$this->getId()."/");

        $project=Project::model()->findByPk($id);
        if(empty($project->project_id))
            $this->renderError("当前信息不存在！");

        $model=ProjectCreditApply::model()->find("project_id=".$id." 
            and status<".ProjectCreditApply::STATUS_CONFIRM ." and status>=".ProjectCreditApply::STATUS_SUBMIT."");
        if(!empty($model->apply_id))
            $this->renderError("当前项目有正在申请中的额度占用，请不要重复申请！");

        $applyAmount=CreditService::getProjectApplyAmount($project->project_id,$project);
        if($applyAmount<0)
        {
            Mod::log("获取项目需申请额度出错，错误码：".$applyAmount,"error");
            $applyAmount=0;
            //$this->renderError("获取项目需申请额度出错！");
        }
        $data=$project->getAttributes(array("project_id","project_name","status","manager_user_id"));
        $data["applyAmount"]=$applyAmount;

        $this->pageTitle="额度占用申请";
        $this->render("edit",array(
            "data"=>$data
        ));
    }


    public function isCanApply($projectStatus,$applyStatus)
    {
        return ($projectStatus>=Project::STATUS_SUBMIT && $projectStatus<Project::STATUS_CONTRACT_CHECKING
            && ($applyStatus<ProjectCreditApply::STATUS_SUBMIT || $applyStatus>ProjectCreditApply::STATUS_CONFIRM));
    }
    public function isCanTrash($projectStatus,$applyStatus)
    {
        return ($projectStatus>=Project::STATUS_SUBMIT && $projectStatus<Project::STATUS_CONTRACT_CHECKING
        && $applyStatus>=ProjectCreditApply::STATUS_SUBMIT && $applyStatus<ProjectCreditApply::STATUS_USED);
    }

    public function actionSave()
    {
        $params=json_decode($_POST["data"],true);

        if(!Utility::checkQueryId($params["project_id"]))
        {
            $this->returnError("信息有误！");
        }

        $project=Project::model()->findByPk($params["project_id"]);
        if(empty($project->project_id))
            $this->returnError("项目信息不存在！");

        $detail=ProjectDetail::model()->find("project_id=".$params["project_id"]." and type=2");
        if(empty($detail->detail_id))
            $this->returnError("项目下游交易信息不存在！");

        $model=new ProjectCreditApply();
        $model->project_id=$params["project_id"];
        $amount=0;
        $userAmount=0;

        $items=array();

        foreach ($params["items"] as $v)
        {
            if(empty($v["amount"]) || $v["amount"]<0)
                continue;
            $item=new ProjectCreditApplyDetail();
            $item->project_id=$params["project_id"];
            $item->amount=$v["amount"];
            $item->user_id=$v["user_id"];
            $item->to_user_id=$project->manager_user_id;
            $item->apply_time=new CDbExpression("now()");
            $item->status=ProjectCreditApply::STATUS_SUBMIT;
            $item->status_time=new CDbExpression("now()");
            if($v["user_id"]==$project->manager_user_id)
                $userAmount+=$v["amount"];
            else
                $amount+=$v["amount"];
            $items[]=$item;
        }

        $model->partner_amount=$detail->amount-$amount-$userAmount;
        $model->partner_amount=$model->partner_amount<0?0:$model->partner_amount;
        $model->user_amount=$userAmount;
        $model->other_amount=$amount;
        $model->remark=$params["remark"];
        $model->status=ProjectCreditApply::STATUS_SUBMIT;
        $model->status_time=new CDbExpression("now()");

        $trans=Utility::beginTransaction();
        try{

            $applies=ProjectCreditApply::model()->findAll("project_id=".$params["project_id"]." and status=".ProjectCreditApply::STATUS_CONFIRM."");
            foreach ($applies as $a)
            {
                $a->cancel();
            }

            $model->save();
            foreach ($items as $item)
            {
                $item->apply_id=$model->apply_id;
                $item->save();
            }

            $trans->commit();
            $this->returnSuccess($model->apply_id);
        }
        catch (Exception $e)
        {
            try{$trans->rollback();}catch(Exception $ee){}
            Mod::log("CreditApply Save Error: ".$e->getMessage(),"error");
            $this->returnError("操作出错");
        }
    }


    public function actionDetail(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！");

        $project=Project::model()->findByPk($id);
        if(empty($project->project_id))
            $this->returnError("项目信息不存在！");

        $detail=ProjectDetail::model()->find("project_id=".$id." and type=2");
        if(empty($detail->detail_id))
            $this->returnError("项目下游交易信息不存在！");

        $applies=ProjectCreditApply::model()->with("items")->findAll(array(
            "condition"=>"t.project_id=".$id,
            "order"=>"t.apply_id desc",
        ));
        //var_dump($applies);


        $this->pageTitle="查看项目额度占用申请详情";
        $this->render('detail',array(
            "project"=>$project->attributes,"detail"=>$detail->attributes,
            "applies"=>$applies,
        ));
    }

    public function actionTrash(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->returnError("非法参数！");

        $project=Project::model()->findByPk($id);
        if(empty($project->project_id))
            $this->returnError("当前项目信息不存在！");


        $trans=Utility::beginTransaction();
        try{

            $i=0;
            $applies=ProjectCreditApply::model()->findAll("project_id=".$project["project_id"]." 
            and status<=".ProjectCreditApply::STATUS_CONFIRM." 
            and status>=".ProjectCreditApply::STATUS_SUBMIT."");
            foreach ($applies as $a)
            {
                $i++;
                $a->cancel();
            }

            $trans->commit();

            $this->returnSuccess($i);
        }
        catch (Exception $e)
        {
            try{$trans->rollback();}catch(Exception $ee){}
            Mod::log("CreditApply Trash Error: ".$e->getMessage(),"error");
            $this->returnError("操作出错");
        }

    }
}