<?php
/**
 * Desc: 入库单列表
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/selectStockIns',
   'items' => array(
       array('type' => 'text', 'key' => 'c.name*', 'text' => '上游合作方'),
       array('type' => 'text', 'key' => 'b.contract_code', 'text' => '采购合同编号'),
       array('type' => 'text', 'key' => 'a.code', 'text' => '入库单编号'),
       array('type' => 'date', 'key' => 'a.entry_date', 'id'=>'entry_date', 'text' => '入库日期'),
   )
);

//列表显示
$array = array(
    array('key' => 'stock_in_id,code', 'type' => 'href', 'style' => 'min-width:100px;text-align:left', 'text' => '入库单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/stockInList/view/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'min-width:80px;text-align:left', 'text' => '采购合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'min-width:160px;text-align:left', 'text' => '上游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'entry_date', 'type' => 'date', 'style' => 'min-width:60px;text-align:left', 'text' => '入库日期'),
    array('key' => 'stock_in_id', 'type' => 'href', 'style' => 'min-width:30px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();
    $links[] = '<a href="/' . $self->getId() . '/add?stock_in_id=' . $row["stock_in_id"] . '&type=' . ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER . '" title="选择">选择</a>';

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