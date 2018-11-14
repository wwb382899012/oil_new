<?php
/**
 * Created by youyi000.
 * DateTime: 2017/3/28 14:59
 * Describe：
 */
function checkRowEditAction($row,$self)
{
    $links=array();
    if($row["isCanCheck"])
    {
        $links[]='<a href="/'.$self->getId().'/check?id='.$row["detail_id"].'" title="审核">审核</a>';
    }
    else{
        $links[]='<a href="/'.$self->getId().'/detail?id='.$row["detail_id"].'" title="查看详情">详情</a>';
    }
    $s=implode("&nbsp;|&nbsp;",$links);
    return $s;
}

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'o.cross_code*', 'text' => '调货单编号'),
        array('type' => 'text', 'key' => 'c.contract_code*', 'text' => '销售合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'pa.name*', 'text' => '下游合作方'),
        array('type' => 'text', 'key' => 'p.project_code*', 'text' => '项目编号&emsp;'),
        array('type' => 'text', 'key' => 'g.name*', 'text' => '品名&emsp;&emsp;&emsp;&emsp;'),
        array('type'=>'select','key'=>'checkStatus','noAll'=>'1','map_name'=>'contract_check_status','text'=>'审核状态&emsp;'),
    )
);

//列表显示
$array = array(
    array('key' => 'detail_id', 'type' => 'href', 'style' => 'width:60px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
    array('key' => 'detail_id', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '审核编号'),
    array('key' => 'cross_code', 'type' => '', 'style' => 'width:180px;text-align:center', 'text' => '调货单编号'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:160px;text-align:center', 'text' => '销售合同编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/businessConfirm/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'text-align:left', 'text' => '下游合作方', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'goods_name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '品名'),
    array('key'=>'checkStatus','type'=>'map_val','style'=>'width:80px;text-align:center;','text'=>'审核状态','map_name'=>'cross_order_check_status'),
);

$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");


?>