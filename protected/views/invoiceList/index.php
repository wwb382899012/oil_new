<?php
/**
 * Created by vector.
 * DateTime: 2017/10/24 15:25
 * Describe：
 */

function checkRowEditAction($row,$self)
{
    if($row['type']==ConstantMap::INPUT_INVOICE_TYPE)
        $s = '<a id="t_'.$row['apply_id'].'" title="'.$row['apply_code'].'" target="_blank" href="/inputInvoice/detail/?id='.$row['apply_id'].'&t=1" >'.$row['apply_code'].'</a>';
    else
        $s = '<a id="t_'.$row['apply_id'].'" title="'.$row['apply_code'].'" target="_blank" href="/outputInvoice/detail/?id='.$row['apply_id'].'&t=1" >'.$row['apply_code'].'</a>';
    return $s;
}

if($_data_['type']==ConstantMap::INPUT_INVOICE_TYPE){
    $invoice_type = "vat_invoice_type";
	$invoice_attribute_type = "invoice_input_type";
}
else{
    $invoice_type = "output_invoice_type";
	$invoice_attribute_type = "invoice_output_type";
}

//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
	'input_array'=>array(
        array('type'=>'text','key'=>'a.apply_code*','text'=>'发票编号&emsp;&emsp;'),
        array('type'=>'text','key'=>'p.project_code*','text'=>'项目编号&emsp;&emsp;'),
        array('type'=>'text','key'=>'co.name*','text'=>'交易主体&emsp;&emsp;'),
        array('type'=>'text','key'=>'pa.name*','text'=>'合作方&emsp;&emsp;&emsp;'),
        array('type'=>'text','key'=>'g.name*','text'=>'品名&emsp;&emsp;&emsp;&emsp;'),
        array('type'=>'select','key'=>'a.type_sub','map_name'=>'invoice_input_type','text'=>'发票属性&emsp;&emsp;'),
        array('type'=>'date','key'=>'start_date','id'=>'datepicker','text'=>'开始日期&emsp;&emsp;'),
        array('type'=>'date','key'=>'end_date','id'=>'datepicker2','text'=>'截止日期&emsp;&emsp;'),
        array('type'=>'select','key'=>'a.contract_type','map_name'=>'goods_contract_type','text'=>'货款合同类型'),
        array('type'=>'text','key'=>'c.contract_code*','text'=>'货款合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type'=>'select','key'=>'a.invoice_contract_type','map_name'=>'contract_category','text'=>'发票合同类型'),
        array('type'=>'text','key'=>'a.invoice_contract_code*','text'=>'发票合同编号'),        
    ),
    'buttonArray' => array(
        array('text' => '导出', 'buttonId' => 'export'),
    )
);

//列表显示
$array =array(
    array('key'=>'apply_id,apply_code','type'=>'href','style'=>'width:140px;text-align:center','text'=>'发票编号','href_text'=>'checkRowEditAction'),
    // array('key'=>'type','type'=>'map_val','style'=>'width:90px;text-align:center','map_name'=>'invoice_type','text'=>'发票类型'),
    array('key'=>'type_sub','type'=>'map_val','style'=>'width:100px;text-align:center','map_name'=>$invoice_attribute_type,'text'=>'发票属性'),
    array('key'=>'goods_name','type'=>'','style'=>'width:90px;text-align:center','text'=>'品名'),
    array('key'=>'quantity','type'=>'number','style'=>'width:90px;text-align:right','text'=>'数量'),
    array('key'=>'unit','type'=>'map_val','style'=>'width:90px;text-align:center','map_name'=>'goods_unit','text'=>'单位'),
    array('key'=>'price','type'=>'amount','style'=>'width:90px;text-align:right','text'=>'单价'),
    array('key'=>'rate','type'=>'','style'=>'width:90px;text-align:center','text'=>'税率'),
    array('key'=>'amount','type'=>'amount','text'=>'发票金额','style'=>'width:120px;text-align:right'),
    array('key'=>'rate_amount','type'=>'amount','text'=>'税额','style'=>'width:120px;text-align:right'),
    array('key'=>'project_id,project_code','type'=>'href','style'=>'width:140px;text-align:center','text'=>'项目编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'project_type','type'=>'map_val','style'=>'width:100px;text-align:center;','map_name'=>'project_type','text'=>'项目类型'),
    array('key'=>'corporation_name','type'=>'','style'=>'text-align:left;','text'=>'交易主体'),
    array('key'=>'partner_name','type'=>'','style'=>'text-align:left;','text'=>'合作方'),
    array('key'=>'contract_type','type'=>'map_val','style'=>'width:100px;text-align:center;','map_name'=>'goods_contract_type','text'=>'货款合同类型'),
    array('key'=>'contract_id,contract_code','type'=>'href','style'=>'width:140px;text-align:center','text'=>'货款合同编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank"  href="/businessConfirm/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key'=>'invoice_contract_type','type'=>'map_val','style'=>'width:120px;text-align:center;','map_name'=>'contract_category','text'=>'发票合同类型'),
    array('key'=>'invoice_contract_code','type'=>'','style'=>'width:140px;text-align:center','text'=>'发票合同编号'),
    array('key'=>'invoice_type','type'=>'map_val','style'=>'width:120px;text-align:center','map_name'=>$invoice_type,'text'=>'税票类型'),
    array('key'=>'create_time','type'=>'date','style'=>'width:140px;text-align:center','text'=>'录入时间'),
    array('key'=>'invoice_date','type'=>'','style'=>'width:120px;text-align:center','text'=>'发票时间'),
);

$style = empty($_data_['data']['rows']) ? "min-width:1050px;" : "min-width:2450px;";

$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"",$style,"table-bordered table-layout");

?>

<script>
    $(function () {
        var fieldList = <?php echo json_encode($array)?>;

        $("#export").click(function () {
          var export_str = JSON.stringify(fieldList);
          var export_input = $('<input type="hidden">');
          export_input.val(export_str).attr('name', 'export_str');
          $("form.search-form").append(export_input);
          $("form.search-form").submit();
          setTimeout(function() {
            export_input.remove();
          }, 10);
    
        });
    });
</script>
