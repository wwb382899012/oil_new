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
   'input_array' => array(
       array('type' => 'text', 'key' => 'a.code', 'text' => '入库单编号'),
       array('type' => 'text', 'key' => 'b.contract_code', 'text' => '采购合同编号'),
       array('type' => 'text', 'key' => 'c.name*', 'text' => '上游合作方'),
       array('type' => 'date', 'key' => 'a.entry_date', 'id'=>'entry_date', 'text' => '入库日期'),
   )
);

//列表显示
$array = array(
    array('key' => 'stock_in_id', 'type' => 'href', 'style' => 'width:30px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'stock_in_id,code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '入库单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="入库单详情" href="/stockInList/view/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:80px;text-align:center', 'text' => '采购合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="合同详情" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:160px;text-align:center', 'text' => '上游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="合作方详情" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'entry_date', 'type' => 'date', 'style' => 'width:60px;text-align:center', 'text' => '入库日期'),
);

function getRowActions($row, $self) {
    $links = array();
    $links[] = '<a href="/' . $self->getId() . '/add?stock_in_id=' . $row["stock_in_id"] . '&type=' . ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER . '" title="选择">选择</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

$this->loadForm($form_array, $_data_,null,0,true);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");