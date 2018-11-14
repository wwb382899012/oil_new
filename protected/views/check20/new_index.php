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
    'items' => array(
        array('type' => 'text', 'key' => 'soo.code', 'text' => '出库单编号'),
        array('type' => 'text', 'key' => 'c.contract_code', 'text' => '销售合同编号'),
        array('type' => 'select', 'key' => 'checkStatus',  'noAll'=>'1', 'map_name' => 'stock_out_check_status', 'text' => '审核状态'),
        array('type' => 'text', 'key' => 'do.code', 'text' => '发货单编号'),
        array('type' => 'text', 'key' => 'p.name*', 'text' => '下游合作方'),
    )
);

//列表显示
$array = array(
    array('key' => 'out_order_id,code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:100px;text-align:left', 'text' => '出库单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/stockOutList/view?id={1}&t=1">{2}</a>'),
    array('key' => 'delivery_order_id,delivery_code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:100px;text-align:left', 'text' => '发货单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/deliveryOrder/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:100px;text-align:left', 'text' => '销售合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:160px;text-align:left', 'text' => '下游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'store_name', 'type' => '', 'style' => 'width:160px;text-align:left', 'text' => '出库'),
    array('key' => 'out_date', 'type' => 'date', 'style' => 'width:60px;text-align:left', 'text' => '出库日期'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:80px;text-align:left;','text'=>'审核状态','href_text'=>'FlowService::showCheckStatus'),
    array('key' => 'out_order_id', 'type' => 'href', 'style' => 'min-width:80px;text-align:left;', 'text' => '操作', 'href_text' => 'checkRowActions'),
);

function checkRowActions($row, $self) {
    $links = array();
    if ($row["isCanCheck"]) {
        $links[] = '<a href="/' . $self->getId() . '/check?id=' . $row["obj_id"] . '" title="审核">审核</a>';
    } else {
        $links[] = '<a href="/' . $self->getId() . '/detail?detail_id=' . $row["detail_id"] . '" title="查看详情">查看</a>';
    }
    $s = implode("&nbsp;&nbsp;", $links);

    return $s;
}

$searchArray = ['search_config' => $form_array];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);