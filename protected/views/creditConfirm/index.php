<?php
/**
 * Created by youyi000.
 * DateTime: 2017/4/12 10:03
 * Describe：
 */
function checkRowEditAction($row,$self)
{
    $links=array();

        $links[]='<a href="/'.$self->getId().'/detail?id='.$row["detail_id"].'" title="查看详情">查看</a>';
    if($row["status"]==ProjectCreditApplyDetail::STATUS_SUBMIT)
    {
        $links[]='<a href="/'.$self->getId().'/edit?id='.$row["detail_id"].'" title="确认">确认</a>';
    }

    $s=implode("&nbsp;|&nbsp;",$links);
    return $s;
}


//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type'=>'text','key'=>'p.project_id','text'=>'项目编号'),
        array('type'=>'text','key'=>'p.project_name*','text'=>'项目名称'),
        array('type'=>'managerUser','key'=>'p.manager_user_id','text'=>'项目经理'),
        array('type'=>'text','key'=>'u.name*','text'=>'上游合作方'),
        array('type'=>'text','key'=>'d.name*','text'=>'下游合作方'),
    ),
    'buttonArray'=>array(
        //array('text'=>'添加','buttonId'=>'addButton'),
    ),
);


//列表显示
$array =array(
    array('key'=>'detail_id','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'project_id','type'=>'','style'=>'width:100px;text-align:center','text'=>'项目编号'),
    array('key'=>'amount','type'=>'amountWan','text'=>'申请额度','style'=>'width:150px;text-align:right'),
    array('key'=>'status','type'=>'map_val','text'=>'申请状态','map_name'=>'project_credit_apply_detail_status','style'=>'width:90px;text-align:center'),
    array('key'=>'project_amount','type'=>'amountWan','text'=>'项目总金额','style'=>'width:150px;text-align:right'),
    array('key'=>'project_id,project_name','type'=>'href','style'=>'text-align:left;','text'=>'项目名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'up_partner_id,up_name','type'=>'href','style'=>'text-align:left;','text'=>'上游合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'down_partner_id,down_name','type'=>'href','style'=>'text-align:left;','text'=>'下游合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),



);



$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");
?>