<?php

/**
 * Desc: 发货单审核
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'b.code', 'text' => '发货单编号'),
        array('type' => 'text', 'key' => 'c.name*', 'text' => '交易主体'),
        array('type'=>'select', 'key'=>'checkStatus', 'noAll'=>'1', 'map_name'=>'delivery_order_check_status', 'text'=>'审核状态'),
        array('type' => 'text', 'key' => 'p.name*', 'text' => '下游合作方'),
        array('type'=>'select', 'key'=>'b.type', 'map_name'=>'stock_notice_delivery_type', 'text'=>'发货方式'),
    )
);

//列表显示
$array =array(
    array('key'=>'detail_id','type'=>'','style'=>'width:40px;text-align:left','text'=>'审核编号'),
    array('key' => 'order_id,code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:100px;text-align:left', 'text' => '发货单编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/deliveryOrder/detail/?id={1}&t=1">{2}</a>'),
    array('key'=>'corporation_id,corporation_name','type'=>'href','style'=>'width:100px;text-align:left;','text'=>'交易主体','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '下游合作方', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'stock_notice_delivery_type', 'style' => 'width:40px;text-align:left', 'text' => '发货方式'),
    array('key' => 'stock_in_id,stock_in_code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:160px;text-align:left', 'text' => '入库单编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/stockInList/view/?id={1}&t=1">{2}</a>'),
    array('key' => 'checkStatus', 'type' => 'map_val', 'map_name' => 'delivery_order_check_status', 'style' => 'width:60px;text-align:left', 'text' => '状态'),
    array('key'=>'detail_id','type'=>'href','style'=>'min-width:40px;text-align:left;','text'=>'操作','href_text'=>'checkRowActions'),
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