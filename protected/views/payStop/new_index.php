<?php
/**
 * Created by youyi000.
 * DateTime: 2016/6/21 15:25
 * Describe：
 */

function checkRowEditAction($row, $self)
{
    $links = array();
    if($row['stop_status']==PayApplicationExtra::STATUS_BACK ){
        if($row['status']==PayApplication::STATUS_CHECKED)
            $links[] = '<a href="/'.$self->getId().'/edit?id='.$row["apply_id"].'" title="止付修改">修改</a>';
        $links[] = '<a onclick="trash('.$row["apply_id"].')" title="止付作废" href="#">作废</a>';
    }
    
    if(!empty($row['stop_status']))
        $links[] = '<a href="/'.$self->getId().'/detail?id='.$row["apply_id"].'" title="查看详情">详情</a>';
    
    $s = !empty($links) ? implode("&nbsp;&nbsp;", $links) : '';
    return $s;
    
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
$form_array = array('form_url'=>'/'.$this->getId().'/',
	'items'=>array(
        array('type'=>'text','key'=>'a.apply_id','text'=>'付款申请编号'),
        array('type' => 'select', 'key' => 'a.type', 'map_name' => 'pay_application_type', 'text' => '付款类型'),
        array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
        array('type'=>'text','key'=>'a.payee*','text'=>'收款单位'),
        array('type'=>'select','key'=>'co.category','map_name'=>'contract_category','text'=>'货款合同类型'),
        array('type'=>'text','key'=>'co.contract_code*','text'=>'货款合同编号'),
        array('type' => 'select', 'key' => 'a.subject_id', 'map_name' => 'finance_subjects', 'text' => '用途'),
        array('type'=>'select','key'=>'a.sub_contract_type','map_name'=>'contract_category','text'=>'付款合同类型'),
        array('type'=>'text','key'=>'a.sub_contract_code*','text'=>'付款合同编号'),
        array('type' => 'select', 'key' => 'e.status', 'map_name'=>'pay_stop_status', 'text' => '状态'),
        array('type' => 'text', 'key' => 'p.project_code*', 'text' => '项目编号'),
        array('type' => 'text', 'key' => 'su.name*', 'text' => '申请人'),
        array('type'=>'date','key'=>'start_date','id'=>'datepicker','text'=>'开始日期'),
        array('type'=>'date','key'=>'end_date','id'=>'datepicker2','text'=>'结束日期'),

    )
);

$buttonArray = [
    ['text' => '付款止付',
     'attr' => [
         'id' => 'list',
         'onclick' => "location.href='/".$this->getId()."/list'",],
    ]
];

//列表显示
$array = array(
    array('key' => 'stop_code', 'type' => '', 'style' => 'width:120px;text-align:left', 'text' => '止付编号'),
    array('key' => 'apply_id,apply_id', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '付款申请编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/pay/detail/?t=1&id={1}">{1}</a>'),
    array('key' => 'stop_status', 'type' => 'map_val', 'map_name' => 'pay_stop_status', 'style' => 'width:100px;text-align:left', 'text' => '状态'),
    array('key' => 'amount,amount', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '付款金额', 'href_text' => 'showPayAmount'),
    array('key' => 'amount_paid,amount_paid', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '实付金额', 'href_text' => 'showPaidAmount'),
    array('key' => 'amount_stop,amount_stop', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '止付金额', 'href_text' => 'showStopAmount'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'pay_application_type', 'style' => 'width:120px;text-align:left', 'text' => '付款类型'),
    array('key' => 'payee', 'type' => '', 'style' => 'width:120px;text-align:left', 'text' => '收款单位'),
    array('key' => 'subject_name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '用途'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    // array('key' => 'contract_type', 'type' => 'href', 'style' => 'width:120px;text-align:left;', 'text' => '货款合同类型', 'href_text' => 'showContractType'),
    array('key' => 'contract_category', 'type' => 'map_val', 'map_name' => 'contract_category', 'style' => 'width:140px;text-align:left', 'text' => '货款合同类别'),
    array('key' => 'contract_code', 'type' => '', 'style' => 'width:140px;text-align:left', 'text' => '货款合同编号'),
    array('key' => 'sub_contract_type', 'type' => 'map_val', 'map_name' => 'contract_category', 'style' => 'width:140px;text-align:left', 'text' => '付款合同类别'),
    array('key' => 'sub_contract_code', 'type' => '', 'style' => 'width:140px;text-align:left', 'text' => '付款合同编号'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '申请人'),
    array('key' => 'stop_apply_time', 'type' => '', 'style' => 'text-align:left', 'text' => '止付申请时间'),
    // array('key' => 'exchange_rate', 'type' => '', 'style' => 'text-align:left', 'text' => '汇率'),
    array('key' => 'apply_id', 'type' => 'href', 'style' => 'width:80px;text-align:left;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
);

$headerArray = ['button_config' => $buttonArray, 'is_show_export' => true];
$searchArray = ['search_config' => $form_array];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>

<script type="text/javascript">
    function trash(id) {
		inc.vueConfirm({
			content: "您确定要作废当前信息吗，该操作不可逆？", onConfirm: function(index) {
				var formData = {id: id};
				$.ajax({
					type: 'POST',
					url: '/payStop/trash',
					data: formData,
					dataType: "json",
					success: function (json) {
						if (json.state == 0) {
							inc.vueMessage({duration: 500,message: "操作成功", onClose: function () {
									location.reload();
								}
							});
						}
						else {
							inc.vueAlert(json.data);
						}
					},
					error: function (data) {
						inc.vueAlert("操作失败！" + data.responseText);
					}
				});
			}
        });
    }
</script>
