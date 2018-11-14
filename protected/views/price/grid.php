
<?php
$form_array=array(
    'form_url'=>'/price/',
    'input_array'=>array(
        array('key'=>'goods_name','type'=>'text','text'=>'商品名称'),
    ),
    'buttonArray'=>array(
        array('text'=>'添加','buttonId'=>'addButton'),
    ),
);


$array=array(
    array('key'=>'account_id','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'操作','href_text'=>'operation'),
    array('key'=>'account_id','type'=>'','style'=>'width:80px;text-align:center','text'=>'账户编号'),
    array('key'=>'account_no','type'=>'','text'=>'银行账号','style'=>'width:180px;text-align:left'),
    array('key'=>'bank_name','type'=>'','text'=>'开户行','style'=>'text-align:left'),
    array('key'=>'corporation_id,corporation_name','type'=>'href','style'=>'width:300px;text-align:left;','text'=>'公司名称','href_text'=>'<a id="t_{1}" title="查看详细" href="/corporation/detail/?id={1}&url=/corporation/" >{2}</a>'),
    array('key'=>'status','type'=>'map_val','text'=>'账户状态','map_name'=>'account_status','style'=>'width:80px;text-align:center'),
    // array('key'=>'create_time','type'=>'','text'=>'创建时间','style'=>'width:150px;text-align:center'),
);

function operation($row){
    return '<a href="/price/edit/?id='.$row["price_id"].'" title="修改">修改</a>&nbsp;|&nbsp;
    <a href="/price/detail/?id='.$row["price_id"].'" title="查看详情">详情</a>';

}


$this->loadForm($form_array,$_GET);
$this->widget('ZGridView', array(
    'id'=>'data-grid',
    'emptyText'=>'数据库没有数据',
    'dataProvider'=>$dataProvider,//数据源
    'tableOptions'=>array(
        "style"=>"min-width:1050px;",
    ),
    'columns'=>array(
        array(
            'headerHtmlOptions'=>array('style'=>"width:120px; text-align:center;"),
            'htmlOptions'=>array('style'=>"text-align:center;"),
            'header'=>'操作',
            'type'=>'html',
            'value'=>function($model){
                return operation($model);
            },
        ),
        array(
            'headerHtmlOptions'=>array('width'=>"200px"),
            'name'=>'goods_id',
            'header'=>'商品',
            'value'=>function($model){
                return $model->goods->name;
            },
        ),
        array(
            'headerHtmlOptions'=>array('style'=>"width:120px;text-align:center;"),
            'htmlOptions'=>array('style'=>"text-align:center;"),
            'name'=>'price_date',
            'header'=>'日期',
            /*'value'=>function($model){
                return $model->price_date;
            },*/
        ),
        array(
            'headerHtmlOptions'=>array('style'=>"width:150px;text-align:right;"),
            'htmlOptions'=>array('style'=>"text-align:right;"),
            'name'=>'price',
            'header'=>'价格',
            'value'=>function($model){
                return number_format($model->price/100,2);
            },
        ),
        array(
            'headerHtmlOptions'=>array('width'=>"80px"),
            'name'=>'price',
            'header'=>'单位',
            'value'=>function($model,$index,$self){
                return Map::$v["goods_unit"][$model->unit]["name"];
            },
        ),
        array(
            'name'=>'source',
            'header'=>'来源',
            'value'=>function($model)
            {
                return $model->source;
            },
        ),
    )
));
?>

<script>
    $(function () {
        $("#addButton").click(function () {
            location.href="/price/add/";
        });
    });

    function del(id) {
        if(confirm("您确定要删除该条记录，此操作不可逆？")){
            var formData="id="+id;
            $.ajax({
                type:"POST",
                url:"/price/del",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        inc.showNotice("删除成功！");
                        $("#i_"+id).parent().parent().remove();
                    }else{
                        alertModel(json.data);
                    }
                },
                error:function (data) {
                    alertModel("删除失败！"+data.responseText);
                }
            });
        }
    }
</script>
