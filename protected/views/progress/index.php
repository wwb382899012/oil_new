<?php
/**
 * Created by youyi000.
 * DateTime: 2016/11/18 14:58
 * Describe：
 */

function showProgress($row,$self)
{
    $s="";
    if($row["status"]==Project::STATUS_STOP)
    {
        $status=$row["old_status"];
    }
    else
        $status=$row["status"];
    foreach(ProjectService::$projectProgress as $key=>$v)
    {

        $showInfo="";
        if($status<$v["startStatus"])
            $className="label-default";
        else if($status>=$v["endStatus"])
            $className="label-success";
        else
        {
            if($key!="3")
            {
                /*if($row["status"]>="69"){
                    $showInfo="租后管理";
                }else{
                    $showInfo=$self->map["project_status"][$row["status"]];
                }*/
                $showInfo=$self->map["project_status"][$row["status"]];

                $statusArr=array();
                if(!empty($row["check_id"])){
                    $statusArr = ProjectService::getCheckStatus($row["project_id"],$row["check_id"]);
                }
                $accountantId=UserService::getAccountantCheckRoleId();
                $riskId=UserService::getRiskCheckRoleId();
                if(empty($statusArr) || ($statusArr[$accountantId]['status']==1 && $statusArr[$riskId]['status']==1) || (empty($statusArr[$accountantId]['status']) && empty($statusArr[$riskId]['status']))){
                    $showInfo.=" ".$row["node_name"];
                }else if(empty($statusArr[$accountantId]['status'])){
                    $showInfo.=" 财务初审";
                }else if(empty($statusArr[$riskId]['status'])){
                    $showInfo.=" 风控初审";
                }

            }
            else
            {
                $showInfo=$self->map["contract_status"][$row["contract_status"]];
            }
            $className = "label-info";
        }
        $rejectString="";
        if($row["status"]==Project::STATUS_STOP && $className=="label-info")
        {
            $rejectString="<span class=\"project-status-reject\">R</span>";
            $className="label-success";
            $showInfo="";
        }
        $s.="<div class=\"project-status ".$className."\">
                ".$rejectString."
                <p class=\"project-status-main\">".$v["name"]."</p>
                <p class=\"project-status-info\">".$showInfo."</p>
            </div>";
    }

    return $s;
}

//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type'=>'text','key'=>'a.project_id','text'=>'项目编号'),
        array('type'=>'text','key'=>'a.project_name*','text'=>'项目名称'),
        array('type'=>'managerUser','key'=>'a.manager_user_id','text'=>'项目经理'),
        //array('type'=>'select','key'=>'a.status','map_name'=>'project_status','text'=>'项目状态'),
        array('type'=>'select','key'=>'project_progress','map_name'=>'project_progress','text'=>'项目进展'),
        array('type'=>'text','key'=>'b.name*','text'=>'上游合作方'),
        array('type'=>'text','key'=>'s.name*','text'=>'下游合作方'),
    ),
);

//列表显示
$array =array(
    array('key'=>'project_id,project_id','type'=>'href','style'=>'text-align:center;width:120px;vertical-align:middle;','text'=>'项目编号','href_text'=>'<a id="t_{1}" title="{2}" href="/project/detail/?id={1}" >{2}</a>'),
    array('key'=>'project_id,project_name','type'=>'href','style'=>'text-align:left;width:200px;vertical-align:middle;','text'=>'项目名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'project_id','type'=>'href','style'=>'text-align:left;','text'=>'项目进展','href_text'=>'showProgress'),
);



$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");


?>

