<?php 
/**
 * Created by vector.
 * DateTime: 2018/02/26 14:33
 * Describe：
 */
class PayStopController extends ExportableController{
	public function pageInit()
    {
        $this->filterActions="saveFile,getFile,delFile,detail,getAccPayDetail,getAccounts,submit,list,trash";
        $this->rightCode = "payStop";
        $this->attachmentType=Attachment::C_PAYSTOP;
        $this->newUIPrefix = 'new_';
    }

	
	public function actionIndex()
    {
        // $attr = $this->getSearch();//$_GET[search];
//    	$attr = $_GET[search];
    	$attr = $this->getSearch();

        $query="";
        $start_date='';
        $end_date='';
        if(!empty($attr["start_date"])){
            $start_date=$attr["start_date"];
            unset($attr["start_date"]);
        }
        
        if(!empty($attr["end_date"])){
            $end_date = $attr["end_date"];
            unset($attr["end_date"]);
        }

        if(!empty($start_date) && !empty($end_date))
            $query .= " and a.create_time between '".$start_date."' and '".date( "Y-m-d", strtotime( "$end_date +1 day" ) )."'";
        else if(!empty($start_date))
            $query .= " and a.create_time between '".$start_date."' and '".date( "Y-m-d", strtotime( "+1 day" ) )."'";
        else if(!empty($end_date))
            $query .= " and a.create_time between '".date('Y-m-d')."' and '".date( "Y-m-d", strtotime( "$end_date +1 day" ) )."'";

        $sql="select {col} 
              from t_pay_application a 
              left join t_project p on a.project_id=p.project_id
              left join t_corporation c on c.corporation_id=a.corporation_id 
              left join t_finance_subject fs on fs.subject_id=a.subject_id 
              left join t_contract co on co.contract_id=a.contract_id 
              left join t_pay_application_extra e on a.apply_id=e.apply_id
              left join t_system_user su on su.user_id=e.create_user_id
              ".$this->getWhereSql($attr). $query ." and e.status!=0 and ".AuthorizeService::getUserDataConditionString("a")." and a.status>=".PayApplication::STATUS_CHECKED." order by e.create_time desc, a.apply_id desc {limit}";
        $fields='a.*,p.project_code,(a.amount - a.amount_paid) as amount_stop,e.create_time as stop_apply_time,e.stop_code,e.status as stop_status,c.name as corp_name,fs.name as subject_name,co.type as contract_type,co.category as contract_category,co.contract_code,su.name as create_name';

        $export_str = Mod::app()->request->getParam('export_str');
        // print_r($export_str);die;
        if(!empty($export_str)) {
            $this->export($sql, $fields, $export_str, '付款止付');
            return;
        } else {
            $data = $this->queryTablesByPage($sql, $fields);
        }

        $data = $this->queryTablesByPage($sql,$fields);

        if(!empty($start_date))
            $attr['start_date']=$start_date;
        if(!empty($end_date))
            $attr['end_date']=$end_date;

        $data['search']=$attr;
        // print_r($data);die;
        $this->render('index',$data);
    }

    public function actionExport()
    {
        $attr = $this->getSearch();

        $query="";
        $start_date='';
        $end_date='';
        if(!empty($attr["start_date"])){
            $start_date=$attr["start_date"];
            unset($attr["start_date"]);
        }

        if(!empty($attr["end_date"])){
            $end_date = $attr["end_date"];
            unset($attr["end_date"]);
        }

        if(!empty($start_date) && !empty($end_date))
            $query .= " and a.create_time between '".$start_date."' and '".date( "Y-m-d", strtotime( "$end_date +1 day" ) )."'";
        else if(!empty($start_date))
            $query .= " and a.create_time between '".$start_date."' and '".date( "Y-m-d", strtotime( "+1 day" ) )."'";
        else if(!empty($end_date))
            $query .= " and a.create_time between '".date('Y-m-d')."' and '".date( "Y-m-d", strtotime( "$end_date +1 day" ) )."'";

        $fields='e.stop_code 止付编号, a.apply_id 付款申请编号, e.status 状态, a.amount 付款金额, a.amount_paid 实付金额, (a.amount - a.amount_paid) 止付金额, c.name 交易主体,
                 a.type 付款类型, a.payee 收款单位, fs.name 用途, p.project_code 项目编号, co.category 货款合同类别, co.contract_code 货款合同编号, a.sub_contract_type 付款合同类别, 
                 a.sub_contract_code 付款合同编号, su.name 申请人, e.create_time 止付申请时间, a.currency';
        $sql="select " . $fields . " 
              from t_pay_application a 
              left join t_project p on a.project_id=p.project_id
              left join t_corporation c on c.corporation_id=a.corporation_id 
              left join t_finance_subject fs on fs.subject_id=a.subject_id 
              left join t_contract co on co.contract_id=a.contract_id 
              left join t_pay_application_extra e on a.apply_id=e.apply_id
              left join t_system_user su on su.user_id=e.create_user_id
              ".$this->getWhereSql($attr). $query ." and e.status!=0 and ".AuthorizeService::getUserDataConditionString("a")." and a.status>=".PayApplication::STATUS_CHECKED." order by e.create_time desc, a.apply_id desc";

        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！");
        } else {
            foreach ($data as $key => $row) {
                $data[$key]['付款金额'] = Map::$v['currency'][$row["currency"]]["ico"].number_format($row["付款金额"]/100,2);
                $data[$key]['实付金额'] = Map::$v['currency'][$row["currency"]]["ico"].number_format($row["实付金额"]/100,2);
                $data[$key]['止付金额'] = Map::$v['currency'][$row["currency"]]["ico"].number_format($row["止付金额"]/100,2);
                $data[$key]['状态'] = Map::$v['pay_stop_status'][$row['状态']];
                $data[$key]['付款类型'] = Map::$v['pay_application_type'][$row['付款类型']];
                $data[$key]['货款合同类别'] = Map::$v['contract_category'][$row['货款合同类别']];
                $data[$key]['付款合同类别'] = Map::$v['contract_category'][$row['付款合同类别']];
                unset($data[$key]['currency']);
            }
        }

        $this->exportExcel($data);
    }

    //止付选择列表
    public function actionList()
    {
        // $attr = $this->getSearch();//$_GET[search];
//        $attr = $_GET[search];
        $attr = $this->getSearch();

        $query="";
        $start_date='';
        $end_date='';
        if(!empty($attr["start_date"])){
            $start_date=$attr["start_date"];
            unset($attr["start_date"]);
        }
        
        if(!empty($attr["end_date"])){
            $end_date = $attr["end_date"];
            unset($attr["end_date"]);
        }

        if(!empty($start_date) && !empty($end_date))
            $query .= " and a.create_time between '".$start_date."' and '".date( "Y-m-d", strtotime( "$end_date +1 day" ) )."'";
        else if(!empty($start_date))
            $query .= " and a.create_time between '".$start_date."' and '".date( "Y-m-d", strtotime( "+1 day" ) )."'";
        else if(!empty($end_date))
            $query .= " and a.create_time between '".date('Y-m-d')."' and '".date( "Y-m-d", strtotime( "$end_date +1 day" ) )."'";

        $sql="select {col} 
              from t_pay_application a 
              left join t_corporation c on c.corporation_id=a.corporation_id 
              left join t_finance_subject fs on fs.subject_id=a.subject_id 
              left join t_contract co on co.contract_id=a.contract_id 
              left join t_system_user su on su.user_id=a.create_user_id
              left join t_pay_application_extra e on a.apply_id=e.apply_id
              ".$this->getWhereSql($attr). $query ." and (e.status=0 or e.status=".PayApplicationExtra::STATUS_TRASH.") and ".AuthorizeService::getUserDataConditionString("a")." and a.status>=".PayApplication::STATUS_CHECKED." and a.status<=".PayApplication::STATUS_IN_MANUAL_PAYMENT." order by a.apply_id desc {limit}";
        $fields='a.*,(a.amount - a.amount_paid) as amount_stop,c.name as corp_name,fs.name as subject_name,co.type as contract_type,co.category as contract_category,co.contract_code,su.name as create_name';

        $data = $this->queryTablesByPage($sql,$fields);

        if(!empty($start_date))
            $attr['start_date']=$start_date;
        if(!empty($end_date))
            $attr['end_date']=$end_date;

        $data['search']=$attr;
        // print_r($data);die;
        $this->render('list',$data);
    }

    /**
     * 判断是否可以修改，子类需要修改该方法
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status)
    {
        if($status<PayApplicationExtra::STATUS_CHECKING && $status>PayApplicationExtra::STATUS_TRASH)
        {
            return true;
        }
        else
            return false;
    }

    public function actionAdd()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("参数错误");

        $apply=PayApplication::model()->with("details","contract","details.payment","extra")->findByPk($id);
        if(empty($apply))
            $this->renderError("付款申请信息不存在");

        if(!in_array($apply->status, array(PayApplication::STATUS_CHECKED, PayApplication::STATUS_IN_MANUAL_PAYMENT)))
             $this->renderError("当前状态下不可添加止付信息", $this->mainUrl);

        // $data['stop_id'] = IDService::getPayStopId();

        $data['apply_id'] = $apply->apply_id;
        // $data['balance_amount'] = $apply->amount - $apply->amount_paid;

	    $payInfo = PayService::getAllPayComfirmInfo($apply->apply_id);

	    $payments = array();
	    if($apply->type==PayApplication::TYPE_CONTRACT || $apply->type==PayApplication::TYPE_SELL_CONTRACT){
	    	$attributes=$apply->getAttributesWithRelations(null);
	    	$payments = array_key_exists('details', $attributes) ? $attributes["details"] : array();
	    	$amount_paid = $apply->amount_paid;
	    	if(Utility::isNotEmpty($payments) && $apply->extra->status!=PayApplicationExtra::STATUS_TRASH){
	    		foreach ($payments as $key => $value) {
	    			$res = bccomp($value['amount'],$amount_paid,0);
	    			if($res==-1){
	    				$payments[$key]['amount_paid'] = $value['amount'];
	    				$amount_paid = $amount_paid - $value['amount'];
	    			}else{
	    				$payments[$key]['amount_paid'] = $amount_paid;
	    				break;
	    			}
	    		}
	    	}
	    }

        $this->pageTitle="付款止付";
        $this->render("edit",array(
            "data"=>$data,
            "model"=>$apply,
            "payInfo"=>$payInfo,
            "payments"=>$payments
        ));
    }

    public function actionEdit()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("参数错误", $this->mainUrl);

        $apply=PayApplication::model()->with("details","contract","details.payment","extra")->findByPk($id);
        if(empty($apply))
            $this->renderError("付款申请信息不存在", $this->mainUrl);

        $extra = $apply->extra;
        if(!$this->checkIsCanEdit($extra->status) || !in_array($apply->status, array(PayApplication::STATUS_CHECKED, PayApplication::STATUS_IN_MANUAL_PAYMENT)))
            $this->renderError("当前状态下不可修改止付信息！");

        $payInfo = PayService::getAllPayComfirmInfo($apply->apply_id);
        
        $data['apply_id'] = $apply->apply_id;

        $payments = array();
        if($apply->type==PayApplication::TYPE_CONTRACT || $apply->type==PayApplication::TYPE_SELL_CONTRACT){
	    	$attributes=$apply->getAttributesWithRelations(null);
	    	$payments = array_key_exists('details', $attributes) ? $attributes["details"] : array();
	    }

        $this->pageTitle="付款止付";
        $this->render("edit",array(
            "data"=>$data,
            "model"=>$apply,
            "payInfo"=>$payInfo,
            "payments"=>$payments
        ));
    }


    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("参数错误");

        $apply=PayApplication::model()->with("details","contract","details.payment","extra")->findByPk($id);
        if(empty($apply))
            $this->renderError("付款申请信息不存在");

        $payInfo = PayService::getAllPayComfirmInfo($apply->apply_id);
        $data['apply_id'] = $apply->apply_id;
        $data['status'] = $apply->extra->status;

        $this->pageTitle="付款止付详情";
        $this->render('detail',array(
            "data"=>$data,
            "model"=>$apply,
            "payInfo"=>$payInfo
        ));
    }

    



    public function actionSave()
    {
        $params = $_POST["data"];
        $plans = $params['plans'];
        unset($params['plans']);
        // print_r($params);die;

        $requiredParams = array('apply_id', 'stop_remark');
        $filterInjectParams = Utility::checkRequiredParams($params, $requiredParams);
        if(!$filterInjectParams['isValid'])
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        $params = $filterInjectParams['params'];

        $apply = PayApplication::model()->with('details','extra')->findByPk($params['apply_id']);
        $nowUserId  = Utility::getNowUserId();
        $nowTime    = new CDbExpression("now()");
        $extra = $apply->extra;
        if (empty($apply->apply_id)){
            $this->returnError("付款申请信息不存在！");
        }else{
            if(empty($extra->id)){
            	$extra = new PayApplicationExtra();
            	$extra->apply_id = $apply->apply_id;
            	$extra->create_time = $nowTime;
            }else{
            	if($extra->status==PayApplicationExtra::STATUS_CHECKING || !in_array($apply->status, array(PayApplication::STATUS_CHECKED, PayApplication::STATUS_IN_MANUAL_PAYMENT))){
            	    $this->returnError("当前状态下不可操作止付信息！");
            	}
            }
        }

        $amount_paid = $apply->amount_paid;
        if(!empty($plans)){
        	if(bccomp($amount_paid, $params['total_amount'],0)!=0){
        		$this->returnError("付款计划合计实付金额(".$params['currency_desc'].' '.number_format($params['total_amount']/100, 2).")与付款单实付金额(".$params['currency_desc'].' '.number_format($amount_paid/100, 2).")不一致！");
        	}
        }

        // $obj->setAttributes($params, false);

        /*if(empty($params['isSave'])){
            $obj->status = Payment::STATUS_SUBMITED;
        }else{
            $obj->status = Payment::STATUS_SAVED;
        }*/

        // if($params['currency']==1)
        //     $obj->exchange_rate = 1;
        
       	$extra->create_time 	  = $nowTime;
       	$extra->create_user_id 	  = $nowUserId;
        $extra->update_time       = $nowTime;
        $extra->update_user_id    = $nowUserId;

        $logRemark = ActionLog::getEditRemark($extra->isNewRecord, "付款止付");
        $trans = Utility::beginTransaction();
        try {
        	$extra->status = PayApplicationExtra::STATUS_CHECKING;
        	$extra->stop_remark = $params['stop_remark'];
        	if(empty($extra->stop_code))
        		$extra->stop_code = 'ZF'.IDService::getPayStopId();
        	$extra->save();

            /*if(empty($params['isSave'])){
                $obj->apply->amount_paid += $obj->amount;
                $obj->apply->save();
                if(bccomp($obj->apply->amount,$obj->apply->amount_paid)==0) {
                    PayService::donePaidPayApplication($obj->apply);
                    TaskService::doneTask($obj->apply_id, Action::ACTION_ACTUAL_PAY, ActionService::getActionRoleIds(Action::ACTION_ACTUAL_PAY));
                }
            }*/
            if($apply->type==PayApplication::TYPE_CONTRACT || $apply->type==PayApplication::TYPE_SELL_CONTRACT){
            	if(Utility::isNotEmpty($plans)){
            		foreach ($plans as $key => $value) {
            			$res = PayService::updatePaidAmount($value['detail_id'], $value['amount_paid']);
            			if(!$res)
            				throw new Exception("更新付款申请详情计划失败");
            				
            			/*$res2 = PaymentPlanService::updatePaidAmount($value['plan_id'], -($value['amount'] - $value['amount_paid']));
            			if(!$res2)
            				throw new Exception("更新合同付款计划失败");*/
            		}
            	}
            }



            FlowService::startFlow(19,$apply->apply_id);
            TaskService::doneTask($apply->apply_id,Action::ACTION_STOP_BACK);
            TaskService::doneTask($apply->apply_id,Action::ACTION_ACTUAL_PAY);

            
            $trans->commit();
            Utility::addActionLog(json_encode($extra->oldAttributes), $logRemark, "PayApplicationExtra", $extra->apply_id);
            $this->returnSuccess($extra->apply_id);
            
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$PAY_STOP_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }

    }


    public function actionSubmit()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", $this->mainUrl);
        }

        // $obj = Payment::model()->with('apply')->findByPk($id);
        $apply = PayApplication::model()->with('extra','details','details.payment')->findByPk($id);
        
        $extra = $apply->extra;
        if($extra->status==PayApplicationExtra::STATUS_CHECKING || !in_array($apply->status, array(PayApplication::STATUS_CHECKED, PayApplication::STATUS_IN_MANUAL_PAYMENT)))
            $this->returnError("当前状态下不可提交付款止付信息！");

        $oldStatus=$extra->status;
        $trans = Utility::beginTransaction();
        try{
            $extra->status = PayApplicationExtra::STATUS_CHECKING;
            $extra->update_time      = new CDbExpression("now()");
            $extra->create_user_id   = Utility::getNowUserId();
            $extra->update_user_id   = Utility::getNowUserId();
            $extra->save();

            if(!empty($apply->contract_id) && is_array($apply->details) && count($apply->details)>0){
        	  	foreach ($apply->details as $detail) {
        	  	  $res = PaymentPlanService::updatePaidAmount($detail['plan_id'], -($detail['amount'] - $detail['amount_paid']));
        	  	  if(!$res)
        	  	    throw new Exception("更新合同付款计划失败");
        	  	}
        	}

        	FlowService::startFlow(19,$apply->apply_id);
            TaskService::doneTask($apply->apply_id,Action::ACTION_STOP_BACK);
            TaskService::doneTask($apply->apply_id,Action::ACTION_ACTUAL_PAY);

            $trans->commit();
            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交付款止付", "PayApplicationExtra", $apply->apply_id);
            $this->returnSuccess();
            
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$PAY_STOP_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
        
    }


    public function actionTrash()
    {
        $id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($id))
            $this->returnError("参数错误");

        $apply=PayApplication::model()->with("extra")->findByPk($id);
        $extra = $apply->extra;
        if(empty($apply) || empty($extra->id))
            $this->returnError("止付信息不存在");
        
        if(!$this->checkIsCanEdit($extra->status))
            $this->returnError("当前状态下不允许作废止付信息！");

        try{
        	$extra->status = PayApplicationExtra::STATUS_TRASH;
        	$extra->update_time = new CDbExpression('now()');
        	$extra->update_user_id = Utility::getNowUserId();
            $res=$extra->save();
            if($res === true) {
            	TaskService::doneTask($apply->apply_id,Action::ACTION_STOP_BACK);
                
                Utility::addActionLog(null, "付款止付作废", "PayApplication", $apply->apply_id);
                $this->returnSuccess("Success");
            }
            else
                $this->returnError("作废失败：".$res);

            
        }
        catch (Exception $e)
        {
            $this->returnError("作废失败");
        }
    }
}