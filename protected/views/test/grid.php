<?php

$this->widget('ZGridView', array(
    'id'=>'data-grid',
    'emptyText'=>'数据库没有数据',
    'dataProvider'=>$dataProvider,//数据源
    //'isShowSummary'=>false,
    //'isShowPager'=>false,
    'columns'=>array(
        array(
            'headerHtmlOptions'=>array('style'=>"width:120px; text-align:center;"),
            'htmlOptions'=>array('style'=>"text-align:center;"),
            'header'=>'操作',
            'type'=>'html',
            'value'=>function($model){
                return '<a href="/contract/detail?id=' . $model["contract_id"] . '" title="查看详情">详情</a>';
            },
        ),
        array(
            'headerHtmlOptions'=>array('width'=>"250px"),
            'name'=>'contract_id',
            'header'=>'合同',
            'value'=>function($model){
                //var_dump($model);
                return $model["contract_id"]." ".$model["contract_code"];
            },
        ),
        array(
            //'headerHtmlOptions'=>array('width'=>"250px"),
            'name'=>'contract_id',
            'header'=>'AllData',
            'value'=>function($model){
                return json_encode($model);
            },
        ),
        'project_id',
        'contract_code',
    )
));
?>
