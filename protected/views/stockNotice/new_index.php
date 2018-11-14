<?php
/**
 * Desc: 入库通知单
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'items' => array(
       array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
       array('type' => 'text', 'key' => 'c.name*', 'text' => '上游合作方'),
       array('type' => 'text', 'key' => 'd.name*', 'text' => '交易主体'),
       array('type' => 'text', 'key' => 'a.contract_code*', 'text' => '采购合同编号'),
       array('type' => 'text', 'key' => 'b.project_code', 'text' => '项目编号'),

   )
);

//列表显示
$array = array(
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'width:140px;text-align:left', 'text' => '采购合同编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type' => 'text', 'class' => 'no-ellipsis', 'style' => 'width:160px;text-align:left', 'text' => '外部合同编号'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '项目编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '上游合作方', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'min-width:90px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();
    if ($row['contract_status']<Contract::STATUS_SETTLED_SUBMIT) {
        $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["contract_id"] . '" title="添加">添加</a>';
    }
    $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["contract_id"] . '" title="查看详情">详情</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);