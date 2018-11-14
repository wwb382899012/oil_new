<?php

function getInterestAction($row, $self) {
    $interest = '￥'.number_format(round($row['interest_pay'] - $row['interest_receive'])/100, 2);
    return $interest;
}


$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'i.corporation_name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'i.user_name*', 'text' => '业务负责人'),
        array('type' => 'text', 'key' => 'i.project_code*', 'text' => '项目编号')
    )
);

//列表显示
$array = array(
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:150px;text-align:left;', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'user_name', 'type' => '', 'style' => 'width:80px;text-align:left;', 'text' => '业务负责人'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:180px;text-align:left;', 'text' => '项目编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'amount_receive', 'type' => 'amount', 'style' => 'width:150px;text-align:right;', 'text' => '累计收款金额'),
    array('key' => 'amount_pay', 'type' => 'amount', 'style' => 'width:150px;text-align:right;', 'text' => '累计实付金额'),
    array('key' => 'interest_pay', 'type' => 'amount', 'style' => 'width:150px;text-align:right;', 'text' => '累计实付利息'),
    array('key' => 'interest_receive', 'type' => 'amount', 'style' => 'width:150px;text-align:right;', 'text' => '累计收款利息'),
    array('key' => 'interest_pay', 'type' => 'href', 'style' => 'width:150px;text-align:right;', 'text' => '合计利息', 'href_text' => 'getInterestAction'),    
);

$headerArray = ['is_show_export' => true];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray  = ['column_config' => $array, 'float_columns'=>0];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>
