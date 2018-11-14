<?php

/**
 * Desc: 风控额度预警报表
 * User: wwb
 * Date: 2018/5/24 0022
 * Time: 17:10
 */

$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'b.name*', 'text' => '合作方名称'),
		array('type' => 'text', 'key' => 'd.project_code*', 'text' => '项目编号'),
		array('type' => 'text', 'key' => 'c.contract_code*', 'text' => '合同编号'),
    ),
    'buttonArray' => array(
        array('text' => '导出', 'buttonId' => 'exportButton')
    ),
);

//列表显示
$array = array(
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:center;', 'text' => '下游合作方名称', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
	array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '项目编号', 'href_text' => '<a id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1" target="_blank">{2}</a>'),
	array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '合同编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/contract/detail/?id={1}&t=1" target="_blank">{2}</a>'),
	array('key' => 'contract_amount', 'type' => 'amount_cny', 'style' => 'width:100px;text-align:right;', 'text' => '签约金额'),
	array('key' => 'delivery_amount', 'type' => 'amount_cny', 'style' => 'width:100px;text-align:right;', 'text' => '已提货货值'),
	array('key' => 'receive_amount', 'type' => 'amount_cny', 'style' => 'width:100px;text-align:right;', 'text' => '已收款金额'),
	array('key' => 'not_receive_amount', 'type' => 'amount_cny', 'style' => 'width:100px;text-align:right;', 'text' => '未收款金额'),
	array('key' => 'delivery_quantity', 'type' => 'href','style' => 'width:50px;text-align:center;', 'text' => '已提货数量','href_text'=>'getRowQuantity','params'=>'delivery_quantity'),
	array('key' => 'not_delivery_quantity', 'type' => 'href','style' => 'width:50px;text-align:center;', 'text' => '未提货数量','href_text'=>'getRowQuantity','params'=>'not_delivery_quantity'),
	array('key' => 'not_delivery_amount', 'type' => 'amount_cny', 'style' => 'width:100px;text-align:right;', 'text' => '未提货货值'),
	array('key' => 'invoice_quantity', 'type' => 'href','style' => 'width:50px;text-align:center;', 'text' => '已开票数量','href_text'=>'getRowQuantity','params'=>'invoice_quantity'),
	/*array('key' => 'invoice_max_overdue_time', 'type' => 'text', 'style' => 'width:120px;text-align:center;', 'text' => '最长超期开票时间'),*/
	array('key' => 'not_invoice_quantity_delivery', 'type' => 'href','style' => 'width:100px;text-align:center;', 'text' => '未开票数量（按提货）','href_text'=>'getRowQuantity','params'=>'not_invoice_quantity_delivery'),
	array('key' => 'not_invoice_amount_delivery', 'type' => 'amount_cny', 'style' => 'width:100px;text-align:right;', 'text' => '未开票金额（按提货）'),
	array('key' => 'not_invoice_quantity_contract', 'type' => 'href','style' => 'width:100px;text-align:center;', 'text' => '未开票数量（按合同）','href_text'=>'getRowQuantity','params'=>'not_invoice_quantity_contract'),
	array('key' => 'not_invoice_amount_contract', 'type' => 'amount_cny', 'style' => 'width:100px;text-align:right;', 'text' => '未开票金额（按合同）'),
	array('key' => 'settle_quantity', 'type' => 'href','style' => 'width:100px;text-align:center;', 'text' => '已结算数量','href_text'=>'getSettleQuantity','params'=>'settle_quantity'),
	array('key' => 'settle_amount', 'type' => 'href', 'style' => 'width:100px;text-align:right;', 'text' => '已结算人民币金额（货款）','href_text'=>'getSettleAmount','params'=>'settle_amount'),

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
function getSettleQuantity($row,$self,$key)
{
	if($row['contract_status']==Contract::STATUS_SETTLED&&$row[$key]>0)
		return $row[$key].'吨';
	else
		return '-';
}
function getSettleAmount($row,$self,$key)
{
	if($row['contract_status']==Contract::STATUS_SETTLED&&$row[$key]>0)
		return '￥'.number_format($row[$key]/100,2).'元';
	else
		return '-';
}

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_['data'], "", "min-width:1650px;", "table-bordered table-layout", "", true);
?>
<script>
	$(function () {
		$("#exportButton").click(function(){
			var formData= $(this).parents("form.search-form").serialize();
			location.href="/<?php echo $this->getId() ?>/export?"+formData;
		});
	});
</script>
