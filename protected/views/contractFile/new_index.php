<?php
/**
 * Created by youyi000.
 * DateTime: 2016/11/15 15:00
 * Describe：
 */
function checkRowEditAction($row, $self) {
    $links = array();
    if (ProjectService::checkIsCanContractUpload($row['project_id'], $self->moduleType)) {
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["project_id"] . '" title="上传合同">上传</a>';
    }
    if(ContractFileService::checkIsCanViewDetail($row['project_id'], $self->moduleType)) {
        $links[] = '<a href="/contractUpload/detail?id=' . $row["project_id"] . '" title="查看合同">查看</a>';
    }
    $s = implode("&nbsp;&nbsp;", $links);

    return $s;
}

function showProjectTypeDesc ($row, $self) {
    $str = $self->map['project_type'][$row['type']];
    if(!empty($row['buy_sell_type'])) {
        $str .= '-' . $self->map['purchase_sale_order'][$row["buy_sell_type"]];
    }
    return $str;
}

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    );

$items = [
    array('type' => 'text', 'key' => 'up.name*', 'text' => '上游合作方'),
    array('type' => 'text', 'key' => 'dp.name*', 'text' => '下游合作方'),
    array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'a.create_time>','text'=>'申请时间'),
    array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'a.create_time<','text'=>'到'),
    array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
    array('type' => 'select', 'key' => 'project_type', 'map_name' => 'project_detail_type', 'text' => '项目类型'),
    array('type' => 'text', 'key' => 'a.project_id', 'text' => '项目ID'),
    array('type' => 'text', 'key' => 'a.project_code', 'text' => '项目编号'),
    array('type' => 'text', 'key' => 'b.name', 'text' => '项目负责人'),
];
if ($this->moduleType == ConstantMap::ELECTRON_SIGN_CONTRACT_FILE) {
    $items = [
        array('type' => 'text', 'key' => 'a.project_code', 'text' => '项目编号'),
        array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'up.name*', 'text' => '上游合作方'),
        array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'a.create_time>','text'=>'申请时间'),
        array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'a.create_time<','text'=>'到'),
        array('type' => 'text', 'key' => 'dp.name*', 'text' => '下游合作方'),
        array('type' => 'select', 'key' => 'project_type', 'map_name' => 'project_detail_type', 'text' => '项目类型'),
        array('type' => 'text', 'key' => 'a.project_id', 'text' => '项目ID'),
        array('type' => 'text', 'key' => 'b.name', 'text' => '项目负责人'),
    ];
} elseif ($this->moduleType == ConstantMap::PAPER_SIGN_CONTRACT_FILE) {
    $items = [
        array('type' => 'text', 'key' => 'up.name*', 'text' => '上游合作方'),
        array('type' => 'text', 'key' => 'dp.name*', 'text' => '下游合作方'),
        array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'a.create_time>','text'=>'申请时间'),
        array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'a.create_time<','text'=>'到'),
        array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'a.project_code', 'text' => '项目编号'),
        array('type' => 'select', 'key' => 'project_type', 'map_name' => 'project_detail_type', 'text' => '项目类型'),
        array('type' => 'text', 'key' => 'a.project_id', 'text' => '项目ID'),
        array('type' => 'text', 'key' => 'b.name', 'text' => '项目负责人'),
    ];
}

$form_array['items'] = $items;

//列表显示
$array = array(
    array('key' => 'project_id', 'type' => '', 'style' => 'width:100px;text-align:left', 'text' => '项目ID'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '项目编号', 'href_text'=>'<a target="_blank" id="t_{1}" title="项目详情" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'up_partner_id,up_partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left;', 'text' => '上游合作方', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '交易主体', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'down_partner_id,down_partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left;', 'text' => '下游合作方', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'type', 'type' => 'href', 'style' => 'width:100px;text-align:left', 'text' => '项目类型', 'href_text' => 'showProjectTypeDesc'),
    array('key' => 'name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '项目负责人'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '创建人'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:120px;text-align:left', 'text' => '申请时间'),
    array('key' => 'project_id', 'type' => 'href', 'style' => 'width:100px;text-align:left;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
);

$searchArray = ['search_config' => $form_array, 'search_lines' => 3];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);
?>

