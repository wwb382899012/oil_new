<?php 
/**
 * Desc:
 * User:  vector
 * Date: 2018/6/11
 * Time: 14:35
 */

$corpNameLink = '<a target="_blank" class="text-link" title="' . $_data_['payment']['corporation_name'] . '" href="/corporation/detail/?id=' . $_data_['payment']['corporation_id'] . '&t=1">' . $_data_['payment']['corporation_name'] . '</a>';
$userName     = $_data_['payment']['user_name'];
$projectLink  = '<a class="text-link" target="_blank" title="' . $_data_['payment']['project_code'] . '" href="/project/detail/?id=' . $_data_['payment']['project_id'] . '&t=1">' . $_data_['payment']['project_code'] . '</a>';
$contractLink = '<a class="text-link" target="_blank" title="' . $_data_['payment']['contract_code'] . '" href="/contract/detail/?id=' . $_data_['payment']['contract_id'] . '&t=1">' . $_data_['payment']['contract_code'] . '</a>';
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
?>
<div class="content-wrap">
    <ul class="item-com">
        <li>
            <label for="type">交易主体：</label>
            <div>
                <p><?php echo $corpNameLink;?></p>
            </div>
        </li>
        
        <li>
            <label for="type">业务负责人：</label>
            <div>
                <pmust-logo><?php echo $userName;?></p>
            </div>
        </li>
        <li>
            <label for="type">项目编号：</label>
            <div>
                <pmust-logo><?php echo $projectLink;?></p>
            </div>
        </li>
        <li>
            <label for="type">合同编号：</label>
            <div>
                <pmust-logo><?php echo $contractLink;?></p>
            </div>
        </li>
        <li>
            <label for="type">合同签约总额：</label>
            <div>
                <pmust-logo><?php echo $amount_sign;?></p>
            </div>
        </li>
        <li>
            <label for="type">合计利息：</label>
            <div>
                <pmust-logo><?php echo $interest;?></p>
            </div>
        </li>
        <li>
            <label for="type">停息日期：</label>
            <div>
                <pmust-logo><?php echo $stop_date;?></p>
            </div>
        </li>
        <li>
            <label for="type">停息操作人：</label>
            <div>
                <pmust-logo><?php echo $operatorName;?></p>
            </div>
        </li>
    </ul>
    <ul class="item-com">
        <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
            <label>停息理由：</label>
            <p class="form-control-static form-control-static-custom"><?php echo $stop_reason;?></p>
        </li>
    </ul>
</div>

<?php

$form_array = array(
    'form_url' => '/' . $this->getId() . '/detail/',
    'items' => array(
        array('type' => 'date','id'=>'startTime',  'key' => 'interest_date>', 'text' => '开始日期'),
        array('type' => 'date','id'=>'endTime',  'key' => 'interest_date<', 'text' => '结束日期'),
        array('type' => 'hidden', 'key' => 'contract_id', 'value' => $contract_id),
    ),
);

//列表显示
$array = array(
    array('key' => 'interest_date', 'type' => 'date', 'style' => 'text-align:center;', 'text' => '时间'),
    array('key' => 'amount_goods', 'type' => 'amount', 'style' => 'text-align:right;', 'text' => $amount_desc),
    array('key' => 'amount_actual', 'type' => 'amount', 'style' => 'text-align:right;', 'text' => $payment_desc),
    array('key' => 'amount_day', 'type' => 'amount', 'style' => 'text-align:right;', 'text' => $amount_day),
    array('key' => 'interest_day', 'type' => 'amount', 'style' => 'text-align:right;', 'text' => '日息'),
);


$headerArray = ['is_show_export' => true, 'is_show_back_bread'=>'/' . $this->getId() . '/', 'export_action'=>'detailExport'];
$searchArray = ['search_config' => $form_array, 'search_lines' =>2];
$tableArray  = ['column_config' => $array, 'float_columns'=>0];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>



