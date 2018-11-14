<?php
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'items' => array(
       array('type' => 'text', 'key' => 'c.name*', 'text' => '下游合作方'),
       array('type' => 'text', 'key' => 'b.contract_code', 'text' => '销售合同编号'),
       array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
       array('type' => 'select', 'map_name'=>'stock_notice_delivery_type', 'key' => 'a.type', 'text' => '发货方式'),
       array('type' => 'select', 'map_name'=>'delivery_settlement_status', 'key' => 'status', 'text' => '状态'),
       array('type' => 'text', 'key' => 'p.project_code', 'text' => '项目编号'),
       array('type' => 'text', 'key' => 'd.name*', 'text' => '交易主体'),
       array('type' => 'text', 'key' => 'a.code', 'text' => '发货单编号'),
       //array('type' => 'select', 'map_name'=>'contract_category_sell_type', 'key' => 'b.category', 'text' => '合同类型'),
       // array('type' => 'text', 'key' => 'u.name', 'text' => '合同负责人'),
       array('type' => 'date', 'id'=>'settleStartTime','key' => 's.settle_date>', 'text' => '结算日期'),
       array('type' => 'date','id'=>'settleEndTime', 'key' => 's.settle_date<', 'text' => '到'),

       
   )
);

//列表显示
$array = array(
    array('key' => 'order_id,code', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '发货单编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/deliveryOrder/detail/?t=1&id={1}">{2}</a>'),
    array('key' => 'settle_date', 'type' => 'text', 'style' => 'width:120px;text-align:left', 'text' => '结算日期'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '项目编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?t=1&id={1}">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '销售合同编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/contract/detail/?t=1&id={1}">{2}</a>'),
    array('key' => 'code_out', 'type' => 'text', 'style' => 'width:120px;text-align:left', 'text' => '外部合同编号'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?t=1&id={1}">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '下游合作方', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?t=1&id={1}">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'stock_notice_delivery_type', 'style' => 'width:60px;text-align:left', 'text' => '发货方式'),
    array('key' => 'stock_in_id,stock_in_code', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '入库单编号', 'href_text' => 'getStockIn'),
    array('key' => 'status_desc', 'type' => 'map_val', 'map_name' => 'delivery_settlement_status', 'style' => 'width:80px;text-align:left', 'text' => '状态'),
    array('key' => 'order_id', 'type' => 'href', 'style' => 'width:80px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();
    if(empty($row['settle_id']))
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["order_id"] . '" title="结算">结算</a>';
    else{
        if($row['settle_status']!=\ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_NEW){
            if(\ddd\application\contractSettlement\SettleService::settlementIsCanEdit($row["settle_status"]))
                $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["order_id"] . '" title="修改">修改</a>';
            if(!empty($row['settle_type'])){
                if($row['settle_type']==\ddd\domain\entity\contractSettlement\SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT)
                    $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["order_id"] . '" title="查看">查看</a>';
                else //待修改
                    $links[] = '<a href="/sellContractSettlement/detail?id=' . $row["contract_id"] . '" title="查看">查看</a>';
            }
        }else{
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["order_id"] . '" title="结算">结算</a>';
        }
        
    }

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

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