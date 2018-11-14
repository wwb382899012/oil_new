<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/25 14:55
 * Describe：
 */

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


function operation($row,$self)
{
    $links=array();
    if (!empty($row['contract_id']))
    {
        $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["contract_id"] . '" title="查看详情">详情</a>';
    }
    $s =  implode("&nbsp;|&nbsp;", $links);

    return $s;
}


$this->loadForm($form_array,$_GET);
$this->widget('ZGridView', array(
    'id'=>'data-grid',
    'emptyText'=>'数据库没有符合条件的数据',
    'dataProvider'=>$dataProvider,//数据源
    'tableOptions'=>array(
        "class"=>"data-table",
        //"style"=>"min-width:1050px;",
        "data-config"=>"{ 
            fixedHeader: true,
            fixedColumns: {
                leftColumns: 1
            }
            }",
    ),
    'columns'=>array(
        array(
            'headerHtmlOptions'=>array('style'=>"width:80px; text-align:center;"),
            'htmlOptions'=>array('style'=>"text-align:center;"),
            'header'=>'操作',
            'type'=>'html',
            'value'=>function($model,$index,$self){
                return operation($model,$self);
            },
        ),
        array(
            'class'=>'ZAColumn',
            'headerHtmlOptions'=>array("style"=>"width:60px;text-align:center;"),
            'htmlOptions'=>array("style"=>"text-align:center;"),
            'name'=>'contract_id',
            'header'=>'合同ID',
            'template'=>'<a href="/contract/detail?id={0}" title="{0}">{0}</a>',
            'params'=>array("contract_id")
        ),
       /*array(
            'headerHtmlOptions'=>array("style"=>"width:60px;text-align:center;"),
            'htmlOptions'=>array("style"=>"text-align:center;"),
            'name'=>'contract_id',
            'header'=>'合同ID',
            'value'=>function($model,$index,$self){
                if(!empty($model["contract_id"]))
                    return "<a href='/contract/detail?id=".$model["contract_id"]."'>".$model["contract_id"]."</a>";
                else
                    return $model["contract_id"];
            },
        ),*/
        array(
            'headerHtmlOptions'=>array('style'=>"width:150px;text-align:center;"),
            'htmlOptions'=>array('style'=>"text-align:center;"),
            'name'=>'contract_code',
            'header'=>'合同编号',
            'value'=>function($model,$index,$self){
                if(!empty($model["contract_id"]))
                    return "<a href='/contract/detail?id=".$model["contract_id"]."&t=1' target='_blank'  title='".$model["contract_code"]."'>".$model["contract_code"]."</a>";
                else
                    return $model["contract_code"];
            },
        ),
        'code_out:text:外部合同编号:width:150px;text-align:center;',
        array(
            'headerHtmlOptions'=>array('style'=>"width:150px;text-align:center;"),
            'htmlOptions'=>array('style'=>"text-align:center;"),
            'name'=>'corporation_id',
            'header'=>'交易主体',
            'value'=>function($model){
                if(!empty($model["corporation_id"]))
                    return "<a href='/corporation/detail?id=".$model["corporation_id"]."&t=1' target='_blank' title='".$model["corp_name"]."'>".$model["corp_name"]."</a>";
                else
                    return $model["corp_name"];
            },
        ),
        array(
            'headerHtmlOptions'=>array("style"=>"width:120px;text-align:center;"),
            'class'=>'enum',
            'key'=>'contract_status',
            'name'=>'status',
            'header'=>'合同状态',
        ),
        array(
            'class'=>'enum',
            'key'=>'buy_sell_type',
            'name'=>'type',
            'header'=>'合同类型',
        ),
        array(
            'headerHtmlOptions'=>array('style'=>"width:150px;text-align:center;"),
            'htmlOptions'=>array('style'=>"text-align:center;"),
            'name'=>'partner_id',
            'header'=>'合作方',
            'value'=>function($model){
                if(!empty($model["partner_id"]))
                    return "<a href='/partner/detail?id=".$model["partner_id"]."&t=1' target='_blank' title='".$model["partner_name"]."'>".$model["partner_name"]."</a>";
                else
                    return $model["partner_name"];
            },
        ),
        'contract_date:text:合同签订日期:width:100px;text-align:center;',
        array(
            'headerHtmlOptions'=>array('style'=>"width:150px;text-align:center;"),
            'htmlOptions'=>array('style'=>"text-align:center;"),
            'name'=>'project_code',
            'header'=>'项目编号',
            'value'=>function($model,$index,$self){
                return '<a title="'.$model["project_code"].'" target="_blank" href="/project/detail/?id='.$model["project_id"].'&t=1">'.$model["project_code"].'</a>';
            },
        ),
        array(
           'class'=>'ZEnumColumn',
           'key'=>'project_type',
           'name'=>'project_type',
           'header'=>'项目类型',
        ),
        array(
            'headerHtmlOptions'=>array('style'=>"width:150px;text-align:center;text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"),
            'htmlOptions'=>array('style'=>"text-align:center;"),
            'name'=>'contract_id',
            'header'=>'品名',
            'value'=>function($model){
                $goods = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($model['contract_id']));
                return '<span title="'.$goods.'">'.$goods.'</span>';
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount',
            'header'=>'合同总金额',
            'value'=>function($model){
                return Map::$v['currency'][$model['currency']]['ico'].Utility::numberFormatFen2Yuan($model['amount']);
            },
        ),
        array(
            'class'=>'ZMoneyColumn',
            'name'=>'amount_cny',
            'header'=>'合同人民币金额'
        ),
        array(
            'headerHtmlOptions'=>array("style"=>"width:120px;text-align:center;"),
            'htmlOptions'=>array("style"=>"text-align:center;"),
            'name'=>'manage_user_id',
            'value'=>'$data[\'name\']',
            'header'=>'项目负责人',
        ),

    )
));
//echo microtime(true);
//echo (microtime(true)-$start1)."# ";
?>
