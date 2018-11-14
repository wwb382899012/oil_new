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
        $links[]='<a href="/'.$self->getId().'/edit?id='.$row["project_id"].'" title="审核">审核</a>';
    }
    else{
        $links[]='<a href="/'.$self->getId().'/detail?id='.$row["project_id"].'&check_status='.$row['checkStatus'].'" title="查看详情">详情</a>';
    }
    $s=implode("&nbsp;&nbsp;",$links);
    return $s;
}

//查询区域
$form_array = array(
    'form_url'=>'/'.$this->getId().'/',
    'items'=>array(
        array('type'=>'text','key'=>'p.project_code*','text'=>'项目编号'),
        array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 's.name', 'text' => '项目负责人'),
        array('type' => 'text', 'key' => 'up.name*', 'text' => '上游合作方'),
        array('type' => 'text', 'key' => 'dp.name*', 'text' => '下游合作方'),
        array('type'=>'select','key'=>'checkStatus','noAll'=>'1','map_name'=>'contract_check_status','text'=>'审核状态'),
        array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'p.create_time>','text'=>'申请时间'),
        array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'p.create_time<','text'=>'到'),
        array('type' => 'select', 'key' => 'project_type', 'map_name' => 'project_detail_type', 'text' => '项目类型'),
    )
);

//列表显示
$array =array(
    array('key'=>'detail_id','type'=>'','style'=>'width:80px;text-align:left','text'=>'审核编号'),
    array('key'=>'project_id,project_code','type'=>'href','style'=>'width:120px;text-align:left','text'=>'项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'up_partner_id,up_partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left;', 'text' => '上游合作方', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '交易主体', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'down_partner_id,down_partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left;', 'text' => '下游合作方', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'project_type_desc','type'=>'','style'=>'width:100px;text-align:left','text'=>'项目类型'),
    array('key'=>'manager_name','type'=>'','style'=>'width:80px;text-align:left;','text'=>'项目负责人'),
    array('key'=>'checkStatus','type'=>'map_val','style'=>'width:80px;text-align:left;','text'=>'审核状态','map_name'=>'contract_check_status'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '创建人'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:150px;text-align:left', 'text' => '申请时间'),
    array('key'=>'project_id','type'=>'href','style'=>'width:80px;text-align:left;','text'=>'操作','href_text'=>'checkRowEditAction'),
);

$searchArray = ['search_config' => $form_array, 'search_lines' => 3];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);
?>