<?php
/**
 * Created by youyi000.
 * DateTime: 2016/11/15 15:00
 * Describe：
 */
function checkRowEditAction($row,$self)
{
    $links=array();
    $links[]='<a href="/project/all?t=1&id='.$row["project_id"].'" target="_blank" title="信息全览">全览</a>';
    
    if($self->checkIsCanEdit($row["project_status"]))
    {
        $links[]='<a href="/'.$self->getId().'/edit?id='.$row["project_id"].'" title="上传合同">上传</a>';
    }else{
        $links[]='<a href="/'.$self->getId().'/detail?id='.$row["project_id"].'" title="查看详情">查看</a>';
    }
    
    $s=implode("&nbsp;|&nbsp;",$links);
    return $s;
}


//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type'=>'text','key'=>'a.project_id','text'=>'项目编号'),
        array('type'=>'text','key'=>'b.name*','text'=>'上游合作方'),
        array('type'=>'text','key'=>'s.name*','text'=>'下游合作方'),
        array('type'=>'select','key'=>'d.status','map_name'=>'contract_stamp_status','text'=>'上传状态'),
    ),
    'buttonArray'=>array(
        //array('text'=>'添加','buttonId'=>'addButton'),
    ),
);

//列表显示
$array =array(
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'project_id','type'=>'','style'=>'width:100px;text-align:center','text'=>'项目编号'),
    array('key'=>'project_id,project_name','type'=>'href','style'=>'text-align:left;','text'=>'项目名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?t=1&id={1}" >{2}</a>'),
    array('key'=>'up_partner_id,up_name','type'=>'href','style'=>'text-align:left;','text'=>'上游合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'down_partner_id,down_name','type'=>'href','style'=>'text-align:left;','text'=>'下游合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'project_status','type'=>'map_val','text'=>'项目状态','map_name'=>'project_status','style'=>'width:140px;text-align:center'),
    array('key'=>'status','type'=>'map_val','text'=>'上传状态','map_name'=>'contract_stamp_status','style'=>'width:80px;text-align:center'),
);



$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");


?>

