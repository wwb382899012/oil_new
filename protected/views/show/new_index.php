<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/22 10:30
 * Describe：
 */
$searchForm = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => $searchItems,
);

$tableOptions=$tableOptions===null?array(
    "class"=>"data-table dataTable stripe hover nowrap table-fixed",
):$tableOptions;

$headerArray = ['is_show_export' => $isExport];
$searchArray = ['search_config' => $searchForm];
$widgetConfig = ['widget_property' => array(
    'id'=>'data-grid',
    'emptyText'=>'您好，当前没有数据。',
    'dataProvider'=>$dataProvider,
    'tableOptions'=>$tableOptions,
    'itemsCssClass' => 'show-table',
    'isShowSummary' => false,
    'columns'=>
        $columns,
    'pager' => function($data) {
        $data['total'] = $data['itemCount'];
        $data['page'] = $data['currentPage'];
        include(ROOT_DIR. '/protected/views/layouts/new_page.php');
    }
    ),
    'float_columns' => 0,];
$this->showGridViewWithNewUI($headerArray, $searchArray, $widgetConfig);
?>