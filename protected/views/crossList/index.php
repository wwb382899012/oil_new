<?php
/**
 * Created by vector.
 * DateTime: 2017/10/17 18:16
 * Describe：
 */
function checkRowEditAction($row, $self) {
    return '<a href="/' . $self->getId() . '/detail?id=' . $row["cross_id"] . '" title="查看详情">详情</a>';
}


//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'o.cross_code*', 'text' => '调货单编号'),
        array('type' => 'text', 'key' => 'c.contract_code*', 'text' => '销售合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'pa.name*', 'text' => '下游合作方'),
        array('type'=>'select','key'=>'o.status','map_name'=>'cross_list_status','text'=>'状态&emsp;&emsp;&emsp;&emsp;'),
        array('type' => 'text', 'key' => 'g.name*', 'text' => '品名&emsp;&emsp;&emsp;&emsp;'),        
    )
);

//列表显示
$array = array(
    array('key' => 'cross_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
    array('key' => 'cross_code', 'type' => '', 'style' => 'width:220px;text-align:left', 'text' => '调货单编号',),
    array('key' => 'sell_id,sell_code', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '销售合同编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/businessConfirm/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'goods_name', 'type' => '', 'style' => 'width:120px;text-align:left', 'text' => '品名'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'text-align:left', 'text' => '下游合作方', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'status', 'type' => 'map_val', 'style' => 'width:120px;text-align:left', 'text' => '状态','map_name'=>'cross_list_status',),
);


$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");
?>
