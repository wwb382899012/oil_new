<style type="text/css">
<!--
.reloadBtn{background-coloe:#9da5a1;}
-->
</style>
<?php
/**
 * Desc: 分配统计利润明细
 * User: susiehuang
 * Date: 2017/11/25 0025
 * Time: 14:34
 */
//查询区域 

$form_array = array('form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'a.start_apply_time>','text'=>'申请时间'),
        array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'a.start_apply_time<','text'=>'到'),
        array('type'=>'subject', 'id'=>'subject_id','key'=>'a.subject_id','text'=>'用途'),
        array('type'=>'text', 'id'=>'apply_id','key'=>'a.apply_id','text'=>'付款编号'),
        array('type'=>'text', 'id'=>'user_name','key'=>'u.name*','text'=>'申请人'),
        array('type'=>'text', 'id'=>'payee','key'=>'a.payee*','text'=>'收款单位'),
        
    ),
    'buttonArray'=>array(
        array('text'=>'导出','buttonId' => 'exportButton'),
        array('text'=>'同步数据','buttonId' => 'reloadButton'),
    ),
);

function showTimeAll($row,$self)
{
    $s = "&nbsp;<br/>&nbsp;<br />";
    $s .= '<span class="label label-primary">' . Utility::timeSpanToString($row['total_time_value']) . '</span>';
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
function showTimeContract($row,$self,$array)
{
    $time=$array['t']; //时间
    $value=$array['v'];//时效值
    $user_name=$array['u'];//用户名
   
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

$columns = array(
    array(
        'headerHtmlOptions'=>array("style"=>"width:120px;text-align:center;"),
        'htmlOptions'=>array("style"=>"text-align:center;"),
        'name'=>'apply_id',
        'header'=>'付款申请编号',
        'value'=>function($model,$index,$self){
            return "<a id=".$model['apply_id']." target='blank' title=".$model['apply_id']." href='/pay/detail/?id=".$model['apply_id']."&t=1'>".$model['apply_id']."</a>";
        },
    )
);

    array_push(
        $columns,
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'username',
            'header'=>'申请人',
            'value'=>function($model,$index,$self){
                 return $model['username'];
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'subject_name',
            'header'=>'用途',
            'value'=>function($model,$index,$self){
            return $model['subject_name'];
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'payee',
            'header'=>'收款单位',
            'value'=>function($model,$index,$self){
            return $model['payee'];
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'start_apply_time',
            'header'=>'开始申请时间',
            'value'=>function($model,$index,$self){
            return $model['start_apply_time'];
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'contract_check_value',
            'header'=>'合同物流跟单申请',
            'value'=>function($model,$index,$self){
                $param=array("t"=>"end_apply_time","v"=>"contract_check_value","u"=>"username");
                return showTimeContract($model,$self,$param);
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'business_check_value',
            'header'=>'商务主管审核',
            'value'=>function($model,$index,$self){
                $param=array("t"=>"business_check_time","v"=>"business_check_value","u"=>"business_user_name");
                return showTime($model,$self,$param);
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'risk_check_value',
            'header'=>'风控时效审核',
            'value'=>function($model,$index,$self){
                $param=array("t"=>"risk_check_time","v"=>"risk_check_value","u"=>"risk_user_name");
                return showTime($model,$self,$param);
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'energy_account_check_value',
            'header'=>'能源会计审核',
            'value'=>function($model,$index,$self){
                $param=array("t"=>"energy_account_check_time","v"=>"energy_account_check_value","u"=>"energy_account_user_name");
                return showTime($model,$self,$param);
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'factor_account_check_value',
            'header'=>'保理会计审核',
            'value'=>function($model,$index,$self){
                $param=array("t"=>"factor_account_check_time","v"=>"factor_account_check_value","u"=>"factor_account_user_name");
                return showTime($model,$self,$param);
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'factor_manager_check_value',
            'header'=>'保理板块负责人审核',
            'value'=>function($model,$index,$self){
                $param=array("t"=>"factor_manager_check_time","v"=>"factor_manager_check_value","u"=>"factor_manager_user_name");
                return showTime($model,$self,$param);
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'energy_cashier_check_value',
            'header'=>'能源出纳审核',
            'value'=>function($model,$index,$self){
                $param=array("t"=>"energy_cashier_check_time","v"=>"energy_cashier_check_value","u"=>"energy_cashier_user_name");
                return showTime($model,$self,$param);
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'factor_cashier_check_value',
            'header'=>'保理出纳审核',
            'value'=>function($model,$index,$self){
                $param=array("t"=>"factor_cashier_check_time","v"=>"factor_cashier_check_value","u"=>"factor_cashier_user_name");
                return showTime($model,$self,$param);
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'energy_cashier_payment_value',
            'header'=>'实付出纳实付操作',
            'value'=>function($model,$index,$self){
                $param=array("t"=>"energy_cashier_payment_time","v"=>"energy_cashier_payment_value","u"=>"energy_cashier_payment_user_name");
                return showTime($model,$self,$param);
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'reject_times',
            'header'=>'驳回次数',
            'value'=>function($model,$index,$self){
            return $model['reject_times'];
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'total_time_value',
            'header'=>'总时效',
            'value'=>function($model,$index,$self){
                return showTimeAll($model,$self);
            },
        )
       
    );


$this->loadForm($form_array, $_data_);
//$this->show_table($array, $_data_[data], "", "min-width:3050px;", "table-bordered table-layout");

$this->widget('ZGridView', array(
    'id'=>'data-grid',
    'emptyText'=>'数据库没有数据',
    'dataProvider'=>$dataProvider,//数据源
    'tableOptions'=>array(
        "class"=>"data-table",
//        "style"=>!empty($search['type']) ? "width:3050px;" : "width:1650px;",
        "data-config"=>"{ 
            fixedHeader: true,
            fixedColumns: {
                leftColumns: 1
            }
            }",
    ),
    'columns'=>$columns
    )
);

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

        //数据同步
        $("#reloadButton").click(function(){
        	 $(this).val("正在同步...").attr("disabled",true).addClass("reloadBtn");
             $.ajax({
                 type:'GET',
                 data:{},
                 url:"<?php echo '/'.$this->getId().'/add'; ?>",
                 dataType:"json",
                 success:function(json){
                      if(json.state=="0"){
                    	  layer.alert(json.msg, {icon: 1},function(){window.location.reload();});
                     	 $("#reloadButton").val("数据同步").attr("disabled",false).removeClass("reloadBtn");
                      }
                 },
                 error:function(json){
                 	layer.alert(json.msg, {icon: 5});
                 }

              })
            })
    });
    

</script>
