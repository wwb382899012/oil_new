<?php
/**
 * Created by vector.
 * DateTime: 2017/10/24 15:25
 * Describe：
 */

function checkRowEditAction($row,$self)
{
    if($row['type']==ConstantMap::INPUT_INVOICE_TYPE)
        $s = '<a id="t_'.$row['apply_id'].'" title="'.$row['apply_code'].'" target="_blank" href="/inputInvoice/detail/?id='.$row['apply_id'].'&t=1" >'.$row['apply_code'].'</a>';
    else
        $s = '<a id="t_'.$row['apply_id'].'" title="'.$row['apply_code'].'" target="_blank" href="/outputInvoice/detail/?id='.$row['apply_id'].'&t=1" >'.$row['apply_code'].'</a>';
    return $s;
}

if($_data_['type']==ConstantMap::INPUT_INVOICE_TYPE){
    $invoice_type = "vat_invoice_type";
    $invoice_attribute_type = "invoice_input_type";
}
else{
    $invoice_type = "output_invoice_type";
    $invoice_attribute_type = "invoice_output_type";
}

$searchItems = array(
    array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
    array('type'=>'text','key'=>'p.project_code*','text'=>'项目编号'),
    array('type'=>'date','key'=>'start_date','id'=>'datepicker','text'=>'开始日期'),
    array('type'=>'date','key'=>'end_date','id'=>'datepicker2','text'=>'截止日期'),
    array('type'=>'text','key'=>'pa.name*','text'=>'合作方'),
    array('type'=>'text','key'=>'a.apply_code*','text'=>'发票编号'),
    array('type'=>'text','key'=>'g.name*','text'=>'品名'),
    array('type'=>'select','key'=>'a.type_sub','map_name'=>$invoice_attribute_type,'text'=>'发票属性'),
    array('type'=>'select','key'=>'a.contract_type','map_name'=>'goods_contract_type','text'=>'货款合同类型'),
    array('type'=>'text','key'=>'c.contract_code*','text'=>'货款合同编号'),
    array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
    array('type'=>'select','key'=>'a.invoice_contract_type','map_name'=>'contract_category','text'=>'发票合同类型'),
    array('type'=>'text','key'=>'a.invoice_contract_code*','text'=>'发票合同编号'),
);

$listArray = array(
    array('key'=>'apply_id,apply_code','type'=>'href','style'=>'width:140px;text-align:left','text'=>'发票编号','href_text'=>'checkRowEditAction'),
    array('key'=>'type_sub','type'=>'map_val','style'=>'width:100px;text-align:left','map_name'=>$invoice_attribute_type,'text'=>'发票属性'),
    array('key'=>'goods_name','type'=>'','style'=>'width:90px;text-align:left','text'=>'品名'),
    array('key'=>'quantity','type'=>'number','style'=>'width:90px;text-align:left','text'=>'数量'),
    array('key'=>'unit','type'=>'map_val','style'=>'width:40px;text-align:left','map_name'=>'goods_unit','text'=>'单位'),
    array('key'=>'price','type'=>'amount','style'=>'width:90px;text-align:right','text'=>'单价'),
    array('key'=>'rate','type'=>'','style'=>'width:90px;text-align:left','text'=>'税率'),
    array('key'=>'amount','type'=>'amount','text'=>'发票金额','style'=>'width:120px;text-align:right'),
    array('key'=>'rate_amount','type'=>'amount','text'=>'税额','style'=>'width:120px;text-align:right'),
    array('key'=>'project_id,project_code','type'=>'href','style'=>'width:140px;text-align:left','text'=>'项目编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'project_type','type'=>'map_val','style'=>'width:100px;text-align:left;','map_name'=>'project_type','text'=>'项目类型'),
    array('key'=>'corporation_name','type'=>'','style'=>'text-align:left;','text'=>'交易主体'),
    array('key'=>'partner_name','type'=>'','style'=>'text-align:left;','text'=>'合作方'),
    array('key'=>'contract_type','type'=>'map_val','style'=>'width:100px;text-align:left;','map_name'=>'goods_contract_type','text'=>'货款合同类型'),
    array('key'=>'contract_id,contract_code','type'=>'href','style'=>'width:140px;text-align:left','text'=>'货款合同编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank"  href="/contract/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:left', 'text' => '外部合同编号'),
    array('key'=>'invoice_contract_type','type'=>'map_val','style'=>'width:120px;text-align:left;','map_name'=>'contract_category','text'=>'发票合同类型'),
    array('key'=>'invoice_contract_code','type'=>'','style'=>'width:140px;text-align:left','text'=>'发票合同编号'),
    array('key'=>'invoice_type','type'=>'map_val','style'=>'width:120px;text-align:left','map_name'=>$invoice_type,'text'=>'税票类型'),
    array('key'=>'create_time','type'=>'date','style'=>'width:140px;text-align:left','text'=>'录入时间'),
    array('key'=>'invoice_date','type'=>'','style'=>'width:120px;text-align:left','text'=>'发票时间'),
);

if ($_data_['type']==ConstantMap::OUTPUT_INVOICE_TYPE) {
    array_splice($searchItems,4,0,array(array('type'=>'select','key'=>'status','map_name'=>'invoice_open_status','text'=>'开票状态')));
    array_splice($listArray, 2,0,array(array('key'=>'status','type'=>'map_val','map_name'=>'invoice_open_status','style'=>'width:80px;text-align:left','text'=>'开票状态')));
}

//查询区域
$form_array = array(
    'form_url'=>'/'.$this->getId().'/',
    'items'=> $searchItems
);

//列表显示
$array = $listArray;

$headerArray = ['is_show_export' => true];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array, 'float_columns' => 0];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);

?>
