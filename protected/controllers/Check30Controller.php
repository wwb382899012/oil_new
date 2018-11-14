<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/28 14:28
 * Describe：
 *      准入风控初审
 */
class Check30Controller extends CheckController
{

    public $checkInfo=array(
        "1"=>array(//上游
            "1"=>array(//生产型供应商
                array("name"=>"股东背景","key"=>"101"),
                array("name"=>"仓储能力","key"=>"102"),
                array("name"=>"厂区面积","key"=>"103"),
                array("name"=>"产品质量","key"=>"104"),
                array("name"=>"员工人数","key"=>"105"),
                array("name"=>"产品市场竞争力","key"=>"106"),
                array("name"=>"生产装置","key"=>"107"),
                array("name"=>"发货速度","key"=>"108"),
                array("name"=>"产能","key"=>"109"),
                array("name"=>"发货运输方式","key"=>"110"),
            ),
            "2"=>array(//贸易型供应商
                array("name"=>"行业口碑","key"=>"151"),
                array("name"=>"货物来源","key"=>"152"),
                array("name"=>"贸易能力","key"=>"153"),
            ),
        ),
        "2"=>array(//下游
            array("name"=>"企业简介","key"=>"201","label"=>"1","text"=>"1"),
            array("name"=>"股东背景","key"=>"202"),
            array("name"=>"近三年业务规模及变化情况","key"=>"203"),
            array("name"=>"企业资产情况","key"=>"204"),
            array("name"=>"企业负债情况","key"=>"205"),
            array("name"=>"应收质押状况","key"=>"206"),
            array("name"=>"有无重大负面消息","key"=>"207"),
            array("name"=>"前五大客户资质","key"=>"208","text"=>"1"),
            array("name"=>"前五大供应商资质","key"=>"209","text"=>"1"),
        ),
    );


    public $amountInfo = array(
        array("name"=>"客户申请授信额","key"=>"301","fieldName"=>"apply_amount"),
        array("name"=>"客户能够偿还的授信能力","key"=>"302","fieldName"=>"amount1"),
        array("name"=>"客户所需授信额","key"=>"303","fieldName"=>"amount2"),
        array("name"=>"账面总资产的50%或所有者权益的1倍","key"=>"304","fieldName"=>"amount3"),
        array("name"=>"连续三个月销售量平均值的50%","key"=>"305","fieldName"=>"amount4"),
        array("name"=>"公司剩余可用授信额度","key"=>"306","fieldName"=>"amount5"),
        array("name"=>"拟授予额度","key"=>"307","fieldName"=>"credit_amount"),
        /*array("name"=>"生效日期","key"=>"308","fieldName"=>"start_date"),
        array("name"=>"失效日期","key"=>"309","fieldName"=>"end_date"),*/
        array("name"=>"备注","key"=>"310","label"=>"1","fieldName"=>"remark"),
    );

    public $creditAmount = array(
        "1"=>array("amount"=>500000000),
        "2"=>array("amount"=>1000000000),
        );

    public $mainRightCode="check30_";

    public function pageInit()
    {
        parent::pageInit();
        $this->attachmentType=Attachment::C_PARTNER_CHECK;
        $this->filterActions="saveFile,getFile,calculate";
        $this->businessId=30;
        $this->rightCode = $this->mainRightCode;
        $this->checkViewName="/check30/check";
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
        $user = SystemUser::getUser($this->nowUserId);

        $sql="
                 select {col} from t_check_detail a
                 left join t_partner_apply p on a.obj_id=p.partner_id
                 left join t_ownership o on p.ownership=o.id
                 left join t_check_item c on c.check_id=a.check_id and c.node_id>0
                 left join t_flow_node d on d.node_id=c.node_id
                ".$this->getWhereSql($attr)." and a.business_id=".$this->businessId."
                and (a.role_id=".$this->nowUserRoleId." or a.check_user_id=".$this->nowUserId.")";

        $fields="a.*,p.name,p.partner_id,p.status as partner_status,p.name,p.corporate,p.type,
            p.start_date,p.runs_state,p.auto_level,p.custom_level,p.apply_amount,
            p.level,p.credit_amount,o.name as ownership_name,d.node_name";

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
            case 5:
                $sql .= " and a.status=1 and a.check_status=5";
                $fields.=",0 isCanCheck ";
                break;
            case 6:
                $sql .= " and a.status=1 and a.check_status=6";
                $fields.=",0 isCanCheck ";
                break;
            default:
                $sql .= " and a.status=0";
                $fields.=",1 isCanCheck ";
                break;
        }

        $sql .= " order by a.check_id desc {limit}";

        $data = $this->queryTablesByPage($sql,$fields);

        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $typeDesc = PartnerApplyService::getPartnerType($row['type']);
                $data['data']['rows'][$key]['type'] = str_replace('&nbsp;', ' ', $typeDesc);
            }
        }
        $attr["checkStatus"]=$checkStatus;
        $data["search"]=$attr;
        $data["b"]=$this->businessId;
        $this->render('/check30/index',$data);
    }

    /**
     * 判断是否可以修改
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status)
    {
        if( $status==="0") 
        {
            return true;
        }
        else
            return false;
    }

    /**
     * @desc 计算合作方授信额度
     */
    public function actionCalculate() {
        $params = $_POST["amount"];
        if(!is_array($params['data']) || empty($params['data'])){
            $this->returnError("参数错误！");
        }
        if (!Utility::checkQueryId($params["detail_id"]))
        {
            $this->returnError("非法操作！");
        }

        $credit = PartnerCheckCredit::model()->find('detail_id='.$params['detail_id']);
        if(empty($credit->id)){
            $credit = new PartnerCheckCredit();
            $credit->create_time = date('Y-m-d H:i:s');
            $credit->create_user_id = $this->nowUserId;
        }

        $credit->detail_id = $params['detail_id'];
        $credit->start_date = date('Y-m-d H:i:s');
        $credit->end_date = date('Y-m-d H:i:s',strtotime('+1 year'));
        $credit->remark = $params['data']['remark'];
        $credit->status = 1;
        unset($params['detail_id']);
        unset($params['data']['remark']);
        unset($params['data']['credit_amount']);
        $credit_amount = array_search(min($params['data']),$params['data']);
        $credit->credit_amount=$params['data'][$credit_amount];
        $credit->update_time = date('Y-m-d H:i:s');
        $credit->update_user_id = $this->nowUserId;
        
        $credit->setAttributes($params['data'], false);
        $res = $credit->save();
        if ($res === true) {
            $this->returnSuccess($credit->credit_amount);
        } else {
            $this->returnError("保存失败！" . $res);
        }
    }

    public function getCheckData($id)
    {
        return $data=Utility::query("
              select
                  a.partner_id,a.name,a.custom_level,a.level as risk_level,a.auto_level,a.type,a.business_type,
                  a.status as partner_status,c.business_id,c.check_id,b.status,b.detail_id,b.role_id,b.check_user_id,b.obj_id
              from t_partner_apply a
                left join t_check_detail b on b.obj_id=a.partner_id
                left join t_check_item c on b.check_id=c.check_id and c.business_id=".$this->businessId."
                where b.detail_id=".$id."");
    }

    public function actionCheck()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", $this->mainUrl);
        }

        $data=$this->getCheckData($id);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $credit = PartnerCheckCredit::model()->find("detail_id=".$id);
        $amount = array();
        if(!empty($credit->id)){
            $amount = $credit->getAttributes(true,array("create_user_id","create_time","update_user_id","update_time",));
        }

        if(!empty($amount) && !empty($amount['credit_amount'])){
            $data[0]['credit_amount'] = $amount['credit_amount'];
        }

        $riskType=PartnerService::getPartnerRiskType($data[0]["type"]);
        $data[0]["riskType"]=$riskType;

        $this->pageTitle="合作方风控初审";
        $this->render($this->checkViewName,array(
            "data"=>$data[0],
            "amount"=>$amount,
        ));
    }

    protected function getPartnerCheckInfo()
    {
        $items=json_decode($_POST["info"],true);
        return $items;
    }

    public function actionSave()
    {
        $params = $_POST["obj"];
        // print_r($params);die;
        if (!Utility::checkQueryId($params["check_id"]))
        {
            $this->returnError("非法操作！");
        }
        if (!Utility::checkQueryId($params["detail_id"]))
        {
            $this->returnError("非法操作！");
        }

        $checkItem = CheckItem::model()->findByPk($params["check_id"]);
        if (empty($checkItem->check_id))
        {
            $this->returnError("非法操作！");
        }
        $userId = Utility::getNowUserId();

        $attachments=PartnerService::getCheckAttachments($params["detail_id"]);
        if($params["checkStatus"]!=-1){
            if(empty($attachments['30001']) || count($attachments['30001'])<1){
                $this->returnError("请上传初审报告后再提交！");
            }
        }

        $extras = $this->getExtras();
        $extraCheckItems = $this->getExtraCheckItems();
        if (empty($params["remark"]) && is_array($extraCheckItems))
        {
            $remark = "";
            foreach ($extraCheckItems as $v)
            {
                if ($v["check_status"] == 0)
                    $remark .= $v["remark"] . ";&emsp;";
            }
            $params["remark"] = $remark;
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try
        {
            $partner=PartnerApply::model()->findByPk($checkItem->obj_id);
            $tArr = explode(',', $partner->type);
            if(in_array(2, $tArr)){
                $type=2;
                $checkArr = $this->checkInfo[$type];
            }else{
                $type=1;
                $checkArr = $this->checkInfo[$type][$partner->business_type];
            }

            $partner->mark = $this->getMark($params["checkStatus"]);
            if(!empty($params['level']))
                $partner->level = $params['level'];
            if(!empty($params['credit_amount']))
                $partner->credit_amount = $params['credit_amount']*10000*100;
            
            
            $partner->save();



            FlowService::check($checkItem, $params["checkStatus"], UserService::getUserMainRoleId($userId), $params["remark"], $userId, "0", $extras, $extraCheckItems);

            $info = $this->getPartnerCheckInfo();
            $infoModel=PartnerCheckInfo::model()->find("detail_id=".$params["detail_id"]);
            if(empty($infoModel->id))
            {
                $infoModel=new PartnerCheckInfo();
                $infoModel->create_user_id=$userId;
                $infoModel->create_time=date('Y-m-d H:i:s');
            }
            $content=array();
            foreach ($checkArr as $v)
            {
                $item=$v;
                $item["value"]=$info['info_'.$v["key"]];
                $content[$v["key"]]=$item;
                unset($info['info_'.$v["key"]]);
            }

            // print_r(json_encode($content));die;
            if(!empty($params['credit_amount']))
                $params['credit_amount'] = $params['credit_amount']*10000*100;

            $params['check_status'] = $params['checkStatus'];
            $params['conclusion']   = $params["remark"];
            unset($params['checkStatus']);
            unset($params['remark']);

            $infoModel->setAttributes($params,false);
            $infoModel->content=json_encode($content);
            $infoModel->check_time=date('Y-m-d H:i:s');
            $infoModel->status=1;
            $infoModel->update_user_id=$userId;
            $infoModel->update_time=date('Y-m-d H:i:s');
            $infoModel->save();

            $trans->commit();
            $this->returnSuccess();
        }
        catch (Exception $e)
        {
            try{$trans->rollback();} catch (Exception $ee){}
            $this->returnError($e->getMessage());
        }
    }


    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", $this->mainUrl);
        }

        $data = $this->getCheckData($id);
        $info = PartnerCheckInfo::model()->find("detail_id=".$id);
        if(Utility::isEmpty($data) || empty($info->id))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $credit = PartnerCheckCredit::model()->find("detail_id=".$id);
        $amount = array();
        if(!empty($credit->id)){
            $amount = $credit->getAttributes(true,array("create_user_id","create_time","update_user_id","update_time",));
        }
        // print_r($amount);die;

        $this->pageTitle="合作方风控初审";
        $this->render('/check30/detail',array(
            "data"=>$data[0],
            "info"=>$info->getAttributes(true,array("update_user_id","update_time")),
            "amount"=>$amount,
        ));
    }



    public function getMark($checkStatus){
        $mark = 0;
        switch ($checkStatus) {
            case '0':
            case '1':
                $mark = 1;
                break;
        }
        return $mark;
    }

}