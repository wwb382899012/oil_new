<?php
/**
*	销项票品名列表
*/
class OutputListController extends ExportableController {
    public function pageInit() {
        $this->filterActions = "";
        $this->rightCode = "outputList";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
        // $attr = Mod::app()->request->getParam('search');

//        $attr = $_GET[search];
        $attr= $this->getSearch();
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
            $query .= " and id.invoice_date between '".$start_date."' and '".$end_date."'";
        else if(!empty($start_date))
            $query .= " and id.invoice_date between '".$start_date."' and '".date('Y-m-d')."'";
        else if(!empty($end_date))
            $query .= " and id.invoice_date between '".date('Y-m-d')."' and '".$end_date."'";

        $user = SystemUser::getUser(Utility::getNowUserId());
        $sql="select {col}"
            ." from t_invoice_application a "
            ." left join t_invoice i on a.apply_id=i.apply_id"
            ." left join t_invoice_detail id on i.invoice_id=id.invoice_id"
            ." left join t_goods g on id.goods_id=g.goods_id"
            ." left join t_project p on a.project_id=p.project_id "
            ." left join t_corporation co on a.corporation_id=co.corporation_id "
            ." left join t_contract c on a.contract_id=c.contract_id "
            ." left join t_partner pa on pa.partner_id = c.partner_id "
            ." left join t_system_user u on a.create_user_id=u.user_id"
            ." left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1"
            .$this->getWhereSql($attr);
        $sql    .= $query;
        $sql    .= " and a.type=".ConstantMap::OUTPUT_INVOICE_TYPE." and i.status>=".Invoice::STATUS_PASS." and a.corporation_id in (".$user['corp_ids'].") order by a.apply_id desc {limit}";
        $fields  = "a.apply_id,a.apply_code,a.type,a.type_sub,a.create_time,a.create_user_id,c.contract_code,c.contract_id,
                   a.contract_type,p.project_id,p.project_code,p.type as project_type,u.name as user_name,a.invoice_type,
                   a.invoice_contract_type,a.invoice_contract_code,a.amount,g.goods_id,IFNULL(g.name, id.invoice_name) as goods_name,id.invoice_date,
                   co.corporation_id,co.name as corporation_name,concat(id.rate*100, '%') as rate ,id.amount,id.invoice_name,pa.partner_id,pa.name as partner_name,
                   id.rate*id.amount as rate_amount,cf.code_out,
                   case when a.type_sub=2 then '-' else id.quantity end as quantity,
                   case when a.type_sub=2 then '-' else id.unit end as unit,
                   case when a.type_sub=2 then 0 else id.price end as price,
                   case when a.amount=a.amount_paid then 1 else 0 end as status";

        $export_str = Mod::app()->request->getParam('export_str');
        // print_r($export_str);die;
        if(!empty($export_str)) {
            $this->export($sql, $fields, $export_str);
            return;
        } else {
            $data = $this->queryTablesByPage($sql, $fields);
        }

        if(!empty($start_date))
            $attr['start_date']=$start_date;
        if(!empty($end_date))
            $attr['end_date']=$end_date;
        if($status=="0" || $status=="1")
            $attr["status"]=$status;
        $data['search'] = $attr;
        $data['type'] = ConstantMap::OUTPUT_INVOICE_TYPE;

        $this->pageTitle = '销项票品名列表';
        $this->render('/invoiceList/index', $data);
    }

    public function actionExport() {
        $attr= $this->getSearch();
        $query="";
        if(isset($attr["status"]) && $attr["status"]=="0"){
            $query=" and a.amount>a.amount_paid";
            unset($attr["status"]);
        }else if($attr["status"]=="1"){
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
            $query .= " and id.invoice_date between '".$start_date."' and '".$end_date."'";
        else if(!empty($start_date))
            $query .= " and id.invoice_date between '".$start_date."' and '".date('Y-m-d')."'";
        else if(!empty($end_date))
            $query .= " and id.invoice_date between '".date('Y-m-d')."' and '".$end_date."'";

        $fields  = "a.apply_code 发票编号, a.type_sub 发票属性, case when a.amount=a.amount_paid then 1 else 0 end as 开票状态, IFNULL(g.name, id.invoice_name) 品名, case when a.type_sub=2 then '-' else id.quantity end as 数量, 
                    case when a.type_sub=2 then '-' else id.unit end as 单位, case when a.type_sub=2 then 0 else id.price end as 单价, concat(id.rate*100, '%') as 税率, 
                    id.amount 发票金额, id.rate*id.amount as 税额, p.project_code 项目编号, p.type 项目类型, co.name 交易主体, pa.name 合作方, a.contract_type 货款合同类型,
                    c.contract_code 货款合同编号, cf.code_out 外部合同编号, a.invoice_contract_type 发票合同类型, a.invoice_contract_code 发票合同编号, 
                    a.invoice_type 税票类型, a.create_time 录入时间, id.invoice_date 发票时间";

        $user = SystemUser::getUser(Utility::getNowUserId());
        $sql="select " . $fields
             ." from t_invoice_application a "
             ." left join t_invoice i on a.apply_id=i.apply_id"
             ." left join t_invoice_detail id on i.invoice_id=id.invoice_id"
             ." left join t_goods g on id.goods_id=g.goods_id"
             ." left join t_project p on a.project_id=p.project_id "
             ." left join t_corporation co on a.corporation_id=co.corporation_id "
             ." left join t_contract c on a.contract_id=c.contract_id "
             ." left join t_partner pa on pa.partner_id=c.partner_id  "
             ." left join t_system_user u on a.create_user_id=u.user_id"
             ." left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1"
             .$this->getWhereSql($attr);

        $sql    .= $query;
        $sql    .= " and a.type=".ConstantMap::OUTPUT_INVOICE_TYPE." and i.status>=".Invoice::STATUS_PASS." and a.corporation_id in (".$user['corp_ids'].") order by a.apply_id desc";

        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！");
        } else {
            foreach ($data as $key => $row) {
                $data[$key]['单价'] = '￥'.number_format($row["单价"]/100,2);
                $data[$key]['发票金额'] = '￥'.number_format($row["发票金额"]/100,2);
                $data[$key]['税额'] = '￥'.number_format($row["税额"]/100,2);

                $data[$key]['发票属性'] = Map::$v['invoice_output_type'][$row['发票属性']];
                $data[$key]['开票状态'] = Map::$v['invoice_open_status'][$row['开票状态']];
                $data[$key]['单位'] = Map::$v['goods_unit'][$row['单位']]['name'];
                $data[$key]['项目类型'] = Map::$v['project_type'][$row['项目类型']];
                $data[$key]['货款合同类型'] = Map::$v['goods_contract_type'][$row['货款合同类型']];
                $data[$key]['发票合同类型'] = Map::$v['contract_category'][$row['发票合同类型']];
                $data[$key]['税票类型'] = Map::$v['output_invoice_type'][$row['税票类型']];
            }
        }

        $this->exportExcel($data);
    }
}
