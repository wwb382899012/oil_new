<?php

/**
 * Created by PhpStorm.
 * User: vector
 * Date: 2016/11/7
 * Time: 17:39
 * Desc：合作方额度报表
 */
class PartnerReportController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="";
        $this->rightCode="partnerReport";
    }

    public function actionIndex(){
        $attr=$_GET[search];

        $sql    = "select {col} from t_partner a 
                left join t_partner_credit b on a.partner_id=b.partner_id "
                .$this->getWhereSql($attr);
        $sql   .= " and a.status>=".PartnerApply::STATUS_PASS." and a.type in(0,2) order by a.partner_id desc {limit}";
        $fields = "a.*,b.use_amount,(b.credit_amount-b.use_amount) as balance_amount";
        $data=$this->queryTablesByPage($sql,$fields);

        $data['search'] = $attr;
        //print_r($data);die;
        $this->render("index",$data);
    }


    public function actionDetail() {
        $partner_id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($partner_id)) {
            $this->renderError("非法参数！", "/partnerAmount/");
        }

        $obj = Partner::model()->findByPk($partner_id);
        if (empty($obj->partner_id)) {
            $this->renderError("当前信息不存在！", "/partnerAmount/");
        }

        $credit=PartnerCredit::model()->findByPk($partner_id);

        $amountInfo = $this->getPartnersAmountInfo($partner_id);
        // print_r($amountInfo);die;
        // print_r($logData);die;
        $this->pageTitle = "合作方额度详情";
        $this->render('detail', array(
            "data" => $obj->getAttributes(true,array("create_user_id","create_time","update_user_id","update_time",)), 
            "credit" => $credit->getAttributes(true,array("create_user_id","create_time","update_user_id","update_time",)), 
            "amountInfo"=> $amountInfo['data']
            )
        );
    }

    /**
     * 获取合作方额度信息
     */
    public function getPartnersAmountInfo($partnerId)
    {
        $partner = Partner::model()->findByPk($partnerId);
        if(empty($partner->partner_id))
            return "当前合作方不存在！";

        $sql = "select {col} from t_partner a
                left join t_project p on a.partner_id=p.down_partner_id
                left join t_project_detail d on p.project_id=d.project_id and d.type=2
                left join t_project_credit pc on pc.project_id=p.project_id
                left join t_settlement s on p.project_id=s.project_id and s.type=2
                left join (select @rowNO :=0) b on 1=1
                where a.partner_id=".$partner->partner_id." and p.status> ".Project::STATUS_SUBMIT." order by p.project_id {limit}";
        $fields = " (@rowNO := @rowNo+1) AS rowno,a.partner_id,a.name as partner_name,
                    IFNULL(d.amount,0) as plan_amount,IFNULL(s.amount,d.amount) as actual_amount,
                    (pc.partner_amount-pc.partner_amount_free) as balance_amount,
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

}