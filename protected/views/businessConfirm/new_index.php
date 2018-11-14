<?php
/**
 * Created by vector.
 * DateTime: 2017/08/31 18:16
 * Describe：
 */
function checkRowEditAction($row, $self) {
    $links = array();
    /*if ($self->isShowAllLink == 1) {
        $links[] = '<a href="/' . $self->getId() . '/all?t=1&id=' . $row["contract_id"] . '&project_id=' . $row["project_id"] . ' target="_blank" title="信息全览">全览</a>';
    }*/

    if ($self->checkIsCanEdit($row["contract_status"])) {
        if($row['is_main']) {
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["contract_id"] . '&project_id=' . $row["project_id"] . '" title="修改">修改</a>';
        } else {
            $links[] = '<a href="/subContract/edit?id=' . $row["contract_id"] . '" title="修改">修改</a>';
        }
    }

    if (!empty($row['contract_id'])) {
        $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["contract_id"] . '" title="查看详情">详情</a>';
    }
    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'up.name*', 'text' => '上游合作方'),
        array('type' => 'text', 'key' => 'dp.name*', 'text' => '下游合作方'),
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'business_confirm_status', 'text' => '状态'),
        array('type' => 'select', 'key' => 'project_type', 'map_name' => 'project_detail_type', 'text' => '项目类型'),
        array('type' => 'select', 'key' => 'cg.type', 'map_name' => 'buy_sell_type', 'text' => '合同类型'),
        array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
        array('type' => 'managerUser', 'key' => 'p.manager_user_id', 'text' => '项目负责人'),
        array('type' => 'text', 'key' => 'p.project_code*', 'text' => '项目编号'),
        array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'p.create_time>','text'=>'申请时间'),
        array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'p.create_time<','text'=>'到'),
    )
);
$buttonArray = array();
if ($this->isCanAdd == 1) {
    $addSubContractBtn = array(
            'text' => '增加采销合同',
            'attr' => [
                'id' => 'addSubContractBtn',
                'onclick' => "location.href='/subContract/'"
            ],
        );
    array_push($buttonArray, $addSubContractBtn);
}

//列表显示
$array = array(
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:160px;text-align:left', 'text' => '项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'buy_sell_desc', 'type' => '', 'style' => 'width:140px;text-align:left', 'text' => '购销信息'),
    array('key' => 'project_type_desc', 'type' => '', 'style' => 'width:200px;text-align:left', 'text' => '项目类型'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:150px;text-align:left', 'text' => '交易主体', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'name', 'type' => '', 'style' => 'width:140px;text-align:left', 'text' => '项目负责人'),
    array('key' => 'up_partner_id,up_partner_name', 'type' => 'href', 'style' => 'width:150px;text-align:left;', 'text' => '上游合作方', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'down_partner_id,down_partner_name', 'type' => 'href', 'style' => 'width:150px;text-align:left;', 'text' => '下游合作方', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'contract_status', 'type' => 'map_val', 'map_name' => 'business_confirm_status', 'style' => 'width:100px;text-align:left', 'text' => '状态'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:120px;text-align:left', 'text' => '创建人'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:120px;text-align:left', 'text' => '申请时间'),
    array('key' => 'project_id', 'type' => 'href', 'style' => 'width:120px;text-align:left;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
);

$headerArray = ['button_config' => $buttonArray];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>