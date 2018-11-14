<?php
/**
 * Desc: 保理编号管理
 * User: susiehuang
 * Date: 2017/12/19 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'b.contract_code', 'text' => '保理对接编号'),
        array('type' => 'text', 'key' => 'a.code', 'text' => '资金对接编号'),
        array('type' => 'text', 'key' => 'b.apply_id', 'text' => '付款申请编号'),
        array('type' => 'select', 'key' => 'a.type', 'map_name' => 'factor_code_type', 'text' => '保理类型'),
        array('type' => 'datetime', 'id' => 'startTime', 'key' => 'a.create_time>', 'text' => '取号时间'),
        array('type' => 'datetime', 'id' => 'endTime', 'key' => 'a.create_time<', 'text' => '到')
    ),
    'buttonArray' => array(
        array('text' => '取号', 'buttonId' => 'getCodeButton'),
        array('text' => '导出', 'buttonId' => 'exportButton'),
    )
);

//列表显示
$array = array(
    array('key' => 'contract_code', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '保理对接编号'),
    array('key' => 'code', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '资金对接编号'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'factor_code_type', 'style' => 'width:120px;text-align:center', 'text' => '保理类型'),
    array('key' => 'apply_id', 'type' => 'href', 'style' => 'width:160px;text-align:center', 'text' => '付款申请编号', 'href_text'=>'<a title="{1}" target="_blank" href="/pay/detail/?id={1}&t=1">{1}</a>'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:200px;text-align:center', 'text' => '取号时间'),
    array('key' => 'remark', 'type' => '', 'style' => 'width:280px;text-align:center', 'text' => '备注')
);
$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout data-table", "", true);
?>
<script>
	$(function () {
		$("#exportButton").click(function(){
			var formData= $(this).parents("form.search-form").serialize();
			location.href="/<?php echo $this->getId() ?>/export?"+formData;
		});

		$("#getCodeButton").click(function () {
			location.href="/<?php echo $this->getId() ?>/add/";
		})
	});
</script>
