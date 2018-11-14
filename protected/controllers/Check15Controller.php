<?php
/**
 * Describe：进项票申请审核
 */
class Check15Controller extends CheckController
{
    public function pageInit()
    {
        parent::pageInit();
        $this->filterActions="";
        $this->businessId=15;
        $this->rightCode = "check15_";
        $this->checkButtonStatus["reject"]=0;
    }

    public function initRightCode()
    {
//        $attr= $_REQUEST["search"];
        $attr= $this->getSearch();
        $checkStatus=$attr["checkStatus"];
        $this->treeCode="check15_".$checkStatus;
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
                 left join t_invoice_application o on a.obj_id=o.apply_id
                 left join t_contract c on o.contract_id=c.contract_id
                 left join t_partner pa on pa.partner_id = c.partner_id
                 left join t_corporation co on c.corporation_id=co.corporation_id
                 left join t_project p on o.project_id=p.project_id
                 left join t_system_user s on o.create_user_id=s.user_id
                 left join t_check_item i on i.check_id=a.check_id and i.node_id>0 
                 left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1 
                ".$this->getWhereSql($attr)." and a.business_id=".$this->businessId."
                and (a.role_id=".$this->nowUserRoleId." or a.check_user_id=".$this->nowUserId.")";

        $fields="a.detail_id,a.obj_id,o.apply_id,o.apply_code,o.type_sub,o.create_user_id,c.contract_code,c.contract_id,
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
        $data['type'] = ConstantMap::INPUT_INVOICE_TYPE;
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

        $sql = "select i.apply_id,i.apply_code,i.invoice_date,i.exchange_rate,i.company_name,i.tax_code,
                i.invoice_type,i.contract_type,i.type,i.type_sub,i.invoice_contract_code,
                i.invoice_contract_type,i.num,co.name as corporation_name,i.remark as o_remark,
                c.contract_id, c.contract_code, p.project_id,p.project_code,i.status
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

        $checkDetail=CheckDetail::model()->find("obj_id=".$id." and status=".CheckDetail::STATUS_NEW." and business_id=".$this->businessId." and (role_id=".$this->nowUserRoleId." or check_user_id=".$this->nowUserId.")");
        if(empty($checkDetail))
            $this->renderError("没有需要您审核的信息！", $this->mainUrl);

        $data[0]['check_id'] = $checkDetail->check_id;

        $invoiceDetail  = InvoiceService::getInvoiceApplyDetail($data[0]['apply_id']);
        foreach ($invoiceDetail as $key => $value) {
            $data[0]['total_amount'] += $value['amount'];
        }
        if(bccomp($data[0]['exchange_rate'],0)>0){
            $data[0]['dollar_amount'] = $data[0]['total_amount'] / $data[0]['exchange_rate'];
        }
        
        $plans          = InvoiceService::getPaymentById($data[0]['apply_id'], $data[0]['type']);

        $attachments    = InvoiceAttachment::model()->findAllToArray('base_id='.$data[0]['apply_id'].' AND status=1');

        if($data[0]['type']==1){
            $mapName = 'invoice_input_type';
        }else{
            $mapName = 'invoice_output_type';
        }

        $data[0]['title_map_name'] = $mapName;
        $map = Map::$v;
        $title = $map[$mapName][$data[0]['type_sub']];
        $this->pageTitle=$title."审核";
        $this->render('check',array(
            'data'=>$data[0],
            "invoiceDetail"=>$invoiceDetail,
            "attachments"=>$attachments,
            "plans"=>$plans,
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

        $sql = "select i.apply_id,i.apply_code,i.invoice_date,i.exchange_rate,i.company_name,i.tax_code,
                i.invoice_type,i.contract_type,i.type,i.type_sub,i.invoice_contract_code,l.remark,
                i.invoice_contract_type,i.num,co.name as corporation_name,i.remark as o_remark,
                c.contract_id, c.contract_code, p.project_id,p.project_code,i.status,d.check_id
                from t_check_detail d
                left join t_invoice_application i on d.obj_id=i.apply_id
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
        if(bccomp($data[0]['exchange_rate'],0, 6)>0){
            $data[0]['dollar_amount'] = $data[0]['total_amount'] / $data[0]['exchange_rate'];
        }
        
        $plans          = InvoiceService::getPaymentById($data[0]['apply_id'], $data[0]['type']);

        $attachments    = InvoiceAttachment::model()->findAllToArray('base_id='.$data[0]['apply_id'].' AND status=1');

        if($data[0]['type']==1){
            $mapName = 'invoice_input_type';
        }else{
            $mapName = 'invoice_output_type';
        }
        $data[0]['title_map_name'] = $mapName;
        $map = Map::$v;
        $title = $map[$mapName][$data[0]['type_sub']];
        $this->pageTitle=$title."审核详情";
        $this->render('detail',array(
            'data'=>$data[0],
            "invoiceDetail"=>$invoiceDetail,
            "attachments"=>$attachments,
            "plans"=>$plans,
            )
        );
    }
}