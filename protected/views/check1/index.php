<?php

function checkRowEditAction($row,$self) {
    $links = array();
    // 在没提交状态可以编辑,提交后只能查看
    if ($row['status'] == Storehouse::STATUS_IN_APPROVAL) {
        return "<a title='审核' href='/{$self->getId()}/check?id={$row['store_id']}&detail_id={$row['detail_id']}'>审核</a>";
    } else {
        return "<a title='查看' href='/{$self->getId()}/detail?store_id={$row['store_id']}&detail_id={$row['detail_id']}'>查看</a>";
    }
}

$form_array = array(
    'form_url'=>"/{$this->getId()}/index",
    'input_array'=>array(
        array('type'=>'text','key'=>'sh.code','text'=>'仓库编号', 'placeholder'=>'仓库编号'),
        array('type'=>'text','key'=>'sh.name','text'=>'仓库名称'), 'placeholder'=>'仓库编号'
        ),
    'buttonArray'=>array(
        ),
    );

$this->loadForm($form_array,$_GET);


//列表显示
$array =array(
    array('key'=>'store_id','type'=>'href','style'=>'width:80px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'store_id','type'=>'','style'=>'width:80px;','text'=>'仓库编号'),
    array('key'=>'name','style'=>'width:260px;','text'=>'仓库名称'),
    array('key'=>'company_name','style'=>'width:260px;','text'=>'所属公司'),
    array('key'=>'address','style'=>'text-align:left','text'=>'仓库地址'),
    array('key'=>'capacity','style'=>'width:120px;','text'=>'仓库容积'),
    array('key'=>'type','style'=>'width:100px;','text'=>'仓库类型', 'type'=>'map_val', 'map_name'=>'storehouse_type'),
    array('key'=>'status','style'=>'width:80px;','text'=>'状态', 'type'=>'map_val', 'map_name'=>'storehouse_status'),
);

$this->show_table($array,$_data_['data']['data'],"","min-width:1050px;","table-bordered table-layout",'');

?>