<?php

/**
 * Desc: 入库单审核
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 't.code', 'text' => '入库单编号'),
        array('type' => 'text', 'key' => 'c.contract_code', 'text' => '采购合同编号'),
        array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'p.name*', 'text' => '上游合作方'),
        array('type' => 'text', 'key' => 's.name*', 'text' => '入库'),
        array('type' => 'date', 'key' => 't.entry_date', 'id'=>'entry_date', 'text' => '入库日期'),
        array('type'=>'select', 'key'=>'checkStatus', 'noAll'=>'1', 'map_name'=>'stock_in_check_status', 'text'=>'审核状态'),
    )
);

//列表显示
$array =array(
    array('key'=>'detail_id','type'=>'href','style'=>'width:40px;text-align:center;','text'=>'操作','href_text'=>'checkRowActions'),
    array('key'=>'detail_id','type'=>'','style'=>'width:60px;text-align:center','text'=>'审核编号'),
    array('key' => 'batch_id,code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '入库单编号', 'href_text' => '<a id="t_{1}" target="_blank" title="入库单详情" href="/stockIn/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '采购合同编号', 'href_text' => '<a id="t_{1}" target="_blank" title="合同详情" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out','type'=>'','style'=>'width:140px;text-align:center','text'=>'外部合同编号'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:160px;text-align:center', 'text' => '上游合作方', 'href_text' => '<a id="t_{1}" target="_blank" title="合作方详情" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'store_name', 'type' => '', 'style' => 'width:160px;text-align:center', 'text' => '入库'),
    array('key' => 'entry_date', 'type' => 'date', 'style' => 'width:100px;text-align:center', 'text' => '入库日期'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:80px;text-align:center;','text'=>'审核状态','href_text'=>'FlowService::showCheckStatus'),
);

function checkRowActions($row, $self) {
    $links = array();
    if ($row["isCanCheck"]) {
        $links[] = '<a href="/' . $self->getId() . '/check?id=' . $row["obj_id"] . '" title="审核">审核</a>';
    } else {
        $links[] = '<a href="/' . $self->getId() . '/detail?detail_id=' . $row["detail_id"] . '" title="查看详情">查看</a>';
    }
    $s = implode("&nbsp;|&nbsp;", $links);

    return $s;
}

$style = empty($_data_[data]['rows']) ? "min-width:1050px;" : "min-width:1250px;";

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", $style, "table-bordered table-layout");