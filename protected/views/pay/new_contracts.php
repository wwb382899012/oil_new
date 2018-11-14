<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/23 14:59
 * Describe：
 */
function checkRowEditAction($row, $self,$tdConfig) {
    $s = '<a href="/pay/add?contractId=' . $row["contract_id"] . '&type='.$tdConfig.'" title="发起合同付款申请">申请</a>';
    return $s;
}


//查询区域
$form_array = array(
    'form_url' => '/pay/contracts',
    'items' => array(
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'c.contract_code*', 'text' => '合同编号'),
        array('type' => 'corpName', 'key' => 'c.corporation_id', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'b.name*', 'text' => '合作方'),
        array('type' => 'select', 'key' => 'c.type', 'map_name' => 'buy_sell_type', 'text' => '合同类型'),
        array('type' => 'text', 'key' => 'p.project_code*', 'text' => '项目编号'),
        array('type' => 'select', 'key' => 'p.type', 'map_name' => 'project_type', 'text' => '项目类型'),
        array('type' => 'hidden','id'=>"type", 'key' => 'type'),
    ),
);

//列表显示
$array = array(
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:160px;text-align:left', 'text' => '合同编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:left', 'text' => '外部合同编号'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'buy_sell_type', 'style' => 'width:100px;text-align:left', 'text' => '合同类型'),
    array('key' => 'corp_name', 'type' => '', 'style' => 'text-align:left;', 'text' => '交易主体'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'project_type', 'type' => 'map_val', 'map_name' => 'project_type', 'style' => 'width:140px;text-align:left', 'text' => '项目类型'),
    array('key' => 'partner_name', 'type' => '', 'style' => 'width:260px;text-align:left;', 'text' => '合作方'),
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'width:80px;text-align:left;', 'text' => '操作','params'=>$type, 'href_text' => 'checkRowEditAction'),
);

$headerArray = ['is_show_back_bread' => true];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>
