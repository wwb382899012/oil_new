<?php
/**
 * Created by youyi000.
 * DateTime: 2016/6/21 15:25
 * Describe：
 */

function checkRowEditAction($row)
{
    $s = "<input type='checkbox' value=".$row['invoice_id']." name=check_".$row['invoice_id']." />";
    return $s;
        
}

//查询区域
$form_array = array('form_url'=>'/monthIncome/add',
	'input_array'=>array(
        array('type'=>'corpName','key'=>'a.corporation_id','id'=>'corp_id','text'=>'交易主体'),
        array('type'=>'date','key'=>'start_date','id'=>'datepicker','text'=>'起始日期','placeholder'=>'起始日期'),
        array('type'=>'date','key'=>'end_date','id'=>'datepicker2','text'=>'截止日期','placeholder'=>'截止日期')
    ),
    'buttonArray'=>array(
        array('text'=>'生成收入单据','buttonId'=>'createButton'),
    ),
);

//列表显示
$array =array(
    array('key'=>'invoice_id','type'=>'href','style'=>'width:100px;text-align:center;vertical-align:middle;','text'=>'<input type="button" value="全选" class="btn btn-primary btn-xs" id="selectAll">&nbsp;<input type="button" value="全不选" class="btn btn-primary btn-xs" id="unSelect">','href_text'=>'checkRowEditAction'),
    array('key'=>'invoice_date_actual','type'=>'','style'=>'text-align:center;width:80px;vertical-align:middle;','text'=>'开票日期'),
    array('key'=>'corporation_id,corporation_name','type'=>'href','style'=>'text-align:left;width:100px;vertical-align:middle;','text'=>'交易主体','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'project_id','type'=>'href','style'=>'text-align:center;width:100px;vertical-align:middle;','text'=>'销售出库单号','href_text'=>'<a id="t_{1}" title="查看详情" target="_blank" href="/project/detail/?id={1}&t=1" >{1}</a>'),
    array('key'=>'down_partner_id,down_name','type'=>'href','style'=>'text-align:left;width:100px;vertical-align:middle;','text'=>'下游合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'project_id','type'=>'href','style'=>'text-align:center;width:100px;vertical-align:middle;','text'=>'采购合同单号','href_text'=>'<a id="t_{1}" title="查看详情" target="_blank" href="/project/detail/?id={1}&t=1" >{1}</a>'),
    array('key'=>'up_partner_id,up_name','type'=>'href','style'=>'text-align:left;width:100px;vertical-align:middle;','text'=>'上游合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'goods_type','type'=>'map_val','map_name'=>'goods_type','style'=>'text-align:left;width:100px;vertical-align:middle;','text'=>'交易品种'),
    array('key'=>'quantity','type'=>'','style'=>'text-align:right;width:80px;vertical-align:middle;','text'=>'出库数量'),
    array('key'=>'sd_price','type'=>'amount','style'=>'text-align:right;width:80px;vertical-align:middle;','text'=>'销售单价'),
    array('key'=>'invoice_amount','type'=>'amount','style'=>'text-align:right;width:120px;vertical-align:middle;','text'=>'销售金额'),
    array('key'=>'quantity','type'=>'','style'=>'text-align:right;width:80px;vertical-align:middle;','text'=>'实际采购数量'),
    array('key'=>'su_price','type'=>'amount','style'=>'text-align:right;width:80px;vertical-align:middle;vertical-align:middle;','text'=>'采购单价'),
    array('key'=>'purchase_amount','type'=>'amount','style'=>'text-align:right;width:120px;vertical-align:middle;','text'=>'实际采购金额'),
);



$style = empty($_data_[data]['rows']) ? "min-width:1050px;" : "min-width:1650px;";

$this->loadForm($form_array,$_GET);
$this->show_table($array,$_data_[data],"",$style,"table-bordered table-layout");


?>
<script>
    $(function () {
        //全选
        $("#selectAll").click(function () {
            $("input[type='checkbox']").prop("checked", true);  
        });
        //全不选
        $("#unSelect").click(function () {  
           $("input[type='checkbox']").prop("checked", false);  
        });
        //获取选中选项的值
        $("#createButton").click(function(){
            var corp_id = $('#corp_id option:selected').val();
            var corp_name = $('#corp_id option:selected').text();
            if(corp_id < 1){
                alert("请选择交易主体");
                return;
            }

            var startDate = $('#datepicker').val();
            var endDate = $('#datepicker2').val();
            if(startDate.length < 1){
                alert("请选择开票的起始日期");
                return;
            }
            if(endDate.length < 1){
                alert("请选择开票的截止日期");
                return;
            }

            var vals = "";
            $("input[type='checkbox']").each(function(i){
                if($(this).prop("checked")==true){ 
                    vals += $(this).val()+",";//转换为逗号隔开的字符串
                }
            });
            var str = vals.substring(0,vals.length-1);
            if(str.length < 1){
                alert("请选择要生成单据的列表项");
                return;
            }

            /*var url = '/monthIncome/create';
            var formData ={
                id:str,
                corp_id:corp_id,
                corp_name:corp_name,
                start_date:startDate,
                end_date:endDate
            };
            $.post(url,formData,location.reload());*/

            //alert(str);
            location.href="/monthIncome/create/?id="+str+"&corp_id="+corp_id+"&corp_name="+corp_name+"&start_date="+startDate+"&end_date="+endDate;
            //alert(vals);
        });
    });
</script>