<?php

function checkRowEditAction($row,$self)
{
    $sArr = PartnerReview::getReviewStatus($row["partner_id"]);

    if(!empty($sArr) && $sArr['status']==PartnerReview::STATUS_REVIEW_NEW){
        $title="修改";
        $flag=1;
        $s .= '<a href="/partnerReview/edit?flag='.$flag.'&id='.$row["partner_id"].'" title="'.$title.'评审记录">'.$title.'</a>';
    }else if(empty($sArr) || $row['status']==PartnerApply::STATUS_REVIEW  || ($sArr['status']==PartnerReview::STATUS_INFO_PASS && $row['status']==PartnerApply::STATUS_ADD_INFO_NEED_REVIEW)){
        $title="添加";
        $flag=2;
        $s .= '<a href="/partnerReview/edit?flag='.$flag.'&id='.$row["partner_id"].'" title="'.$title.'评审记录">'.$title.'</a>';
    }

    if(($row['status']==PartnerApply::STATUS_REVIEW && !empty($sArr))  || (($sArr['status']==PartnerReview::STATUS_INFO_PASS || $sArr['status']==PartnerReview::STATUS_REVIEW_NEW )&& $row['status']==PartnerApply::STATUS_ADD_INFO_NEED_REVIEW))
        $s .= '&nbsp;|&nbsp;';
    if(!empty($sArr))
        $s .= '<a href="/partnerReview/detail/?id='.$row["partner_id"].'" title="查看详情">详情</a>';

    return $s;
}

$form_array=array('form_url'=>'/partnerReview/',
    'input_array'=>array(
        array('type'=>'text','key'=>'a.partner_id','text'=>'企业编号'),
        array('type'=>'text','key'=>'a.name','text'=>'企业名称'),
        array('type'=>'select','key'=>'a.status','map_name'=>'partner_status','text'=>'合作方状态'),
        array('type'=>'select','key'=>'a.review_status','map_name'=>'partner_review_status','text'=>'评审状态'),
        array('type'=>'select','key'=>'a.runs_state','map_name'=>'runs_state','text'=>'经营状态'),
        array('type'=>'select','key'=>'a.type','map_name'=>'partner_type','text'=>'类别&emsp;&emsp;&emsp;'),
        
    ),
);
$array=array(
    array('key'=>'partner_id','type'=>'href','style'=>'width:80px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'partner_id','type'=>'','style'=>'width:80px;text-align:center','text'=>'企业编号'),
    array('key'=>'partner_id,name','type'=>'href','style'=>'text-align:left;','text'=>'企业名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partnerApply/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'type','type'=>'','style'=>'width:100px;','text'=>'类别'),
    array('key'=>'corporate','type'=>'','text'=>'法人代表','style'=>'width:90px;text-align:center'),
    array('key'=>'ownership_name','type'=>'','text'=>'企业所有制','style'=>'text-align:left'),
    array('key'=>'start_date','type'=>'','text'=>'成立日期','style'=>'width:100px;text-align:center'),
    array('key'=>'runs_state','type'=>'map_val','text'=>'经营状态','map_name'=>'runs_state','style'=>'width:80px;text-align:center'),
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
