<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/4 11:20
 * Describe：月度收入报表
 */
class MonthIncomeController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="cancel,export";
        $this->rightCode = "monthIncome";
    }

    
   public function actionIndex()
    {
        $attr = $_GET[search];
        if(!is_array($attr) || !array_key_exists("a.status",$attr))
        {
            $attr["a.status"]="-1";
        }

        $query = "";
        $status="";
        if($attr['a.status'] == "-1"){
            $status="-1";
            unset($attr['a.status']);
            $query .= " and (a.status=0 or a.status=1)";
        }

        $startDate="";
        if(!empty($attr['a.account_period'])){
            $startDate = $attr['a.account_period'];
            $queryDate = str_replace('-', '', $attr['a.account_period']);
            $query .= " and a.account_period='".$queryDate."' ";
            unset($attr['a.account_period']);
        }

        $sql="select {col}"
            ." from t_income_cost a "
            ." left join t_corporation b on a.corp_id=b.corporation_id "
            .$this->getWhereSql($attr);
        $sql .= $query;
        $sql .= " order by a.code desc {limit}";
        //echo $sql;die;
        $data = $this->queryTablesByPage($sql,"a.*,b.name");
        //print_r($data);die;
        if($status=="-1")
            $attr["a.status"]=$status;
        if(!empty($startDate))
            $attr['a.account_period']=$startDate;
        $data['search'] = $attr;
        $this->render('index',$data);
    }


    public function actionAdd(){
        $attr = $_GET[search];
        if(!is_array($attr) || !array_key_exists("a.corporation_id",$attr))
        {
            $attr["a.corporation_id"]="1";
        }
        $query="";
        $start_date = '';
        $end_date   = '';
        if(!empty($attr["start_date"])){
            $start_date=$attr["start_date"];
            unset($attr["start_date"]);
        }
        if(!empty($attr["end_date"])){
            $end_date = $attr["end_date"];
            unset($attr["end_date"]);
        }
        if(!empty($start_date) && !empty($end_date))
            $query .= " and i.invoice_date_actual between '".$start_date."' and '".$end_date."'";
        else if(!empty($start_date))
            $query .= " and i.invoice_date_actual between '".$start_date."' and '".date('Y-m-d')."'";
        else if(!empty($end_date))
            $query .= " and i.invoice_date_actual between '".date('Y-m-d')."' and '".$end_date."'";

        $sql="select {col} "
                ."from t_project a
                  left join t_partner c on c.partner_id=a.up_partner_id
                  left join t_partner s on s.partner_id=a.down_partner_id
                  left join t_settlement su on a.project_id=su.project_id and su.type=1
                  left join t_settlement sd on a.project_id=sd.project_id and sd.type=2
                  left join t_corporation co on a.corporation_id=co.corporation_id
                  left join t_invoice i on a.project_id=i.project_id "
                .$this->getWhereSql($attr);
        $sql   .= $query;
        $sql   .= " and i.status >= ".Invoice::STATUS_OPEN_INVOICE.
                  " and i.invoice_id not in(select invoice_id from t_income_cost_detail where status>0) order by i.invoice_date_actual desc {limit}";
        //echo $sql;die;
        $fields = "a.*,c.name as up_name,s.name as down_name,
                  su.price as su_price,sd.price as sd_price,
                  co.name as corporation_name,i.invoice_id,i.invoice_date_actual,i.amount as invoice_amount";
        //$fields = "a.invoice_date_actual,b.corporation_id,b.name,";
        $data   = $this->queryTablesByPage($sql,$fields);
        if(!empty($data['data']) && !empty($data['data']['rows'])){
            foreach ($data['data']['rows'] as $key => $value) {
                if($value['sd_price']>0){
                    $data['data']['rows'][$key]['quantity'] = $value['invoice_amount']/$value['sd_price'];
                    $data['data']['rows'][$key]['purchase_amount'] = $data['data']['rows'][$key]['quantity'] * $value['su_price'];
                }
            }
        }
        
        //print_r($data);die;
        if(!empty($start_date))
            $attr['start_date']=$start_date;
        if(!empty($end_date))
            $attr['end_date']=$end_date;
        $data['search'] = $attr;
        $this->pageTitle="生成单据";
        $this->render("manager",$data);
    }


    public function actionCreate()
    {
        if(!is_array($_REQUEST) || empty($_REQUEST)){
            $this->renderError("信息异常！", "/monthIncome/");
        }
        $codeId=IDService::getCodeId();

        $sql = "select a.*,c.name as up_name,s.name as down_name,
                  su.price as su_price,sd.price as sd_price,
                  co.name as corporation_name,i.invoice_id,i.invoice_date_actual,i.amount as invoice_amount
                  from t_project a
                  left join t_partner c on c.partner_id=a.up_partner_id
                  left join t_partner s on s.partner_id=a.down_partner_id
                  left join t_settlement su on a.project_id=su.project_id and su.type=1
                  left join t_settlement sd on a.project_id=sd.project_id and sd.type=2
                  left join t_corporation co on a.corporation_id=co.corporation_id
                  left join t_invoice i on a.project_id=i.project_id
                  where i.invoice_id in (".$_REQUEST['id'].") and a.corporation_id='".$_REQUEST['corp_id']
                  ."' and i.invoice_date_actual between '".$_REQUEST['start_date']."' and '".$_REQUEST['end_date']."'";
        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/monthIncome/");
        }
        
        $tempArr = array();
        $dtArr   = array();
        foreach ($data as $key => $value) {
            if($value['sd_price']>0){
                $data[$key]['quantity'] = $value['invoice_amount']/$value['sd_price'];
                $data[$key]['purchase_amount'] = $data[$key]['quantity'] * $value['su_price'];
            }
            $tempArr[$value['goods_type']][] = $data[$key]; 
            $dtArr['quantity_total']    += $data[$key]['quantity'];
            $dtArr['sell_amount_total'] += $value['invoice_amount'];
            $dtArr['sell_price_total']  += $value['sd_price'];
            $dtArr['purchase_amount_total'] += $data[$key]['purchase_amount'];
            $dtArr['purchase_price_total']  += $value['su_price'];
        }

        $incomeArr = array();
        $totalArr  = array();
        foreach ($tempArr as $key => $value) {
            foreach ($value as $k => $v) {
                $incomeArr[$key]['quantity'] += $v['quantity'];
                $incomeArr[$key]['tax_sales_amount'] += $v['invoice_amount'];
                $incomeArr[$key]['tax_purchase_amount'] += $v['purchase_amount'];
            }
            $incomeArr[$key]['sales_amount'] = $incomeArr[$key]['tax_sales_amount'] / 1.17;
            $incomeArr[$key]['purchase_amount'] = $incomeArr[$key]['tax_purchase_amount'] / 1.17;
            $incomeArr[$key]['tax_sales'] = $incomeArr[$key]['tax_sales_amount'] - $incomeArr[$key]['sales_amount']; 
            $incomeArr[$key]['tax_purchase'] = $incomeArr[$key]['tax_purchase_amount'] - $incomeArr[$key]['purchase_amount']; 

            $totalArr['quantity_total'] += $incomeArr[$key]['quantity'];
            $totalArr['tax_purchase_amount_total'] += $incomeArr[$key]['tax_purchase_amount'];
            $totalArr['tax_sales_amount_total'] += $incomeArr[$key]['tax_sales_amount'];
            $totalArr['purchase_amount_total'] += $incomeArr[$key]['purchase_amount'];
            $totalArr['sales_amount_total'] += $incomeArr[$key]['sales_amount'];
            $totalArr['tax_sales_total'] += $incomeArr[$key]['tax_sales'];
            $totalArr['tax_purchase_total'] += $incomeArr[$key]['tax_purchase'];
            
        }
        ksort($incomeArr);
        //print_r($incomeArr);die;
        //echo $sql;die;
        $this->pageTitle="收入确认";
        $this->render("confirm",array(
            "data"=>$data,
            "incomeArr"=>$incomeArr,
            "totalArr"=>$totalArr,
            "args"=>$_REQUEST,
            "codeId"=>$codeId,
            "dtArr"=>$dtArr
        ));
    }

    public function actionSave()
    {
        $id         = $_REQUEST["id"];
        $corpId     = $_REQUEST["corp_id"];
        $corpName   = $_REQUEST["corp_name"];
        $codeId     = $_REQUEST["code_id"];
        $startDate  = $_REQUEST["start_date"];
        $totalArr   = json_decode($_REQUEST["total_amount"],true);
        $itemArr    = json_decode($_REQUEST["income_items"],true); 
        $detailArr  = json_decode($_REQUEST["income_detail"],true); 
        //print_r($detailArr);die;

        $user = $this->getUser();

        //写入收入成本确认单
        $obj = new IncomeCost();
        //$obj->statement_id      = IDService::getStatementId();
        $obj->corp_id           = $corpId;
        $obj->code              = $codeId;
        $obj->account_period    = $startDate;
        $obj->purchase_amount   = $totalArr["tax_purchase_amount_total"];
        $obj->sell_amount       = $totalArr["tax_sales_amount_total"];
        $obj->purchase_quantity = $totalArr["quantity_total"];
        $obj->purchase_price    = $totalArr["tax_purchase_amount_total"] / $totalArr["quantity_total"];
        $obj->purchase_tax      = $totalArr["tax_purchase_total"];
        $obj->sell_quantity     = $totalArr["quantity_total"];
        $obj->sell_price        = $totalArr["tax_sales_amount_total"] / $totalArr["quantity_total"];
        $obj->sell_tax          = $totalArr["tax_sales_total"];
        $obj->status            = 1;
        $obj->create_time       = date("Y-m-d H:i:s");
        $obj->create_user_id    = $user["user_id"];
        $obj->update_time       = date("Y-m-d H:i:s");
        $obj->update_user_id    = $user["user_id"];
        $obj->status_time       = date("Y-m-d H:i:s");
        $res = $obj->save($itemArr,$detailArr);
        if($res === true){
            $this->returnSuccess($obj->statement_id);
        }else{
            $this->returnError("保存失败:".$res);
        }
    }

    public function actionEdit()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/monthIncome/");
        }

        $sql = "select a.*,co.name as corporation_name 
                from t_income_cost a
                left join t_corporation co on a.corp_id=co.corporation_id
                where a.statement_id=".$id;
        $data = Utility::query($sql);
        
        $this->pageTitle="收入-成本确认作废";
        $this->render("edit",array(
            "data"=>$data[0]
        ));
    }

    public function actionCancel()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/monthIncome/");
        }

        $sql = "select a.statement_id,b.item_id 
                from t_income_cost a
                left join t_income_cost_detail b on a.statement_id=b.statement_id
                where a.statement_id=".$id;
        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/monthIncome/");
        }

        $res = IncomeCost::updateIncomeStatus($data);
        if($res === true){
            $this->returnSuccess();
        }else{
            $this->returnError("保存失败:".$res);
        }
        
    }

    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/monthIncome/");
        }

        $sql = "select a.statement_id,a.account_period,a.code,a.status as income_status,p.*,c.name as up_name,
                s.name as down_name,su.price as su_price,sd.price as sd_price,co.name as corporation_name,
                i.invoice_id,i.invoice_date_actual,i.amount as invoice_amount 
                from t_income_cost a
                left join t_income_cost_detail b on a.statement_id=b.statement_id
                left join t_invoice i on b.invoice_id=i.invoice_id
                left join t_project p on i.project_id=p.project_id
                left join t_partner c on c.partner_id=p.up_partner_id
                left join t_partner s on s.partner_id=p.down_partner_id
                left join t_settlement su on p.project_id=su.project_id and su.type=1
                left join t_settlement sd on p.project_id=sd.project_id and sd.type=2
                left join t_corporation co on a.corp_id=co.corporation_id
                where a.statement_id=".$id;
        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/monthIncome/");
        }
        //print_r($data);die;
        
        $tempArr = array();
        $dtArr   = array();
        foreach ($data as $key => $value) {
            if($value['sd_price']>0){
                $data[$key]['quantity'] = $value['invoice_amount']/$value['sd_price'];
                $data[$key]['purchase_amount'] = $data[$key]['quantity'] * $value['su_price'];
            }
            $tempArr[$value['goods_type']][] = $data[$key]; 
            $dtArr['quantity_total'] += $data[$key]['quantity'];
            $dtArr['sell_amount_total'] += $value['invoice_amount'];
            $dtArr['sell_price_total'] += $value['sd_price'];
            $dtArr['purchase_amount_total'] += $data[$key]['purchase_amount'];
            $dtArr['purchase_price_total'] += $value['su_price'];
        }
        //print_r($dtArr);die;

        $incomeArr = array();
        $totalArr  = array();
        foreach ($tempArr as $key => $value) {
            foreach ($value as $k => $v) {
                $incomeArr[$key]['quantity'] += $v['quantity'];
                $incomeArr[$key]['tax_sales_amount'] += $v['invoice_amount'];
                $incomeArr[$key]['tax_purchase_amount'] += $v['purchase_amount'];
            }
            $incomeArr[$key]['sales_amount'] = $incomeArr[$key]['tax_sales_amount'] / 1.17;
            $incomeArr[$key]['purchase_amount'] = $incomeArr[$key]['tax_purchase_amount'] / 1.17;
            $incomeArr[$key]['tax_sales'] = $incomeArr[$key]['tax_sales_amount'] - $incomeArr[$key]['sales_amount']; 
            $incomeArr[$key]['tax_purchase'] = $incomeArr[$key]['tax_purchase_amount'] - $incomeArr[$key]['purchase_amount']; 

            $totalArr['quantity_total'] += $incomeArr[$key]['quantity'];
            $totalArr['tax_purchase_amount_total'] += $incomeArr[$key]['tax_purchase_amount'];
            $totalArr['tax_sales_amount_total'] += $incomeArr[$key]['tax_sales_amount'];
            $totalArr['purchase_amount_total'] += $incomeArr[$key]['purchase_amount'];
            $totalArr['sales_amount_total'] += $incomeArr[$key]['sales_amount'];
            $totalArr['tax_sales_total'] += $incomeArr[$key]['tax_sales'];
            $totalArr['tax_purchase_total'] += $incomeArr[$key]['tax_purchase'];
            
        }
        ksort($incomeArr);
        $flag=Mod::app()->request->getParam("flag");
        if($flag==1){
            //$user = $this->getUser();
            $this->layout="empty";
            $view = "print";
        }else{
            $view = "detail";
        }
        
        $this->pageTitle="收入-成本确认详情";
        $this->render($view,array(
            "data"=>$data,
            "incomeArr"=>$incomeArr,
            "totalArr"=>$totalArr,
            "dtArr"=>$dtArr
        ));

    }


    public function actionExport()
    {
        $id     = Mod::app()->request->getParam("id");
        $code   = Mod::app()->request->getParam("code");
        if(empty($id) || empty($code))
        {
            $this->renderError("信息异常！", "/monthIncome/");
        }

        $fields = "co.name as 交易主体,p.project_id as 销售出库单号,
                s.name as 下游合作方,p.project_id as 采购合同单号,
                c.name as 上游合作方,p.goods_type as 交易品种,
                i.amount/sd.price as 出库数量,sd.price/100 as '销售单价(元)',
                i.amount/100 as '销售金额(元)',i.invoice_date_actual as 开票日期,
                i.amount/sd.price as 实际采购数量,su.price/100 as '采购单价(元)',
                i.amount/sd.price*su.price/100 as '实际采购金额(元)' ";
        $sql = "select ".$fields."
                from t_income_cost a
                left join t_income_cost_detail b on a.statement_id=b.statement_id
                left join t_invoice i on b.invoice_id=i.invoice_id
                left join t_project p on i.project_id=p.project_id
                left join t_partner c on c.partner_id=p.up_partner_id
                left join t_partner s on s.partner_id=p.down_partner_id
                left join t_settlement su on p.project_id=su.project_id and su.type=1
                left join t_settlement sd on p.project_id=sd.project_id and sd.type=2
                left join t_corporation co on a.corp_id=co.corporation_id
                where a.statement_id=".$id;
        
        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/monthIncome/");
        }
        $data=Utility::query($sql);
        $map= include(ROOT_DIR . "/protected/components/Map_old.php");
        foreach ($data as $key => $value) {
           $data[$key]['交易品种']=$map['goods_type'][$value['交易品种']];
        }

        //print_r($data);die;
        $this->exportExcel($data,"收入确认明细");
    }


    
    
}