<?php
/**
 * Desc: 入库通知单
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'input_array' => array(
       array('type' => 'text', 'key' => 'a.code', 'text' => '入库通知单编号'),
       array('type' => 'text', 'key' => 'b.contract_code', 'text' => '采购合同编号'),
       array('type' => 'text', 'key' => 'c.project_code', 'text' => '项目编号'),
       array('type' => 'text', 'key' => 'd.name*', 'text' => '上游合作方'),
       array('type' => 'text', 'key' => 'e.name*', 'text' => '交易主体'),
       array('type' => 'select', 'key' => 'a.type', 'map_name' => 'stock_notice_delivery_type', 'text' => '发货方式'),
       array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
   )
);

//列表显示
$array = array(
    array('key' => 'batch_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'batch_id,code', 'type' => 'href', 'style' => 'width:150px;text-align:center', 'text' => '入库通知单编号', 'href_text' => '<a id="t_{1}" title="入库通知单详情" href="/stockIn/detail/?id={1}">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '采购合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '项目编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '上游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'stock_notice_delivery_type', 'style' => 'width:60px;text-align:center', 'text' => '发货方式'),
);

function getRowActions($row, $self) {
    $links = array();
    if ($row['status'] >= StockNotice::STATUS_SUBMIT && $row['status']<StockNotice::STATUS_SETTLE_SUBMIT) {
        $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["batch_id"] . '" title="添加">添加</a>';
    }
    $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["batch_id"] . '" title="查看详情">详情</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

$this->loadForm($form_array, $_GET);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");