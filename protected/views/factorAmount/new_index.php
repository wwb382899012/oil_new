<?php
/**
 * Desc: 保理对接款管理
 * User: susiehuang
 * Date: 2017/12/19 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'a.apply_id', 'text' => '付款编号'),
        array('type' => 'text', 'key' => 'p.project_code', 'text' => '项目编码'),
        array('type' => 'text', 'key' => 'e.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'c.contract_code', 'text' => '合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'd.name*', 'text' => '合作方'),
        array('type'=>'date','key'=>'a.actual_pay_date>','id'=>'start_date','text'=>'实际放款时间'),
        array('type'=>'date','key'=>'a.actual_pay_date<','id'=>'end_date','text'=>'到'),
        array('type' => 'select', 'key' => 'p.type', 'map_name' => 'project_type', 'text' => '项目类型'),
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'factor_amount_status', 'text' => '状态'),
    )
);


function checkRowEditAction($row,$self)
{
    $links=array();
    if (!empty($row['factor_id']) && FactoringService::checkIsCanConfirm($row['status'])) {
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["factor_id"] . '" title="确认">确认</a>';
    }
    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

function getPayApplyAmount($row) {
    return Map::$v['currency'][$row['currency']]['ico'].Utility::numberFormatFen2Yuan($row['pay_apply_amount']);
}

function getRate($row) {
    return ($row['rate'] * 100).'%';
}

//列表显示
$array = array(
    array('key' => 'factor_id', 'type' => 'href', 'style' => 'width:60px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
    array('key' => 'apply_id', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '付款申请编号', 'href_text'=>'<a title="{1}" target="_blank" href="/pay/detail/?id={1}&t=1">{1}</a>'),
    array('key' => 'contract_code', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '保理对接编号'),
    array('key' => 'contract_code_fund', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '资金对接编号'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'factor_amount_status', 'style' => 'width:60px;text-align:center', 'text' => '状态'),
    array('key' => 'actual_pay_date', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '实际放款时间'),
    array('key' => 'amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right', 'text' => '实际保理对接金额'),
    array('key' => 'rate', 'type' => 'href', 'style' => 'width:80px;text-align:center', 'text' => '年化利率', 'href_text' => 'getRate'),
    array('key' => 'pay_apply_amount', 'type' => 'href', 'style' => 'width:120px;text-align:right', 'text' => '付款申请金额', 'href_text' => 'getPayApplyAmount'),
    array('key' => 'apply_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right', 'text' => '申请保理对接金额'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '项目编码', 'href_text'=>'<a title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'project_type', 'style' => 'width:80px;text-align:center', 'text' => '项目类型'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:150px;text-align:center', 'text' => '交易主体', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'contract_id,c_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '合同编号', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/contract/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:150px;text-align:left;', 'text' => '合作方', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>')

);
$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1300px;", "table-bordered table-layout data-table", "", true);