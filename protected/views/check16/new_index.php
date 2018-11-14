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
        $links[]='<a href="/'.$self->getId().'/check?id='.$row["obj_id"].'" title="审核">审核</a>';
    }
    else{
        $links[]='<a href="/'.$self->getId().'/detail?id='.$row["detail_id"].'" title="查看详情">详情</a>';
    }
    $s=implode("&nbsp;&nbsp;",$links);
    return $s;
}

function invoiceApplyAction($row, $self)
{
    if($row['type']==ConstantMap::INPUT_INVOICE_TYPE)
        $s = '<a id="t_'.$row['apply_id'].'" title="'.$row['apply_code'].'" target="_blank" href="/inputInvoice/detail/?id='.$row['apply_id'].'&t=1" >'.$row['apply_code'].'</a>';
    else
        $s = '<a id="t_'.$row['apply_id'].'" title="'.$row['apply_code'].'" target="_blank" href="/outputInvoice/detail/?id='.$row['apply_id'].'&t=1" >'.$row['apply_code'].'</a>';
    return $s;
}

if($_data_['type']==ConstantMap::INPUT_INVOICE_TYPE){
    $invoice_type = "invoice_input_type";
}
else{
    $invoice_type = "invoice_output_type";
}


//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
    'items'=>array(
        array('type'=>'text','key'=>'c.contract_code*','text'=>'货款合同编号'),
        array('type'=>'text','key'=>'pa.name*','text'=>'合作方'),
        array('type'=>'select','key'=>'checkStatus','noAll'=>'1','map_name'=>'contract_check_status','text'=>'审核状态'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type'=>'text','key'=>'p.project_code*','text'=>'项目编号'),
        array('type'=>'text','key'=>'o.apply_code*','text'=>'发票编号'),
    ),
);

//列表显示
$array =array(
    array('key'=>'detail_id','type'=>'','style'=>'width:80px;text-align:left','text'=>'审核编号'),
    array('key'=>'apply_code','type'=>'href','style'=>'width:140px;text-align:left','text'=>'发票编号','href_text'=>'invoiceApplyAction'),
    array('key'=>'type','type'=>'map_val','style'=>'width:80px;text-align:left','map_name'=>'invoice_type','text'=>'发票类型'),
    array('key'=>'type_sub','type'=>'map_val','style'=>'width:100px;text-align:left','map_name'=>'invoice_output_type','text'=>'发票属性'),
    array('key'=>'user_name','type'=>'','style'=>'width:80px;text-align:left','text'=>'申请人'),
    array('key'=>'project_id,project_code','type'=>'href','style'=>'width:130px;text-align:left','text'=>'项目编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank"  href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'partner_name','type'=>'','style'=>'text-align:left;','text'=>'合作方'),
    array('key'=>'contract_type','type'=>'map_val','style'=>'width:100px;text-align:left;','map_name'=>'goods_contract_type','text'=>'货款合同类型'),
    array('key'=>'contract_id,contract_code','type'=>'href','style'=>'width:130px;text-align:left','text'=>'货款合同编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank"  href="/businessConfirm/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:100px;text-align:left', 'text' => '外部合同编号'),
    array('key'=>'amount','type'=>'amount','text'=>'发票金额','style'=>'width:110px;text-align:right'),
    array('key'=>'invoice_amount','type'=>'amount','text'=>'开票金额','style'=>'width:110px;text-align:right'),
    array('key'=>'invoice_num','type'=>'','text'=>'开票数量(张)','style'=>'width:100px;text-align:left'),
    array('key'=>'checkStatus','type'=>'map_val','style'=>'width:80px;text-align:left;','text'=>'审核状态','map_name'=>'cross_order_check_status'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:60px;text-align:left;','text'=>'操作','href_text'=>'checkRowEditAction'),
);

$headerArray = [];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);


?>