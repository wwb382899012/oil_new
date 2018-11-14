<?php
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'input_array' => array(
       array('type' => 'text', 'key' => 'e.contract_code', 'text' => '销售合同编号'),
       array('type' => 'text', 'key' => 'f.project_code', 'text' => '项目编号'),
       array('type' => 'text', 'key' => 'g.name*', 'text' => '交易主体'),
       array('type' => 'text', 'key' => 'c.name*', 'text' => '下游合作方'),
       array('type' => 'text', 'key' => 'b.name*', 'text' => '合同负责人'),
       array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
       array('type' => 'select', 'noAll'=>'1', 'map_name'=>'delivery_order_check_status', 'key' => 'checkStatus', 'text' => '审核状态'),
	   array('type' => 'date', 'id'=>'settleStartTime','key' => 's.settle_date>', 'text' => '结算日期'),
	   array('type' => 'date','id'=>'settleEndTime', 'key' => 's.settle_date<', 'text' => '到'),
   )
);

//列表显示
$array = array(
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'width:60px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '销售合同编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/contract/detail/?id={1}&t=1" target="_blank">{2}</a>'),
	array('key' => 'settle_date', 'type' => 'text', 'style' => 'width:80px;text-align:center', 'text' => '结算日期'),
	array('key' => 'code_out', 'type' => 'text', 'style' => 'width:120px;text-align:center', 'text' => '外部合同编号'),
	array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '项目编号', 'href_text' => '<a id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1" target="_blank">{2}</a>'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1" target="_blank">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '下游合作方', 'href_text' => '<a id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1" target="_blank">{2}</a>'),
	array('key' => 'manager_user_name', 'type' => 'text',  'style' => 'width:60px;text-align:center', 'text' => '合同负责人'),
    array('key' => 'category', 'type' => 'map_val', 'map_name' => 'contract_category_sell_type', 'style' => 'width:80px;text-align:center', 'text' => '合同类型'),
    array('key' => 'checkStatus', 'type' => 'map_val', 'map_name' => 'stock_batch_settle_check_status', 'style' => 'width:60px;text-align:center', 'text' => '审核状态'),

    );

function getRowActions($row, $self) {
	// 状态待确认
	if($row['contract_status'] == StockBatchSettlement::STATUS_SUBMIT) {
	} else {
	}
	if($row['checkStatus'] == 1) {
		$html = '<a href="/check22/check?id=' . $row["obj_id"] . '" title="审核">审核</a>';
	} else {
		$html = '<a href="/check22/detail?detail_id=' . $row["detail_id"] . '" title="查看">查看</a>';
	}
	return $html;
}

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");