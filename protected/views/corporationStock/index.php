<?php

/**
 * Desc: 库存报表
 * User: wwb
 * Date: 2018/7/2 0022
 * Time: 17:10
 */

$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'b.name*', 'text' => '交易主体'),
		array('type' => 'text', 'key' => 'c.name*', 'text' => '品名'),
    ),
    'buttonArray' => array(
        array('text' => '导出', 'buttonId' => 'exportButton')
    ),
);
function getRowEditAction($row,$self)
{
	$links=array();
	$links[]='<a href="/contractStock?name='.$row["corporation_name"].'&goods_id='.$row['goods_id'].'" title="查看详情">查看明细</a>';
	$s=implode("&nbsp;|&nbsp;",$links);
	return $s;
}
//列表显示
$array = array(
	array('key' => 'corporation_id', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowEditAction'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:200px;text-align:center;', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
	array('key' => 'goods_name', 'type' => 'text', 'style' => 'width:200px;text-align:center;', 'text' => '品名'),
	array('key' => 'on_way_quantity', 'type' => 'href','style' => 'width:100px;text-align:center;', 'text' => '在途货物','href_text'=>'getRowQuantity','params'=>'on_way_quantity'),
	array('key' => 'stock_quantity', 'type' => 'href','style' => 'width:100px;text-align:center;', 'text' => '在库库存','href_text'=>'getRowQuantity','params'=>'stock_quantity'),
	array('key' => 'not_lading_quantity', 'type' => 'href','style' => 'width:100px;text-align:center;', 'text' => '已付未提数量','href_text'=>'getRowQuantity','params'=>'not_lading_quantity'),
	array('key' => 'unexecuted_quantity', 'type' => 'href','style' => 'width:100px;text-align:center;', 'text' => '采购未执行数量','href_text'=>'getRowQuantity','params'=>'unexecuted_quantity'),
	array('key' => 'reserve_quantity', 'type' => 'href','style' => 'width:100px;text-align:center;', 'text' => '代储货物数量','href_text'=>'getReserveQuantity','params'=>'reserve_quantity'),
);
function getRowQuantity($row,$self,$key)
{
	return $row[$key].'吨';
}
function getReserveQuantity($row,$self,$key)
{
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
