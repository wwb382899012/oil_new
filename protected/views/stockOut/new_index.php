<?php
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'items' => array(
       array('type' => 'text', 'key' => 'c.name*', 'text' => '下游合作方'),
       array('type' => 'select', 'key' => 'a.type', 'map_name' => 'stock_notice_delivery_type', 'text' => '发货方式'),
       array('type' => 'text', 'key' => 'a.code', 'text' => '发货单编号'),
       array('type' => 'text', 'key' => 'd.name*', 'text' => '交易主体'),
       array('type' => 'select', 'key' => 'a.is_virtual', 'map_name' => 'split_type_enum', 'text' => '是否平移生成'),
   )
);

//列表显示
$array = array(
    array('key' => 'order_id,code', 'class' => 'no-ellipsis', 'type' => 'href', 'style' => 'min-width:80px;text-align:left', 'text' => '发货单编号', 'href_text' => '<a id="t_{1}" title="{2}" href="/deliveryOrder/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'min-width:140px;text-align:left', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'min-width:140px;text-align:left', 'text' => '下游合作方', 'href_text' => '<a id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'stock_notice_delivery_type', 'style' => 'min-width:60px;text-align:left', 'text' => '发货方式'),
    array('key' => 'stock_in_id,stock_in_code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'min-width:100px;text-align:left', 'text' => '入库单编号', 'href_text' => 'getStockIn'),
    array('key' => 'is_virtual', 'type' => 'map_val', 'map_name' => 'split_type_enum', 'style' => 'width:55px;text-align:center', 'text' => '是否平移生成'),
    array('key' => 'order_id', 'type' => 'href', 'style' => 'min-width:60px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();
    if (DeliveryOrderService::isCanAddStockOutOrder($row['type'],$row['status'],$row['is_virtual'])) {
        $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["order_id"] . '" title="添加">添加</a>';
    }
    $links[] = '<a href="/' . $self->getId() . '/list?id=' . $row["order_id"] . '" title="查看出库单详情">详情</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

function getStockIn($row, $self) {
  if($row['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE) {
    return '--';
  } else {
    return "<a id='t_{$row['stock_in_id']}' title='{$row['stock_in_code']}' href='/stockInList/view/?id={$row['stock_in_id']}&t=1'>{$row['stock_in_code']}</a>";
  }
}

$searchArray = ['search_config' => $form_array];
$tableArray = ['column_config' => $array, 'attr' => ['style' => 'width: 100%']];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);