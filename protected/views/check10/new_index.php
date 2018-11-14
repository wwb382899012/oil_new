<?php

/**
 * Desc: 发货单结算审核
 */

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'p.name*', 'text' => '下游合作方'),
        array('type' => 'text', 'key' => 'ct.contract_code*', 'text' => '销售合同编号'),
        array('type' => 'text', 'key' => 'b.code', 'text' => '发货单编号'),
        array('type' => 'text', 'key' => 'c.name*', 'text' => '交易主体'),
        array('type'=>'select', 'key'=>'checkStatus', 'noAll'=>'1', 'map_name'=>'delivery_order_check_status', 'text'=>'审核状态'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'date', 'id'=>'settleStartTime','key' => 's.settle_date>', 'text' => '结算日期'),
        array('type' => 'date','id'=>'settleEndTime', 'key' => 's.settle_date<', 'text' => '到'),
        array('type'=>'select', 'key'=>'b.type', 'map_name'=>'stock_notice_delivery_type', 'text'=>'发货方式'),
    )
);

//列表显示
$array =array(
    array('key'=>'detail_id','type'=>'','style'=>'width:60px;text-align:left','text'=>'审核编号'),
    array('key' => 'order_id,code', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '发货单编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/deliveryOrder/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'settle_date', 'type' => 'text', 'style' => 'width:80px;text-align:left', 'text' => '结算日期'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '销售合同编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type' => 'text', 'style' => 'width:120px;text-align:left', 'text' => '外部合同编号'),
    array('key'=>'corporation_id,corporation_name','type'=>'href','style'=>'width:100px;text-align:left;','text'=>'交易主体','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:160px;text-align:left', 'text' => '下游合作方', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'stock_notice_delivery_type', 'style' => 'width:60px;text-align:left', 'text' => '发货方式'),
    array('key' => 'stock_in_id,stock_in_code', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '入库单编号', 'href_text' => 'getStockIn'),
    // array('key' => 'type', 'type' => 'map_val', 'map_name' => 'delivery_order_check_status', 'style' => 'width:80px;text-align:left;', 'text' => '审核状态'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:80px;text-align:left;','text'=>'审核状态','href_text'=>'FlowService::showCheckStatus'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:40px;text-align:left;','text'=>'操作','href_text'=>'checkRowActions'),
);

function checkRowActions($row, $self) {
    $links = array();
    if ($row["isCanCheck"]) {
        $links[] = '<a href="/' . $self->getId() . '/check?id=' . $row["obj_id"] . '" title="审核">审核</a>';
    } else {
        $links[] = '<a href="/' . $self->getId() . '/detail?detail_id=' . $row["detail_id"] . '" title="查看详情">查看</a>';
    }
    $s = implode("&nbsp;&nbsp;", $links);

    return $s;
}

function getStockIn($row, $self) {
  if($row['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE) {
    return '--';
  } else {
    return "<a target='_blank' id='t_{$row['stock_in_id']}' title='{$row['stock_in_code']}' target='_blank' href='/stockInList/view/?t=1&id={$row['stock_in_id']}'>{$row['stock_in_code']}</a>";
  }
}

$headerArray = [];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);