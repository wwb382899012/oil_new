<?php
/**
 * Desc: 分配统计利润明细
 * User: susiehuang
 * Date: 2017/11/25 0025
 * Time: 14:34
 */
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type'=>'hidden','key'=>'category','text'=>'统计类型'),
        array('type'=>'hidden','key'=>'type','text'=>'分配类型'),
        array('type'=>'dateMonth','id'=>'startMonth','key'=>'startMonth','text'=>'结算周期'),
        array('type'=>'dateMonth','id'=>'endMonth','key'=>'endMonth','text'=>'到'),
    )
);

$columns = array(
    array(
        'headerHtmlOptions'=>array("style"=>"width:80px;text-align:center;"),
        'htmlOptions'=>array("style"=>"text-align:center;"),
        'name'=>'month',
        'header'=>'结算月份',
    )
);

if ($this->category == ProjectProfit::CATEGORY_PROJECT) {
    array_push($form_array['input_array'], array('type' => 'text', 'key' => 'p.project_code', 'text' => '项目编号'), array('type' => 'select', 'key' => 'p.type', 'map_name' => 'project_type', 'text' => '项目类型'));

    $projectCode = array(
        'headerHtmlOptions'=>array("style"=>"width:140px;text-align:center;"),
        'htmlOptions'=>array("style"=>"text-align:center;"),
        'name'=>'project_code',
        'header'=>'项目编号',
        'value'=>function($model,$index,$self){
            if(!empty($model["project_id"]))
                return "<a target='_blank' href='/project/detail/?id=".$model["project_id"]."&t=1' title='".$model["project_code"]."'>".$model["project_code"]."</a>";
            else
                return $model["project_code"];
        },
    );
    $projectType = array(
        'class'=>'ZEnumColumn',
        'key'=>'project_type',
        'name'=>'project_type',
        'header'=>'项目类型',
    );
    array_push($columns, $projectCode, $projectType);
} elseif ($this->category == ProjectProfit::CATEGORY_CORPORATION) {
    array_push($form_array['input_array'], array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'));

    $corp = array(
        'headerHtmlOptions'=>array("style"=>"width:160px;text-align:center;"),
        'htmlOptions'=>array("style"=>"text-align:center;"),
        'name'=>'corp_name',
        'header'=>'交易主体',
        'value'=>function($model,$index,$self){
            if(!empty($model["corporation_id"]))
                return "<a target='_blank' href='/corporation/detail/?id=".$model["corporation_id"]."&t=1' title='".$model["corp_name"]."'>".$model["corp_name"]."</a>";
            else
                return $model["corp_name"];
        },
    );
    array_push($columns, $corp);
} elseif ($this->category == ProjectProfit::CATEGORY_PROJECT_LEADER) {
    array_push($form_array['input_array'], array('type' => 'text', 'key' => 'su.name*', 'text' => '项目负责人'));

    $manager = array(
        'headerHtmlOptions'=>array("style"=>"width:100px;text-align:center;"),
        'htmlOptions'=>array("style"=>"text-align:center;"),
        'name'=>'manager_name',
        'header'=>'项目负责人',
    );
    array_push($columns, $manager);
}

if (!empty($search['type'])) {
    array_push(
        $columns,
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'buy_amount_invoice',
            'header'=>'已收票金额',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'buy_amount_paid',
            'header'=>'实付货款金额',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'buy_amount_settle',
            'header'=>'入库结算金额',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'sell_amount_paid',
            'header'=>'实收货款金额',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'sell_amount_invoice',
            'header'=>'已开票金额',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'sell_amount_settle',
            'header'=>'已出库结算金额',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'factoring_interest',
            'header'=>'保理利息',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'factoring_fee',
            'header'=>'保理服务费',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'factoring_fee2',
            'header'=>'保理服务费<br/>（霍尔果斯）',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_tax',
            'header'=>'增值税',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_custom',
            'header'=>'关税',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_stamp',
            'header'=>'印花税',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_surtax',
            'header'=>'附加税',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_store',
            'header'=>'仓储费用',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_traffic',
            'header'=>'运输费用',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_other',
            'header'=>'杂费',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_agent',
            'header'=>'代理费',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_period',
            'header'=>'期间费用',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'cross_profit_amount',
            'header'=>$search['type']==ProjectProfit::TYPE_CONFIRM?'可分配毛利':'可计算毛利',
            /*'value'=>function($model) {
                return $model["sell_amount_settle"] - $model["buy_amount_settle"];
            },*/
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'net_profit_amount',
            'header'=>$search['type']==ProjectProfit::TYPE_CONFIRM?'可分配净利':'可计算净利',
            /*'value'=>function($model) {
                return $model["sell_amount_settle"] - $model["buy_amount_settle"] - $model["factoring_interest"] - $model["factoring_fee"] - $model["factoring_fee2"] - $model["amount_tax"] - $model["amount_custom"] - $model["amount_stamp"] - $model["amount_surtax"] - $model["amount_store"] - $model["amount_traffic"] - $model["amount_other"] - $model["amount_agent"] - $model["amount_period"];
            },*/
        )
    );
} else {
    array_push(
        $columns,
        array(
            'headerHtmlOptions'=>array("style"=>"width:120px;text-align:right;"),
            'htmlOptions'=>array("style"=>"text-align:right;"),
            'name'=>'distributed_cross_profit_amount',
            'header'=>'可分配毛利',
            'value'=>function($model){
                return $model['type'] == ProjectProfit::TYPE_CONFIRM ? '￥' . number_format($model["cross_profit_amount"] / 100 , 2) : 0;
            },
        ),
        array(
            'headerHtmlOptions'=>array("style"=>"width:120px;text-align:right;"),
            'htmlOptions'=>array("style"=>"text-align:right;"),
            'name'=>'distributed_net_profit_amount',
            'header'=>'可分配净利',
            'value'=>function($model){
                return $model['type'] == ProjectProfit::TYPE_CONFIRM ? '￥' . number_format($model["net_profit_amount"] / 100, 2) : 0;
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'capital_cost',
            'header'=>'资金成本总额',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'tax_cost',
            'header'=>'税务成本总额',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'direct_cost',
            'header'=>'直接成本费用',
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_period',
            'header'=>'期间成本费用',
        ),
        array(
            'headerHtmlOptions'=>array("style"=>"width:120px;text-align:right;"),
            'htmlOptions'=>array("style"=>"text-align:right;"),
            'name'=>'computable_cross_profit_amount',
            'header'=>'可计算毛利',
            'value'=>function($model){
                return $model['type'] == ProjectProfit::TYPE_SETTLED ? '￥' . number_format($model["cross_profit_amount"] / 100, 2) : 0;
            },
        ),
        array(
            'headerHtmlOptions'=>array("style"=>"width:120px;text-align:right;"),
            'htmlOptions'=>array("style"=>"text-align:right;"),
            'name'=>'computable_net_profit_amount',
            'header'=>'可计算净利',
            'value'=>function($model){
                return $model['type'] == ProjectProfit::TYPE_SETTLED ? '￥' . number_format($model["net_profit_amount"] / 100, 2) : 0;
            },
        )
    );
}

$this->loadForm($form_array, $_data_);
//$this->show_table($array, $_data_[data], "", "min-width:3050px;", "table-bordered table-layout");

$this->widget('ZGridView', array(
    'id'=>'data-grid',
    'emptyText'=>'数据库没有数据',
    'dataProvider'=>$dataProvider,//数据源
    'tableOptions'=>array(
        "class"=>"data-table",
//        "style"=>!empty($search['type']) ? "width:3050px;" : "width:1650px;",
        "data-config"=>"{ 
            fixedHeader: true,
            fixedColumns: {
                leftColumns: 1
            }
            }",
    ),
    'columns'=>$columns
    )
);