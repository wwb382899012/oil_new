<?php
$form_array = array(
	'form_url' => '/businessAssistant/',
	'input_array' => array(
		array('type' => 'text', 'key' => 'a.name*', 'text' => '姓名'),
	)
);
$array = array(
	array('key' => 'main_user_id', 'type' => 'href', 'style' => 'width:10%;text-align:center;', 'text' => '操作', 'href_text' => 'getRowEditAction'),
	array('key' => 'main_user_id', 'type' => '', 'style' => 'width:5%;text-align:center', 'text' => '序号'),
	array('key' => 'main_name', 'type' => '', 'style' => 'width:10%;text-align:center', 'text' => '姓名'),
	array('key' => 'code', 'type' => '', 'style' => 'width:10%;text-align:center', 'text' => '编码'),
	array('key' => 'sex', 'type' => 'map_val', 'map_name' => 'gender', 'style' => 'width:5%;text-align:center', 'text' => '性别'),
	array('key' => 'id_code', 'type' => '', 'style' => 'width:20%;text-align:center', 'text' => '身份证号码'),
	array('key' => 'phone', 'type' => '', 'style' => 'width:15%;text-align:center', 'text' => '手机号码'),
	array('key' => 'email', 'type' => '', 'style' => 'width:20%;text-align:center', 'text' => '邮箱'),
	array('key' => 'status', 'type' => 'map_val', 'map_name' => 'user_status', 'style' => 'width:5%;text-align:center', 'text' => '状态'),
);

function getRowEditAction($row, $self) {
	$html = '<a href="/businessAssistant/edit?user_id=' . $row["main_user_id"] . '" title="修改">修改</a>&nbsp;|&nbsp;
			<a href="/businessAssistant/detail/?user_id=' . $row["main_user_id"] . '" title="详情">详情</a>';

	return $html;
}


$this->loadForm($form_array, $_data_);
$this->show_table($array,$_data_['data'],"","min-width:1050px;","table-bordered table-layout");

