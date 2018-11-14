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
	/*	array('type' => 'date', 'id'=>'join_time_start','key' => 'a.join_time>', 'text' => '合作日期'),
		array('type' => 'date','id'=>'join_time_end', 'key' => 'a.join_time<', 'text' => '到'),*/
    ),

);
function getRowEditAction($row,$self)
{
	$links=array();
	$links[]='<a href="/partnerSellContractDetail?name='.$row["partner_name"].'" title="查看详情">查看明细</a>';
	$s=implode("&nbsp;|&nbsp;",$links);
	return $s;
}
//列表显示
$array = array(

    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left;', 'text' => '下游合作方名称', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
	/*array('key' => 'join_time', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '合作日期','href_text'=>'getRowDate'),*/
	array('key' => 'contract_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '签约金额'),
	array('key' => 'delivery_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '已提货货值'),
	array('key' => 'receive_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '已收款金额'),
	array('key' => 'not_receive_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '未收款金额'),
	array('key' => 'delivery_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '已提货数量','href_text'=>'getRowQuantity','params'=>'delivery_quantity'),
	array('key' => 'not_delivery_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '未提货数量','href_text'=>'getRowQuantity','params'=>'not_delivery_quantity'),
	array('key' => 'not_delivery_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '未提货货值'),
	array('key' => 'invoice_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '已开票数量','href_text'=>'getRowQuantity','params'=>'invoice_quantity'),
	/*array('key' => 'invoice_max_overdue_time', 'type' => 'text', 'style' => 'width:120px;text-align:center;', 'text' => '最长超期开票时间'),*/
	array('key' => 'not_invoice_quantity_delivery', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '未开票数量（按提货）','href_text'=>'getRowQuantity','params'=>'not_invoice_quantity_delivery'),
	array('key' => 'not_invoice_amount_delivery', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '未开票金额（按提货）'),
	array('key' => 'not_invoice_quantity_contract', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '未开票数量（按合同）','href_text'=>'getRowQuantity','params'=>'not_invoice_quantity_contract'),
	array('key' => 'not_invoice_amount_contract', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '未开票金额（按合同）'),
	array('key' => 'settle_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '已结算数量','href_text'=>'getRowQuantity','params'=>'settle_quantity'),
	array('key' => 'settle_amount', 'type' => 'amount_cny', 'style' => 'width:140px;text-align:right;', 'text' => '已结算人民币金额（货款）'),
	array('key' => 'partner_id', 'type' => 'href', 'style' => 'width:120px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowEditAction'),

);

function getRowQuantity($row,$self,$key)
{
	return $row[$key].'吨';
}
function getRowStatus($row)
{
    return $row['max_over_days'].'天';
}
function getRowDate($row)
{
	return date("Y/m/d",strtotime($row['join_time']));
}



$headerArray = ['is_show_export' => true];
$searchArray = ['search_config' => $form_array,'search_lines' => 1];
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
