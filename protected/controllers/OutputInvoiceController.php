<?php

/**
 * Created by vector.
 * DateTime: 2017/10/28 11:20
 * Describe：销项票
 */
class OutputInvoiceController extends AttachmentController
{
    public function pageInit()
    {
        $this->filterActions="getFile,reset,saveFile,delFile,getGoods,getPayment,submit,getTaxCode,getContractType,getCompanyDetail,getCompany";
        $this->rightCode = "outputInvoice";
        $this->attachmentType=Attachment::C_INVOICE_APPLY;
        $this->newUIPrefix = 'new_';
    }

    /**
     * 判断是否可以修改，子类需要修改该方法
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status)
    {
        if($status==InvoiceApplication::STATUS_BACK || $status == InvoiceApplication::STATUS_SAVED)
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
            ." left join t_partner d on d.partner_id=c.partner_id "
            ." left join t_system_user u on a.create_user_id=u.user_id"
            ." left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1"
            .$this->getWhereSql($attr);
        $sql    .= $query;
        $sql    .= " and a.type=".ConstantMap::OUTPUT_INVOICE_TYPE." and a.corporation_id in (".$user['corp_ids'].") order by a.apply_id desc {limit}";
        $fields = "a.apply_id,a.apply_code,a.type,a.type_sub,a.create_time,a.create_user_id,a.status,c.contract_code,c.contract_id,
                   a.contract_type,p.project_id,p.project_code,p.type as project_type,u.name as user_name,d.partner_id,d.name as partner_name,
                   a.invoice_contract_type,a.invoice_contract_code,a.amount,a.num,
                   co.corporation_id,co.name as corporation_name,cf.code_out,d.name as partner_name";
        $data = $this->queryTablesByPage($sql,$fields);
        // print_r($data);die;
        if(!empty($start_date))
            $attr['start_date']=$start_date;
        if(!empty($end_date))
            $attr['end_date']=$end_date;
        $data['search'] = $attr;
        $data['type'] = ConstantMap::OUTPUT_INVOICE_TYPE;
        $this->render('/invoiceApply/index',$data);
    }

    public function actionAdd()
    {
        $type=Mod::app()->request->getParam("type");
        if(!Utility::checkQueryId($type))
            $this->renderError("参数有误!", $this->mainUrl);
        
        $data['type'] = ConstantMap::OUTPUT_INVOICE_TYPE;
        $data['type_sub'] = $type;
        // $data['invoice_type'] = ConstantMap::INVOICE_RATE_NORMAL_TYPE;

        if($data['type_sub']==ConstantMap::PAYMENT_GOODS_TYPE)
            $data['contract_type'] = ConstantMap::SALE_TYPE;

        $data['title_map_name'] = "invoice_output_type";

        $data['apply_id'] = IDService::getInoviceApplicationId();


        $contracts  = InvoiceService::getAllContract();
        $projects   = InvoiceService::getAllProject();
        $uProjects  = InvoiceService::getUniqueProject();
        // $goods      = Goods::getActiveTreeTable();

        $map = Map::$v;
        $title = $map['invoice_output_type'][$type];
        $this->pageTitle=$title."录入";
        // print_r($data);die;
        $this->render("/invoiceApply/edit",array(
            "data"=>$data,
            // "goods"=>$goods,
            "contracts"=>$contracts,
            "projects"=>$projects,
            "uProjects"=>$uProjects,
        ));
    }


    /*public function actionGetProjects()
    {
        $id=Mod::app()->request->getParam("corpId");
        if(!Utility::checkQueryId($id))
            $this->renderError("参数错误");

        $sql="select * from t_project where corporation_id=".$id." and status>=".Project::STATUS_SUBMIT." and status<=".Project::STATUS_DONE." and ".AuthorizeService::getUserDataConditionString()." order by project_id desc";
        $data= Utility::query($sql);
        $this->returnSuccess($data);
    }*/

    public function actionEdit()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("参数有误", $this->mainUrl);
        }

        $sql = "select i.apply_id,i.apply_code,i.invoice_date,i.exchange_rate,i.company_name,i.tax_code,
                i.invoice_type,i.contract_type,i.type,i.type_sub,i.invoice_contract_code,
                i.invoice_contract_type,i.num,co.name as corporation_name,i.remark,i.corporation_id,
                c.contract_id, c.contract_code,c.type as c_type, p.project_id,p.project_code,i.status,
                i.address,i.phone,i.bank_name,i.bank_account
                from t_invoice_application i 
                left join t_corporation co on i.corporation_id=co.corporation_id
                left join t_contract c on i.contract_id=c.contract_id
                left join t_project p on i.project_id=p.project_id
                where i.apply_id=".$id;
        $data = Utility::query($sql);

        // print_r($data);die;
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }
        $invoiceItems   = InvoiceService::getInvoiceApplyDetail($id);
        $paymentItems   = InvoiceService::getPayment($data[0]['contract_id'], $id, 2);

        $attachments    = InvoiceAttachment::model()->findAllToArray('base_id='.$id.' AND status=1');
        $contracts      = InvoiceService::getAllContract();
        $projects       = InvoiceService::getAllProject();
        $uProjects      = InvoiceService::getUniqueProject();

        /*foreach ($invoiceItems as $key => $value) {
            $allGoods[$value['goods_id']]['goods_id']=$value['goods_id'];
            $allGoods[$value['goods_id']]['name']=$value['goods_name'];
        }*/

        $allGoods = InvoiceService::getContractGoods($data[0]['contract_id'], $data[0]['c_type']);

        $data[0]['title_map_name'] = "invoice_output_type";

        // print_r($paymentItems);die;
        $map = Map::$v;
        $title = $map['invoice_output_type'][$data[0]['type_sub']];
        $this->pageTitle=$title."修改";
        $this->render("/invoiceApply/edit",array(
            "data"=>$data[0],
            "invoiceItems"=>$invoiceItems,
            "attachments"=>$attachments,
            "paymentItems"=>$paymentItems,
            "allGoods"=>$allGoods,
            "contracts"=>$contracts,
            "projects"=>$projects,
            "uProjects"=>$uProjects,
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
                i.invoice_type,i.contract_type,i.type,i.type_sub,i.invoice_contract_code,
                i.invoice_contract_type,i.num,co.name as corporation_name,i.remark,
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

        $invoiceDetail  = InvoiceService::getInvoiceApplyDetail($id);
        foreach ($invoiceDetail as $key => $value) {
            $data[0]['total_amount'] += $value['amount'];
        }
        if(bccomp($data[0]['exchange_rate'], 0, 6)>0){
            $data[0]['dollar_amount'] = $data[0]['total_amount'] / $data[0]['exchange_rate'];
        }
        
        $plans          = InvoiceService::getPaymentById($id, 2);

        $attachments    = InvoiceAttachment::model()->findAllToArray('base_id='.$id.' AND status=1');
        
        $data[0]['title_map_name'] = "invoice_output_type";
        $map = Map::$v;
        $title = $map['invoice_output_type'][$data[0]['type_sub']];
        $this->pageTitle=$title."详情";
        $this->render("/invoiceApply/detail",array(
            "data"=>$data[0],
            "invoiceDetail"=>$invoiceDetail,
            "attachments"=>$attachments,
            "plans"=>$plans,
        ));
    }


    public function actionGetGoods()
    {
        $data   = array();
        $id     = Mod::app()->request->getParam("contract_id");
        $type   = Mod::app()->request->getParam("type");
        if(!Utility::checkQueryId($id) || !Utility::checkQueryId($type))
            $this->returnSuccess($data);
            // $this->renderError("参数错误");

        /*$sql = "select g.goods_id, g.name 
                from t_contract_goods c 
                left join t_goods g on c.goods_id=g.goods_id
                where c.contract_id=".$id." and c.type=".$type." group by g.goods_id order by c.detail_id asc";
        $data= Utility::query($sql);*/
        $data = InvoiceService::getContractGoods($id, $type);
        $this->returnSuccess($data);
    }


    public function actionGetPayment()
    {
        $payment = array();
        $id = Mod::app()->request->getParam("contract_id");
        if(!Utility::checkQueryId($id))
            $this->returnSuccess($payment);
            // $this->renderError("参数错误");

        $sql = "select plan_id,project_id,contract_id,pay_date,expense_type,expense_name,
                amount as pay_amount,currency,amount_invoice
                from t_payment_plan 
                where contract_id=".$id." and type=2 order by plan_id asc";
        $data    = Utility::query($sql);
        $map = Map::$v;
        if(Utility::isNotEmpty($data)){
            foreach ($data as $key => $value) {
                $payment[$value['plan_id']] = $value;
                $payment[$value['plan_id']]['currency_desc']    = $map['currency'][$value['currency']]['name'];
                $payment[$value['plan_id']]['currency_ico']     = $map['currency'][$value['currency']]['ico'];
                $payment[$value['plan_id']]['expense_desc']     = $value['expense_type']!=5?$map['pay_type'][$value['expense_type']]['name']:$map['pay_type'][$value['expense_type']]['name'].'--'.$value['expense_name'];
            }
        }
        $this->returnSuccess($payment);
    }


    public function actionSave()
    {
        $params = $_POST["data"];
        // print_r($params);die;

        $invoiceItems = $params['invoiceItems'];
        $paymentItems = $params['payment'];
        unset($params['invoiceItems']);
        unset($params['payment']);

        if(Utility::isEmpty($invoiceItems))
            $this->returnError("请添加发票明细！");
        // if($params['type_sub']==ConstantMap::PAYMENT_GOODS_TYPE && Utility::isEmpty($paymentItems))
        //     $this->returnError("请添加付款计划明细！");

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
            $this->returnError("发票明细中".$title."重复！");


        $requiredParams = array('apply_id', 'type', 'type_sub', 'corporation_id', 'invoice_type');
        if($params['type_sub']==ConstantMap::PAYMENT_GOODS_TYPE){
            $requiredParams[] = 'contract_id';
            $requiredParams[] = 'project_id';
        }
        
        $filterInjectParams = Utility::checkRequiredParams($params, $requiredParams);
        if(!$filterInjectParams['isValid'])
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        $params = $filterInjectParams['params'];


        $obj = InvoiceApplication::model()->findByPk($params["apply_id"]);
        $nowUserId  = Utility::getNowUserId();
        $nowTime    = new CDbExpression("now()");
        if (empty($obj->apply_id)){
            $obj = new InvoiceApplication();
            $obj->apply_code        = 'KP'.IDService::getId('invoice_KP', 5);
            $obj->status_time       = $nowTime;
            $obj->create_time       = $nowTime;
            $obj->create_user_id    = $nowUserId;
        }

        $params['num'] = 0;
        $obj->setAttributes($params, false);

        if(empty($params['isSave'])){
            $obj->status = InvoiceApplication::STATUS_CHECKING;
            $obj->status_time = $nowTime;
        }else{
            $obj->status = InvoiceApplication::STATUS_SAVED;
        }
        
        $obj->amount            = $total_amount;
        $obj->update_time       = $nowTime;
        $obj->update_user_id    = $nowUserId;
        $obj->invoice_date      = empty($obj->invoice_date)?null:$obj->invoice_date;

        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "销项票申请");
        $trans = Utility::beginTransaction();
        try {

            $obj->save();

            InvoiceService::saveInvoiceApplyDetail($invoiceItems, $obj->apply_id, $params['isSave'], $params['type_sub']);
            
            if(Utility::isNotEmpty($paymentItems)){
                InvoiceService::savePaymentDetail($paymentItems, $obj->apply_id, $params['isSave']);
            }else{
                $sql    = "select detail_id from t_invoice_pay_plan where apply_id=" . $obj->apply_id;
                $data   = Utility::query($sql);
                $p      = array();
                if (Utility::isNotEmpty($data)) {
                    foreach ($data as $v) {
                        $p[$v["detail_id"]] = $v['detail_id'];
                    }
                    InvoicePayPlan::model()->deleteAll('detail_id in(' . implode(',', $p) . ')');
                }
            }
            

            if(empty($params['isSave'])){
                FlowService::startFlowForCheck17($obj->apply_id);

                TaskService::doneTask($obj->apply_id, Action::ACTION_37);
            }

            // TaskService::addTasks(Action::ACTION_11, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_11), 0, $contract->corporation_id);
            
            $trans->commit();

            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "InvoiceApplication", $obj->apply_id);
            $this->returnSuccess($obj->apply_id);
            
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$INVOICE_APPLY_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }

    }


    public function actionSubmit()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", $this->mainUrl);
        }

        $invoice = InvoiceApplication::model()->findByPk($id);
        if(!$this->checkIsCanEdit($invoice->status))
            $this->returnError("当前状态下不可提交销项票申请信息！");

        $oldStatus = $invoice->status;
        $trans = Utility::beginTransaction();
        try{
            $invoice->status = InvoiceApplication::STATUS_CHECKING;
            $invoice->status_time      = new CDbExpression("now()");
            $invoice->update_time      = new CDbExpression("now()");
            $invoice->update_user_id   = Utility::getNowUserId();
            $invoice->save();

            FlowService::startFlowForCheck17($invoice->apply_id);

            TaskService::doneTask($invoice->apply_id, Action::ACTION_37);

            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交销项票申请", "InvoiceApplication", $invoice->apply_id);
            $this->returnSuccess();
            
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$INVOICE_APPLY_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
        
    }


    public function actionGetTaxCode()
    {
        $company_name =  Mod::app()->request->getParam("company_name");
        $code = "";
        if(empty($company_name))
            $this->returnSuccess();

        $invoice = InvoiceApplication::model()->find("company_name='".trim($company_name)."'");
        if(!empty($invoice->apply_id))
            $code = $invoice->tax_code;

        $this->returnSuccess($code);
    }

    public function actionGetContractType()
    {
        $contractId =  Mod::app()->request->getParam("contract_id");
        $type = "";
        if(empty($contractId))
            $this->returnSuccess();
        $contract = Contract::model()->findByPk($contractId);
        $type = $contract->category;
        $this->returnSuccess($type);
    }


    public function actionGetCompanyDetail()
    {
        $data = array();
        $name = Mod::app()->request->getParam("company_name");
        if(empty($name))
            $this->returnSuccess($data);
        /*$invoice = InvoiceApplication::model()->find(
            array(
              'select'=>array('phone','address','bank_account','bank_name'),
              'order' => 'apply_id DESC',
              'limit' => '1',
              'condition' => 'company_name like "%'.$name.'%"'
            )
        );*/
        $sql = "select phone,address,bank_account,bank_name from t_invoice_application where status>=" . InvoiceApplication::STATUS_PASS . " and company_name like '%".$name."' order by apply_id desc limit 1";
        $data = Utility::query($sql);
        $this->returnSuccess($data);
    }

    public function actionGetCompany() {
        $contract_id = Mod::app()->request->getParam('contract_id');
        if (!Utility::checkQueryId($contract_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        
        $sql = 'select p.partner_id,p.name as partner_name
                from t_contract c
                left join t_partner p on c.partner_id=p.partner_id 
                where c.contract_id = ' . $contract_id;

        $this->returnSuccess(Utility::query($sql));
    }


}