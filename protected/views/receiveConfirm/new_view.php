<?php
//列表显示
$array = array(
    array('key' => 'receive_id', 'type' => 'text', 'style' => 'width:140px;', 'text' => '收款编号'),
    array('key' => 'user_name', 'type' => 'text', 'style' => 'width:140px;', 'text' => '认领人'),
    array('key'=>'status','style'=>'width:100px;','text'=>'状态', 'type'=>'map_val', 'map_name'=>'receive_confirm_status'),
    array('key' => 'project_code', 'type' => 'text', 'style' => 'width:140px;', 'text' => '项目编号'),
    array('key' => 'project_type', 'type' => 'map_val', 'style' => 'width:80px;', 'text' => '项目类型', 'map_name' => 'project_type'),
    array('key' => 'contract_type', 'type' => 'map_val', 'style' => 'width:80px;', 'text' => '合同类型', 'map_name' => 'buy_sell_type'),
    array('key' => 'contract_code', 'type' => 'text', 'style' => 'width:140px;', 'text' => '货款合同编号'),
    array('key' => 'sub_contract_code', 'type' => 'text', 'style' => 'width:140px;', 'text' => '收款合同编号'),
    array('key' => 'subject_name', 'type' => 'text', 'style' => 'width:80px;', 'text' => '用途'),
    array('key' => 'currency', 'type' => 'map_val', 'style' => 'width:80px;', 'text' => '币种', 'map_name' => 'currency_type'),
    array('key' => 'amount', 'type' => 'amount', 'style' => 'width:140px;', 'text' => '金额'),
    array('key' => 'create_time', 'type' => 'text', 'style' => 'width:140px;', 'text' => '认领时间'),
    array('key' => 'flow_id', 'type' => 'href', 'style' => 'width:100px;;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();

    if($row['status'] == ReceiveConfirm::STATUS_NEW) {
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["receive_id"] . '" title="修改">修改</a>';
    }
    $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["receive_id"] . '" title="查看">查看</a>';
    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

function getRowAmount($row, $self) {
    return $self->map['currency'][$row['currency']]['ico'] . $row['amount'];
}

$headerArray = ['button_config' => $buttonArray, 'is_show_back_bread' => true];
$searchArray = [];
$tableArray = ['column_config' => $array, 'is_show_page' => false];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>
