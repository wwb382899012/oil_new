<?php
/**
 * Created by youyi000.
 * DateTime: 2016/7/5 17:43
 * Describe：
 */


function checkRowActions($row, $self) {
    $links = array();
    if ($row["isCanCheck"]) {
        $links[] = '<a href="/' . $self->getId() . '/check?id=' . $row["obj_id"] . '" title="审核">审核</a>';
    } else {
        $links[] = '<a href="/' . $self->getId() . '/detail?detail_id=' . $row["detail_id"] . '" title="查看详情">查看</a>';
    }
    $s = implode("&nbsp;|&nbsp;", $links);

    return $s;
}

//查询区域
$form_array = array(
    'form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type'=>'select','key'=>'checkStatus','noAll'=>'1','map_name'=>'search_check_status','text'=>'审核状态'),
    ),
    'buttonArray'=>array(

    ),
);

//列表显示
$array =array(
    array('key'=>'detail_id','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'操作','href_text'=>'checkRowActions'),
    array('key'=>'check_id','type'=>'','style'=>'width:100px;text-align:center','text'=>'审核编号'),
    array('key'=>'node_name','type'=>'','style'=>'width:120px;text-align:center','text'=>'审核节点'),
    array('key'=>'detail_id','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'审核状态','href_text'=>'FlowService::showCheckStatus'),
    array('key'=>'create_time','type'=>'','style'=>'text-align:center','text'=>'提审时间'),

);



$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");


?>
<script>

</script>
