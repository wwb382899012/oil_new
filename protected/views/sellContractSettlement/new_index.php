<?php
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'items' => array(
       array('type' => 'text', 'key' => 'c.name*', 'text' => '下游合作方'),
       array('type' => 'text', 'key' => 'a.contract_code', 'text' => '销售合同编号'),
       array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
       array('type' => 'select', 'map_name'=>'delivery_settlement_status', 'key' => 'status', 'text' => '状态'),
       array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
       array('type' => 'text', 'key' => 'b.project_code', 'text' => '项目编号'),
       // array('type' => 'select', 'map_name'=>'contract_category_sell_type', 'key' => 'a.category', 'text' => '合同类型'),
       array('type' => 'date', 'id'=>'settleStartTime','key' => 's.settle_date>', 'text' => '结算日期'),
       array('type' => 'date','id'=>'settleEndTime', 'key' => 's.settle_date<', 'text' => '到'),
       array('type' => 'text', 'key' => 'e.name*', 'text' => '合同负责人'),
   )
);

//列表显示
$array = array(
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:;left', 'text' => '销售合同编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/contract/detail/?id={1}&t=1" target="_blank">{2}</a>'),
    array('key' => 'settle_date', 'type' => 'text', 'style' => 'width:140px;text-align:;left', 'text' => '结算日期'),
    array('key' => 'code_out', 'type' => 'text', 'style' => 'width:120px;text-align:;left', 'text' => '外部合同编号'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:;left', 'text' => '项目编号', 'href_text' => '<a id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1" target="_blank">{2}</a>'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:140px;text-align:;left', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1" target="_blank">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:;left', 'text' => '下游合作方', 'href_text' => '<a id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1" target="_blank">{2}</a>'),
    array('key' => 'category', 'type' => 'map_val', 'map_name' => 'contract_category_sell_type', 'style' => 'width:60px;text-align:;left', 'text' => '合同类型'),
    array('key' => 'manager_user_name', 'type' => 'text', 'style' => 'width:120px;text-align:;left', 'text' => '合同负责人'),
    array('key' => 'status_desc', 'type' => 'map_val', 'map_name' => 'delivery_settlement_status', 'style' => 'width:80px;text-align:;left', 'text' => '状态'),
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'width:100px;text-align:;left', 'text' => '操作', 'href_text' => 'getRowActions'),

);

function getRowActions($row, $self) {
    $links = array();
    if(empty($row['settle_id']))
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["contract_id"] . '" title="结算">结算</a>';
    else{
        if($row['settle_status']!=\ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_NEW){//已经结算过
            if(\ddd\application\contractSettlement\SettleService::settlementIsCanEdit($row["settle_status"]))
                $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["contract_id"] . '" title="修改">修改</a>';
            if(!empty($row['settle_type'])){
                $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["contract_id"] . '" title="查看">查看</a>';
            }
        }else{
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["contract_id"] . '" title="结算">结算</a>';
        }
        
        
       
    }
    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

$headerArray = [];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);