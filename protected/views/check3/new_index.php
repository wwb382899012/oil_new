<?php
$form_array = array(
	'form_url' => '/' . $this->getId() . '/',
	'items' => array(
        array('type' => 'text', 'key' => 'up.name*', 'text' => '上游合作方'),
        array('type' => 'text', 'key' => 'dp.name*', 'text' => '下游合作方'),
        array('type'=>'select','key'=>'checkStatus','noAll'=>'1','map_name'=>'transection_status','text'=>'审核状态'),
        array('type' => 'text', 'key' => 'c.project_code', 'text' => '项目编号'),
        array('type' => 'select', 'key' => 'c.type', 'map_name' => 'project_type', 'text' => '项目类型'),
        array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'u.name', 'text' => '项目负责人'),
        array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'b.create_time>','text'=>'申请时间'),
        array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'b.create_time<','text'=>'到'),
	)
);
$array = array(
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '项目编号', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'contract_status', 'type' => 'map_val', 'map_name' => 'contract_status', 'style' => 'width:100px;text-align:left', 'text' => '合同状态'),
    array('key' => 'project_type', 'type' => 'href', 'style' => 'width:100px;text-align:left', 'text' => '购销信息', 'href_text' => 'getBuySellType'),
    array('key' => 'up_partner_id,up_partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '上游合作方', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '交易主体', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'down_partner_id,down_partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '下游合作方', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'project_type', 'type' => 'map_val', 'map_name'=>'project_type', 'style' => 'width:100px;text-align:left', 'text' => '项目类型'),
    array('key' => 'name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '项目负责人'),
    array('key' => 'checkStatus', 'type' => 'map_val', 'map_name' => 'transection_status', 'style' => 'width:80px;text-align:left', 'text' => '审核状态'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '创建人'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:140px;text-align:left', 'text' => '申请时间'),
    array('key' => 'project_id', 'type' => 'href', 'style' => 'width:80px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowEditAction'),
);

function getRowEditAction($row, $self) {
	// 状态待确认
	if($row['contract_status'] == Contract::STATUS_SUBMIT) {
	} else {
	}
	if($row['checkStatus'] == 1) {
		$html = '<a href="/' . $self->getId() . '/check?id=' . $row["obj_id"] . '" title="审核">审核</a>';
	} else {
		$html = '<a href="/' . $self->getId() . '/detail?detail_id=' . $row["detail_id"] . '" title="查看">查看</a>';
	}
	return $html;
}

function getBuySellType($row, $self) {
    $contractModel=Contract::model();
	if($row['is_main']==1){
	    $buy_sell_desc = $self->map['buy_sell_desc_type'][$row['is_main']];
	}else{
        if($contractModel->isSplit($row['split_type'],$row['original_id'])){
            $buy_sell_desc = '平移新合同';
        }else{
            $buy_sell_desc = $self->map['buy_sell_desc_type'][$row['is_main']][$row['contract_type']].$row['num'];
        }
	}
	return $buy_sell_desc;
}


$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);

