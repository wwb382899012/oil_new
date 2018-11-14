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
        $links[]='<a href="/'.$self->getId().'/detail?id='.$row["detail_id"].'&review_id='.$row["review_id"].'" title="查看详情">详情</a>';
    }
    $s=implode("&nbsp;|&nbsp;",$links);
    return $s;
}

//查询区域
$form_array = array(
    'form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type'=>'text','key'=>'p.name*','text'=>'企业名称'),
        array('type'=>'select','key'=>'checkStatus','noAll'=>'1','map_name'=>'supply_info_check_type','text'=>'审核状态'),
        array('type'=>'select','key'=>'p.type','map_name'=>'partner_type','text'=>'类别&emsp;&emsp;'),
    ),
    'buttonArray'=>array(

    ),
);

//列表显示
$array =array(
    array('key'=>'review_id','type'=>'href','style'=>'width:80px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'partner_id','type'=>'','style'=>'width:90px;text-align:center','text'=>'企业编号'),
    array('key'=>'review_id,partner_id','type'=>'href','style'=>'width:110px;text-align:center','text'=>'会议评审编号','href_text'=>'<a id="t_{1}" title="查看会议评审详情" target="_blank" href="/partnerReview/detail/?id={2}&t=1" >{1}</a>'),
    array('key'=>'partner_id,name','type'=>'href','style'=>'text-align:left;','text'=>'企业名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partnerApply/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'type','type'=>'','style'=>'width:100px;','text'=>'类别'),
    array('key'=>'corporate','type'=>'','text'=>'法人代表','style'=>'width:80px;text-align:center'),
    array('key'=>'ownership_name','type'=>'','text'=>'企业所有制','style'=>'width:140px;text-align:left'),
    array('key'=>'node_name','type'=>'','style'=>'width:100px;text-align:center','text'=>'待审核节点'),
    array('key'=>'partner_status','type'=>'map_val','text'=>'合作方状态','map_name'=>'partner_status','style'=>'width:120px;text-align:center'),

);



$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");


?>