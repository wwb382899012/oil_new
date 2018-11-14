<?php
/**
 * Created by vector.
 * DateTime: 2017/10/17 18:16
 * Describe：
 */
function checkRowEditAction($row, $self) {
    $links = array();
    $status = CrossOrderService::isCanAddOrEdit($row['contract_id'], $row['goods_id']);
    if(($status==0 || $status >= CrossOrder::STATUS_PASS) && $row['contract_status']<Contract::STATUS_SETTLED_SUBMIT){
        $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["detail_id"] . '" title="添加调货">添加</a>';
    }else if($status==CrossOrder::STATUS_BACK || $status == CrossOrder::STATUS_SAVED){
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["detail_id"] . '" title="修改调货">修改</a>';
    }

    if (!empty($status)) {
        $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["detail_id"] . '" title="查看详情">详情</a>';
    }
    
    $s = !empty($links) ? implode("&nbsp;|&nbsp;", $links) : '';
    return $s;
}


//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'c.contract_code*', 'text' => '销售合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'pa.name*', 'text' => '下游合作方'),
        array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'p.project_code*', 'text' => '项目编号&emsp;&emsp;'),
        array('type' => 'text', 'key' => 'g.name*', 'text' => '品名&emsp;&emsp;&emsp;'),
    )
);

//列表显示
$array = array(
    array('key' => 'detail_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:150px;text-align:center', 'text' => '销售合同编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/businessConfirm/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:150px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'goods_name', 'type' => '', 'style' => 'width:150px;text-align:left', 'text' => '品名'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:150px;text-align:left', 'text' => '下游合作方', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:150px;text-align:left', 'text' => '交易主体', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:180px;text-align:center', 'text' => '项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),

);


$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");
?>
