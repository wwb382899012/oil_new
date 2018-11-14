<?php
/**
 * Desc: 保理对接列表
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'a.contract_code', 'text' => '保理对接流水号'),
        array('type' => 'text', 'key' => 'a.contract_code_fund', 'text' => '资金对接流水号'),
        array('type' => 'text', 'key' => 'a.apply_id', 'text' => '付款申请编号'),
        array('type' => 'text', 'key' => 'c.contract_code', 'text' => '采购合同编号'),
        array('type' => 'text', 'key' => 'g.contract_code', 'text' => '保理对接编号'),
        array('type' => 'text', 'key' => 'g.contract_code_fund', 'text' => '资金对接编号'),
        array('type' => 'text', 'key' => 'e.name*', 'text' => '交易主体'),
        array('type' => 'date', 'id' => 'contractPayStartTime', 'key'=>'a.pay_date>','text'=>'合同放款时间'),
        array('type' => 'date', 'id' => 'contractPayEndTime', 'key'=>'a.pay_date<','text'=>'到'),
        array('type' => 'text', 'key' => 'd.name*', 'text' => '上游合作方'),
        array('type' => 'date', 'id' => 'contractReturnStartTime', 'key'=>'a.return_date>','text'=>'合同回款时间'),
        array('type' => 'date', 'id' => 'contractReturnEndTime', 'key'=>'a.return_date<','text'=>'到'),
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'factor_detail_status', 'text' => '保理状态'),
        array('type'=>'datetime','id'=>'startTime','key'=>'a.create_time>','text'=>'申请开始时间'),
        array('type'=>'datetime','id'=>'endTime','key'=>'a.create_time<','text'=>'申请结束时间'),
        array('type' => 'text', 'key' => 'f.name*', 'text' => '申请人'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
    ),
    'buttonArray' => array(
        array('text' => '对接申请', 'buttonId' => 'applyButton'),
        array('text' => '导出', 'buttonId' => 'exportButton'),
    ),
);

//列表显示
$array = array(
    array('key' => 'detail_id', 'type' => 'href', 'style' => 'width:60px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'water_code', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '保理对接流水号'),
    array('key' => 'fund_water_code', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '资金对接流水号'),
    array('key' => 'apply_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '付款申请编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{1}" href="/pay/detail/?id={1}&t=1">{1}</a>'),
    array('key' => 'contract_id,c_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '采购合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/businessConfirm/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'contract_code', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '保理对接编号'),
    array('key' => 'contract_code_fund', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '资金对接编号'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'factor_detail_status', 'style' => 'width:80px;text-align:center', 'text' => '保理状态'),
    array('key' => 'pay_date', 'type' => '', 'style' => 'width:120px;text-align:center', 'text' => '合同放款时间'),
    array('key' => 'return_date', 'type' => '', 'style' => 'width:120px;text-align:center', 'text' => '合同回款时间'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '上游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'rate', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '年化利率', 'href_text' => 'getRate'),
    array('key' => 'pay_apply_amount', 'type' => 'href', 'style' => 'width:120px;text-align:right;', 'text' => '付款申请金额', 'href_text' => 'getPayApplyAmount'),
    array('key' => 'amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;', 'text' => '对接本金'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:60px;text-align:center;', 'text' => '申请人'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:120px;text-align:center;', 'text' => '申请时间'),
);

function getRowActions($row, $self) {
    $links = array();
    if (!empty($row['detail_id'])) {
        if($row['status'] < FactorDetail::STATUS_SUBMIT) {
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["detail_id"] . '" title="修改">修改</a>';}

        $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["detail_id"] . '" title="查看详情">详情</a>';
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
		$("#applyButton").click(function () {
			location.href="/<?php echo $this->getId() ?>/list/";
		});

		$("#exportButton").click(function(){
			var formData= $(this).parents("form.search-form").serialize();
			location.href="/<?php echo $this->getId() ?>/export?"+formData;
		});
	});
</script>
