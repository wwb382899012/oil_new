<?php
/**
 * Created by youyi000.
 * DateTime: 2016/6/21 15:25
 * Describe：
 */

function checkRowEditAction($row,$self)
{
    //$s = '<a href="/project/all?t=1&id='.$row["project_id"].'" target="_blank" title="信息全览">全览</a>';
    if($row["status"]<2)
        $s .= '<a href="/'.$self->getId().'/edit?id='.$row["plan_id"].'" title="还款催收">催收</a>';
    if($self->type==1){
        $mark = UpRemind::getRemindInfoById($row['plan_id']);
    }else{
        $mark = DownRemind::getRemindInfoById($row['plan_id']);
    }
    
    if($mark==1){
        if($row["status"]<2)
            $s .= "&nbsp;|&nbsp;";
        $s .= '<a href="/'.$self->getId().'/detail?id='.$row["plan_id"].'" title="查看详情">详情</a>';
    }else if($mark==0 && $row["status"]==2){
        $s .= "-";
    }
    
    return $s;
}


//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
	'input_array'=>array(
        array('type'=>'text','key'=>'b.project_id','text'=>'项目编号'),
        array('type'=>'text','key'=>'b.project_name*','text'=>'项目名称'),
        array('type'=>'text','key'=>'c.name*','text'=>'企业名称'),
        array('type'=>'select','key'=>'d.status','map_name'=>'remind_status','text'=>'状态&emsp;&emsp;'),
        array('type'=>'date','key'=>'start_date','id'=>'datepicker','text'=>'开始日期'),
        array('type'=>'date','key'=>'end_date','id'=>'datepicker2','text'=>'结束日期'),
    ),
);

//列表显示
$array =array(
    array('key'=>'plan_id','type'=>'href','style'=>'width:130px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'project_id','type'=>'','style'=>'width:120px;text-align:center','text'=>'项目编号'),
    array('key'=>'project_id,project_name','type'=>'href','style'=>'text-align:left;','text'=>'项目名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank"  href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'partner_id,customer_name','type'=>'href','style'=>'text-align:left;','text'=>'企业名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'period','type'=>'','style'=>'width:100px;text-align:center','text'=>'还款期数'),
    array('key'=>'return_date','type'=>'','style'=>'width:120px;text-align:center','text'=>'还款日期'),
    array('key'=>'amount','type'=>'amount','style'=>'width:140px;text-align:right','text'=>'还款金额'),
    array('key'=>'status','type'=>'map_val','text'=>'状态','map_name'=>'remind_status','style'=>'width:100px;text-align:center'),
);



$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");


?>
