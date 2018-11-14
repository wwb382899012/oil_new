
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
    var_dump($i);
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


function showTimeAll($row,$self)
{
   
    $s = '<span class="label label-primary">' . Utility::timeSpanToString($row['total_time_value']) . '</span>';
    return $s;
}

function showTime($row,$self,$array)
{
    $time=$array['t']; //时间
    $value=$array['v'];//时效值
    $user_name=$array['u'];//用户名
    if(empty($row[$value])){
        return '';
    }
    else{
    $s="";
   
    if(!empty($array['u'])){
        $s.="<span style='font-size:75%;'>".$row[$user_name]."</span>";
    }
    else
        $s.="<span style='font-size:75%;'></span>";
    $s.="<br/>";
    $s.="<span style='font-size:75%;'>".(empty($row[$time])?'-':$row[$time])."</sapn>";
    $s.="<br/>";
    if(!empty($row[$value]))
        $s.='<span class="label label-info">'.Utility::timeSpanToString($row[$value]).'</span>';
    else 
        $s.="<span style='font-size:75%;'>-</span>";
    return $s;
    }
}


//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'a.start_apply_time>','text'=>'申请时间'),
        array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'a.start_apply_time<','text'=>'到'),
        array('type'=>'subject', 'id'=>'subject_id','key'=>'a.subject_id','text'=>'用途'),
        array('type'=>'text', 'id'=>'apply_id','key'=>'a.apply_id','text'=>'付款编号'),
        array('type'=>'text', 'id'=>'user_name','key'=>'u.user_name*','text'=>'申请人'),
        array('type'=>'text', 'id'=>'payee','key'=>'a.payee*','text'=>'收款单位'),

    ),
    'buttonArray'=>array(
        array('text'=>'导出','buttonId' => 'exportButton'),
    ),
);

//列表显示
$array =array(
    array('key'=>'apply_id','type'=>'href','style'=>'width:120px;text-align:center','text'=>'付款申请编号','href_text'=>'<a id="t_{1}" target="blank" title="{1}" href="/pay/detail/?id={1}">{1}</a>'),
    array('key'=>'username','type'=>'','style'=>'width:120px;text-align:center','text'=>'申请人'),
    
    array('key'=>'subject_name','type'=>'','style'=>'width:120px;text-align:center','text'=>'用途'),
    array('key'=>'payee','type'=>'','style'=>'width:120px;text-align:center','text'=>'收款单位'),
    
    array('key'=>'start_apply_time','type'=>'','style'=>'width:150px;text-align:center','text'=>'开始申请时间'),
    array('key'=>'contract_check_value','type'=>'href','style'=>'width:150px;text-align:center','text'=>'合同物流跟单申请','href_text'=>'showTime','params'=>array("t"=>"end_apply_time","v"=>"contract_check_value","u"=>"username")),
    array('key'=>'business_check_value','type'=>'href','style'=>'width:150px;text-align:center','text'=>'商务主管审核','href_text'=>'showTime','params'=>array("t"=>"business_check_time","v"=>"business_check_value","u"=>"business_user_name")),
    
    array('key'=>'risk_check_value','type'=>'href','style'=>'width:150px;text-align:center','text'=>'风控时效审核','href_text'=>'showTime','params'=>array("t"=>"risk_check_time","v"=>"risk_check_value","u"=>"risk_user_name")),
    
    array('key'=>'energy_account_check_value','type'=>'href','style'=>'width:150px;text-align:center','text'=>'能源会计审核','href_text'=>'showTime','params'=>array("t"=>"energy_account_check_time","v"=>"energy_account_check_value","u"=>"energy_account_user_name")),
    array('key'=>'factor_account_check_value','type'=>'href','style'=>'width:150px;text-align:center','text'=>'保理会计审核','href_text'=>'showTime','params'=>array("t"=>"factor_account_check_time","v"=>"factor_account_check_value","u"=>"factor_account_user_name")),
    array('key'=>'factor_manager_check_value','type'=>'href','style'=>'width:150px;text-align:center','text'=>'保理板块负责人审核','href_text'=>'showTime','params'=>array("t"=>"factor_manager_check_time","v"=>"factor_manager_check_value","u"=>"factor_manager_user_name")),
    
    array('key'=>'energy_cashier_check_value','type'=>'href','style'=>'width:150px;text-align:center','text'=>'能源出纳审核','href_text'=>'showTime','params'=>array("t"=>"energy_cashier_check_time","v"=>"energy_cashier_check_value","u"=>"energy_cashier_user_name")),
    array('key'=>'factor_cashier_check_value','type'=>'href','style'=>'width:150px;text-align:center','text'=>'保理出纳审核','href_text'=>'showTime','params'=>array("t"=>"factor_cashier_check_time","v"=>"factor_cashier_check_value","u"=>"factor_cashier_user_name")),
    array('key'=>'energy_cashier_payment_value','type'=>'href','style'=>'width:150px;text-align:center','text'=>'能源出纳实付操作','href_text'=>'showTime','params'=>array("t"=>"energy_cashier_payment_time","v"=>"energy_cashier_payment_value","u"=>"energy_cashier_payment_user_name")),
    
    array('key'=>'reject_times','type'=>'','style'=>'width:120px;text-align:center','text'=>'驳回次数'),
    array('key'=>'total_time_value','type'=>'href','style'=>'width:120px;text-align:center','text'=>'总时效','href_text'=>'showTimeAll'),
    
    /*array('key'=>'project_id,project_name','type'=>'href','style'=>'width:200px;text-align:left;','text'=>'项目名称','href_text'=>'<a id="t_{1}" title="{2}" href="/project/detail/?id={1}" >{2}</a>'),
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
    array('key'=>'project_id','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'用时汇总','href_text'=>'showCellAll'),*/



);



$this->loadForm($form_array,$_data_);
//$this->show_table($array,$_data_[data],"","min-width:2660px;","table-bordered table-layout","dataTable");
$this->show_table($array,$_data_[data],"","min-width:2360px;","table-bordered table-layout","dataTable");


?>
<script>
$(function () {
	$("#exportButton").click(function(){
				   var url = "";
				   url+="startApplyTime="+$("#createStartTime").val();
				   url+="&&endApplyTime="+$("#createEndTime").val();
				   url+="&&subject_id="+$("#subject_id").val();
				   url+="&&apply_id="+$("#apply_id").val();
				   url+="&&user_name="+$("#user_name").val();
				   url+="&&payee="+$("#payee").val();
		location.href="/<?php echo $this->getId() ?>/export?"+url;
	});
});


</script>
