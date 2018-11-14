<?php
/**
 * Desc: 发货单
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'b.name*', 'text' => '下游合作方'),
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'delivery_order_status', 'text' => '状态'),
        array('type' => 'text', 'key' => 'e.contract_code', 'text' => '销售合同编号'),
        array('type' => 'text', 'key' => 'd.code', 'text' => '入库单编号'),
        //array('type' => 'select', 'key' => 'a.type', 'map_name' => 'stock_notice_delivery_type', 'text' => '发货方式'),
        //array('type' => 'text', 'key' => 'c.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'a.code', 'text' => '发货单编号'),
        array('type' => 'select', 'key' => 'a.is_virtual', 'map_name' => 'split_type_enum', 'text' => '是否平移生成'),
    ),
);

//列表显示
$array = array(
    array('key' => 'order_id,code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:100px;', 'text' => '发货单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/deliveryOrder/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:160px;', 'text' => '销售合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail?id={1}&t=1">{2}</a>'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:200px;', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'text' => '下游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'stock_notice_delivery_type', 'style' => 'width:60px;', 'text' => '发货方式'),
    array('key' => 'stock_in_id,stock_in_code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:180px;', 'text' => '入库单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/stockInList/view/?id={1}&t=1">{2}</a>'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'delivery_order_status', 'style' => 'width:140px;', 'text' => '状态'),
    array('key' => 'is_virtual', 'type' => 'map_val', 'map_name' => 'split_type_enum', 'style' => 'width:75px;text-align:center', 'text' => '是否平移生成'),
    array('key' => 'order_id', 'type' => 'href', 'style' => 'min-width:80px;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();
    if (!empty($row['order_id'])) {
        if ($row['status'] < DeliveryOrder::STATUS_SUBMIT) {
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["order_id"] . '" title="修改">修改</a>';
        }
        $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["order_id"] . '" title="查看详情">详情</a>';
    }

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

$searchArray = ['search_config' => $form_array];
$tableArray = ['column_config' => $array];
$headerArray = ['button_config' => [
    ['text' => '新建经仓发货单',
    'attr' => [
        'onclick' => "location.href='/".$this->getId()."/selectContracts'",
    ]],
    ['text' => '新建直调发货单',
     'attr' => [
         'onclick' => "location.href='/".$this->getId()."/selectStockIns'",
     ]]
]];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);