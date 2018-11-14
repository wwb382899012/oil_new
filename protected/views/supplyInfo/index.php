<?php

function checkRowEditAction($row,$self)
{
    if($row['r_status']==PartnerReview::STATUS_INFO_BACK){
        $title="修改";
        $s .= '<a href="/supplyInfo/edit?flag=1&id='.$row["review_id"].'" title="'.$title.'补充资料">'.$title.'</a>&nbsp;|&nbsp;';
    }else if($row['r_status']>=PartnerReview::STATUS_NEED_REVIEW && $row['r_status']<=PartnerReview::STATUS_NOT_REVIEW){
        $title="添加";
        $s .= '<a href="/supplyInfo/edit?flag=2&id='.$row["review_id"].'" title="'.$title.'补充资料">'.$title.'</a>';
    }

    if($row['r_status']>=PartnerReview::STATUS_INFO_BACK)
        $s .= '<a href="/supplyInfo/detail/?id='.$row["review_id"].'" title="查看详情">详情</a>';

    return $s;
}

$form_array=array('form_url'=>'/supplyInfo/',
    'input_array'=>array(
        array('type'=>'text','key'=>'b.name','text'=>'企业名称'),
        array('type'=>'select','key'=>'a.status','map_name'=>'partner_review_info_status','text'=>'资料状态'),
        array('type'=>'select','key'=>'b.type','map_name'=>'partner_type','text'=>'类别&emsp;&emsp;'),        
    ),

);
$array=array(
    array('key'=>'partner_id','type'=>'href','style'=>'width:80px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'review_id,partner_id','type'=>'href','style'=>'width:120px;text-align:center','text'=>'会议评审编号','href_text'=>'<a id="t_{1}" title="查看会议评审详情" target="_blank" href="/partnerReview/detail/?id={2}&t=1" >{1}</a>'),
    array('key'=>'partner_id','type'=>'','style'=>'width:90px;text-align:center','text'=>'企业编号'),
    array('key'=>'partner_id,name','type'=>'href','style'=>'text-align:left;','text'=>'企业名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partnerApply/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'type','type'=>'','style'=>'width:100px;','text'=>'类别'),
    array('key'=>'corporate','type'=>'','text'=>'法人代表','style'=>'width:90px;text-align:center'),
    array('key'=>'ownership_name','type'=>'','text'=>'企业所有制','style'=>'text-align:left'),
    array('key'=>'start_date','type'=>'','text'=>'成立日期','style'=>'width:100px;text-align:center'),
    array('key'=>'review_status','type'=>'map_val','text'=>'资料状态','map_name'=>'partner_review_info_status','style'=>'width:80px;text-align:center'),
    array('key'=>'status','type'=>'map_val','text'=>'合作方状态','map_name'=>'partner_status','style'=>'width:120px;text-align:left'),
    array('key'=>'auto_level','type'=>'map_val','text'=>'系统分类','map_name'=>'partner_level','style'=>'width:70px;text-align:center'),
    array('key'=>'custom_level','type'=>'map_val','text'=>'商务分类','map_name'=>'partner_level','style'=>'width:70px;text-align:center'),
    array('key'=>'apply_amount','type'=>'amountWan','text'=>'拟申请额度','style'=>'width:120px;text-align:right'),
    array('key'=>'level','type'=>'map_val','text'=>'风控分类','map_name'=>'partner_level','style'=>'width:70px;text-align:center'),
    array('key'=>'credit_amount','type'=>'amountWan','text'=>'确认额度','style'=>'width:120px;text-align:right'),
);


$style = empty($_data_[data]['rows']) ? "min-width:1050px;" : "min-width:1650px;";

$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"",$style,"table-bordered table-layout");

?>
