<?php

function getInterestAction($row, $self) {
    $interest = '￥'.number_format(round($row['interest_pay'] - $row['interest_receive'])/100, 2);
    return $interest;
}


$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'i.corporation_name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'i.user_name*', 'text' => '业务负责人'),
        array('type' => 'text', 'key' => 'i.project_code*', 'text' => '项目编号')
    ),
    'buttonArray' => array(
        array('text' => '导出', 'buttonId' => 'exportButton')
    ),
);

//列表显示
$array = array(
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:110px;text-align:left;', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'user_name', 'type' => '', 'style' => 'width:80px;text-align:center;', 'text' => '业务负责人'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'text-align:center;', 'text' => '项目编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'amount_receive', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '累计收款金额'),
    array('key' => 'amount_pay', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '累计实付金额'),
    array('key' => 'interest_pay', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '累计实付利息'),
    array('key' => 'interest_receive', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '累计收款利息'),
    array('key' => 'interest_pay', 'type' => 'href', 'style' => 'width:80px;text-align:right;', 'text' => '合计利息', 'href_text' => 'getInterestAction'),    
);

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_['data'], "", "min-width:1050px;", "table-bordered table-layout", "", true);
?>


<script>
    $(function () {
        $("#exportButton").click(function(){
            var formData= $(this).parents("form.search-form").serialize();
            location.href="/<?php echo $this->getId() ?>/export?"+formData;
        });
    })
</script>
