<?php
/**
 * Desc: 入库单列表
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/selectContracts',
    'items' => array(
        array('type' => 'text', 'key' => 'dp.name*', 'text' => '下游合作方'),
        array('type' => 'text', 'key' => 'c.contract_code*', 'text' => '销售合同编号'),
        array('type' => 'text', 'key' => 'p.project_code*', 'text' => '项目编号'),
        array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
        array('type' => 'select', 'key' => 'p.type', 'map_name' => 'project_type', 'text' => '项目类型'),
        array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
    )
);

//列表显示
$array = array(
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:180px;text-align:left', 'text' => '销售合同编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:left', 'text' => '外部合同编号'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '下游合作方', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'project_type', 'type' => 'map_val', 'map_name'=>'project_type', 'style' => 'width:100px;text-align:left', 'text' => '项目类型'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '交易主体', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'min-width:30px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();
    $links[] = '<a href="/' . $self->getId() . '/add?contract_id=' . $row["contract_id"] . '&type=' . ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE . '" title="选择">选择</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

$searchArray = ['search_config' => $form_array];
$tableArray = ['column_config' => $array];

$headerArray = ['is_show_back_bread'=> true,'menu_config'=> [
    ['text' => '出库管理'],
    ['text' => '新建发货单', 'link' => '/deliveryOrder/'],
    ['text' => $this->pageTitle]
]];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);