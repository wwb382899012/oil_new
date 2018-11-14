<?php

/**
 * Created by vector.
 * DateTime: 2017/10/30 11:20
 * Describe：销项票发票确认
 */
class InvoiceController extends AttachmentController
{
    public function pageInit()
    {
        $this->filterActions="reset,getGoods,getPayment,submit";
        $this->rightCode = "invoice";
        $this->newUIPrefix = 'new_';
    }

    /**
     * 判断是否可以修改，子类需要修改该方法
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status)
    {
        if($status==Invoice::STATUS_BACK || $status == Invoice::STATUS_SAVED)
        {
            return true;
        }
        else
            return false;
    }

    
    public function actionIndex()
    {
//        $attr = $_GET[search];
        $attr = $this->getSearch();
        $query="";
        $status="";
        if(isset($attr["status"]) && $attr["status"]=="0"){
            $status="0";
            $query=" and a.amount>a.amount_paid";
            unset($attr["status"]);
        }else if($attr["status"]=="1"){
            $status = "1";
            $query=" and a.amount=a.amount_paid";
            unset($attr["status"]);
        }

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

        $user = SystemUser::getUser(Utility::getNowUserId());
        $sql="select {col}"
            ." from t_invoice_application a "
            ." left join t_project p on a.project_id=p.project_id "
            ." left join t_corporation co on a.corporation_id=co.corporation_id "
            ." left join t_contract c on a.contract_id=c.contract_id "
            ." left join t_partner d on d.partner_id = c.partner_id "
            ." left join t_system_user u on a.create_user_id=u.user_id"
            ." left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1"
            .$this->getWhereSql($attr);
        $sql    .= $query;
        $sql    .= " and a.type=".ConstantMap::OUTPUT_INVOICE_TYPE." and a.status=".InvoiceApplication::STATUS_PASS." and a.corporation_id in (".$user['corp_ids'].") order by a.apply_id desc {limit}";
        // echo $sql;die;
        $fields = "a.apply_id,a.apply_code,a.type,a.type_sub,a.create_time,a.create_user_id,case when a.amount=a.amount_paid then 1 else 0 end as status,c.contract_code,c.contract_id,
                   a.contract_type,p.project_id,p.project_code,p.type as project_type,u.name as user_name,d.partner_id,d.name as partner_name,
                   a.invoice_contract_type,a.invoice_contract_code,a.amount,a.num,a.amount_paid,
                   co.corporation_id,co.name as corporation_name,cf.code_out";
        $data = $this->queryTablesByPage($sql,$fields);

        if($status=="0" || $status=="1")
            $attr["status"]=$status;
        // print_r($data);die;
        if(!empty($start_date))
            $attr['start_date']=$start_date;
        if(!empty($end_date))
            $attr['end_date']=$end_date;
        $data['search'] = $attr;
        // $data['type'] = ConstantMap::OUTPUT_INVOICE_TYPE;
        $this->render('index',$data);
    }

    public function actionAdd()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("参数有误", $this->mainUrl);
        }

        $sql = "select i.apply_id,i.apply_code,i.invoice_date,i.exchange_rate,i.company_name,i.tax_code,i.amount_paid,
                i.invoice_type,i.contract_type,i.type,i.type_sub,i.invoice_contract_code,i.amount as total_amount,
                i.invoice_contract_type,ifnull(i.num, 0) as num,co.name as corporation_name,i.remark as apply_remark,i.corporation_id,
                c.contract_id, c.contract_code, p.project_id,p.project_code,i.status,
                i.address,i.phone,i.bank_name,i.bank_account
                from t_invoice_application i 
                left join t_corporation co on i.corporation_id=co.corporation_id
                left join t_contract c on i.contract_id=c.contract_id
                left join t_project p on i.project_id=p.project_id
                where i.apply_id=".$id;
        $data = Utility::query($sql);

        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $invoiceDetail  = InvoiceService::getInvoiceApplyDetail($data[0]['apply_id']);
        /*foreach ($invoiceDetail as $key => $value) {
            $data[0]['total_amount'] += $value['amount'];
        }*/
        foreach ($invoiceDetail as $key => $value) {
            $invoiceDetail[$key]['unit_format'] = Map::$v['goods_unit'][$value['unit']]['name'];
        }
        if(!empty($data[0]['exchange_rate'])){
            $data[0]['dollar_amount'] = $data[0]['total_amount'] * $data[0]['exchange_rate'];
        }

        if($data[0]['type_sub']==ConstantMap::PAYMENT_GOODS_TYPE){
            foreach ($invoiceDetail as $key => $value) {
                $allGoods[$value['goods_id']]['goods_id']=$value['goods_id'];
                $allGoods[$value['goods_id']]['name']=$value['goods_name'];
            }
        }
        
        $plans          = InvoiceService::getPaymentById($data[0]['apply_id'], $data[0]['type']);

        $attachments    = InvoiceAttachment::model()->findAllToArray('base_id='.$data[0]['apply_id'].' AND status=1');

        $invoices       = array();
        $invoiceArr     =  InvoiceService::getAllInvoiceDetail($data[0]['apply_id']);
        $goodsItems     = array();
        foreach ($invoiceDetail as $key => $value) {
            $goodsItems[$value['goods_id']]['unit'] = $value['unit'];
            $goodsItems[$value['goods_id']]['unit_format'] = Map::$v['goods_unit'][$value['unit']]['name'];
            $goodsItems[$value['goods_id']]['price'] = $value['price'];
            $goodsItems[$value['goods_id']]['rate'] = $value['rate'];
        }
        // print_r($invoiceArr);die;
        $total_amount = 0.0;
        if(Utility::isNotEmpty($invoiceArr)){
            foreach ($invoiceArr as $key => $value) {
                $total_amount += $value['amount'];
                $invoices[$value['invoice_id']]['detail'][]      = $value;
                $invoices[$value['invoice_id']]['remark']        = $value['remark'];
                $invoices[$value['invoice_id']]['invoice_num']   = $value['invoice_num'];
            }
        }
        $data[0]['total_invoice_amount'] = $total_amount;
        $map = Map::$v;
        $title = $map['invoice_output_type'][$data[0]['type_sub']];
        $this->pageTitle=$title."开票";
        // print_r($uProjects);die;
        $this->render("edit",array(
            'data'=>$data[0],
            "invoiceDetail"=>$invoiceDetail,
            "attachments"=>$attachments,
            "plans"=>$plans,
            "allGoods"=>$allGoods,
            "invoiceItems"=>$invoiceDetail,
            "invoices"=>$invoices,
            "goodsItems"=>$goodsItems
        ));
    }


    public function actionEdit()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("参数有误", $this->mainUrl);
        }

        $sql = "select i.apply_id,i.apply_code,i.invoice_date,i.exchange_rate,i.company_name,i.tax_code,
                i.invoice_type,i.contract_type,i.type,i.type_sub,i.invoice_contract_code,i.amount as total_amount,i.amount_paid,
                i.invoice_contract_type,ifnull(i.num, 0) as num,co.name as corporation_name,i.remark as apply_remark,i.corporation_id,
                c.contract_id, c.contract_code, p.project_id,p.project_code,
                i.address,i.phone,i.bank_name,i.bank_account
                from t_invoice_application i 
                left join t_corporation co on i.corporation_id=co.corporation_id
                left join t_contract c on i.contract_id=c.contract_id
                left join t_project p on i.project_id=p.project_id
                where i.apply_id=".$id;
        $data = Utility::query($sql);

        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $invoiceDetail  = InvoiceService::getInvoiceApplyDetail($data[0]['apply_id']);
        // print_r($invoiceDetail);die;
        /*foreach ($invoiceDetail as $key => $value) {
            $data[0]['total_amount'] += $value['amount'];
        }*/
        if(bccomp($data[0]['exchange_rate'],0)>0){
            $data[0]['dollar_amount'] = $data[0]['total_amount'] / $data[0]['exchange_rate'];
        }

        if($data[0]['type_sub']==ConstantMap::PAYMENT_GOODS_TYPE){
            foreach ($invoiceDetail as $key => $value) {
                $allGoods[$value['goods_id']]['goods_id']=$value['goods_id'];
                $allGoods[$value['goods_id']]['name']=$value['goods_name'];
            }
        }
        
        $plans          = InvoiceService::getPaymentById($data[0]['apply_id'], $data[0]['type']);

        $attachments    = InvoiceAttachment::model()->findAllToArray('base_id='.$data[0]['apply_id'].' AND status=1');

        $invoiceItems   = array();
        $invoices       = array();
        $invoiceArr     =  InvoiceService::getAllInvoiceDetail($data[0]['apply_id']);
        // print_r($invoiceArr);die;
        $goodsItems     = array();
        foreach ($invoiceDetail as $key => $value) {
            $goodsItems[$value['goods_id']]['unit'] = $value['unit'];
            $goodsItems[$value['goods_id']]['unit_format'] = Map::$v['goods_unit'][$value['unit']]['name'];
            $goodsItems[$value['goods_id']]['price'] = $value['price'];
            $goodsItems[$value['goods_id']]['rate'] = $value['rate'];
        }
        // print_r($goodsItems);die;
        $total_amount = 0.0;
        if(Utility::isNotEmpty($invoiceArr)){
            $data[0]['invoice_id'] = $invoiceArr[0]['invoice_id'];
            $data[0]['remark'] = $invoiceArr[0]['remark'];
            $data[0]['invoice_num'] = $invoiceArr[0]['invoice_num'];
            foreach ($invoiceArr as $key => $value) {
                if($value['invoice_id']==$data[0]['invoice_id']){
                    $invoiceItems[$value['invoice_id']][] = $value;
                    continue;
                }
                $total_amount += $value['amount'];
                $invoices[$value['invoice_id']]['detail'][]      = $value;
                $invoices[$value['invoice_id']]['remark']        = $value['remark'];
                $invoices[$value['invoice_id']]['invoice_num']   = $value['invoice_num'];
            }
        }

        foreach ($invoiceItems[$data[0]['invoice_id']] as $key => $value) {
            $invoiceItems[$data[0]['invoice_id']][$key]['unit_format'] = Map::$v['goods_unit'][$value['unit']]['name'];
        }
        $data[0]['total_invoice_amount'] = $total_amount;
        // print_r($invoiceItems[$data[0]['invoice_id']]);die;
        $map = Map::$v;
        $title = $map['invoice_output_type'][$data[0]['type_sub']];
        $this->pageTitle=$title."修改";
        $this->render("edit",array(
            'data'=>$data[0],
            "invoiceDetail"=>$invoiceDetail,
            "attachments"=>$attachments,
            "plans"=>$plans,
            "allGoods"=>$allGoods,
            "invoiceItems"=>$invoiceItems[$data[0]['invoice_id']],
            "invoices"=>$invoices,
            "goodsItems"=>$goodsItems
        ));
    }

    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("参数有误", $this->mainUrl);
        }

        $sql = "select i.apply_id,i.apply_code,i.invoice_date,i.exchange_rate,i.company_name,i.tax_code,
				i.address,i.phone,i.bank_name,i.bank_account,
                i.invoice_type,i.contract_type,i.type,i.type_sub,i.invoice_contract_code,i.amount as total_amount,
                i.invoice_contract_type,ifnull(i.num, 0) as num,co.name as corporation_name,i.remark as apply_remark,i.corporation_id,
                c.contract_id, c.contract_code, p.project_id,p.project_code
                from t_invoice_application i 
                left join t_corporation co on i.corporation_id=co.corporation_id
                left join t_contract c on i.contract_id=c.contract_id
                left join t_project p on i.project_id=p.project_id
                where i.apply_id=".$id;
        $data = Utility::query($sql);

        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $invoiceDetail  = InvoiceService::getInvoiceApplyDetail($data[0]['apply_id']);
        /*foreach ($invoiceDetail as $key => $value) {
            $data[0]['total_amount'] += $value['amount'];
        }*/
        if(bccomp($data[0]['exchange_rate'], 0, 6)>0){
            $data[0]['dollar_amount'] = $data[0]['total_amount'] / $data[0]['exchange_rate'];
        }
        
        $plans          = InvoiceService::getPaymentById($data[0]['apply_id'], $data[0]['type']);

        $attachments    = InvoiceAttachment::model()->findAllToArray('base_id='.$data[0]['apply_id'].' AND status=1');

        $invoiceItems   = array();
        $invoices       = array();
        $invoiceArr     =  InvoiceService::getAllInvoiceDetail($data[0]['apply_id']);
        // print_r($invoiceArr);die;
        $total_amount = 0.0;
        $invoice_amount = 0.0;
        if(Utility::isNotEmpty($invoiceArr)){
            $data[0]['invoice_id'] = $invoiceArr[0]['invoice_id'];
            $data[0]['status'] = $invoiceArr[0]['status'];
            foreach ($invoiceArr as $key => $value) {
                if($value['invoice_id']==$data[0]['invoice_id']){
                    $invoiceItems[$value['invoice_id']]['detail'][] = $value;
                    $invoice_amount += $value['amount'];
                    $invoiceItems[$value['invoice_id']]['remark']        = $value['remark'];
                    $invoiceItems[$value['invoice_id']]['invoice_num']   = $value['invoice_num'];
                    continue;
                }
                $total_amount += $value['amount'];
                $invoices[$value['invoice_id']]['detail'][]      = $value;
                $invoices[$value['invoice_id']]['remark']        = $value['remark'];
                $invoices[$value['invoice_id']]['invoice_num']   = $value['invoice_num'];
            }
        }

        $data[0]['total_invoice_amount'] = $total_amount;
        $data[0]['invoice_amount'] = $invoice_amount;
        $map = Map::$v;
        $title = $map['invoice_output_type'][$data[0]['type_sub']];
        $this->pageTitle=$title."开票详情";
        $this->render("detail",array(
            "data"=>$data[0],
            "invoiceDetail"=>$invoiceDetail,
            "attachments"=>$attachments,
            "plans"=>$plans,
            "invoices"=>$invoices,
            "invoiceItems"=>$invoiceItems
        ));
    }


    public function actionSave()
    {
        $params = $_POST["data"];
        // print_r($params);die;

        $invoiceItems = $params['invoiceItems'];
        unset($params['invoiceItems']);

        if(Utility::isEmpty($invoiceItems))
            $this->returnError("请添加开票明细！");

        $uniqArr = array();
        foreach ($invoiceItems as $key => $value) {
            $total_amount += $value['amount'];
            if($params['type_sub']==ConstantMap::PAYMENT_GOODS_TYPE){
                $uniqArr[$value['goods_id']]=$value['goods_id'];
                $title = "品名";
            }else{
                $uniqArr[md5(trim($value['invoice_name']))]=$value['invoice_name'];
                $title = "费用名称";
            }
        }
        // print_r(count($uniqArr));die;
        if(count($uniqArr)!=count($invoiceItems))
            $this->returnError("开票明细中".$title."重复！");


        $requiredParams = array('apply_id', 'type', 'type_sub', 'corporation_id', 'invoice_num');
        if($params['type_sub']==ConstantMap::PAYMENT_GOODS_TYPE){
            $requiredParams[] = 'contract_id';
            $requiredParams[] = 'project_id';
        }
        
        $filterInjectParams = Utility::checkRequiredParams($params, $requiredParams);
        if(!$filterInjectParams['isValid'])
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        $params = $filterInjectParams['params'];

        $nowUserId  = Utility::getNowUserId();
        $nowTime    = new CDbExpression("now()");
        if(empty($params['invoice_id'])){
            $obj = new Invoice();
            $obj->status_time       = $nowTime;
            $obj->create_time       = $nowTime;
            $obj->create_user_id    = $nowUserId;
        }else{
            $obj = Invoice::model()->findByPk($params["invoice_id"]);
            if(empty($obj->invoice_id))
                $this->returnError("当前开票信息不存在！");
        }
        
        unset($params['invoice_id']);
        $obj->setAttributes($params, false);

        if(empty($params['isSave'])){
            $obj->status = Invoice::STATUS_CHECKING;
            $obj->status_time = $nowTime;
        }else{
            $obj->status = Invoice::STATUS_SAVED;
        }
        
        $obj->amount            = $total_amount;
        $obj->update_time       = $nowTime;
        $obj->update_user_id    = $nowUserId;

        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "货款销项票开票");
        $trans = Utility::beginTransaction();
        try {

            $obj->save();

            InvoiceService::saveInvoiceDetail($invoiceItems, $obj->invoice_id, $params['isSave'], $params['type_sub']);
            

            if(empty($params['isSave'])){
                FlowService::startFlowForCheck16($obj->invoice_id);

                TaskService::doneTask($obj->apply_id, Action::ACTION_29);
            }

            // TaskService::addTasks(Action::ACTION_11, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_11), 0, $contract->corporation_id);
            
            $trans->commit();

            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "Invoice", $obj->invoice_id);
            $this->returnSuccess($obj->apply_id);
            
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$INVOICE_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }

    }


    public function actionSubmit()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", $this->mainUrl);
        }

        $invoice = Invoice::model()->findByPk($id);
        if(!$this->checkIsCanEdit($invoice->status))
            $this->returnError("当前状态下不可提交销项票开票信息！");

        $trans = Utility::beginTransaction();
        try{
            $oldStatus = $invoice->status;
            $invoice->status = Invoice::STATUS_CHECKING;
            $invoice->status_time      = new CDbExpression("now()");
            $invoice->update_time      = new CDbExpression("now()");
            $invoice->update_user_id   = Utility::getNowUserId();
            $invoice->save();

            FlowService::startFlowForCheck16($invoice->invoice_id);

            TaskService::doneTask($invoice->apply_id, Action::ACTION_29);

            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交货款销项票开票", "Invoice", $invoice->invoice_id);
            $this->returnSuccess();
            
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$INVOICE_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
        
    }


}