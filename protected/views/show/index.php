<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/22 10:30
 * Describe：
 */
$searchForm = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => $searchItems,
    'buttonArray'=>array(
        //array('text'=>'导出','buttonId'=>'exportButton'),
    ),
);
if($isExport)
    $searchForm["buttonArray"][]=array('text'=>'导出','buttonId'=>'exportButton');

$tableOptions=$tableOptions===null?array(
    "class"=>"data-table",
    "data-config"=>"{ 
            fixedHeader: true,
            fixedColumns: {
                leftColumns: 1
                }
            }",
):$tableOptions;
$this->loadForm($searchForm,$_GET);
$this->widget('ZGridView', array(
    'id'=>'data-grid',
    'emptyText'=>'数据库没有符合条件的数据',
    'dataProvider'=>$dataProvider,
    'tableOptions'=>$tableOptions,
    'columns'=>
        $columns,
    'pager'=>function($pager){
        echo "test".$pager["pages"];
    }
));

?>
<script>
    $(function(){
        $("#exportButton").click(function(){
           var formData= $(this).parents("form.search-form").serialize();
            location.href="/<?php echo $this->getId() ?>/export?id=<?php echo $this->showId ?>&"+formData;
        });
    });
</script>