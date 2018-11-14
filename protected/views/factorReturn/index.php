<?php
/**
 * Desc: 保理回款
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'factor_return_status', 'text' => '状态'),
        array('type' => 'text', 'key' => 'f.contract_code', 'text' => '保理对接编号'),
        array('type' => 'text', 'key' => 'f.contract_code_fund', 'text' => '资金对接编号'),
        array('type' => 'text', 'key' => 'a.apply_id', 'text' => '付款申请编号'),
        array('type' => 'date', 'id' => 'payStartDate', 'key' => 'a.pay_date>', 'text' => '合同放款日'),
        array('type' => 'date', 'id' => 'payEndDate', 'key' => 'a.pay_date<', 'text' => '到'),
        array('type' => 'text', 'key' => 'a.contract_code', 'text' => '保理对接流水号'),
        array('type' => 'date', 'id' => 'returnStartDate', 'key' => 'a.return_date>', 'text' => '合同回款日'),
        array('type' => 'date', 'id' => 'returnEndDate', 'key' => 'a.return_date<', 'text' => '到'),
        array('type' => 'text', 'key' => 'e.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'c.contract_code', 'text' => '采购合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'd.project_code', 'text' => '项目编号')
    )
);

//列表显示
$array = array(
    array('key' => 'factor_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'factor_return_status', 'style' => 'width:80px;text-align:center', 'text' => '状态'),
    array('key' => 'detail_id,water_code', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '保理对接流水号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/factor/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'amount', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '对接本金（元）'),
    array('key' => 'interest', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '应收利息（元）'),
    array('key' => 'total_amount', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '对接本息（元）'),
    array('key' => 'rate', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '年化利率', 'href_text' => 'getRate'),
    array('key' => 'return_capital', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '已收本金（元）'),
    array('key' => 'return_interest', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '已收利息（元）'),
    array('key' => 'return_amount', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '已收本息（元）'),
    array('key' => 'balance_capital', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '未还本金（元）'),
    array('key' => 'balance_interest', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '未还利息（元）'),
    array('key' => 'pay_date', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '合同放款日'),
    array('key' => 'return_date', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '合同回款日'),
    array('key' => 'contract_period', 'type' => '', 'style' => 'width:80px;text-align:center;', 'text' => '合同期限（天）'),
    //array('key' => 'actual_pay_date', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '实际放款日'),
    array('key' => 'factor_service_fee', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '保理服务费'),
    array('key' => 'service_fee', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '服务费<br/>（霍尔果斯）'),
    array('key' => 'contract_code', 'type' => '', 'style' => 'width:120px;text-align:center;', 'text' => '保理对接编号'),
    array('key' => 'contract_code_fund', 'type' => '', 'style' => 'width:120px;text-align:center;', 'text' => '资金对接编号'),
    array('key' => 'contract_id,c_code', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '采购合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/businessConfirm/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'apply_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '付款申请编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{1}" href="/pay/detail/?id={1}&t=1">{1}</a>'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '项目编号', 'href_text' => '<a id="t_{1}" title="{1}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
);

function getRowActions($row, $self) {
    $links = array();
    if (!empty($row['detail_id'])) {
        if ($row['status'] >= FactorDetail::STATUS_PASS) {
            if ($row['status'] < FactorDetail::STATUS_RETURNED) {
                $links[] = '<a href="/' . $self->getId() . '/add?detail_id=' . $row["detail_id"] . '" title="回款">回款</a>';
            }
            $links[] = '<a href="/' . $self->getId() . '/detail?detail_id=' . $row["detail_id"] . '" title="查看明细">明细</a>';
        }
    }

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

function getRate($row) {
    return ($row['rate'] * 100).'%';
}

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:2850px;", "table-bordered table-layout", "", true);