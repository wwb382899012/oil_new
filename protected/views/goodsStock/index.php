<?php
/**
 * Desc: 商品库存查询
 * User: susiehuang
 * Date: 2017/11/14 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'input_array' => array(
       array('type' => 'text', 'key' => 'c.name*', 'text' => '交易主体'),
       array('type' => 'text', 'key' => 'g.name*', 'text' => '品名'),
   )
);

//列表显示
$array = array(
    array('key' => 'corporation_id,goods_id,unit', 'type' => 'href', 'style' => 'width:60px;text-align:center;', 'text' => '操作', 'href_text' => '<a title="查看" href="/goodsStock/detail/?corp_id={1}&goods_id={2}&unit={3}">查看</a>'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:200px;text-align:center', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'goods_name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '品名'),
    array('key' => 'total_quantity_balance', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '可用库存', 'href_text' => 'showTotalQuantityBalance'),
    array('key' => 'total_quantity_frozen', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '冻结库存', 'href_text' => 'showTotalQuantityFrozen'),
    array('key' => 'total_stock_quantity', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '当前库存', 'href_text' => 'showStockQuantityDetail'),
);

function showTotalQuantityBalance($row) {
    return Utility::numberFormatToDecimal($row['total_quantity_balance'], 4).Map::$v['goods_unit'][$row['unit']]['name'];
}

function showTotalQuantityFrozen($row) {
    return Utility::numberFormatToDecimal($row['total_quantity_frozen'], 4).Map::$v['goods_unit'][$row['unit']]['name'];
}

function showStockQuantityDetail($row) {
    return Utility::numberFormatToDecimal(($row['total_quantity_balance'] + $row['total_quantity_frozen']), 4).Map::$v['goods_unit'][$row['unit']]['name'];
}

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");