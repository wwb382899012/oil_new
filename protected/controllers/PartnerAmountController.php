<?php

/**
 * Created by PhpStorm.
 * User: vector
 * Date: 2016/11/7
 * Time: 17:39
 * Desc：合作方额度调整
 */
class PartnerAmountController extends AttachmentController
{
    public function pageInit()
    {
        $this->attachmentType=Attachment::C_PARTNER_APPLY;
        $this->filterActions="getCompanies,getFile,checkInwhite,attachments,getOwnerships";
        $this->rightCode="partnerAmount";
    }

    public function actionIndex(){
        $attr=$_GET[search];

        $sql    = "select {col} from t_partner a 
                left join t_partner_credit b on a.partner_id=b.partner_id
                left join t_partner_apply d on a.partner_id=d.partner_id "
                .$this->getWhereSql($attr);
        $sql   .= " and a.status>=".PartnerApply::STATUS_PASS." and a.type in(0,2) and a.level=1 order by a.partner_id desc {limit}";
        $fields = "a.*,b.use_amount,(b.credit_amount-b.use_amount) as balance_amount,d.status as partner_status";
        $data=$this->queryTablesByPage($sql,$fields);

        $data['search'] = $attr;
        //print_r($data);die;
        $this->render("index",$data);
    }

    /**
     * 判断是否可以修改
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status)
    {
        $roleId = UserService::getNowUserMainRoleId();
        $busId  = UserService::getBusinessRoleId();
        if( $status==PartnerApply::STATUS_PASS && $roleId==$busId) 
        {
            return true;
        }
        else
            return false;
    }


    public function actionEdit() {
        $id   = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！","/partnerAmount/");

        $roleId = UserService::getNowUserMainRoleId();
        $busId  = UserService::getBusinessRoleId();
        // $riskId = UserService::getRiskRoleId();
        if($roleId != $busId){
            $this->renderError("非法操作！","/partnerAmount/");
        }

        $obj = PartnerApply::model()->findByPk($id);
        if (empty($obj->partner_id)) {
            $this->renderError("当前信息不存在！", "/partnerAmount/");
        }

        if (!$this->checkIsCanEdit($obj->status)) {
            $this->renderError("该状态下，不允许修改合作方信息！");
        }

        $isCanEditName=1;
        if($obj->status==PartnerApply::STATUS_PASS)
        {
            $model=Partner::model()->findByPk($obj->partner_id);
            if($model->status==Partner::STATUS_PASS)
                $isCanEditName=0;
        }

        $data = $obj->getAttributes(true,array("create_user_id","create_time","update_user_id","update_time",));
        if(empty($data['is_stock']))
            unset($data['is_stock']);
        if(!empty($data['apply_amount']))
            $data['apply_amount'] = $data['apply_amount']/100;

        $this->pageTitle = "调整合作方额度";
        $this->render("edit", array("data" => $data,"isCanEditName"=>$isCanEditName));
    }

   public function actionSave() {
        $params = $_POST['obj'];
        // print_r($params);die;
        $user = $this->getUser();
        $requiredParams = array();
        if(empty($params['partner_id']))
            $this->returnError("非法参数！");

        if($params['is_stock']=='on')
            $params['is_stock'] = 1;
        else
            $params['is_stock'] = 0;

        if (!$params['is_temp_save']) {
            $requiredParams = array("name", "contact_person", "contact_phone", "business_type", "user_id", "trade_info", "apply_amount");
        }

        $paramsCheckInfo = Utility::checkRequiredParams($params, $requiredParams);
        if (!$paramsCheckInfo['isValid']) {
            $this->returnError("*号标注字段不得为空！");
        }
        $filterInjectParams = $paramsCheckInfo['params'];
        // $filterInjectParams['auto_level'] = PartnerApplyService::getPartnerLevel($filterInjectParams);

        //保存需检查附件资料完整性
        if (!$filterInjectParams['is_temp_save']) {
            $checkAttachmentsInfo = PartnerApplyService::checkAttachmentsIntegrity($filterInjectParams);
            if (!empty($checkAttachmentsInfo)) {
                $this->returnError($checkAttachmentsInfo);
            }
        }

        $obj = PartnerApply::model()->findByPk($filterInjectParams["partner_id"]);
        $logRemark = '调整合作方额度';
        

        if (empty($obj->partner_id)) {
            $this->returnError("当前信息不存在！");
        } else { //检查是否可修改
            if (!$this->checkIsCanEdit($obj->status)) {
                $this->returnError("该状态下，不允许修改合作方信息！");
            }
        }

        if(!$filterInjectParams['is_temp_save']){
            $filterInjectParams['status'] = PartnerApply::STATUS_SUBMIT;
        }
        if(empty($filterInjectParams['custom_level']))
            $filterInjectParams['custom_level'] = $filterInjectParams['auto_level'];
        if(empty($filterInjectParams['auto_level']))
            $filterInjectParams['auto_level'] = $filterInjectParams['custom_level'];
        if(!empty($filterInjectParams['apply_amount']))
            $filterInjectParams['apply_amount'] = $filterInjectParams['apply_amount']*10000*100;
        
        
        unset($filterInjectParams['partner_id']);
        $obj->setAttributes($filterInjectParams, false);
        $obj->goods_ids         = $params["gIds"];
        $obj->update_user_id    = $user["user_id"];
        $obj->update_time       = date('Y-m-d H:i:s');

        $res = $obj->save();
        if ($res === true) {
            FlowService::startFlowForCheck30($obj->partner_id);
            if (!$filterInjectParams['is_temp_save']) 
                PartnerService::addPartnerLog($logRemark, $obj);
            $this->returnSuccess($obj->partner_id);
        } else {
            $this->returnError("保存失败！" . $res);
        }
    }

    /**
     * @desc 根据name获取合作方信息
     */
    public function actionGetCompanies() {
        $name = $_GET['name'];
        $name = Utility::filterInject($name);
        if (empty($name)) {
            $this->returnError("企业名称不得为空！");
        }

        $partnerInfo = PartnerService::getPartnersInfo($name);
        $this->returnSuccess($partnerInfo);
    }

    public function actionCheckLevel() {
        $params = $_POST['obj'];
        $requiredParams = array("name", "contact_person", "contact_phone", "business_type", "user_id", "trade_info", "goods_ids");
        $paramsCheckInfo = Utility::checkRequiredParams($params, $requiredParams);
        if (!$paramsCheckInfo['isValid']) {
            $this->returnError("*号标注字段不得为空！");
        }
        $systemCheckLevel = PartnerApplyService::getPartnerLevel($paramsCheckInfo['params']);
        $map = include(ROOT_DIR . "/protected/components/Map_old.php");
        $levelInfo = array('system_level' => $systemCheckLevel, 'level_desc' => $map['partner_level'][$systemCheckLevel]);
        $this->returnSuccess($levelInfo);
    }

    public function actionDetail() {
        $partner_id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($partner_id)) {
            $this->renderError("非法参数！", "/partnerAmount/");
        }

        $obj = PartnerApply::model()->findByPk($partner_id);
        if (empty($obj->partner_id)) {
            $this->renderError("当前信息不存在！", "/partnerAmount/");
        }
        $attachments = PartnerApplyService::getAttachment($partner_id);

        $sql = "select {col} from t_partner_log where object_id =" . $partner_id . " and table_name ='" . $obj->tableName() . "' order by create_time desc {limit}";
        // $logData = $this->queryTablesByPage($sql, '*');
        $logData = PartnerApply::formatPartnerLog($this->queryTablesByPage($sql, '*'));

        $amountInfo = $this->getPartnersAmountInfo($partner_id);
        // print_r($amountInfo);die;
        // print_r($logData);die;
        $this->pageTitle = "合作方详情";
        $this->render('detail', array(
            "data" => $obj->getAttributes(true,array("create_user_id","create_time","update_user_id","update_time",)), 
            "attachments" => $attachments, 
            "logData" => $logData['data'],
            "amountInfo"=> $amountInfo['data']
            )
        );
    }

    public function actionCheckInwhite() {
        $name = $_GET['name'];
        $name = Utility::filterInject($name);
        if (empty($name)) {
            $this->returnError("企业名称不得为空！");
        }
        $obj = PartnerWhite::model()->find("name='" . $name . "'");
        $this->returnSuccess($obj->attributes);
    }

    public function actionAttachments() {
        $partner_id = Mod::app()->request->getParam("partner_id");
        if (!Utility::checkQueryId($partner_id)) {
            $this->renderError("非法参数！", "/partnerAmount/");
        }

        $obj = PartnerApply::model()->findByPk($partner_id);
        if (empty($obj->partner_id)) {
            $this->renderError("当前信息不存在！", "/partnerAmount/");
        }
        if (!$this->checkIsCanEdit($obj->status)) {
            $this->renderError("当前状态不允许修改附件", "/partnerAmount/");
        }
        $attachments = PartnerApplyService::getAttachment($partner_id);
        $this->pageTitle = "合作方附件上传";
        $this->render('attachments', array("data" => $obj->attributes, "attachments" => $attachments,));
    }

    /**
     * 获取合作方额度信息
     */
    public function getPartnersAmountInfo($partnerId)
    {
        $partner = PartnerApply::model()->findByPk($partnerId);
        if(empty($partner->partner_id))
            return "当前合作方不存在！";

        $sql = "select {col} from t_partner a
                left join t_partner_apply b on a.partner_id=b.partner_id 
                left join t_partner_credit c on a.partner_id=c.partner_id
                left join t_project p on a.partner_id=p.down_partner_id
                left join t_project_detail d on p.project_id=d.project_id and d.type=2
                left join t_settlement s on p.project_id=s.project_id and s.type=2
                left join (select @rowNO :=0) b on 1=1
                where a.partner_id=".$partner->partner_id." and p.status> ".Project::STATUS_SUBMIT." order by p.project_id {limit}";
        $fields = " (@rowNO := @rowNo+1) AS rowno,a.partner_id,a.name as partner_name,b.status as partner_status,
                    c.credit_amount,c.use_amount,(c.credit_amount-c.use_amount) as balance_amount,
                    IFNULL(d.amount,0) as plan_amount,IFNULL(s.amount,d.amount) as actual_amount,
                    p.project_id,p.project_name,p.trade_type ";
        $data = $this->queryTablesByPage($sql, $fields);
        
        if (count($data['data']['rows']) > 0) {
            foreach ($data['data']['rows'] as $key => $value) {
                $data['data']['rows'][$key]['received_amount'] = DownReceive::getReceiveAmount($value['project_id']);
                $total_amount = DownReceive::getReturnAmount($value['project_id']);
                $data['data']['rows'][$key]['unreceive_amount']= $total_amount - $data['data']['rows'][$key]['received_amount'];
            }
        }
        return $data;
    }

	public function actionGetOwnerships() {
		$this->returnSuccess(Ownership::getOwnerships());
	}

}