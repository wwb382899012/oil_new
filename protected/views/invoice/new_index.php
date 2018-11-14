<?php
/**
 * Created by vector.
 * DateTime: 2017/10/24 15:25
 * Describe：
 */

function checkRowEditAction($row, $self)
{
    $links = array();
    $data = InvoiceService::getLastInvoiceInfo($row['apply_id']);
    if(empty($data[0]['status']) || ($data[0]['status']==Invoice::STATUS_PASS && bccomp($row['amount'], $row['amount_paid'])==1))
        $links[] = '<a href="/'.$self->getId().'/add?id='.$row["apply_id"].'" title="销项票开票添加">开票</a>';
    else if($self->checkIsCanEdit($data[0]['status']))
        $links[] = '<a href="/'.$self->getId().'/edit?id='.$row["apply_id"].'" title="销项票开票修改">修改</a>';
    
    if(count($data)>0)
        $links[] = '<a href="/'.$self->getId().'/detail?id='.$row["apply_id"].'" title="查看详情">详情</a>';
    
    $s = !empty($links) ? implode("&nbsp;&nbsp;", $links) : '';
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

//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
	'items'=>array(
        array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
        array('type'=>'text','key'=>'p.project_code*','text'=>'项目编号'),
        array('type'=>'date','key'=>'start_date','id'=>'datepicker','text'=>'开始日期'),
        array('type'=>'date','key'=>'end_date','id'=>'datepicker2','text'=>'截止日期'),
        array('type'=>'select','key'=>'status','map_name'=>'invoice_open_status','text'=>'开票状态'),
        array('type'=>'text','key'=>'d.name*','text'=>'合作方'),
        array('type'=>'text','key'=>'a.apply_code*','text'=>'发票编号'),
        array('type'=>'select','key'=>'a.contract_type','map_name'=>'goods_contract_type','text'=>'货款合同类型'),
        array('type'=>'text','key'=>'c.contract_code*','text'=>'货款合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type'=>'text','key'=>'a.invoice_contract_code*','text'=>'发票合同编号'),
        array('type'=>'select','key'=>'a.invoice_contract_type','map_name'=>'contract_category','text'=>'发票合同类型'),
    ),
);

//列表显示
$array =array(
    array('key'=>'apply_code','type'=>'href','style'=>'width:140px;text-align:left','text'=>'发票编号','href_text'=>'invoiceApplyAction'),
    array('key'=>'type','type'=>'map_val','style'=>'width:90px;text-align:left','map_name'=>'invoice_type','text'=>'发票类型'),
    array('key'=>'type_sub','type'=>'map_val','style'=>'width:100px;text-align:left','map_name'=>'invoice_output_type','text'=>'发票属性'),
    array('key'=>'user_name','type'=>'','style'=>'width:100px;text-align:left','text'=>'申请人'),
    array('key'=>'status','type'=>'map_val','text'=>'开票状态','map_name'=>'invoice_open_status','style'=>'width:80px;text-align:left'),
    array('key'=>'project_id,project_code','type'=>'href','style'=>'width:140px;text-align:left','text'=>'项目编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank"  href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'project_type','type'=>'map_val','style'=>'width:100px;text-align:left;','map_name'=>'project_type','text'=>'项目类型'),
    array('key'=>'corporation_name','type'=>'','style'=>'text-align:left;','text'=>'交易主体'),
    array('key'=>'partner_name','type'=>'','style'=>'text-align:left;','text'=>'合作方'),
    array('key'=>'contract_type','type'=>'map_val','style'=>'width:100px;text-align:left;','map_name'=>'goods_contract_type','text'=>'货款合同类型'),
    array('key'=>'contract_id,contract_code','type'=>'href','style'=>'width:140px;text-align:left','text'=>'货款合同编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank"  href="/contract/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:left', 'text' => '外部合同编号'),
    array('key'=>'invoice_contract_type','type'=>'map_val','style'=>'width:100px;text-align:left;','map_name'=>'contract_category','text'=>'发票合同类型'),
    array('key'=>'invoice_contract_code','type'=>'','style'=>'width:120px;text-align:left','text'=>'发票合同编号'),
    array('key'=>'num','type'=>'','style'=>'width:80px;text-align:left','text'=>'发票数量'),
    array('key'=>'create_time','type'=>'date','style'=>'width:140px;text-align:left','text'=>'申请时间'),
    array('key'=>'amount','type'=>'amount','text'=>'发票金额','style'=>'width:120px;text-align:right'),
    array('key'=>'apply_id','type'=>'href','style'=>'width:80px;text-align:left;','text'=>'操作','href_text'=>'checkRowEditAction'),
);

$headerArray = [];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);

?>