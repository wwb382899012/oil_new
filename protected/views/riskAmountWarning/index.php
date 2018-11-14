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
		array('type' => 'date', 'id'=>'join_time_start','key' => 'a.join_time>', 'text' => '合作日期'),
		array('type' => 'date','id'=>'join_time_end', 'key' => 'a.join_time<', 'text' => '到'),
    ),
    'buttonArray' => array(
        array('text' => '导出', 'buttonId' => 'exportButton')
    ),
);

//列表显示
$array = array(
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:center;', 'text' => '合作方名称', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
	array('key' => 'status', 'type' => 'map_val','map_name'=>'risk_amount_warning_status', 'style' => 'width:80px;text-align:center;', 'text' => '状态'),
	array('key' => 'join_time', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '合作日期','href_text'=>'getRowDate'),
    array('key' => 'level', 'type' => 'map_val', 'map_name'=>'partner_level','style' => 'width:50px;text-align:center;', 'text' => '评级'),
    array('key' => 'credit_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '初始额度'),
    array('key' => 'change_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '变动额度'),
    array('key' => 'change_reason', 'type' => 'text', 'style' => 'width:120px;text-align:center;', 'text' => '变动原因'),
    array('key' => 'credit_total_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '信用总额度'),
    array('key' => 'actual_used_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '额度占用'),
    array('key' => 'available_amount', 'type' => 'amountWan', 'style' => 'width:100px;text-align:right;', 'text' => '可用额度'),
    array('key' => 'over_nums', 'type' => 'text', 'style' => 'width:100px;text-align:center;', 'text' => '历史逾期次数'),
    array('key' => 'max_over_days', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '最长逾期天数','href_text'=>'getRowStatus'),

);

function getRowStatus($row)
{
    return $row['max_over_days'].'天';
}
function getRowDate($row)
{
	return date("Y/m/d",strtotime($row['join_time']));
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
