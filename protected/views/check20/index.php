<?php

/**
 * Desc: 出库单审核
 * User: phpdraogn
 * Date: 2018/03/13 17:33
 * Time: 17:33
 */

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'c.contract_code', 'text' => '销售合同编号'),
        array('type' => 'text', 'key' => 'soo.code', 'text' => '出库单编号'),
        array('type' => 'text', 'key' => 'do.code', 'text' => '发货单编号'),
        array('type' => 'text', 'key' => 'p.name*', 'text' => '下游合作方&emsp;'),
        array('type' => 'select', 'key' => 'checkStatus',  'noAll'=>'1', 'map_name' => 'stock_out_check_status', 'text' => '审核状态&emsp;'),
    )
);

//列表显示
$array = array(
    array('key' => 'out_order_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowActions'),
    array('key' => 'out_order_id,code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '出库单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="出库单详情" href="/stockOutList/view?id={1}&t=1">{2}</a>'),
    array('key' => 'delivery_order_id,delivery_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '发货单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="发货单详情" href="/deliveryOrder/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '销售合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="销售合同详情" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:160px;text-align:center', 'text' => '下游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="合作方详情" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'store_name', 'type' => '', 'style' => 'width:160px;text-align:center', 'text' => '出库'),
    array('key' => 'out_date', 'type' => 'date', 'style' => 'width:60px;text-align:center', 'text' => '出库日期'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:80px;text-align:center;','text'=>'审核状态','href_text'=>'FlowService::showCheckStatus'),
);

function checkRowActions($row, $self) {
    $links = array();
    if ($row["isCanCheck"]) {
        $links[] = '<a href="/' . $self->getId() . '/check?id=' . $row["obj_id"] . '" title="审核">审核</a>';
    } else {
        $links[] = '<a href="/' . $self->getId() . '/detail?detail_id=' . $row["detail_id"] . '" title="查看详情">查看</a>';
    }
    $s = implode("&nbsp;|&nbsp;", $links);

    return $s;
}

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");