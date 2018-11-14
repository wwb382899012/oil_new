<?php
/**
 * Created by vector.
 * DateTime: 2017/10/24 15:25
 * Describe：
 */

function checkRowEditAction($row,$self)
{
    $links = array();
    if($self->checkIsCanEdit($row['status'])){
        $links[] = '<a href="/'.$self->getId().'/edit?id='.$row["apply_id"].'" title="修改">修改</a>';
    }

    $links[] = '<a href="/'.$self->getId().'/detail?id='.$row["apply_id"].'" title="查看详情">详情</a>';

    $s = !empty($links) ? implode("&nbsp;&nbsp;", $links) : '';
    return $s;
}
if($_data_['type']==ConstantMap::INPUT_INVOICE_TYPE){
    $button = "进";
    $invoice_type = "invoice_input_type";
}
else{
    $button = "销";
    $invoice_type = "invoice_output_type";
}
function generateAdd($self)
{
    $str = '<div class="dropdown action-more common-dropdown right-open">
                <a href="#" class="oil-btn" data-toggle="dropdown">
                    发票申请 <i class="icon icon-xiala" style="color: white;margin-left: -4px;"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="drop1">';
    $str .= '<li><a href="/'.$self->getId().'/add?type=1">货款类发票申请</a></li>';
    $str .= '<li><a href="/'.$self->getId().'/add?type=2">非货款类发票申请</a></li>';
    $str.="</ul></div>";

    return $str;
}



//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
                    'items'=>array(
                        array('type'=>'text','key'=>'c.contract_code*','text'=>'货款合同编号'),
                        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
                        array('type'=>'text','key'=>'d.name*','text'=>'合作方'),
                        array('type'=>'select','key'=>'a.status','map_name'=>'invoice_apply_status','text'=>'申请状态'),
                        array('type'=>'text','key'=>'a.apply_code*','text'=>'发票编号'),
                        array('type'=>'text','key'=>'p.project_code*','text'=>'项目编号'),
                        array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
                        array('type'=>'select','key'=>'a.contract_type','map_name'=>'goods_contract_type','text'=>'货款合同类型'),
                        array('type'=>'date','key'=>'start_date','id'=>'datepicker','text'=>'开始日期'),
                        array('type'=>'date','key'=>'end_date','id'=>'datepicker2','text'=>'截止日期'),
                        array('type'=>'text','key'=>'a.invoice_contract_code*','text'=>'发票合同编号'),
                        array('type'=>'select','key'=>'a.invoice_contract_type','map_name'=>'contract_category','text'=>'发票合同类型'),
                    ),
);

//列表显示
$array =array(
    array('key'=>'apply_code','type'=>'','style'=>'width:140px;text-align:left','text'=>'发票编号'),
    array('key'=>'type_sub','type'=>'map_val','style'=>'width:80px;text-align:left','map_name'=>$invoice_type,'text'=>'发票属性'),
    array('key'=>'user_name','type'=>'','style'=>'width:100px;text-align:left','text'=>'申请人'),
    array('key'=>'status','type'=>'map_val','text'=>'申请状态','map_name'=>'invoice_apply_status','style'=>'width:80px;text-align:left'),
    array('key'=>'project_id,project_code','type'=>'href','style'=>'width:140px;text-align:left','text'=>'项目编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank"  href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'project_type','type'=>'map_val','style'=>'width:100px;text-align:left;','map_name'=>'project_type','text'=>'项目类型'),
    array('key'=>'corporation_name','type'=>'','style'=>'text-align:left;','text'=>'交易主体'),
    array('key'=>'partner_name','type'=>'','style'=>'text-align:left;','text'=>'合作方'),
    array('key'=>'contract_type','type'=>'map_val','style'=>'width:100px;text-align:left;','map_name'=>'goods_contract_type','text'=>'货款合同类型'),
    array('key'=>'contract_id,contract_code','type'=>'href','style'=>'width:140px;text-align:left','text'=>'货款合同编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank"  href="/contract/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:left', 'text' => '外部合同编号'),
    array('key'=>'invoice_contract_type','type'=>'map_val','style'=>'width:100px;text-align:left;','map_name'=>'contract_category','text'=>'发票合同类型'),
    array('key'=>'invoice_contract_code','type'=>'','style'=>'width:100px;text-align:left','text'=>'发票合同编号'),
    array('key'=>'create_time','type'=>'date','style'=>'width:140px;text-align:left','text'=>'申请时间'),
    array('key'=>'amount','type'=>'amount','text'=>'发票金额','style'=>'width:120px;text-align:right'),
);

if($_data_['type']==ConstantMap::OUTPUT_INVOICE_TYPE){
    $array[]= array('key'=>'num','type'=>'','style'=>'width:80px;text-align:left','text'=>'发票数量');
}
$array[]= array('key'=>'apply_id','type'=>'href','style'=>'width:80px;text-align:left;','text'=>'操作','href_text'=>'checkRowEditAction');

$buttons = [
    ['type' => 'custom', 'content' => 'generateAdd']
];
$headerArray = ['button_config' => $buttons];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);

?>
