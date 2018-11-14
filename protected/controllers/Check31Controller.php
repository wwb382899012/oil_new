<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/28 14:28
 * Describe：
 *      准入风控会议评审
 */
class Check31Controller extends CheckController
{

    public $mainRightCode="check31_";

    public function pageInit()
    {
        parent::pageInit();
        $this->attachmentType=Attachment::C_PARTNER_REVIEW_EXTRA;
        $this->filterActions="saveFile,getFile";
        $this->businessId=31;
        $this->rightCode = $this->mainRightCode;
        $this->checkButtonStatus["reject"]=0;
        $this->checkViewName="/check31/check";
    }

    public function initRightCode()
    {
        
        $attr= $_REQUEST["search"];
        $checkStatus=$attr["checkStatus"];
        $this->treeCode=$this->mainRightCode.$checkStatus;
    }

    public function actionIndex()
    {
        $attr = $_REQUEST[search];

        $checkStatus=1;
        if(!empty($attr["checkStatus"]))
        {
            $checkStatus=$attr["checkStatus"];
            unset($attr["checkStatus"]);
        }

        $type = 0;
        $query = "";
        if(!empty($attr["p.type"])){
            $type = $attr["p.type"];
            unset($attr["p.type"]);
            $query .= " and find_in_set(".$type.",p.type)";
        }

        $sql="
                 select {col} from t_check_detail a
                 left join t_partner_review r on a.obj_id=r.review_id
                 left join t_partner_apply p on r.partner_id=p.partner_id
                 left join t_ownership o on p.ownership=o.id
                 left join t_check_item c on c.check_id=a.check_id and c.node_id>0
                 left join t_flow_node d on d.node_id=c.node_id
                ".$this->getWhereSql($attr)." and a.business_id=".$this->businessId."
                and (a.role_id=".$this->nowUserRoleId." or a.check_user_id=".$this->nowUserId.")";

        $fields="a.*,r.review_id,p.name,p.partner_id,p.status as partner_status,p.corporate,p.type,o.name as ownership_name,d.node_name";

        switch($checkStatus)
        {
            case 2:
                $sql .= " and a.status=1 and a.check_status=1";
                $fields.=",0 isCanCheck ";
                break;
            case 3:
                $sql .= " and a.status=1 and a.check_status=0";
                $fields.=",0 isCanCheck ";
                break;
            case 4:
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields.=",0 isCanCheck ";
                break;
            default:
                $sql .= " and a.status=0";
                $fields.=",1 isCanCheck ";
                break;
        }

        $sql .= $query . " order by a.obj_id desc {limit}";

        $data = $this->queryTablesByPage($sql,$fields);
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $typeDesc = PartnerApplyService::getPartnerType($row['type']);
                $data['data']['rows'][$key]['type'] = str_replace('&nbsp;', ' ', $typeDesc);
            }
        }
        $attr["checkStatus"]=$checkStatus;

        if(!empty($type))
            $attr["p.type"] = $type;

        $data["search"]=$attr;
        $data["b"]=$this->businessId;
        $this->render('/partnerCheck/index',$data);
    }

    /*public function getCheckData($id,$reviewId)
    {
        return $data=Utility::query("
              select
                a.review_id,p.partner_id,p.name as partner_name,p.type,p.auto_level,
                p.custom_level,p.level as risk_level,p.apply_amount,
                p.credit_amount as o_credit_amount,c.business_id,
                c.check_id,b.detail_id,b.role_id,b.check_user_id,b.obj_id
              from t_partner_review a
                left join t_partner_apply p on a.partner_id=p.partner_id 
                left join t_check_detail b on a.review_id=b.obj_id
                left join t_check_item c on b.check_id=c.check_id and c.business_id=".$this->businessId."
                where b.detail_id=".$id." and a.review_id=".$reviewId);
    }*/
    public function getCheckData($id)
    {
        return $data=Utility::query("
              select
                a.review_id,p.partner_id,p.name as partner_name,p.type,p.auto_level,
                p.custom_level,p.level as risk_level,p.apply_amount,
                p.credit_amount as o_credit_amount,c.business_id,
                c.check_id,b.detail_id,b.role_id,b.check_user_id,b.obj_id
              from t_partner_review a
                left join t_partner_apply p on a.partner_id=p.partner_id 
                left join t_check_detail b on a.review_id=b.obj_id
                left join t_check_item c on b.check_id=c.check_id and c.business_id=".$this->businessId."
                where b.detail_id=".$id);
    }

    public function actionCheck()
    {
        $id  = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("信息异常！",$this->mainUrl);

        $checkDetail=CheckDetail::model()->find("detail_id=".$id." and status=".CheckDetail::STATUS_NEW." and business_id=".$this->businessId." and (role_id=".$this->nowUserRoleId." or check_user_id=".$this->nowUserId.")");
        if(empty($checkDetail))
            $this->renderError("没有需要您审核的信息！", $this->mainUrl);

        $data=$this->getCheckData($id,$checkDetail->obj_id);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $partnerAttachments = PartnerReview::getReviewAttachments($review_id);
        $this->pageTitle="补充资料审核";
        $this->render("/partnerCheck/check",array(
            "data"=>$data[0],
            "partnerAttachments"=>$partnerAttachments,
        ));
    }


    public function actionDetail()
    {
        $id         = Mod::app()->request->getParam("id");
        $review_id  = Mod::app()->request->getParam("review_id");
        if(!Utility::checkQueryId($id) || !Utility::checkQueryId($review_id))
            $this->renderError("信息异常！",$this->mainUrl);

        $user = $this->getUser();
        $data=Utility::query("
              select a.review_id,p.partner_id,p.name as partner_name,p.type,p.auto_level,
                p.custom_level,p.level as risk_level,p.apply_amount,
                p.credit_amount as o_credit_amount,c.business_id,c.check_id,c.remark,c.create_time,c.create_user_id
              from t_partner_review a
                left join t_partner_apply p on a.partner_id=p.partner_id
                left join t_check_log c on a.review_id=c.obj_id and c.business_id=" . $this->businessId."
                where  a.review_id=".$review_id." and c.detail_id=".$id);//" and c.user_id=".$user['user_id']."
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $partnerAttachments = PartnerReview::getReviewAttachments($review_id);
        $this->pageTitle="补充资料审核详情";
        $this->render("/partnerCheck/detail",array(
            "data"=>$data[0],
            "partnerAttachments"=>$partnerAttachments,
        ));
    }

}