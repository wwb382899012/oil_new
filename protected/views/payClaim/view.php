<?php
//查询区域
//列表显示
$array = array(
    array('key' => 'claim_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
    array('key' => 'claim_id', 'type' => 'text', 'style' => 'width:80px;text-align:center', 'text' => '认领编号'),
    array('key' => 'contract_type', 'type' => 'map_val', 'style' => 'width:80px;text-align:center', 'text' => '货款合同类型', 'map_name' => 'buy_sell_type'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '货款合同编号', 'href_text' => '<a target="_blank" title="{2}" href="/contract/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'sub_contract_type', 'type' => 'map_val', 'style' => 'width:80px;text-align:center', 'text' => '付款合同类型', 'map_name' => 'contract_category'),
    array('key' => 'sub_contract_code', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '付款合同编号'),
    array('key' => 'amount', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '认领金额', 'href_text' => 'getRowAmount'),
    array('key' => 'status_time', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '认领时间'),
);

function getRowAmount($row, $self) {
    return $self->map['currency'][$row['currency']]['ico'] . Utility::numberFormatFen2Yuan($row["amount"]);
}


function checkRowEditAction($row) {
    $s = '<a href="/payClaim/detail?id=' . $row["claim_id"] . '" title="查看">查看</a>';
    return $s;
}
$this->show_table_nopage($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout scrolltable");

?>
<button type="button" class="btn btn-default pull-right" onclick="back()">返回</button>

<script>
	function back() {
		location.href = '/<?php echo $this->getId() ?>';
    }
</script>