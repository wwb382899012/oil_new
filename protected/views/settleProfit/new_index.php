<?php

/**
 * Desc: 结算利润报表
 * User: wwb
 * Date: 2018/7/2 0022
 * Time: 17:10
 */


$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'b.name*', 'text' => '交易主体'),
		array('type' => 'text', 'key' => 'c.project_code*', 'text' => '项目编号'),
		array('type' => 'select', 'map_name'=>'project_type', 'key' => 'c.type', 'text' => '项目类型'),
		array('type' => 'text', 'key' => 'd.name*', 'text' => '业务员'),
    ),

);

//列表显示
$array = array(

    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:260px;text-align:left;', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
	array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:160px;text-align:left;', 'text' => '项目编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1">{2}</a>'),
	array('key' => 'type', 'type' => 'map_val', 'map_name' => 'project_type', 'style' => 'width:80px;text-align:left', 'text' => '项目类型'),
	array('key' => 'manager_user_name', 'type' => 'text', 'style' => 'width:100px;text-align:left;', 'text' => '业务员'),

	array('key' => 'settle_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '结算销售数量（吨）','href_text'=>'getRowQuantity','params'=>'settle_quantity'),
	array('key' => 'settle_amount', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '结算销售金额（元）'),
	array('key' => 'sell_invoice_amount', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '已开票金额（元）'),
	array('key' => 'buy_price', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '实际采购单价（元）'),
	array('key' => 'buy_amount', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '实际采购金额（元）'),
	array('key' => 'buy_invoice_amount', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '已收票金额（元）'),
	array('key' => 'pay_amount', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '已付上游款（元）'),
	array('key' => 'receive_amount', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '已收下游款（元）'),
	array('key' => 'actual_gross_profit', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '实际毛利（元）'),

	array('key' => 'freight', 'type' => 'href','style' => 'width:140px;text-align:right;', 'text' => '运费（元）','href_text'=>'getNotsure','params'=>'freight'),
	array('key' => 'warehouse_fee', 'type' => 'href','style' => 'width:140px;text-align:right;', 'text' => '仓储费（元）','href_text'=>'getNotsure','params'=>'warehouse_fee'),

	array('key' => 'miscellaneous_fee', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '杂费（元）'),
	array('key' => 'vat', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '增值税（元）'),
	array('key' => 'sur_tax', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '附加税（元）'),
	array('key' => 'stamp_tax', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '印花税（元）'),
	array('key' => 'after_tax_profit', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '税后毛利（元）'),
	array('key' => 'fund_cost', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '资金成本（元）'),
	array('key' => 'profit', 'type' => 'amount_number', 'style' => 'width:140px;text-align:right;', 'text' => '业务净利润（元）'),

);
function getRowQuantity($row,$self,$key)
{
	return $row[$key];
}
function getNotsure($row,$self,$key)
{
	return '-';  //保留字段
}



$headerArray = ['is_show_export' => true];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray  = ['column_config' => $array,'float_columns'=>0];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>
<script>
	$(function () {
		$("#exportButton").click(function(){
			var formData= $(this).parents("form.search-form").serialize();
			location.href="/<?php echo $this->getId() ?>/export?"+formData;
		});
	});
</script>
