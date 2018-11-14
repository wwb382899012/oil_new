<?php

function checkRowEditAction($row,$self)
{
    if($self->checkIsCanEdit($row['partner_status']))
        $s .= '<a href="/partnerAmount/edit/?id='.$row["partner_id"].'" title="调整额度">调整</a>';
    
    $p = Project::model()->find("down_partner_id=".$row["partner_id"]." and status>10");
    if($row['partner_status']==PartnerApply::STATUS_PASS && !empty($p->project_id) && ($roleId==$busId || $roleId==$riskId))
        $s .= '&nbsp;|&nbsp;';
    if(!empty($p->project_id))
        $s .= '<a href="/partnerAmount/detail/?id='.$row["partner_id"].'" title="查看详情">详情</a>';

    return $s;
}

$form_array=array('form_url'=>'/partnerAmount/',
    'input_array'=>array(
        array('type'=>'text','key'=>'b.name','text'=>'企业名称'),
        array('type'=>'text','key'=>'a.corporation','text'=>'法人代表'),
        // array('type'=>'select','key'=>'a.status','map_name'=>'partner_status','text'=>'合作方状态'),
        // array('type'=>'select','key'=>'b.type','map_name'=>'partner_type','text'=>'类别&emsp;&emsp;'),        
    ),
);
$array=array(
    array('key'=>'partner_id','type'=>'href','style'=>'width:80px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'partner_id','type'=>'','style'=>'width:80px;text-align:center','text'=>'企业编号'),
    array('key'=>'partner_id,name','type'=>'href','style'=>'text-align:left;','text'=>'企业名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'type','type'=>'map_val','style'=>'width:60px;text-align:center;','text'=>'类别','map_name'=>'partner_type'),
    array('key'=>'corporate','type'=>'','text'=>'法人代表','style'=>'width:80px;text-align:center'),
    // array('key'=>'ownership_name','type'=>'','text'=>'企业所有制','style'=>'text-align:left'),
    array('key'=>'registered_capital','type'=>'','text'=>'注册资本','style'=>'width:100px;text-align:right'),
    // array('key'=>'start_date','type'=>'','text'=>'成立日期','style'=>'width:100px;text-align:center'),
    array('key'=>'level','type'=>'map_val','text'=>'风控分类','map_name'=>'partner_level','style'=>'width:70px;text-align:center'),
    array('key'=>'credit_amount','type'=>'amount','text'=>'常规额度','style'=>'width:120px;text-align:right'),
    array('key'=>'use_amount','type'=>'amount','text'=>'正占用额度','style'=>'width:110px;text-align:right'),
    array('key'=>'balance_amount','type'=>'amount','text'=>'剩余额度','style'=>'width:120px;text-align:right'),
);



$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");

?>
