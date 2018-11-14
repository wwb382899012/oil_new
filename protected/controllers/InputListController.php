<?php
/**
*	进项票品名列表
*/
class InputListController extends ExportableController {
    public function pageInit() {
        $this->filterActions = "";
        $this->rightCode = "inputList";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
        // $attr = Mod::app()->request->getParam('search');

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
            $query .= " and a.invoice_date between '".$start_date."' and '".$end_date."'";
        else if(!empty($start_date))
            $query .= " and a.invoice_date between '".$start_date."' and '".date('Y-m-d')."'";
        else if(!empty($end_date))
            $query .= " and a.invoice_date between '".date('Y-m-d')."' and '".$end_date."'";

        $user = SystemUser::getUser(Utility::getNowUserId());
        $sql="select {col}"
            ." from t_invoice_application a "
            ." left join t_invoice_application_detail d on a.apply_id=d.apply_id"
            ." left join t_goods g on d.goods_id=g.goods_id"
            ." left join t_project p on a.project_id=p.project_id "
            ." left join t_corporation co on a.corporation_id=co.corporation_id "
            ." left join t_contract c on a.contract_id=c.contract_id "
            ." left join t_partner pa on pa.partner_id = c.partner_id "
            ." left join t_system_user u on a.create_user_id=u.user_id"
            ." left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1"
            .$this->getWhereSql($attr);
        $sql    .= $query;
        $sql    .= " and a.type=".ConstantMap::INPUT_INVOICE_TYPE." and a.status>=".InvoiceApplication::STATUS_PASS." and a.corporation_id in (".$user['corp_ids'].") order by a.apply_id desc, d.detail_id desc {limit}";
        $fields  = "a.apply_id,a.apply_code,a.type,a.type_sub,a.create_time,a.create_user_id,c.contract_code,c.contract_id,pa.partner_id,pa.name as partner_name,
                   a.contract_type,p.project_id,p.project_code,p.type as project_type,u.name as user_name,a.invoice_type,
                   a.invoice_contract_type,a.invoice_contract_code,a.amount,g.goods_id,IFNULL(g.name, d.invoice_name) as goods_name,a.invoice_date,
                   co.corporation_id,co.name as corporation_name,concat(d.rate*100, '%') as rate ,d.amount,d.invoice_name,
                   d.rate*d.amount as rate_amount,cf.code_out,
                   case when a.type_sub=2 then '-' else d.quantity end as quantity,
                   case when a.type_sub=2 then '-' else d.unit end as unit,
                   case when a.type_sub=2 then 0 else d.price end as price";

        $export_str = Mod::app()->request->getParam('export_str');
        // print_r($export_str);die;
        if(!empty($export_str)) {
            $this->export($sql, $fields, $export_str, '进项票品名列表');
            return;
        } else {
            $data = $this->queryTablesByPage($sql, $fields);
        }

        if(!empty($start_date))
            $attr['start_date']=$start_date;
        if(!empty($end_date))
            $attr['end_date']=$end_date;
        $data['search'] = $attr;
        $data['type'] = ConstantMap::INPUT_INVOICE_TYPE;

        $this->pageTitle = '进项票品名列表';
        $this->render('/invoiceList/index', $data);
    }

    public function actionExport() {
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
            $query .= " and a.invoice_date between '".$start_date."' and '".$end_date."'";
        else if(!empty($start_date))
            $query .= " and a.invoice_date between '".$start_date."' and '".date('Y-m-d')."'";
        else if(!empty($end_date))
            $query .= " and a.invoice_date between '".date('Y-m-d')."' and '".$end_date."'";

        $user = SystemUser::getUser(Utility::getNowUserId());
        $fields  = "a.apply_code 发票编号, a.type_sub 发票属性, IFNULL(g.name, d.invoice_name) 品名, case when a.type_sub=2 then '-' else d.quantity end as 数量, 
                    case when a.type_sub=2 then '-' else d.unit end as 单位, case when a.type_sub=2 then 0 else d.price end as 单价, concat(d.rate*100, '%') as 税率, 
                    d.amount 发票金额, d.rate*d.amount as 税额, p.project_code 项目编号, p.type 项目类型, co.name 交易主体, pa.name 合作方, a.contract_type 货款合同类型,
                    c.contract_code 货款合同编号, cf.code_out 外部合同编号, a.invoice_contract_type 发票合同类型, a.invoice_contract_code 发票合同编号, 
                    a.invoice_type 税票类型, a.create_time 录入时间, a.invoice_date 发票时间";
        $sql="select " . $fields
             ." from t_invoice_application a "
             ." left join t_invoice_application_detail d on a.apply_id=d.apply_id"
             ." left join t_goods g on d.goods_id=g.goods_id"
             ." left join t_project p on a.project_id=p.project_id "
             ." left join t_corporation co on a.corporation_id=co.corporation_id "
             ." left join t_contract c on a.contract_id=c.contract_id "
             ." left join t_partner pa on pa.partner_id = c.partner_id "
             ." left join t_system_user u on a.create_user_id=u.user_id"
             ." left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1"
             .$this->getWhereSql($attr);
        $sql    .= $query;
        $sql    .= " and a.type=".ConstantMap::INPUT_INVOICE_TYPE." and a.status>=".InvoiceApplication::STATUS_PASS." and a.corporation_id in (".$user['corp_ids'].") order by a.apply_id desc, d.detail_id desc";

        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！");
        } else {
            foreach ($data as $key => $row) {
                $data[$key]['单价'] = '￥'.number_format($row["单价"]/100,2);
                $data[$key]['发票金额'] = '￥'.number_format($row["发票金额"]/100,2);
                $data[$key]['税额'] = '￥'.number_format($row["税额"]/100,2);

                $data[$key]['发票属性'] = Map::$v['invoice_input_type'][$row['发票属性']];
                $data[$key]['单位'] = Map::$v['goods_unit'][$row['单位']]['name'];
                $data[$key]['项目类型'] = Map::$v['project_type'][$row['项目类型']];
                $data[$key]['货款合同类型'] = Map::$v['goods_contract_type'][$row['货款合同类型']];
                $data[$key]['发票合同类型'] = Map::$v['contract_category'][$row['发票合同类型']];
                $data[$key]['税票类型'] = Map::$v['vat_invoice_type'][$row['税票类型']];
            }
        }

        $this->exportExcel($data);
    }
}
