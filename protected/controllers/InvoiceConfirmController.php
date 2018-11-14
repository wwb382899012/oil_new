<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/4 11:20
 * Describe：发票确认
 */
class InvoiceConfirmController extends AttachmentController
{
    public function pageInit()
    {
        $this->filterActions="getFile";
        $this->rightCode = "invoiceConfirm";
        $this->attachmentType=Attachment::C_DOWNCONFIRM;
    }

    
    public function actionIndex()
    {
        $attr = $_GET[search];

        if(!is_array($attr) || !array_key_exists("d.status",$attr))
        {
            $attr["d.status"]="0";
        }

        $query="";
        $status="";
        if($attr["d.status"]=="0"){
            $status="0";
            unset($attr["d.status"]);
            $query=" and d.status=".Invoice::STATUS_EXPRESS_INVOICE;
        }else if($attr["d.status"]=="1"){
            $status="1";
            unset($attr["d.status"]);
            $query=" and d.status>".Invoice::STATUS_EXPRESS_INVOICE;
        }else{
            unset($attr["d.status"]);
            $query=" and d.status>=".Invoice::STATUS_EXPRESS_INVOICE;
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
            $query .= " and d.invoice_date between '".$start_date."' and '".$end_date."'";
        else if(!empty($start_date))
            $query .= " and d.invoice_date between '".$start_date."' and '".date('Y-m-d')."'";
        else if(!empty($end_date))
            $query .= " and d.invoice_date between '".date('Y-m-d')."' and '".$end_date."'";

        $user = SystemUser::getUser(Utility::getNowUserId());
        $sql="select {col}"
            ." from t_project a "
            ." left join t_partner c on a.down_partner_id=c.partner_id "
            ." left join t_invoice d on a.project_id=d.project_id "
            .$this->getWhereSql($attr);
        $sql .= $query;
        $sql .= " and a.corporation_id in (".$user['corp_ids'].") order by a.project_id {limit}";
        $fields = "a.project_id,a.project_name,c.partner_id,c.name as customer_name,
                   case when d.status=6 then 0
                   when d.status>6 then 1
                   end as status,d.amount,d.invoice_date,d.invoice_id ";
        $data = $this->queryTablesByPage($sql,$fields);
        if($status=="0" || $status=="1")
            $attr['d.status']=$status;
        if(!empty($start_date))
            $attr['start_date']=$start_date;
        if(!empty($end_date))
            $attr['end_date']=$end_date;
        $data['search'] = $attr;
        $this->render('index',$data);
    }

    public function actionSave()
    {
        $params = $_POST["obj"];
        $user = $this->getUser();

        if (!empty($params["invoice_id"])) {
            $obj = Invoice::model()->findByPk($params["invoice_id"]);
        }

        if (empty($obj->invoice_id))
        {
            $this->returnError("当前信息不存在！");
        }

        unset($params['invoice_id']);
        $obj->setAttributes($params,false);
        $obj->status=Invoice::STATUS_CONFIRM_INVOICE;

        $obj->update_time = date("Y-m-d H:i:s");
        $obj->update_user_id = $user["user_id"];
        $res=$obj->save();
        if($res===true)
        {
            //ProjectService::updateProjectStatus($obj->project_id,Project::STATUS_INVOICE_CONFIRM);
            //TaskService::addTasks(Action::ACTION_27,$obj->project_id,ActionService::getActionRoleIds(Action::ACTION_27));
            TaskService::doneTask($obj->invoice_id,Action::ACTION_28);
            $this->returnSuccess($obj->invoice_id);
        }
        else
            $this->returnError("保存失败:".$res);

    }

    public function actionEdit()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/invoiceConfirm/");
        }

        $data=Utility::query("select a.invoice_id,a.invoice_name,a.amount,a.invoice_date,a.comment,"
                ." a.start_number,a.end_number,a.invoice_code,a.address,a.phone,a.content,a.tax_code,"
                ." a.bank_name,a.bank_account,a.create_time,a.update_time,f.amount as settle_amount,"
                ." b.project_id,b.project_name,c.id as attachment_id,c.file_url,d.company_name,d.express_fee, "
                ." d.recipient,d.express_no,d.address,d.phone,pa.partner_id,pa.name as customer_name,"
                ." f.price as down_price,f.quantity as down_quantity"
                ." from t_invoice a"
                ." left join t_project b on a.project_id=b.project_id "
                ." left join t_partner pa on b.down_partner_id=pa.partner_id "
                ." left join t_settlement f on b.project_id=f.project_id and f.type=2"
                ." left join t_rent_attachment c on a.invoice_id=c.relation_id and c.status=1 and c.type=51 "
                ." left join t_express_info d on a.type=d.type and b.project_id=d.project_id "
                ." where a.invoice_id=".$id);
    
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/invoiceConfirm/");
        }

        if(Utility::isNotEmpty($data))
        {
            if($data[0]["status"]>Invoice::STATUS_EXPRESS_INVOICE)
            {
                $this->renderError("下游收票已确认，不能修改！", "/invoiceConfirm/");
            }
        }

        $data[0]['down_amount']=$data[0]['settle_amount'];

        $expAttachments = Invoice::getAttachments($data[0]['invoice_id'],101);
        $confirmAttachments = Invoice::getAttachments($data[0]['invoice_id'],201);
        $plans = ProjectService::getDownReturnPlans($data[0]['project_id']);

        $this->pageTitle="发票确认";
        $this->render("edit",array(
            "data"=>$data[0],
            "attachments"=>$expAttachments,
            "confirmAttachments"=>$confirmAttachments,
            "plans"=>$plans
        ));
    }

    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/invoiceConfirm/");
        }

        $data=Utility::query("select a.invoice_id,a.invoice_name,a.amount,a.invoice_date,a.comment,"
                ." a.start_number,a.end_number,a.invoice_code,a.address,a.phone,a.content,a.tax_code,"
                ." a.bank_name,a.bank_account,a.create_time,a.update_time,f.amount as settle_amount,"
                ." b.project_id,b.project_name,c.id as attachment_id,c.file_url,d.company_name,d.express_fee, "
                ." d.recipient,d.express_no,d.address,d.phone,pa.partner_id,pa.name as customer_name,"
                ." f.price as down_price,f.quantity as down_quantity"
                ." from t_invoice a"
                ." left join t_project b on a.project_id=b.project_id "
                ." left join t_partner pa on b.down_partner_id=pa.partner_id "
                ." left join t_settlement f on b.project_id=f.project_id and f.type=2"
                ." left join t_rent_attachment c on a.invoice_id=c.relation_id and c.status=1 and c.type=51 "
                ." left join t_express_info d on a.type=d.type and b.project_id=d.project_id "
                ." where a.invoice_id=".$id);
    
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/invoiceConfirm/");
        }

        $data[0]['down_amount']=$data[0]['settle_amount'];

        $expAttachments = Invoice::getAttachments($data[0]['invoice_id'],101);
        $confirmAttachments = Invoice::getAttachments($data[0]['invoice_id'],201);
        $plans = ProjectService::getDownReturnPlans($data[0]['project_id']);

        $this->pageTitle="发票确认详情";
        $this->render("detail",array(
            "data"=>$data[0],
            "attachments"=>$expAttachments,
            "confirmAttachments"=>$confirmAttachments,
            "plans"=>$plans
        ));
    }

    protected function getFileExtras()
    {
        $relationId=Mod::app()->request->getParam("relation_id");
        return array("relation_id"=>$relationId);
    }

    /*public function actionSaveFile()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
            $this->returnError("信息有误！");
        $type=Mod::app()->request->getParam("type");
        if(empty($type))
            $this->returnError("信息有误！");
        $relationId=Mod::app()->request->getParam("relation_id");

        $user = $this->getUser();
        $obj=new RentAttachment(Attachment::C_DOWNCONFIRM);
        $res=$obj->saveFile($id,$type,$relationId,$_FILES["files"],$user["user_id"]);

        if($res==1)
            $this->returnSuccess($obj->file["id"]);
        else
            $this->returnError($res);
    }

    public function actionGetFile()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
            $this->returnError("信息有误！");
        $obj=RentAttachmentModel::model()->findByPk($id);
        Mod::log(file_exists($obj->file_path));
        $filePath=$obj->file_path;
        if(file_exists($filePath)) {
            try {
                $mime_type = Utility::getFileMIME($filePath);
                header("Content-type:" . $mime_type);
                header("Content-Disposition: attachment; filename=".basename($filePath));
                echo file_get_contents($filePath);
            } catch (Exception $e) {
                $this->renderError($e->getMessage());
            }
        }
        else
            $this->renderError("文件不存在");
    }*/
}