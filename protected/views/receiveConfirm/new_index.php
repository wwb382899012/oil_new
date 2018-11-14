<?php
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'items' => array(
       array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
       array('type' => 'text', 'key' => 'a.pay_partner*', 'text' => '付款公司'),
       array('type' => 'date', 'key' => 'a.receive_date>', 'text' => '收款开始时间','id'=>'startDate'),
       array('type' => 'date', 'key' => 'a.receive_date<', 'text' => '收款结束时间','id'=>'endDate'),
       array('type' => 'select', 'key' => 'a.status', 'map_name' => 'bank_flow_status', 'text' => '状态'),
       array('type' => 'text', 'key' => 'a.code*', 'text' => '银行流水编号'),
       array('type' => 'text', 'key' => 'a.bank_name*', 'text' => '收款银行'),
       array('type' => 'text', 'key' => 'a.account_name*', 'text' => '银行账户名'),
   )
);

//列表显示
$array = array(
    array('key' => 'status', 'type' => 'map_val', 'style' => 'width:80px;text-align:left', 'text' => '状态', 'map_name' => 'bank_flow_status'),
    array('key' => 'receive_date', 'type' => 'text', 'style' => 'width:00px;text-align:left', 'text' => '收款时间'),
    array('key' => 'flow_id', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '收款金额', 'href_text' => 'getRowAmount'),
    array('key' => 'pay_partner', 'type' => 'text', 'style' => 'width:240px;text-align:left', 'text' => '付款公司'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" target="_blank" title="交易主体详情" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'flow_id', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => 'ID'),
    array('key' => 'code', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '银行流水编号'),
    array('key' => 'bank_name', 'type' => 'text', 'style' => 'width:120px;text-align:left', 'text' => '收款银行'),
    array('key' => 'account_name', 'type' => 'text', 'style' => 'width:120px;text-align:left', 'text' => '银行账户名'),
    array('key' => 'pay_bank', 'type' => 'text', 'style' => 'width:240px;text-align:left', 'text' => '付款银行'),
    array('key' => 'flow_id', 'type' => 'href', 'style' => 'width:100px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();
    if($row['status']==BankFlow::STATUS_SUBMITED) {
      $links[] = '<a href="/' . $self->getId() . '/add?flow_id=' . $row["flow_id"] . '" title="认领">认领</a>';
    }
    $links[] = '<a href="/' . $self->getId() . '/view?flow_id=' . $row["flow_id"] . '" title="查看">查看</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

function getRowAmount($row, $self) {
    return $self->map['currency'][$row['currency']]['ico'] . number_format($row["amount"]/100,2);
}

$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);
?>