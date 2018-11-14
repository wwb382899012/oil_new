<?php
/**
 * Created by youyi000.
 * DateTime: 2016/6/21 15:25
 * Describe：
 */

function checkRowEditAction($row)
{
    $s = "";
    if($row['status']==1){
        $s .= '<a href="/monthIncome/edit?id='.$row['statement_id'].'" title="单据作废">作废</a>&nbsp;|&nbsp;';
    }

    $s .= '<a href="/monthIncome/detail?id='.$row['statement_id'].'" title="查看详情">详情</a>';
    return $s;
        
}

//查询区域
$form_array = array('form_url'=>'/monthIncome/',
	'input_array'=>array(
        array('type'=>'text','key'=>'a.code','text'=>'单号&emsp;&emsp;'),
        array('type'=>'corpName','key'=>'a.corp_id','text'=>'交易主体'),
        array('type'=>'dateMonth','key'=>'a.account_period','id'=>'datepicker','text'=>'所属期间'),
        array('type'=>'select','key'=>'a.status','map_name'=>'receipts_status','text'=>'单据状态'),
    ),
    'buttonArray'=>array(
        array('text'=>'添加单据','buttonId'=>'addButton'),
    ),
);

//列表显示
$array =array(
    array('key'=>'statement_id','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'code','type'=>'','style'=>'width:120px;text-align:center','text'=>'单号'),
    array('key'=>'account_period','type'=>'','style'=>'width:100px;text-align:center;','text'=>'收入归属月'),
    array('key'=>'corp_id,name','type'=>'href','style'=>'text-align:left;','text'=>'交易主体','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'purchase_amount','type'=>'amount','style'=>'width:140px;text-align:right','text'=>'实际采购金额'),
    array('key'=>'sell_amount','type'=>'amount','style'=>'width:140px;text-align:right','text'=>'销售金额'),
    array('key'=>'status','type'=>'map_val','text'=>'单据状态','map_name'=>'receipts_status','style'=>'width:100px;text-align:center'),
);



$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:1050px;");


?>
<script>
    $(function () {
        $("#addButton").click(function () {
            location.href="/monthIncome/add/";
        });
    });
</script>