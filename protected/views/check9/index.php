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
    'input_array' => array(
        array('type' => 'text', 'key' => 'b.code', 'text' => '发货单编号'),
        array('type' => 'text', 'key' => 'c.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'p.name*', 'text' => '下游合作方'),
        array('type'=>'select', 'key'=>'b.type', 'map_name'=>'stock_notice_delivery_type', 'text'=>'发货方式'),
        array('type'=>'select', 'key'=>'checkStatus', 'noAll'=>'1', 'map_name'=>'delivery_order_check_status', 'text'=>'审核状态'),
    )
);

//列表显示
$array =array(
    array('key'=>'detail_id','type'=>'href','style'=>'width:40px;text-align:center;','text'=>'操作','href_text'=>'checkRowActions'),
    array('key'=>'detail_id','type'=>'','style'=>'width:60px;text-align:center','text'=>'审核编号'),
    array('key' => 'order_id,code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '发货单编号', 'href_text' => '<a id="t_{1}" title="发货单详情" target="_blank" href="/deliveryOrder/detail/?id={1}">{2}</a>'),
    array('key'=>'corporation_id,corporation_name','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'交易主体','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:160px;text-align:center', 'text' => '下游合作方', 'href_text' => '<a id="t_{1}" title="合作方详情" target="_blank" href="/partner/detail/?id={1}">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'stock_notice_delivery_type', 'style' => 'width:60px;text-align:center', 'text' => '发货方式'),
    array('key' => 'stock_in_id,stock_in_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '入库单编号', 'href_text' => '<a id="t_{1}" title="入库单详情" target="_blank" href="/stockInList/view/?id={1}">{2}</a>'),
    array('key' => 'checkStatus', 'type' => 'map_val', 'map_name' => 'delivery_order_check_status', 'style' => 'width:60px;text-align:center', 'text' => '状态'),
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

$style = empty($_data_[data]['rows']) ? "min-width:1050px;" : "min-width:1050px;";

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", $style, "table-bordered table-layout");