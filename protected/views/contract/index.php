<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/29 15:26
 * Describe：
 */

function checkRowEditAction($row, $self) {
    $links = array();

    /*if ($self->checkIsCanEdit($row["contract_status"])) {
        if($row['is_main']) {
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["contract_id"] . '&project_id=' . $row["project_id"] . '" title="修改">修改</a>';
        } else {
            $links[] = '<a href="/subContract/edit?id=' . $row["contract_id"] . '" title="修改">修改</a>';
        }
    }*/

    if (!empty($row['contract_id'])) {
        $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["contract_id"] . '" title="查看详情">详情</a>';
    }
    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}
function getGoodsList($row) {
    $goods = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($row['contract_id']));
    return '<abbr title="'.$goods.'">'.$goods.'</abbr>';
}
function getAmountCnyDesc($row) {
    return Map::$v['currency'][$row['currency']]['ico'].Utility::numberFormatFen2Yuan($row['amount_cny']);
}

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(

        array('type' => 'text', 'key' => 'a.contract_code*', 'text' => '合同编号'),
        array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'a.contract_id', 'text' => '合同ID'),
        array('type' => 'select', 'key' => 'a.type', 'map_name' => 'buy_sell_type', 'text' => '合同类型'),
        array('type' => 'text', 'key' => 'p.project_code*', 'text' => '项目编号'),
        array('type' => 'select', 'key' => 'p.type', 'map_name' => 'project_type', 'text' => '项目类型'),
        array('type' => 'text', 'key' => 'd.name*', 'text' => '合作方'),
        array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'contract_status', 'text' => '状态'),
    )
);


//列表显示
$array = array(
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
    array('key' => 'contract_id', 'type' => '', 'style' => 'width:120px;text-align:center', 'text' => '合同ID'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '合同编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/' . $this->getId() . '/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type' => '', 'style' => 'width:120px;text-align:center', 'text' => '外部合同编号' ),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'contract_status', 'style' => 'width:100px;text-align:center', 'text' => '状态'),
    array('key' => 'corporation_id,corp_name','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'交易主体','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'buy_sell_type', 'style' => 'width:100px;text-align:center', 'text' => '合同类型'),
    array('key' => 'partner_id,partner_name','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'project_type', 'type' => 'map_val', 'map_name' => 'project_type', 'style' => 'width:100px;text-align:center', 'text' => '项目类型'),
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '品名', 'href_text' => 'getGoodsList'),
    array('key' => 'name', 'type' => '', 'style' => 'width:100px;text-align:center', 'text' => '项目负责人'),
    array('key' => 'amount_cny', 'type' => 'href', 'style' => 'width:120px;text-align:right', 'text' => '合同总金额', 'href_text' => 'getAmountCnyDesc'),
    array('key' => 'amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right', 'text' => '合同人民币金额'),
);


$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1650px;", "table-bordered table-layout");
?>


