<?php

/**
 * Desc: 付款止付审核
 */

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items'=>array(
        array('type'=>'text','key'=>'b.apply_id','text'=>'付款申请编号'),
        array('type' => 'select', 'key' => 'b.type', 'map_name' => 'pay_application_type', 'text' => '付款类型'),
        array('type' => 'corpName', 'key' => 'b.corporation_id', 'text' => '交易主体'),
        array('type'=>'text','key'=>'b.payee*','text'=>'收款单位'),
        array('type'=>'select', 'key'=>'checkStatus', 'noAll'=>'1', 'map_name'=>'pay_stop_check_status', 'text'=>'审核状态'),
    ),
);

function showStopAmount($row, $self)
{
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($row["amount_stop"]/100,2);
    return $str;
}


//列表显示
$array =array(
    array('key'=>'check_id','type'=>'','style'=>'width:60px;text-align:left','text'=>'审核编号'),
    array('key' => 'apply_id,stop_code', 'type' => 'href', 'style' => 'width:100px;text-align:left', 'text' => '止付编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/payStop/detail/?t=1&id={1}">{2}</a>'),
    array('key' => 'apply_id,apply_id', 'type' => 'href', 'style' => 'width:100px;text-align:left', 'text' => '付款申请编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/pay/detail/?t=1&id={1}">{1}</a>'),
    array('key' => 'amount_stop', 'type' => 'href', 'style' => 'width:120px;text-align:right', 'text' => '止付金额', 'href_text' => 'showStopAmount'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'pay_application_type', 'style' => 'width:100px;text-align:left', 'text' => '付款类型'),
    array('key' => 'payee', 'type' => '', 'style' => 'width:140px;text-align:left', 'text' => '收款单位'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:80px;text-align:left;','text'=>'审核状态','href_text'=>'FlowService::showCheckStatus'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:40px;text-align:left;','text'=>'操作','href_text'=>'checkRowActions'),
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