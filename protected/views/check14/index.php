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
        array('type' => 'text', 'key' => 'fd.contract_code', 'text' => '保理对接流水号'),
        array('type' => 'text', 'key' => 'c.apply_id', 'text' => '付款申请编号'),
        array('type' => 'text', 'key' => 'b.contract_code', 'text' => '保理对接编号'),
        array('type' => 'text', 'key' => 'b.contract_code_fund', 'text' => '资金对接编号'),
        array('type' => 'text', 'key' => 'e.contract_code', 'text' => '采购合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'p.name*', 'text' => '上游合作方'),
        array('type' => 'text', 'key' => 'd.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 's.name', 'text' => '申请人'),
        array('type'=>'select', 'key'=>'checkStatus', 'noAll'=>'1', 'map_name'=>'factor_check_status', 'text'=>'审核状态'),
    )
);

//列表显示
$array =array(
    array('key'=>'detail_id','type'=>'href','style'=>'width:40px;text-align:center;','text'=>'操作','href_text'=>'checkRowActions'),
    array('key'=>'detail_id','type'=>'','style'=>'width:60px;text-align:center','text'=>'审核编号'),
    array('key' => 'obj_id,water_code', 'type' => 'href', 'style' => 'width:80px;text-align:center', 'text' => '保理对接流水号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/factor/detail/?id={1}">{2}</a>'),
    array('key' => 'apply_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '付款申请编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{1}" href="/pay/detail/?id={1}&t=1">{1}</a>'),
    array('key' => 'contract_code', 'type' => '', 'style' => 'width:80px;text-align:center;', 'text' => '保理对接编号'),
    array('key' => 'contract_code_fund', 'type' => '', 'style' => 'width:80px;text-align:center;', 'text' => '资金对接编号'),
    array('key' => 'contract_id,c_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '采购合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/businessConfirm/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '上游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'pay_apply_amount', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '付款申请金额', 'href_text' => 'getPayApplyAmount'),
    array('key' => 'amount', 'type' => 'amount', 'style' => 'width:140px;text-align:right;', 'text' => '对接本金'),
    array('key' => 'checkStatus', 'type' => 'map_val', 'map_name' => 'factor_check_status', 'style' => 'width:60px;text-align:center', 'text' => '状态'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:60px;text-align:center;', 'text' => '申请人'),
);

function checkRowActions($row, $self) {
    $links = array();
    if ($row["isCanCheck"]) {
        $links[] = '<a href="/' . $self->getId() . '/check?id=' . $row["obj_id"] . '" title="审核">审核</a>';
    } else {
        $links[] = '<a href="/factor/detail?id=' . $row["obj_id"] . '" title="查看详情">查看</a>';
    }
    $s = implode("&nbsp;|&nbsp;", $links);

    return $s;
}

function getPayApplyAmount($row) {
    return Map::$v['currency'][$row['currency']]['ico'] . Utility::numberFormatFen2Yuan($row['pay_apply_amount']);
}

$style = empty($_data_[data]['rows']) ? "min-width:1050px;" : "min-width:1550px;";

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", $style, "table-bordered table-layout", "", true);