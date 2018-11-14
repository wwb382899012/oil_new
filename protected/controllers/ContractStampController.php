<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/15 14:55
 * Describe：
 */
class ContractStampController extends AttachmentController
{
    public function pageInit()
    {
        $this->attachmentType=Attachment::C_CONTRACT;
        //$this->isWordToPdf=1;
        $this->filterActions="getFile,pdf,getPdf";
        $this->rightCode="contractStamp";
    }

    public function actionIndex()
    {
        $attr = $_GET[search];
        if(!is_array($attr) || !array_key_exists("a.status",$attr))
        {
            $attr["a.status"]="0";
        }

        $query="";
        $status="";
        if(isset($attr["a.status"]) && $attr["a.status"]=="0"){
            $status="0";
            unset($attr["a.status"]);
            $query=" and a.status<".Project::STATUS_STAMP_CHECKING;
        }else if($attr["a.status"]=="1"){
            $status="1";
            unset($attr["a.status"]);
            $query=" and a.status>=".Project::STATUS_STAMP_CHECKING;
        }
        $user = SystemUser::getUser(Utility::getNowUserId());

        $sql = "select {col}
            from t_project a
            left join t_partner b on a.up_partner_id=b.partner_id
            left join t_partner s on a.down_partner_id=s.partner_id
            ". $this->getWhereSql($attr);
        $sql .= $query;
        $sql .= " and a.status>=".Project::STATUS_UP_DOWN_CONTRACT_STAMP." and a.corporation_id in (".$user['corp_ids'].") order by a.project_id desc {limit}";
        $fields="a.*,b.name as up_name,s.name as down_name,case when a.status>=".Project::STATUS_UP_DOWN_CONTRACT_STAMP." and a.status<".Project::STATUS_STAMP_CHECKING." then '0' else '1' end as project_status";
        $data=$this->queryTablesByPage($sql,$fields);

        if($status=="0" || $status=="1")
            $attr["a.status"]=$status;

        $data["search"]=$attr;
        $this->render("index", $data);
    }

    public function actionEdit()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", $this->getBackPageUrl());
        }

        $data=ProjectService::getDetail($id);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->getBackPageUrl());
        }
        $contractAttachments=Contract::getAttachment($id);

        $check=$this->getContractCheckDetail($id);

        $this->pageTitle="合同签章";
        $this->render('detail',array(
                "data"=>$data[0],
                "contractAttachments"=>$contractAttachments,
                "check"=>$check
            )
        );
    }

    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", $this->getBackPageUrl());
        }

        $data=ProjectService::getDetail($id);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->getBackPageUrl());
        }
        $contractAttachments=Contract::getAttachment($id);

        $check=$this->getContractCheckDetail($id);

        $this->pageTitle="查看签章合同信息";
        $this->render('detail',array(
                "data"=>$data[0],
                "contractAttachments"=>$contractAttachments,
                "check"=>$check
            )
        );
    }


    public function checkIsCanEdit($status)
    {
        if($status>=Project::STATUS_UP_DOWN_CONTRACT_STAMP && $status<Project::STATUS_STAMP_CHECKING)
        {
            return true;
        }
        else
            return false;
    }

    public function actionSubmit()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
            $this->returnError("信息有误！");

        $sql="select c.* from t_contract c 
              left join t_project a on c.project_id=a.project_id
              where c.project_id=".$id." and c.type>200 and c.type<=300 and c.status>0";
        $data=Utility::query($sql);
        $contractAttachments = Contract::getBusinessAttachment($id);
        /*$map=include(ROOT_DIR."/protected/components/Map.php");
        $contractInfo=$map["stamp_contract_attachment_type"];*/

        if(count($data) != count($contractAttachments))
            $this->returnError("所有签章合同必须同时上传才能提交审核！");

        $bus_id  = UserService::getBusinessRoleId();
        $fin_id  = UserService::getFinanceRoleId();
        $finlawId=14;
        $check = $this->getContractCheckDetail($id);
        if(!empty($check) && !empty($check['status'])){
            foreach ($data as $key => $value) {
                if((empty($check[$finlawId][$bus_id][$value['type']][0]['check_status']) ||
                    empty($check[$finlawId][$fin_id][$value['type']][0]['check_status'])) && 
                   !empty($value['check_status'])){
                    $this->returnError("所有签章合同必须同时上传才能提交审核！");
                }
            }
        }
        
        //print_r($data);die;
        foreach ($data as $key => $value) {
            FlowService::startFlowForCheck14($value['contract_id']);
        }
        ProjectService::updateProjectStatus($id,Project::STATUS_STAMP_CHECKING);
        $project  = Project::model()->findbyPk($id);
        //TaskService::addTasks(Action::ACTION_11,$id,ActionService::getActionRoleIds(Action::ACTION_11),0,$project->corporation_id);
        TaskService::doneTask($id,Action::ACTION_9);
        $this->returnSuccess();

    }

    /**
     * 获取初审签章合同审核详细信息
     */
    private function getContractCheckDetail($projectId)
    {
        $sql = "select a.*,p.project_id,p.project_name,t.type
                 from t_check_detail a
                 left join t_contract t on a.obj_id=t.contract_id and t.type>200 and t.type<=300
                 left join t_project p on t.project_id=p.project_id
                 left join t_check_item c on c.check_id=a.check_id
                 where p.project_id=".$projectId." and a.status>0 order by a.check_id desc ";
        $data = Utility::query($sql);

        $check  = array();
        if(Utility::isNotEmpty($data))
        {
            foreach ($data as $key => $value) {
                $check[$value['business_id']][$value['role_id']][$value['type']][]=$value;
                if($check[$value['business_id']][$value['role_id']][$value['type']][0]['status']==1 && 
                    empty($check[$value['business_id']][$value['role_id']][$value['type']][0]['check_status'])){
                    $check['status'] = 1;
                }
            }
        }
        return $check;
    }
}