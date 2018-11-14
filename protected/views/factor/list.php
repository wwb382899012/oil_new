<?php
/**
 * Desc: 保理对接列表
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/list',
    'input_array' => array(
        array('type' => 'text', 'key' => 'a.apply_id', 'text' => '付款申请编号'),
        array('type' => 'text', 'key' => 'p.project_code', 'text' => '项目编号'),
        array('type' => 'text', 'key' => 'c.contract_code', 'text' => '采购合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'a.contract_code', 'text' => '保理对接编号'),
        array('type' => 'text', 'key' => 'a.contract_code_fund', 'text' => '资金对接编号'),
        array('type' => 'text', 'key' => 'e.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'd.name*', 'text' => '合作方')
    ),
    'buttonArray' => array(
        array('text' => '返回', 'buttonId' => 'backButton'),
    ),
);

//列表显示
$array = array(
    array('key' => 'factor_id', 'type' => 'href', 'style' => 'width:60px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'apply_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '付款申请编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{1}" href="/pay/detail/?id={1}&t=1">{1}</a>'),
    array('key' => 'contract_code', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '保理对接编号'),
    array('key' => 'contract_code_fund', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '资金对接编号'),
    array('key' => 'pay_apply_amount', 'type' => 'href', 'style' => 'width:120px;text-align:right;', 'text' => '付款申请金额', 'href_text' => 'getPayApplyAmount'),
    array('key' => 'amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;', 'text' => '实际保理对接金额'),
    array('key' => 'rate', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '年化利率', 'href_text' => 'getRate'),
    array('key' => 'checking_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;', 'text' => '保理对接审核中金额'),
    array('key' => 'butted_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;', 'text' => '已对接金额'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '项目编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'project_type', 'style' => 'width:80px;text-align:center', 'text' => '业务类型'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,c_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '采购合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
);

function getRowActions($row, $self) {
    $links = array();
    if (!empty($row['factor_id']) && FactoringDetailService::checkIsCanAdd($row['factor_id'])) {
        $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["factor_id"] . '" title="申请">申请</a>';
    }

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

function getPayApplyAmount($row) {
    return Map::$v['currency'][$row['currency']]['ico'] . Utility::numberFormatFen2Yuan($row['pay_apply_amount']);
}

function getRate($row) {
    return ($row['rate'] * 100).'%';
}

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1850px;", "table-bordered table-layout", "", true);
?>
<script>
	$(function () {
		$("#backButton").click(function(){
			location.href="/<?php echo $this->getId() ?>/";
		});
	});
</script>
