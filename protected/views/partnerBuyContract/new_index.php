<?php

/**
 * Desc: 风控额度预警报表
 * User: wwb
 * Date: 2018/5/24 0022
 * Time: 17:10
 */



$form_array = array(
	'form_url' => '/' . $this->getId() . '/',
	'items' => array(
		array('type' => 'text', 'key' => 'b.name*', 'text' => '合作方名称'),
	)
);

function getRowEditAction($row,$self)
{
	$links=array();
	$links[]='<a href="/partnerBuyContractDetail?name='.$row["partner_name"].'" title="查看详情">查看明细</a>';
	$s=implode("&nbsp;|&nbsp;",$links);
	return $s;
}

//列表显示
$array = array(

	array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left;', 'text' => '上游合作方名称', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
	/*array('key' => 'join_time', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '合作日期','href_text'=>'getRowDate'),*/
	array('key' => 'overdue_received_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '逾期交货数量','href_text'=>'getRowQuantity','params'=>'overdue_received_quantity'),
	array('key' => 'ontime_received_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '准时交货数量','href_text'=>'getRowQuantity','params'=>'ontime_received_quantity'),
	array('key' => 'received_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '已交货数量','href_text'=>'getRowQuantity','params'=>'received_quantity'),
	array('key' => 'not_received_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '未交货数量','href_text'=>'getRowQuantity','params'=>'not_received_quantity'),
	array('key' => 'not_received_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '未交货货值'),
	array('key' => 'contract_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '签约金额'),
	array('key' => 'received_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '已交货货值'),
	array('key' => 'pay_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '已付款金额'),
	array('key' => 'diff_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '敞口'),
	array('key' => 'invoice_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '已收票数量','href_text'=>'getRowQuantity','params'=>'invoice_quantity'),
	array('key' => 'invoice_max_overdue_time', 'type' => 'text', 'style' => 'width:100px;text-align:left;', 'text' => '最长超期收票时间'),
	array('key' => 'not_invoice_quantity_delivery', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '未收票数量（按交货）','href_text'=>'getRowQuantity','params'=>'not_invoice_quantity_delivery'),
	array('key' => 'not_invoice_amount_delivery', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '未收票金额（按交货）'),
	array('key' => 'not_invoice_quantity_contract', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '未收票数量（按合同）','href_text'=>'getRowQuantity','params'=>'not_invoice_quantity_contract'),
	array('key' => 'not_invoice_amount_contract', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '未收票金额（按合同）'),
	array('key' => 'settle_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '已结算数量','href_text'=>'getRowQuantity','params'=>'settle_quantity'),
	array('key' => 'settle_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '已结算人民币金额（货款）'),
	array('key' => 'partner_id', 'type' => 'href', 'style' => 'width:120px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowEditAction'),


);

function getRowQuantity($row,$self,$key)
{
	return $row[$key].'吨';
}

function getRowDate($row)
{
	return date("Y/m/d",strtotime($row['join_time']));
}



$headerArray = ['is_show_export' => true];
$searchArray = ['search_config' => $form_array, 'search_lines' => 1];
$tableArray  = ['column_config' => $array];
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
