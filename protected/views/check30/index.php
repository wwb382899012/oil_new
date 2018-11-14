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
        $links[]='<a href="/'.$self->getId().'/detail?id='.$row["detail_id"].'&partner_id='.$row["partner_id"].'" title="查看详情">详情</a>';
    }
    $s=implode("&nbsp;|&nbsp;",$links);
    return $s;
}

//查询区域
$form_array = array(
    'form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type'=>'text','key'=>'p.name*','text'=>'企业名称'),
        array('type'=>'select','key'=>'checkStatus','noAll'=>'1','map_name'=>'partner_check_status','text'=>'审核状态'),
    ),
    'buttonArray'=>array(

    ),
);

//列表显示
$array =array(
    array('key'=>'partner_id','type'=>'href','style'=>'width:80px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'check_id','type'=>'','style'=>'width:75px;text-align:center','text'=>'审核编号'),
    array('key'=>'partner_id','type'=>'','style'=>'width:75px;text-align:center','text'=>'企业编号'),
    array('key'=>'partner_id,name','type'=>'href','style'=>'text-align:left;','text'=>'企业名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partnerApply/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'type','type'=>'','style'=>'width:100px;','text'=>'类别'),
    array('key'=>'corporate','type'=>'','text'=>'法人代表','style'=>'width:80px;text-align:center'),
    array('key'=>'ownership_name','type'=>'','text'=>'企业所有制','style'=>'text-align:left'),
    array('key'=>'start_date','type'=>'','text'=>'成立日期','style'=>'width:100px;text-align:center'),
    array('key'=>'runs_state','type'=>'map_val','text'=>'经营状态','map_name'=>'runs_state','style'=>'width:80px;text-align:center'),
    array('key'=>'partner_status','type'=>'map_val','text'=>'合作方状态','map_name'=>'partner_status','style'=>'width:120px;text-align:left'),
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