<?php
/**
 * Desc:
 * User: susieh
 * Date: 17/3/27
 * Time: 15:58
 */
$form_array = array(
	'form_url' => '/partnerWhite/',
	'input_array' => array(
		array('key' => 'a.name*', 'type' => 'text', 'text' => '企业名称'),
		array('key' => 'a.status', 'type' => 'select', 'map_name' => 'partner_white_status', 'text' => '状态'),
	),
	'buttonArray' => array(
		array('text' => '添加', 'buttonId' => 'addButton'),
	)
);

$table_array = array(
	array('key' => 'id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '操作', 'href_text' => 'rowOperation'),
	array('key' => 'name', 'type' => '', 'style' => 'width:200px;text-align:left;', 'text' => '企业名称'),
	array('key' => 'corporate', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '法人代表'),
	array('key' => 'registered_capital', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '注册资本'),
	array('key' => 'start_date', 'type' => 'date', 'style' => 'width:100px;text-align:center;', 'text' => '成立日期'),
	array('key' => 'ownership_name', 'type' => '', 'style' => 'width:200px;text-align:center;', 'text' => '企业所有制'),
	array('key' => 'level', 'type' => 'map_val', 'style' => 'width:80px;text-align:center;', 'map_name' => 'partner_level', 'text' => '企业分级'),
	array('key' => 'status', 'type' => 'map_val', 'style' => 'width:80px;text-align:center;', 'map_name' => 'partner_white_status', 'text' => '状态'),
);

function rowOperation($row) {
	return '<a href="/partnerWhite/edit/?id=' . $row['id'] . '" title="修改">修改</a>&nbsp;|&nbsp;
	<a href="/partnerWhite/detail/?id=' . $row['id'] . '" title="详情">详情</a>';
}

$this->loadForm($form_array, $_GET);
$this->show_table($table_array, $_data_['data'], "", "min-width:900px;","table-bordered table-layout");
?>

<script>
	$(function () {
		$("#addButton").click(function () {
			location.href = "/partnerWhite/add/";
		});
	});
</script>
