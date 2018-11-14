<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/4 11:20
 * Describe：
 */
class ExpInvoiceController extends AttachmentController
{
    public function pageInit()
    {
        $this->filterActions="getFile";
        $this->rightCode = "expInvoice";
        $this->attachmentType=Attachment::C_EXPRESS;
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
            $query=" and d.status=".Invoice::STATUS_OPEN_INVOICE;
        }else if($attr["d.status"]=="1"){
            $status="1";
            unset($attr["d.status"]);
            $query=" and d.status>".Invoice::STATUS_OPEN_INVOICE;
        }else{
            unset($attr["d.status"]);
            $query=" and d.status>=".Invoice::STATUS_OPEN_INVOICE;
        }

        /*$start_date='';
        $end_date='';
        if(!empty($attr["start_date"])){
            $start_date=$attr["start_date"];
            unset($attr["start_date"]);
        }
        
        if(!empty($attr["end_date"])){
            $end_date = $attr["end_date"];
            unset($attr["end_date"]);
        }else{
            $end_date = date('Y-m-d',strtotime("+7 day"));
            unset($attr["end_date"]);
        }

        if(!empty($start_date))
            $query .= " and d.invoice_date between '".$start_date."' and '".$end_date."'";
        else 
            $query .= " and d.invoice_date <= '".$end_date."'";*/
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
                   case when d.status=5 then 0
                   when d.status>5 then 1
                   end as status,d.invoice_date,d.amount,d.invoice_id ";
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
        $recipient=$params['recipient'];
        $address=$params['address'];
        $phone=$params['phone'];
        $express_no=$params['express_no'];
        $company_name=$params['company_name'];
        $express_fee=$params['express_fee']*100;
        unset($params['invoice_id']);
        unset($params['recipient']);
        unset($params['address']);
        unset($params['phone']);
        unset($params['express_no']);
        unset($params['company_name']);
        unset($params['express_fee']);
        $obj->status=Invoice::STATUS_EXPRESS_INVOICE;

        $obj->update_time = date("Y-m-d H:i:s");
        $obj->update_user_id = $user["user_id"];
        $res=$obj->save();
        if($res===true)
        {
            $obj2 = Express::model()->find("express_no='".$express_no."' and type=".$obj->type." and project_id=".$obj->project_id);
            if(empty($obj2->express_id)){
                $obj2 = new Express();
                $obj2->project_id=$obj->project_id;
                $obj2->type=$obj->type;
                $obj2->create_time = date("Y-m-d H:i:s");
                $obj2->create_user_id = $user["user_id"];
            }

            $obj2->recipient=$recipient;
            $obj2->address=$address;
            $obj2->phone=$phone;
            $obj2->express_no=$express_no;
            $obj2->express_fee=$express_fee;
            $obj2->company_name=$company_name;
            $obj2->save();
            // ProjectService::updateProjectStatus($obj->project_id,Project::STATUS_INVOICE_EXPRESS);
            $project  = Project::model()->findbyPk($obj->project_id);
            TaskService::addTasks(Action::ACTION_28,$obj->invoice_id,ActionService::getActionRoleIds(Action::ACTION_28),0,$project->corporation_id);
            TaskService::doneTask($obj->invoice_id,Action::ACTION_27);
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
            $this->renderError("信息异常！", "/expInvoice/");
        }

        $data=Utility::query("select a.invoice_id,a.status,a.project_id,a.invoice_name,a.amount,a.invoice_date,"
                ." a.start_number,a.end_number,a.invoice_code,a.address,a.phone,a.content,a.tax_code,"
                ." a.bank_name,a.bank_account,a.create_time,b.update_time,pa.partner_id,pa.name as customer_name,"
                ." b.project_id,b.project_name,c.id as attachment_id,c.file_url,f.amount as settle_amount,"
                ." f.price as down_price,f.quantity as down_quantity"
                ." from t_invoice a"
                ." left join t_project b on a.project_id=b.project_id "
                ." left join t_partner pa on b.down_partner_id=pa.partner_id "
                ." left join t_settlement f on b.project_id=f.project_id and f.type=2"
                ." left join t_rent_attachment c on a.invoice_id=c.relation_id and c.status=1 and c.type=51 "
                ." where a.invoice_id=".$id);
    
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/expInvoice/");
        }

        if(Utility::isNotEmpty($data))
        {
            if($data[0]["status"]>Invoice::STATUS_OPEN_INVOICE)
            {
                $this->renderError("发票已发快递，不能修改！", "/expInvoice/");
            }
        }

        $data[0]['down_amount']=$data[0]['settle_amount'];

        //$expArr = Utility::query("select * from t_express_info where type=".$data[0]['type']." and project_id=".$data[0]['project_id']." order by express_id desc limit 1 ");
        $expArr = Utility::query("select * from t_express_info where type=2 and project_id=".$data[0]['project_id']." order by express_id desc limit 1 ");
        if(Utility::isNotEmpty($expArr)){
            $data[0]['recipient']=$expArr[0]['recipient'];
            $data[0]['address']=$expArr[0]['address'];
            $data[0]['phone']=$expArr[0]['phone'];
        }

        $expAttachments = Invoice::getAttachments($data[0]['invoice_id'],101);

        $plans = ProjectService::getDownReturnPlans($data[0]['project_id']);


        $this->pageTitle="发票快递";
        $this->render("edit",array(
            "data"=>$data[0],
            "attachments"=>$expAttachments,
            "plans"=>$plans
        ));
    }

    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/expInvoice/");
        }

        $data=Utility::query("select a.invoice_id,a.invoice_name,a.amount,a.invoice_date,"
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
            $this->renderError("当前信息不存在！", "/expInvoice/");
        }

        $data[0]['down_amount']=$data[0]['settle_amount'];

        $expAttachments = Invoice::getAttachments($data[0]['invoice_id'],101);

        $plans = ProjectService::getDownReturnPlans($data[0]['project_id']);

        $this->pageTitle="发票快递详情";
        $this->render("detail",array(
            "data"=>$data[0],
            "attachments"=>$expAttachments,
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
        $obj=new RentAttachment(Attachment::C_EXPRESS);
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