<?php

/**
 * Desc: 库存报表
 * User: wwb
 * Date: 2018/7/2 0022
 * Time: 17:10
 */


$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'b.name*', 'text' => '交易主体'),
		array('type' => 'text', 'key' => 'e.project_code*', 'text' => '项目编号'),
		array('type' => 'text', 'key' => 'd.contract_code*', 'text' => '采购合同编号'),
		array('type' => 'text', 'key' => 'c.name*', 'text' => '品名'),
    ),

);

//列表显示
$array = array(

    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:260px;text-align:left;', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
	array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:160px;text-align:left;', 'text' => '项目编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1">{2}</a>'),
	array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:160px;text-align:left;', 'text' => '采购合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
	array('key' => 'goods_name', 'type' => 'text', 'style' => 'width:100px;text-align:left;', 'text' => '品名'),
	array('key' => 'on_way_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '在途货物（吨）','href_text'=>'getRowQuantity','params'=>'on_way_quantity'),
	array('key' => 'stock_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '在库库存（吨）','href_text'=>'getRowQuantity','params'=>'stock_quantity'),
	array('key' => 'not_lading_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '已付未提数量（吨）','href_text'=>'getRowQuantity','params'=>'not_lading_quantity'),
	array('key' => 'unexecuted_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '采购未执行数量（吨）','href_text'=>'getRowQuantity','params'=>'unexecuted_quantity'),
	array('key' => 'reserve_quantity', 'type' => 'href','style' => 'width:100px;text-align:left;', 'text' => '代储货物数量（吨）','href_text'=>'getReserveQuantity','params'=>'reserve_quantity'),
);
function getRowQuantity($row,$self,$key)
{
	return $row[$key];
}
function getReserveQuantity($row,$self,$key)
{
	return '-';
}



$headerArray = ['is_show_export' => true];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray  = ['column_config' => $array,'float_columns'=>0];
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
