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
    'items' => array(
        array('type' => 'text', 'key' => 'c.contract_code', 'text' => '采购合同编号'),
        array('type' => 'text', 'key' => 't.code', 'text' => '入库单编号'),
        array('type'=>'select', 'key'=>'checkStatus', 'noAll'=>'1', 'map_name'=>'stock_in_check_status', 'text'=>'审核状态'),
        array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'p.name*', 'text' => '上游合作方'),
        array('type' => 'text', 'key' => 's.name*', 'text' => '入库'),
        array('type' => 'date', 'key' => 't.entry_date', 'id'=>'entry_date', 'text' => '入库日期'),
    )
);

//列表显示
$array =array(
    array('key'=>'detail_id','type'=>'','style'=>'width:60px;text-align:left','text'=>'审核编号'),
    array('key' => 'batch_id,code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:140px;text-align:left', 'text' => '入库单编号', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/stockIn/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:140px;text-align:left', 'text' => '采购合同编号', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out','type'=>'','class' => 'no-ellipsis', 'style'=>'width:140px;text-align:left','text'=>'外部合同编号'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:160px;text-align:left', 'text' => '上游合作方', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'store_name', 'type' => '', 'style' => 'width:160px;text-align:left', 'text' => '入库'),
    array('key' => 'entry_date', 'type' => 'date', 'style' => 'width:100px;text-align:left', 'text' => '入库日期'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:80px;text-align:left;','text'=>'审核状态','href_text'=>'FlowService::showCheckStatus'),
    array('key'=>'detail_id','type'=>'href','style'=>'min-width:40px;text-align:left;','text'=>'操作','href_text'=>'checkRowActions'),
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


$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);
