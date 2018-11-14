<?php
/**
 * Created by youyi000.
 * DateTime: 2016/6/21 15:25
 * Describe：
 */

function checkRowEditAction($row, $self)
{
    return '<a href="/'.$self->getId().'/add?id='.$row["apply_id"].'" title="付款止付">止付</a>';;
}

function showPayAmount($row, $self)
{
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($row["amount"]/100,2);
    return $str;
}
function showPaidAmount($row, $self)
{
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($row["amount_paid"]/100,2);
    return $str;
}

function showStopAmount($row, $self)
{
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($row["amount_stop"]/100,2);
    return $str;
}

function showContractType($row, $self)
{
    $str=$self->map["contract_config"][$row['contract_type']][$row['contract_category']]["name"];
    return $str;
}

//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/list/',
	'items'=>array(
        array('type'=>'text','key'=>'a.apply_id','text'=>'付款申请编号'),
        array('type' => 'select', 'key' => 'a.type', 'map_name' => 'pay_application_type', 'text' => '付款类型'),
        array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
        array('type'=>'text','key'=>'a.payee*','text'=>'收款单位'),
        array('type'=>'select','key'=>'co.category','map_name'=>'contract_category','text'=>'货款合同类型'),
        array('type'=>'text','key'=>'co.contract_code*','text'=>'货款合同编号'),
        // array('type'=>'subjectName','key'=>'a.subject_id','text'=>'用途'),
        array('type' => 'select', 'key' => 'a.subject_id', 'map_name' => 'finance_subjects', 'text' => '用途'),
        array('type'=>'select','key'=>'a.sub_contract_type','map_name'=>'contract_category','text'=>'付款合同类型'),
        array('type'=>'text','key'=>'a.sub_contract_code*','text'=>'付款合同编号'),
        array('type'=>'date','key'=>'start_date','id'=>'datepicker','text'=>'开始日期'),
        array('type'=>'date','key'=>'end_date','id'=>'datepicker2','text'=>'结束日期'),
        array('type' => 'text', 'key' => 'su.name*', 'text' => '申请人'),
    )
);

//列表显示
$array = array(
    array('key' => 'apply_id,apply_id', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '付款申请编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/pay/detail/?t=1&id={1}">{1}</a>'),
    array('key' => 'amount', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '付款金额', 'href_text' => 'showPayAmount'),
    array('key' => 'amount_paid', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '实付金额', 'href_text' => 'showPaidAmount'),
    array('key' => 'amount_stop', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '止付金额', 'href_text' => 'showStopAmount'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'pay_application_type', 'style' => 'width:120px;text-align:center', 'text' => '付款类型'),
    array('key' => 'payee', 'type' => '', 'style' => 'width:120px;text-align:center', 'text' => '收款单位'),
    array('key' => 'subject_name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '用途'),
    array('key' => 'contract_type', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '货款合同类型', 'href_text' => 'showContractType'),
    // array('key' => 'contract_type', 'type' => 'map_val', 'map_name' => 'contract_category', 'style' => 'width:140px;text-align:center', 'text' => '货款合同类别'),
    array('key' => 'contract_code', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '货款合同编号'),
    array('key' => 'sub_contract_type', 'type' => 'map_val', 'map_name' => 'contract_category', 'style' => 'width:140px;text-align:center', 'text' => '付款合同类别'),
    array('key' => 'sub_contract_code', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '付款合同编号'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '申请人'),
    array('key' => 'create_time', 'type' => '', 'style' => 'text-align:center', 'text' => '付款申请时间'),
    array('key' => 'project_id', 'type' => 'href', 'style' => 'width:60px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),

);

$headerArray = ['is_show_back_bread' => true];
$searchArray = ['search_config' => $form_array, 'is_show_reset_button' => true];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>

<script type="text/javascript">
    $(function () {
        $("#back").click(function () {
           location.href="/<?php echo $this->getId() ?>/";
        });
    });
</script>
