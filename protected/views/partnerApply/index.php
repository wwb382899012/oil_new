<?php
$form_array=array('form_url'=>'/partnerApply/',
    'input_array'=>array(
        array('type'=>'text','key'=>'a.name*','text'=>'企业名称'),
        array('type'=>'select','key'=>'a.status','map_name'=>'partner_status','text'=>'合作方状态'),
    ),
    'buttonArray'=>array(
        array('text'=>'添加','buttonId'=>'addButton'),
    ),
);
$array=array(
    array('key'=>'partner_id','type'=>'href','style'=>'width:90px;text-align:center;','text'=>'操作','href_text'=>'operation'),
    array('key'=>'partner_id','type'=>'','style'=>'width:80px;text-align:center','text'=>'企业编号'),
    array('key'=>'partner_id,name','type'=>'href','style'=>'text-align:left;','text'=>'企业名称','href_text'=>'<a id="t_{1}" title="{2}" href="/partnerApply/detail/?id={1}" >{2}</a>'),
    array('key'=>'status','type'=>'map_val','map_name'=>'partner_status','text'=>'状态','style'=>'width:110px;text-align:left'),
    array('key'=>'type','type'=>'','style'=>'width:100px;','text'=>'类别',),
    array('key'=>'corporate','type'=>'','text'=>'法人代表','style'=>'width:90px;text-align:center'),
    array('key'=>'registered_capital','type'=>'','text'=>'注册资本','style'=>'width:100px;text-align:right'),
    array('key'=>'start_date','type'=>'','text'=>'成立日期','style'=>'width:100px;text-align:center'),
    array('key'=>'ownership_name','type'=>'','text'=>'企业所有制','style'=>'text-align:left'),
    array('key'=>'runs_state','type'=>'map_val','map_name'=>'runs_state','text'=>'经营状态','style'=>'width:80px;text-align:center'),

);

function operation($row,$self){
    $html = '';
    if($row['status'] < PartnerApply::STATUS_SUBMIT) {
	    $html .= '<a href="/partnerApply/edit/?partner_id='.$row["partner_id"].'" title="修改合作方">修改</a>&nbsp;|&nbsp;';
    }else if($row['status']==PartnerApply::STATUS_PASS){
        $html .= '<a href="/partnerApply/edit/?partner_id='.$row["partner_id"].'" title="调整合作方额度">调整</a>&nbsp;|&nbsp;';
    }
	$html .= '<a href="/partnerApply/detail/?id=' . $row['partner_id'] . '" title="查看详情">详情</a>';
    return $html;
}

$this->loadForm($form_array,$_GET);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");
?>
<script>
    $(function(){
        $("#addButton").click(function(){
            location.href="/partnerApply/add/";
        });
    });
</script>
