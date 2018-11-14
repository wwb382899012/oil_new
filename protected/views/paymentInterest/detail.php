<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/6/11
 * Time: 14:35
 */

$corpNameLink = '<a target="_blank" title="' . $_data_['payment']['corporation_name'] . '" href="/corporation/detail/?id=' . $_data_['payment']['corporation_id'] . '&t=1">' . $_data_['payment']['corporation_name'] . '</a>';
$userName     = $_data_['payment']['user_name'];
$projectLink  = '<a target="_blank" title="' . $_data_['payment']['project_code'] . '" href="/project/detail/?id=' . $_data_['payment']['project_id'] . '&t=1">' . $_data_['payment']['project_code'] . '</a>';
$contractLink = '<a target="_blank" title="' . $_data_['payment']['contract_code'] . '" href="/contract/detail/?id=' . $_data_['payment']['contract_id'] . '&t=1">' . $_data_['payment']['contract_code'] . '</a>';
$amount_sign  = '￥' . number_format($_data_['payment']['amount_sign'] / 100, 2) . '元';
$interest     = '￥' . number_format($_data_['payment']['interest'] / 100, 2) . '元';
$stop_date    = empty($_data_['payment']['stop_date']) ? '-' : $_data_['payment']['stop_date'];
$operatorName = empty($_data_['payment']['operator_name']) ? '-' : $_data_['payment']['operator_name'];
$stop_reason  = empty($_data_['payment']['stop_reason']) ? '-' : $_data_['payment']['stop_reason'];
$type         = $_data_['search']['contract_type'];
$contract_id  = $_data_['search']['contract_id'];

if($type==ConstantMap::BUY_TYPE){
    $amount_desc  = "已入库货值";
    $payment_desc = "累计实付金额";
    $amount_day   = "当日实付金额";
}else{
    $amount_desc  = "已出库货值";
    $payment_desc = "累计收款金额";
    $amount_day   = "当日收款金额";
}

$form_array = array(
    'form_url' => '/' . $this->getId() . '/detail/',
    'input_array' => array(
        array('type' => 'info', 'text' => $corpNameLink, 'label' => '交易主体'),
        array('type' => 'info', 'text' => $userName, 'label' => '业务负责人&emsp;'),
        array('type' => 'info', 'text' => $projectLink, 'label' => '项目编号'),
        array('type' => 'info', 'text' => $contractLink, 'label' => '合同编号'),
        array('type' => 'info', 'text' => $amount_sign, 'label' => '合同签约总额'),
        array('type' => 'info', 'text' => $interest, 'label' => '合计利息'),
        array('type' => 'info', 'text' => $stop_date, 'label' => '停息日期'),
        array('type' => 'info', 'text' => $operatorName, 'label' => '停息操作人&emsp;'),
        array(),
        array('type' => 'textarea', 'text' => $stop_reason, 'label' => '停息理由'),
        array(),
        array(),
        array('type' => 'date','id'=>'startTime',  'key' => 'interest_date>', 'text' => '日期&nbsp;&nbsp;'),
        array('type' => 'date','id'=>'endTime',  'key' => 'interest_date<', 'text' => '到&emsp;&emsp;&emsp;&nbsp;&nbsp;'),
        array('type' => 'hidden', 'key' => 'contract_id', 'value' => $contract_id),
        // array('type' => 'hidden', 'key' => 'contract_type', 'value' => $type),
    ),
    'buttonArray' => array(
        array('text' => '导出', 'buttonId' => 'exportButton'),
        array('text' => '返回', 'buttonId' => 'backButton', 'class'=>'btn btn-default btn-sm'),
    ),
);

//列表显示
$array = array(
    array('key' => 'interest_date', 'type' => 'date', 'style' => 'width:150px;text-align:center;', 'text' => '时间'),
    array('key' => 'amount_goods', 'type' => 'amount', 'style' => 'width:200px;text-align:right;', 'text' => $amount_desc),
    array('key' => 'amount_actual', 'type' => 'amount', 'style' => 'width:200px;text-align:right;', 'text' => $payment_desc),
    array('key' => 'amount_day', 'type' => 'amount', 'style' => 'width:200px;text-align:right;', 'text' => $amount_day),
    array('key' => 'interest_day', 'type' => 'amount', 'style' => 'width:200px;text-align:right;', 'text' => '日息'),
);

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_['data'], "", "min-width:1050px;", "table-bordered table-layout", "", true);
?>

<script>
    $(function () {
        $("#interest_date").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});

        $("#exportButton").click(function(){
            var formData= $(this).parents("form.search-form").serialize();
            location.href="/<?php echo $this->getId() ?>/detailExport?"+formData;
        });

        $("#backButton").click(function(){
            location.href="/<?php echo $this->getId() ?>/";
        });
    })
</script>
