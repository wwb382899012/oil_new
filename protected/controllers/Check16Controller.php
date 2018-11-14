<?php
/**
 * Describe：销项票开票审核
 */
class Check16Controller extends CheckController
{
    public function pageInit()
    {
        parent::pageInit();
        $this->filterActions="";
        $this->businessId=16;
        $this->rightCode = "check16_";
        $this->checkButtonStatus["reject"]=0;
    }

    public function initRightCode()
    {
//        $attr= $_REQUEST["search"];
        $attr= $this->getSearch();
        $checkStatus=$attr["checkStatus"];
        $this->treeCode="check16_".$checkStatus;
        $this->newUIPrefix = 'new_';
    }


    public function actionIndex()
    {
//        $attr = $_REQUEST[search];
        $attr= $this->getSearch();
        if(!empty($attr["checkStatus"]))
        {
            $checkStatus=$attr["checkStatus"];
            unset($attr["checkStatus"]);
        }
        $user = SystemUser::getUser($this->nowUserId);

        $sql="
                 select {col} from t_check_detail a
                 left join t_invoice n on a.obj_id=n.invoice_id
                 left join t_invoice_application o on n.apply_id=o.apply_id
                 left join t_contract c on o.contract_id=c.contract_id
                 left join t_partner pa on pa.partner_id = c.partner_id
                 left join t_corporation co on o.corporation_id=co.corporation_id
                 left join t_project p on o.project_id=p.project_id
                 left join t_system_user s on n.create_user_id=s.user_id
                 left join t_check_item i on i.check_id=a.check_id and i.node_id>0 
                 left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1 
                ".$this->getWhereSql($attr)." and a.business_id=".$this->businessId."
                and (a.role_id=".$this->nowUserRoleId." or a.check_user_id=".$this->nowUserId.")";

        $fields="a.detail_id,a.obj_id,n.invoice_id,n.amount as invoice_amount,n.invoice_num,o.apply_id,o.apply_code,o.type_sub,c.contract_code,c.contract_id,
                o.contract_type,p.project_id,p.project_code,p.type as project_type,s.name as user_name,
                o.invoice_contract_type,o.invoice_contract_code,o.amount,o.type,pa.partner_id,pa.name as partner_name,
                co.corporation_id,co.name as corporation_name,cf.code_out";

        switch($checkStatus)
        {
            case 2:
                $sql .= " and a.status=1 and a.check_status=1 ";
                $fields.=",0 isCanCheck, 2 as checkStatus ";
                break;
            case 3:
                $sql .= " and a.status=1 and a.check_status=0";
                $fields.=",0 isCanCheck, 3 as checkStatus ";
                break;
            case 4:
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields.=",0 isCanCheck, 4 as checkStatus ";
                break;
            default:
                $sql .= " and a.status=0";
                $fields.=",1 isCanCheck, 1 as checkStatus ";
                break;
        }

        $sql .= " and o.corporation_id in (".$user['corp_ids'].")  order by a.detail_id desc";
        $data = $this->queryTablesByPage($sql,$fields);

        $attr["checkStatus"]=$checkStatus;
        $data["search"]=$attr;
        $data["b"]=$this->businessId;
        $this->render('index',$data);
    }

    public function actionCheck()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("参数有误", $this->mainUrl);
        }

        $sql = "select n.invoice_id,i.apply_id,i.apply_code,i.invoice_date,i.exchange_rate,i.company_name,i.tax_code,
                i.invoice_type,i.contract_type,i.type,i.type_sub,i.invoice_contract_code,i.amount as total_amount,
                i.invoice_contract_type,i.num,co.name as corporation_name,i.remark as o_remark,
                c.contract_id, c.contract_code, p.project_id,p.project_code,i.status,
                i.address,i.phone,i.bank_name,i.bank_account
                from t_invoice n 
                left join t_invoice_application i on n.apply_id=i.apply_id
                left join t_corporation co on i.corporation_id=co.corporation_id
                left join t_contract c on i.contract_id=c.contract_id
                left join t_project p on i.project_id=p.project_id
                where n.invoice_id=".$id;

        $data = Utility::query($sql);

        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $checkDetail=CheckDetail::model()->find("obj_id=".$id." and status=".CheckDetail::STATUS_NEW." and business_id=".$this->businessId." and (role_id=".$this->nowUserRoleId." or check_user_id=".$this->nowUserId.")");
        if(empty($checkDetail))
            $this->renderError("没有需要您审核的信息！", $this->mainUrl);

        $data[0]['check_id'] = $checkDetail->check_id;

        $invoiceDetail  = InvoiceService::getInvoiceApplyDetail($data[0]['apply_id']);
        /*foreach ($invoiceDetail as $key => $value) {
            $data[0]['total_amount'] += $value['amount'];
        }*/
        if(bccomp($data[0]['exchange_rate'],0, 6)>0){
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

        
        if($data[0]['type']==1){
            $mapName = 'invoice_input_type';
        }else{
            $mapName = 'invoice_output_type';
        }

        $data[0]['title_map_name'] = $mapName;
        $map = Map::$v;
        $title = $map[$mapName][$data[0]['type_sub']];
        $this->pageTitle=$title."开票审核";
        $this->render('check',array(
            'data'=>$data[0],
            "invoiceDetail"=>$invoiceDetail,
            "attachments"=>$attachments,
            "plans"=>$plans,
            "invoices"=>$invoices,
            "invoiceItems"=>$invoiceItems
            )
        );
    }


    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("参数有误", $this->mainUrl);
        }

        $sql = "select n.invoice_id,i.apply_id,i.apply_code,i.invoice_date,i.exchange_rate,i.company_name,i.tax_code,
                i.invoice_type,i.contract_type,i.type,i.type_sub,i.invoice_contract_code,l.remark,
                i.invoice_contract_type,i.num,co.name as corporation_name,i.remark as o_remark,
                c.contract_id, c.contract_code, p.project_id,p.project_code,i.status,d.check_id,
                i.address,i.phone,i.bank_name,i.bank_account
                from t_check_detail d
                left join t_invoice n on d.obj_id=n.invoice_id
                left join t_invoice_application i on n.apply_id=i.apply_id
                left join t_corporation co on i.corporation_id=co.corporation_id
                left join t_contract c on i.contract_id=c.contract_id
                left join t_project p on i.project_id=p.project_id
                left join t_check_log l on d.detail_id=l.detail_id
                where d.detail_id=".$id;

        $data = Utility::query($sql);

        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $invoiceDetail  = InvoiceService::getInvoiceApplyDetail($data[0]['apply_id']);
        foreach ($invoiceDetail as $key => $value) {
            $data[0]['total_amount'] += $value['amount'];
        }
        if(bccomp($data[0]['exchange_rate'],0)>0){
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

        
        if($data[0]['type']==1){
            $mapName = 'invoice_input_type';
        }else{
            $mapName = 'invoice_output_type';
        }
        $data[0]['title_map_name'] = $mapName;
        $map = Map::$v;
        $title = $map[$mapName][$data[0]['type_sub']];
        $this->pageTitle=$title."开票审核详情";
        $this->render('detail',array(
            'data'=>$data[0],
            "invoiceDetail"=>$invoiceDetail,
            "attachments"=>$attachments,
            "plans"=>$plans,
            "invoices"=>$invoices,
            "invoiceItems"=>$invoiceItems
            )
        );
    }
}