<?php

/**
 * Created by PhpStorm.
 * User: vector
 * Date: 2016/11/7
 * Time: 17:39
 * Desc：会议评审补充资料
 */
class SupplyInfoController extends AttachmentController
{
    public function pageInit()
    {
        $this->attachmentType=Attachment::C_PARTNER_REVIEW_EXTRA;
        $this->filterActions="getFile";
        $this->rightCode="supplyInfo";
    }

    public function actionIndex(){
        $attr=$_GET[search];

        if(!is_array($attr) || !array_key_exists("a.status",$attr))
        {
            $attr["a.status"]="-1";
        }

        $query="";
        $status="";
        if($attr["a.status"]=="-1"){
            $status="-1";
            unset($attr["a.status"]);
            $query=" and a.status >= ".PartnerReview::STATUS_NEED_REVIEW." and a.status <= ".PartnerReview::STATUS_INFO_BACK;
        }else if($attr["a.status"]=="0"){
            $status="0";
            unset($attr["a.status"]);
            $query=" and a.status >= ".PartnerReview::STATUS_NEED_REVIEW." and a.status <= ".PartnerReview::STATUS_NOT_REVIEW;
        }else if($attr["a.status"]=="1"){
            $status="1";
            unset($attr["a.status"]);
            $query=" and a.status = ".PartnerReview::STATUS_INFO_ADDED;
        }else if($attr["a.status"]=="2"){
            $status="2";
            unset($attr["a.status"]);
            $query=" and a.status = ".PartnerReview::STATUS_INFO_BACK;
        }else if($attr["a.status"]=="3"){
            $status="3";
            unset($attr["a.status"]);
            $query=" and a.status = ".PartnerReview::STATUS_INFO_PASS;
        }else{
            unset($attr["a.status"]);
            $query=" and a.status >= ".PartnerReview::STATUS_NEED_REVIEW." and a.status!=".PartnerReview::STATUS_REVIEW_PASS;
        }   

        $type = 0;
        if(!empty($attr["b.type"])){
            $type = $attr["b.type"];
            unset($attr["b.type"]);
            $query .= " and find_in_set(".$type.",b.type)";
        }

        $sql    = "select {col} from t_partner_review a 
                left join t_partner_apply b on a.partner_id=b.partner_id
                left join t_ownership c on b.ownership=c.id "
                .$this->getWhereSql($attr);
        $sql   .= $query ." order by a.review_id desc {limit}";
        $fields = "distinct a.review_id,a.status as r_status,
                case 
                when a.status>=".PartnerReview::STATUS_NEED_REVIEW." and a.status<=".PartnerReview::STATUS_NOT_REVIEW." then '0'
                when a.status=".PartnerReview::STATUS_INFO_ADDED." then '1'
                when a.status=".PartnerReview::STATUS_INFO_BACK." then '2'
                when a.status=".PartnerReview::STATUS_INFO_PASS." then '3' 
                end as review_status,b.*,c.name as ownership_name";
        $data=$this->queryTablesByPage($sql,$fields);
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $typeDesc = PartnerApplyService::getPartnerType($row['type']);
                $data['data']['rows'][$key]['type'] = str_replace('&nbsp;', ' ', $typeDesc);
            }
        }
        
        if($status=="-1" || $status=="0" || $status=="1" || $status=="2" || $status=="3"){
            $attr["a.status"]=$status;
        }

        if(!empty($type))
            $attr["b.type"] = $type;

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
        if( $status>=PartnerReview::STATUS_NEED_REVIEW && $status<=PartnerReview::STATUS_INFO_BACK) 
        {
            return true;
        }
        else
            return false;
    }


    public function actionEdit(){
        $id   = Mod::app()->request->getParam("id");
        $flag = Mod::app()->request->getParam("flag");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！","/supplyInfo/");

        $sql = "select a.*,b.name as partner_name,b.type,b.auto_level,b.custom_level,
                b.level as risk_level,b.apply_amount,b.credit_amount as o_credit_amount
                from t_partner_review a 
                left join t_partner_apply b on a.partner_id=b.partner_id 
                where a.review_id=".$id." order by a.review_id desc limit 1";
        $data=Utility::query($sql);
        
        if(Utility::isEmpty($data)){
            $this->renderError("当前信息不存在！","/supplyInfo/");
        }
        if(!$this->checkIsCanEdit($data[0]['status']))
        {
            $this->returnError("当前状态不允许添加会议评审补充资料！");
        }
        if($data[0]['credit_amount']>0){
            $data[0]['credit_amount'] = $data[0]['credit_amount']/100;
        }

        $partnerAttachments = PartnerReview::getReviewAttachments($data[0]['review_id']);
        if($flag == 1){
            $title = "修改";
        }else{
            $title = "添加";
        }
        $this->pageTitle=$title."会议评审补充资料";
        $this->render("detail",array(
            "data"=>$data[0],
            "partnerAttachments"=>$partnerAttachments,
        ));
    }

    public function actionDetail(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！","/supplyInfo/");

        $data= Utility::query("
            select a.*,b.name as partner_name,b.type,b.auto_level,
                b.custom_level,b.level as risk_level,b.apply_amount,
                b.credit_amount as o_credit_amount,b.status as partner_status
                from t_partner_review a
                left join t_partner_apply b on a.partner_id=b.partner_id
                where a.review_id=".$id);

        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/supplyInfo/");
        }

        $partnerAttachments = PartnerReview::getReviewAttachments($id);

        $this->pageTitle="查看会议评审补充资料";
        $this->render('detail',array(
            "data"=>$data[0],
            "partnerAttachments"=>$partnerAttachments,
            )
        );
    }

    public function actionSave()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->returnError("非法参数！");

        $obj= PartnerReview::model()->findByPk($id);
        if(empty($obj->review_id)){
            $this->returnError("当前信息不存在！");
        }

        $attachments = PartnerReview::getReviewAttachments($id,3201);
        if(empty($attachments)){
            $this->returnError("请上传补充资料后再提交！");
        }

        $user=$this->getUser();

        $obj->status            = PartnerReview::STATUS_INFO_ADDED;
        $obj->update_user_id    = $user["user_id"];
        $obj->update_time       = date("Y-m-d H:i:s");

        $trans = Utility::beginTransaction();

        try{
            $obj->save();
            
            FlowService::startFlowForCheck31($obj->review_id);
            
            //TaskService::addPartnerTasks(Action::ACTION_6, $obj->review_id, ActionService::getActionRoleIds(Action::ACTION_6), 0);
            TaskService::doneTask($obj->review_id, Action::ACTION_5);

            $trans->commit();
            $this->returnSuccess($obj->review_id);
        }catch(Exception $e){
            try{ $trans->rollback(); }catch(Exception $ee){}

            $this->returnError("操作失败！". $e->getMessage());
        }
        /*$res = $obj->save();
        if($res===true) {
            //PartnerService::updateApplyPartnerStatus($partner_id,$status);
            FlowService::startFlowForCheck31($obj->review_id);
            $this->returnSuccess($obj->review_id);
        }else{
            $this->returnError("保存失败".$res);
        }*/
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