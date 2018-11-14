
<?php
/**
 * Created by youyi000.
 * DateTime: 2017/4/6 10:51
 * Describe：
 */


function showCell($row,$self,$i,$k="")
{
    if(is_array($i))
    {
        $k=$i[1];
        $i=$i[0];
    }
    $s="-";
    if(!empty($row["step".$i."_time".$k]))
    {
        $s = $row["step" . $i . "_time".$k];
        $s .= "<br/>";
        $class="label-primary";
        if(StatTimelinessService::$config_time[$i]>0
            && $row["step" . $i . "_span".$k]>StatTimelinessService::$config_time[$i]*60)
            $class="label-danger";
        $s .= '<span class="label '.$class.'">' . Utility::timeSpanToString($row["step" . $i . "_span".$k]) . '</span>';
        if(!empty($k))
        {
            if($row["step".$i."_back_times".$k]>0)
            {
                $s.="<br/>"."驳回 ".$row["step".$i."_back_times".$k] ." 次";
                $s.="<br/>".'<span class="label label-info">' . Utility::timeSpanToString($row["step" . $i . "_back_span".$k]) . '</span>';
            }
        }
    }
    return $s;
}
function showCellAll($row,$self)
{
    $timeSpan=$row["time_span"];
    if($row["project_status"]<Project::STATUS_PAY_CONFIRM)
    {
        $timeSpan=time()-strtotime($row["project_create_time"]);
    }
    $s = '<span class="label label-primary">' . Utility::timeSpanToString($timeSpan) . '</span>';
    return $s;
}
function showCellBack($row,$self)
{
    if($row["back_times"]>0)
        return "驳回".$row["back_times"]."次";
    else
        return "无";
}

//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type'=>'text','key'=>'a.project_id','text'=>'项目编号'),
        array('type'=>'text','key'=>'a.project_name*','text'=>'项目名称'),
        array('type'=>'select','key'=>'a.status','map_name'=>'project_status','text'=>'项目状态'),


        array('type'=>'managerUser','key'=>'a.manager_user_id','text'=>'项目负责人'),
        array('type'=>'text','key'=>'b.name*','text'=>'上游合作方'),
        array('type'=>'text','key'=>'s.name*','text'=>'下游合作方'),
        array('type'=>'select','key'=>'hasBack','map_name'=>'is_or_nor','text'=>'是否有驳回'),
    ),
    'buttonArray'=>array(

    ),
);

//列表显示
$array =array(
    array('key'=>'project_id','type'=>'','style'=>'width:100px;text-align:center','text'=>'项目编号'),
    array('key'=>'project_id,project_name','type'=>'href','style'=>'width:200px;text-align:left;','text'=>'项目名称','href_text'=>'<a id="t_{1}" title="{2}" href="/project/detail/?id={1}" >{2}</a>'),
    array('key'=>'up_partner_id,up_name','type'=>'href','style'=>'width:120px;text-align:left;','text'=>'上游合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'down_partner_id,down_name','type'=>'href','style'=>'width:120px;text-align:left;','text'=>'下游合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'project_status','type'=>'map_val','text'=>'项目状态','map_name'=>'project_status','style'=>'width:100px;text-align:center'),

    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'发起项目<br/>（业务助理）','href_text'=>'showCell','params'=>"1"),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'商务确认<br/>（商务）','href_text'=>'showCell','params'=>"2"),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'合同初审','href_text'=>'showCell','params'=>"3"),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'合同初审<br/>（财务）','href_text'=>'showCell','params'=>array("3","1")),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'合同初审<br/>（法务）','href_text'=>'showCell','params'=>array("3","2")),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'合同初审<br/>（商务）','href_text'=>'showCell','params'=>array("3","3")),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'上下游签章<br/>（业务助理）','href_text'=>'showCell','params'=>"4"),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'上下游签章审核','href_text'=>'showCell','params'=>"5"),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'上下游签章审核<br/>（商务）','href_text'=>'showCell','params'=>array("5","1")),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'上下游签章审核<br/>（财务）','href_text'=>'showCell','params'=>array("5","2")),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'盖章合同上传<br/>（商务）','href_text'=>'showCell','params'=>"6"),
    array('key'=>'project_id','type'=>'href','style'=>'width:80px;text-align:center;','text'=>'驳回信息','href_text'=>'showCellBack'),
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'用时汇总','href_text'=>'showCellAll'),



);



$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:2660px;","table-bordered table-layout","dataTable");


?>
<script>
$(function () {


    /*var myST = new superTable("dataTable", {
        headerRows: 1,
        fixedCols: 0

    });*/
});


</script>
