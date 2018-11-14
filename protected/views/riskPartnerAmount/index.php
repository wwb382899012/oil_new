<?php
/**
 * Desc: 合作方额度
 * User: susiehuang
 * Date: 2018/3/22 0022
 * Time: 17:10
 */

$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'p.name*', 'text' => '合作方名称')
    ),
    'buttonArray' => array(
        array('text' => '导出', 'buttonId' => 'exportButton')
    ),
);

//列表显示
$array = array(
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:center;', 'text' => '合作方名称', 'href_text' => '<a target="_blank" id="t_{1}" title="{1}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id', 'type' => 'href', 'style' => 'width:100px;text-align:right;', 'text' => '合同额度', 'href_text' => 'getContractAmountLink'),
    array('key' => 'partner_id', 'type' => 'href', 'style' => 'width:100px;text-align:right;', 'text' => '实际占用额度', 'href_text' => 'getUsedAmountLink'),
    array('key' => 'p_contract_used_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '采购合同总金额'),
    array('key' => 's_contract_used_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '销售合同总金额'),
    array('key' => 'goods_in_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '入库单金额'),
    array('key' => 'goods_in_settle_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '入库单已结算金额'),
    array('key' => 'goods_in_unsettled_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '入库单未结算金额'),
    array('key' => 'goods_out_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '出库单金额'),
    array('key' => 'goods_out_settle_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '出库单已结算金额'),
    array('key' => 'goods_out_unsettled_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '出库单未结算金额'),
    array('key' => 'amount_out', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '已付货款金额'),
    array('key' => 'amount_in', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '已收货款金额'),
    array('key' => 'amount_invoice_in', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '已收票金额'),
    array('key' => 'amount_invoice_out', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '已开票金额')
);

function getContractAmountLink($row)
{
    return '<a id="t_' . $row['partner_id'] . '" title="￥' . $row['contract_used_amount']/1000000 . '万元" href="/riskPartnerAmount/detail/?search[a.partner_id]=' . $row['partner_id'] . '&search[a.type]=1">￥' . number_format($row['contract_used_amount']/1000000, 2) . '万元</a>';
}

function getUsedAmountLink($row)
{
    return '<a id="t_' . $row['partner_id'] . '" title="￥' . $row['actual_used_amount']/1000000 . '万元" href="/riskPartnerAmount/detail/?search[a.partner_id]=' . $row['partner_id'] . '&search[a.type]=2">￥' . number_format($row['actual_used_amount']/1000000, 2) . '万元</a>';
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
