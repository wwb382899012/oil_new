<?php

/**
 * Created by PhpStorm.
 * User: vector
 * Date: 2016/11/7
 * Time: 17:39
 * Desc：评审会议记录
 */
class PartnerReviewController extends AttachmentController
{
    public function pageInit()
    {
        $this->attachmentType=Attachment::C_PARTNER_REVIEW;
        $this->filterActions="saveFile,getFile";
        $this->rightCode="partnerReview";
    }

    public function actionIndex(){
        $attr = $_GET[search];
        if(!is_array($attr) || !array_key_exists("a.review_status",$attr))
        {
            $attr["a.review_status"]="1";
        }

        $query="";
        $status="";
        if($attr["a.review_status"]=="1"){
            $status="1";
            unset($attr["a.review_status"]);
            $query=" and a.status >= ".PartnerApply::STATUS_REVIEW." and a.status <= ".PartnerApply::STATUS_ADD_INFO_NEED_REVIEW;
        }else if($attr["a.review_status"]=="2"){
            $status="2";
            unset($attr["a.review_status"]);
            $query=" and (a.status = ".PartnerApply::STATUS_PASS." or a.status = ".PartnerApply::STATUS_REJECT." or a.status = ".PartnerApply::STATUS_ADD_INFO_NOT_REVIEW.")";
        }
        $type = 0;
        if(!empty($attr["a.type"])){
            $type = $attr["a.type"];
            unset($attr["a.type"]);
            $query .= " and find_in_set(".$type.",a.type) ";
        }

        $sql  ="select {col} from t_partner_apply a 
                left join t_ownership b on a.ownership=b.id "
                .$this->getWhereSql($attr);
        $sql .= " and a.mark<1 ".$query." order by a.partner_id desc {limit}";

        $data=$this->queryTablesByPage($sql,"a.*,b.name as ownership_name");
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $typeDesc = PartnerApplyService::getPartnerType($row['type']);
                $data['data']['rows'][$key]['type'] = str_replace('&nbsp;', ' ', $typeDesc);
            }
        }

        if($status=="1" || $status=="2"){
            $attr["a.review_status"]=$status;
        }

        if(!empty($type))
            $attr["a.type"] = $type;

        $data['search'] = $attr;
        $this->render("index",$data);
    }

    /**
     * 判断是否可以修改
     * @param $status[0] 合作方状态，$status[1] 会议评审状态
     * @return bool
     */
    public function checkIsCanEdit($status)
    {
        if((($status['p_status']==PartnerApply::STATUS_REVIEW || $status['p_status']==PartnerApply::STATUS_ADD_INFO_NEED_REVIEW) && $status['r_status']==PartnerReview::STATUS_REVIEW_NEW) ||
           ($status['p_status']==PartnerApply::STATUS_ADD_INFO_NEED_REVIEW && ($status['r_status']==PartnerReview::STATUS_INFO_PASS)))
        {
            return true;
        }
        else
            return false;
    }


    public function actionSave(){
        $params=$_POST["obj"];
        $user=$this->getUser();
        // print_r($params);die;
        if(!isset($params['is_temp_save']))
            $this->returnError("非法参数！");

        $attachments = PartnerReview::getReviewAttachments($params["review_id"],3001);
        if(empty($attachments)){
            $this->returnError("请上传评审记录后再保存！");
        }

        if(!empty($params["review_id"])){
            $obj=PartnerReview::model()->findByPk($params["review_id"]);
        }else{
            $this->returnError("非法参数！");
        }

        if(empty($obj->review_id)){
            $obj=new PartnerReview();
            $obj->create_user_id=$user["user_id"];
            $obj->create_time=date("Y-m-d H:i:s");
        }

        if(!empty($params['credit_amount'])){
            $params['credit_amount']= abs($params['credit_amount']*10000*100);
        }

        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "会议评审");
        $trans = Utility::beginTransaction();
        try
        {
            $obj->setAttributes($params,false);
            $obj->status    = $params['checkStatus'];
            $obj->user_ids  = $params["uIds"];
            $obj->update_user_id = $user["user_id"];
            $obj->update_time = date("Y-m-d H:i:s");
            $obj->save();

            if($params['is_temp_save']!=1){
                $amount = 0;
                if($obj->status!=PartnerApply::STATUS_REJECT){
                    $amount = $obj->credit_amount;
                }

                PartnerService::updateApplyPartnerStatus($obj->partner_id,$obj->status,$amount);

                if($obj->status==PartnerApply::STATUS_PASS){
                    PartnerService::updatePartnerInfo($obj->partner_id);
                }else if($obj->status==PartnerApply::STATUS_ADD_INFO_NEED_REVIEW || $obj->status==PartnerApply::STATUS_ADD_INFO_NOT_REVIEW){
                    $partner = PartnerApply::model()->findByPk($obj->partner_id);
                    $typeStr = array();
                    $types = explode(',', $partner->type);
                    foreach ($types as $thisType) {
                        $typeStr[] = Map::$v['partner_type'][$thisType];
                    }
                    $typeStr = implode(',', $typeStr);
                    $taskParams = array('name'=>$partner->name, 'typeName'=>$typeStr);
                    TaskService::addPartnerTasks(Action::ACTION_5, $obj->review_id, 0, $partner->create_user_id, $taskParams);
                }
                TaskService::doneTask($obj->partner_id, Action::ACTION_4);
            }

            $trans->commit();
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "PartnerReview", $obj->review_id);
            $this->returnSuccess($obj->review_id);
        }catch (Exception $e)
        {
            try{$trans->rollback();} catch (Exception $ee){}
            $this->returnError("操作失败".$e->getMessage());
        }


    }

    public function actionEdit(){
        $id   = Mod::app()->request->getParam("id");
        $flag = Mod::app()->request->getParam("flag");
        if(empty($flag)){
            $sArr = PartnerReview::getReviewStatus($id);
            if(!empty($sArr) && $sArr['status']==PartnerReview::STATUS_REVIEW_NEW){
                $flag=1;
            }else{
                $flag=2;
            }
        }

        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！","/partnerReview/");

        $sql = "";
        $data = array();
        if($flag == 2){
            $sql = "select partner_id,name as partner_name,type,auto_level,custom_level,
                level as risk_level,apply_amount,credit_amount as o_credit_amount,status as p_status
                from t_partner_apply where partner_id=".$id;
        }else{
            $sql = "select a.name as partner_name,a.type,a.auto_level,a.custom_level,a.status as p_status,
                a.level as risk_level,a.apply_amount,a.credit_amount as o_credit_amount,b.*
                from t_partner_apply a 
                left join t_partner_review b on a.partner_id=b.partner_id 
                where a.partner_id=".$id." order by b.review_id desc limit 1";
        }
        $data=Utility::query($sql);

        if(Utility::isEmpty($data)){
            $this->renderError("当前信息不存在！","/partnerReview/");
        }

        if($data[0]['credit_amount']<=0 && $data[0]['o_credit_amount']>0){
            $data[0]['credit_amount'] = $data[0]['o_credit_amount'];
        }
        $partnerAttachments = array();

        if($flag == 2){
            $data[0]['review_id']=IDService::getPartnerReviewId();
            $data[0]['status']  = array();
            $data[0]['status']['p_status'] = $data[0]['p_status'];
            $data[0]['status']['r_status'] = 0;
        }else{
            $partnerAttachments = PartnerReview::getReviewAttachments($data[0]['review_id']);
            $status = $data[0]['status'];
            unset($data[0]['status']);
            $data[0]['status']  = array();
            $data[0]['status']['p_status'] = $data[0]['p_status'];
            $data[0]['status']['r_status'] = $status;
        }

        if(!$this->checkIsCanEdit($data[0]['status']))
            $this->renderError("该状态下，不允许操作会议评审信息！");

        $supplyAttachments = array();
        $rArr = PartnerReview::model()->findAllToArray(array("condition"=>"partner_id=".$id,"order"=>"review_id desc"));
        if(!empty($rArr) && count($rArr)>0){
            foreach ($rArr as $key => $value) {
                if((($rArr[0]['status']==PartnerReview::STATUS_REVIEW_PASS || $rArr[0]['status']==PartnerReview::STATUS_REVIEW_REJECT) && flag==2) ||
                    (($rArr[1]['status']==PartnerReview::STATUS_REVIEW_PASS || $rArr[1]['status']==PartnerReview::STATUS_REVIEW_REJECT) && flag==1))
                    break;
                $attachments = PartnerReview::getReviewAttachments($value['review_id'],3201);
                if(!empty($attachments)){
                    $supplyAttachments[0] = $attachments;
                    break;
                }
            }
        }

        if($flag == 1){
            $title = "修改";
        }else{
            $title = "添加";
        }
        $this->pageTitle=$title."会议评审信息";
        $this->render("edit",array(
            "data"=>$data[0],
            "partnerAttachments"=>$partnerAttachments,
            "supplyAttachments"=>$supplyAttachments[0],
        ));
    }



    public function actionDetail(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！","/partnerReview/");

        $partner= Utility::query("select partner_id,name as partner_name,type,auto_level,custom_level,
                level as risk_level,apply_amount,credit_amount as o_credit_amount,status as partner_status
                from t_partner_apply where partner_id=".$id);

        $data   = PartnerReview::model()->findAllToArray(array("condition"=>"partner_id=".$id,"order"=>"review_id desc"));
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/partnerReview/");
        }

        $supplyAttachments = array();
        $rArr = PartnerReview::model()->findAllToArray(array("condition"=>"partner_id=".$id,"order"=>"review_id desc"));
        if(!empty($rArr) && count($rArr)>=1){
            foreach ($rArr as $key => $value) {
                $attachments = PartnerReview::getReviewAttachments($value['review_id'],3201);
                if(!empty($attachments)){
                    $supplyAttachments[$value['review_id']] = $attachments;
                }
            }
        }
        //print_r($data);die;
        $this->pageTitle="查看会议评审详情";
        $this->render('detail',array(
            "partner"=>$partner[0],
            "data"=>$data,
            "supplyAttachments"=>$supplyAttachments
            )
        );
    }

    public function actionSubmit()
    {
        $id         = Mod::app()->request->getParam("id");
        $partner_id = Mod::app()->request->getParam("partner_id");
        $status     = Mod::app()->request->getParam("status");

        if(!Utility::checkQueryId($id) || !Utility::checkQueryId($partner_id) || empty($status))
            $this->returnError("非法参数！");

        $attachments = PartnerReview::getReviewAttachments($id,3001);
        if(empty($attachments)){
            $this->returnError("请上传评审记录后再提交！");
        }

        $obj= PartnerReview::model()->findByPk($id);
        if(empty($obj->review_id)){
            $this->returnError("当前信息不存在！");
        }


        $user=$this->getUser();

        $trans = Utility::beginTransaction();
        try
        {
            $oldStatus = $obj->getAttribute('status');
            $obj->status            = $status;
            $obj->update_user_id    = $user["user_id"];
            $obj->update_time       = date("Y-m-d H:i:s");
            $obj->save();

            $amount = 0;
            if($status!=PartnerApply::STATUS_REJECT){
                $amount = $obj->credit_amount;
            }
            PartnerService::updateApplyPartnerStatus($partner_id,$status,$amount);
            if($status==PartnerApply::STATUS_PASS){
                PartnerService::updatePartnerInfo($partner_id);
            }else if($status==PartnerApply::STATUS_ADD_INFO_NEED_REVIEW || $status==PartnerApply::STATUS_ADD_INFO_NOT_REVIEW){
                $partner = PartnerApply::model()->findByPk($partner_id);
                TaskService::addPartnerTasks(Action::ACTION_5, $obj->review_id, 0, $partner->create_user_id);
            }

            TaskService::doneTask($partner_id, Action::ACTION_4);

            $trans->commit();
            Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), '提交会议评审', 'PartnerReview', $obj->review_id);
            $this->returnSuccess($obj->review_id);
        }catch (Exception $e)
        {
            try{$trans->rollback();} catch (Exception $ee){}
            $this->returnError($e->getMessage());
        }
    }

    /**
     * 重写文件上传获取额外参数的方法
     * @return array
     */
    protected function getFileExtras()
    {
        return array();
    }

}