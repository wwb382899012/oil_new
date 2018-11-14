<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/12 10:01
 * Describe：
 */
class CreditConfirmController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="";
        $this->rightCode="credit_confirm";
    }

    public function actionIndex(){
        $attr=$_GET[search];
        $sql="select {col} from t_project_credit_apply_detail a 
              left join t_project p on a.project_id=p.project_id 
              left join t_project_detail b on p.project_id=b.project_id and b.type=2
              left join t_partner u on p.up_partner_id=u.partner_id
            left join t_partner d on p.down_partner_id=d.partner_id
              ".$this->getWhereSql($attr)." and a.user_id=".Utility::getNowUserId()." order by a.detail_id desc {limit}";
        $data=$this->queryTablesByPage($sql,"a.*,b.amount as project_amount,p.project_id,p.project_name,p.status as project_status
        ,p.up_partner_id,p.down_partner_id,u.name as up_name,d.name as down_name");
        $this->render("index",$data);
    }

    public function actionEdit(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！","/".$this->getId()."/");

        $model=ProjectCreditApplyDetail::model()->findByPk($id);
        if(empty($model->detail_id))
            $this->renderError("当前信息不存在！");
        if($model->status!=ProjectCreditApplyDetail::STATUS_SUBMIT)
            $this->renderError("当状态的额度占用申请无需确认！");

        //$project=Project::model()->findByPk($model->project_id);
        $data=ProjectService::getDetail($model->project_id);
        $project=$data[0];
        if(empty($project["project_id"]))
            $this->renderError("项目信息不存在！");
        if($project["status"]<Project::STATUS_SUBMIT || $project["status"]>=Project::STATUS_CONTRACT_CHECKING)
            $this->renderError("当前状态的项目信息的额度占用无需处理，请联系管理员处理当前数据！");

        $apply=ProjectCreditApply::model()->with("items")->findByPk($model->apply_id);
        if(empty($apply->apply_id))
            $this->renderError("当前额度占用申请信息不存在！");

        $attachments=Project::getAttachment($project["project_id"]);

        $contractAttachments=Contract::getAttachment($project["project_id"]);

        $payments=ProjectService::getUpPayments($project["project_id"]);
        $planItems=ProjectService::getDownReturnPlans($project["project_id"]);

        $userBalanceAmount=UserCreditService::getUserBalanceCreditAmount($model->user_id);

        $this->pageTitle="额度占用确认";
        $this->render("edit",array(
            "model"=>$model,
            "userBalanceAmount"=>$userBalanceAmount,
            "apply"=>$apply,
            "data"=>$project,
            "attachments"=>$attachments,
            "contractAttachments"=>$contractAttachments,
            "planItems"=>$planItems,
            "payments"=>$payments,
        ));
    }


    public function actionSave()
    {
        $params=json_decode($_POST["data"],true);

        if(!Utility::checkQueryId($params["detail_id"]))
        {
            $this->returnError("信息有误！");
        }

        $model=ProjectCreditApplyDetail::model()->findByPk($params["detail_id"]);
        if(empty($model->detail_id))
            $this->renderError("当前信息不存在！");
        if($model->status!=ProjectCreditApplyDetail::STATUS_SUBMIT)
            $this->renderError("当状态的额度占用申请无需确认！");

        /*$apply=ProjectCreditApply::model()->with("items")->findByPk($model->apply_id);
        if(empty($apply->apply_id))
            $this->renderError("当前额度占用申请信息不存在！");*/

        if($params["status"]==1)
            $model->status=ProjectCreditApplyDetail::STATUS_CONFIRM;
        else
            $model->status=ProjectCreditApplyDetail::STATUS_REJECT;
        $model->status_time=new CDbExpression("now()");
        $model->remark=$params["remark"];

        if($model->confirm())
            $this->returnSuccess();
        else
            $this->returnError("确认失败，请联系管理员处理！".$model->errorMessage);
    }

    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！");

        $model=ProjectCreditApplyDetail::model()->findByPk($id);
        if(empty($model->detail_id))
            $this->renderError("当前信息不存在！");

        $data=ProjectService::getDetail($model->project_id);
        $project=$data[0];
        if(empty($project["project_id"]))
            $this->renderError("项目信息不存在！");

        $apply=ProjectCreditApply::model()->with("items")->findByPk($model->apply_id);
        if(empty($apply->apply_id))
            $this->renderError("当前额度占用申请信息不存在！");

        $attachments=Project::getAttachment($project["project_id"]);

        $contractAttachments=Contract::getAttachment($project["project_id"]);

        $payments=ProjectService::getUpPayments($project["project_id"]);
        $planItems=ProjectService::getDownReturnPlans($project["project_id"]);

        $this->pageTitle="查看额度占用详情";
        $this->render("detail",array(
            "model"=>$model,
            "apply"=>$apply,
            "data"=>$project,
            "attachments"=>$attachments,
            "contractAttachments"=>$contractAttachments,
            "planItems"=>$planItems,
            "payments"=>$payments,
        ));
    }

}