<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/29 15:26
 * Describe：
 */

function checkRowEditAction($row, $self) {
    $links = array();
    if (!empty($row['contract_id'])) {
        $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["contract_id"] . '" title="查看详情">详情</a>';
    }
    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}
function getGoodsList($row) {
    $goods = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($row['contract_id']));
    return '<span title="'.$goods.'">'.$goods.'</span>';
}
function getAmountCnyDesc($row) {
    return Map::$v['currency'][$row['currency']]['ico'].Utility::numberFormatFen2Yuan($row['amount']);
}

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'd.name*', 'text' => '合作方'),
        array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'a.contract_code*', 'text' => '合同编号'),
        array('type' => 'text', 'key' => 'a.contract_id', 'text' => '合同ID'),
        array('type' => 'select', 'key' => 'a.type', 'map_name' => 'buy_sell_type', 'text' => '合同类型'),
        array('type' => 'text', 'key' => 'p.project_code*', 'text' => '项目编号'),
        array('type' => 'select', 'key' => 'p.type', 'map_name' => 'project_type', 'text' => '项目类型'),
        array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'contract_status', 'text' => '状态'),
    )
);


//列表显示
$array = array(
    array('key' => 'contract_id', 'type' => '', 'style' => 'width:40px;text-align:left', 'text' => '合同ID'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '合同编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/' . $this->getId() . '/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type' => '', 'style' => 'width:120px;text-align:left', 'text' => '外部合同编号' ),
    array('key' => 'corporation_id,corp_name','type'=>'href','style'=>'width:200px;text-align:left','text'=>'交易主体','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'contract_status', 'style' => 'width:100px;text-align:left', 'text' => '合同状态'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'buy_sell_type', 'style' => 'width:80px;text-align:left', 'text' => '合同类型'),
    array('key' => 'partner_id,partner_name','type'=>'href','style'=>'width:200px;text-align:left','text'=>'合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'contract_date', 'type' => '', 'style' => 'width:100px;text-align:left', 'text' => '合同签订日期' ),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'project_type', 'type' => 'map_val', 'map_name' => 'project_type', 'style' => 'width:80px;text-align:left', 'text' => '项目类型'),
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '品名', 'href_text' => 'getGoodsList'),
    array('key' => 'amount', 'type' => 'href', 'style' => 'width:120px;text-align:right', 'text' => '合同总金额', 'href_text' => 'getAmountCnyDesc'),
    array('key' => 'amount_cny', 'type' => 'amount', 'style' => 'width:120px;text-align:right', 'text' => '合同人民币金额'),
    array('key' => 'name', 'type' => '', 'style' => 'width:100px;text-align:left', 'text' => '项目负责人'),
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'width:60px;text-align:left;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
);

$searchArray = ['search_config' => $form_array];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);
?>


