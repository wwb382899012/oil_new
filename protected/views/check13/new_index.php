<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/26 15:44
 * Describe：
 */

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type'=>'select', 'key'=>'checkStatus', 'noAll'=>'1', 'map_name'=>'pay_application_check_status', 'text'=>'审核状态'),
        array('type' => 'corpName', 'key' => 'b.corporation_id', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'b.apply_id', 'text' => '付款编号'),
        array('type' => 'text', 'key' => 'e.contract_code', 'text' => '合同编号'),
    )
);

//列表显示
$array =array(
    array('key'=>'check_id','type'=>'','style'=>'width:60px;text-align:left','text'=>'审核编号'),
    array('key' => 'apply_id', 'type' => 'href', 'style' => 'width:120px;text-align:left;', 'text' => '付款编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{1}" href="/pay/detail/?id={1}&t=1">{1}</a>'),
    array('key' => 'amount', 'type' => 'href', 'style' => 'width:180px;text-align:right;', 'text' => '付款金额', 'href_text' => 'showPayAmount'),
    array('key'=>'node_name','type'=>'','style'=>'width:120px;text-align:left','text'=>'审核节点'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:60px;text-align:left;','text'=>'审核状态','href_text'=>'FlowService::showCheckStatus'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:180px;text-align:left', 'text' => '合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'text-align:left; width: 220px;', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:60px;text-align:left;','text'=>'操作','href_text'=>'checkRowActions'),

);
function showPayAmount($row, $self)
{
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($row["amount"]/100,2);
    return $str;
}
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