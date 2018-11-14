<?php
/**
 * Desc: 合作方额度
 * User: susiehuang
 * Date: 2018/3/22 0022
 * Time: 17:10
 */
$partnerAmountTypeLabel = $partnerAmountInfo['type'] == PartnerAmount::TYPE_CONTRACT ? '当前合同额度' : '当前实际占用额度';
$partnerNameLink = '<a target="_blank" title="' . $partnerAmountInfo['partner_name'] . '" href="/partner/detail/?id=' . $partnerAmountInfo['partner_id'] . '&t=1">' . $partnerAmountInfo['partner_name'] . '</a>';
$form_array = array(
    'form_url' => '/' . $this->getId() . '/detail/',
    'input_array' => array(
        array('type' => 'info', 'text' => $partnerNameLink, 'label' => '合作方名称'),
        array('type' => 'info', 'text' => '￥' . number_format($partnerAmountInfo['used_amount'] / 1000000, 2) . '万元', 'label' => $partnerAmountTypeLabel),
        array('type' => 'info', 'text' => '￥' . number_format($partnerAmountInfo['init_amount'] / 1000000, 2) . '万元', 'label' => '初始化额度'),

        array('type' => 'text', 'key' => 'b.contract_code', 'text' => '合同编号'),
        array('type' => 'text', 'key' => 'c.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'rc.contract_code', 'text' => '关联合同编号'),
        array('type' => 'datetime', 'key' => 'a.create_time>', 'id' => 'start_create_time', 'text' => '时间'),
        array('type' => 'datetime', 'key' => 'a.create_time<', 'id' => 'end_create_time', 'text' => '到'),
        array('type' => 'hidden', 'key' => 'a.partner_id', 'value' => $partnerAmountInfo['partner_id']),
        array('type' => 'hidden', 'key' => 'a.type', 'value' => $partnerAmountInfo['type']),
    ),
    'buttonArray' => array(
        array('text' => '导出', 'buttonId' => 'exportButton'),
        array('text' => '返回', 'buttonId' => 'backButton', 'class'=>'btn btn-default btn-sm'),
    ),
);

//列表显示
$array = array(
    array('key' => 'create_time', 'type' => '', 'style' => 'width:160px;text-align:center;', 'text' => '时间'),
    array('key' => 'log_id', 'type' => 'href', 'style' => 'width:160px;text-align:right;', 'text' => '额度增减值', 'href_text' => 'getChangedAmount'),
    array('key' => 'remark', 'type' => '', 'style' => 'width:140px;text-align:center;', 'text' => '变动因素'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'width:60px;text-align:center;', 'text' => '合同ID', 'href_text' => '<a target="_blank" id="t_{1}" title="{1}" href="/contract/detail/?id={1}&t=1">{1}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'category', 'type' => 'map_val', 'map_name' => 'contract_category', 'style' => 'width:80px;text-align:center;', 'text' => '合同类型'),
    array('key' => 'contract_amount', 'type' => 'amount', 'style' => 'width:140px;text-align:right;', 'text' => '合同金额'),
    array('key' => 'goods_amount', 'type' => 'amount', 'style' => 'width:140px;text-align:right;', 'text' => '已付货款/已收货款（金额）'),
    array('key' => 'stock_amount', 'type' => 'amount', 'style' => 'width:140px;text-align:right;', 'text' => '入库单/出库单（金额）'),
    array('key' => 'goods_settle_amount', 'type' => 'amount', 'style' => 'width:140px;text-align:right;', 'text' => '合同已结算金额'),
    array('key' => 'goods_unsettled_amount', 'type' => 'amount', 'style' => 'width:140px;text-align:right;', 'text' => '合同未结算金额'),
    array('key' => 'invoice_amount', 'type' => 'amount', 'style' => 'width:140px;text-align:right;', 'text' => '已收票/已开票(金额）'),
    array('key' => 'relation_contract_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '关联合同ID', 'href_text' => 'getRelationContractLink'),
    array('key' => 'relation_contract_id,relation_contract_code', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '关联合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
);

function getChangedAmount($row)
{
    $html = '<span class="text-red glyphicon glyphicon-arrow-up" aria-hidden="true"></span>&emsp;';
    if ($row['method'] == PartnerAmountLog::METHOD_SUBTRACT) {
        $html = '<span class="text-red glyphicon glyphicon-arrow-down" aria-hidden="true"></span>&emsp;';
    }
    return $html . '￥' . Utility::numberFormatFen2Yuan($row['amount']);
}

function getRelationContractLink($row)
{
    $html = '';
    if (!empty($row['relation_contract_id'])) {
        $html = '<a target="_blank" id="t_' . $row['relation_contract_id'] . '" title="' . $row['relation_contract_id'] . '" href="/contract/detail/?id=' . $row['relation_contract_id'] . '&t=1">' . $row['relation_contract_id'] . '</a>';
    }
    return $html;
}

$this->loadForm($form_array, $data);
$this->show_table($array, $data['data'], "", "min-width:1650px;", "table-bordered table-layout", "", true);
?>
<script>
	$(function () {
		$("#exportButton").click(function(){
			var formData= $(this).parents("form.search-form").serialize();
			location.href="/<?php echo $this->getId() ?>/detailExport?"+formData;
		});
		$("#backButton").click(function(){
			location.href="/<?php echo $this->getId() ?>/";
		});
	});
</script>
