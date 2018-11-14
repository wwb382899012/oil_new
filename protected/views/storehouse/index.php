<?php

function checkRowEditAction($row,$self) {
    $links = array();
    // 在没提交状态可以编辑,提交后只能查看
    if (StorehouseService::editable($row, $self->getUser())) {
        return "<a title='编辑' href='/storehouse/edit?store_id={$row['store_id']}'>编辑</a> &nbsp;|&nbsp; <a title='查看' href='/storehouse/detail?store_id={$row['store_id']}'>查看</a>";
    } else {
        return "<a title='查看' href='/storehouse/detail?store_id={$row['store_id']}'>查看</a>";
    }
}

$form_array = array(
    'form_url'=>'/storehouse/index',
    'input_array'=>array(
        array('type'=>'text','key'=>'sh.store_id','text'=>'仓库编号', 'placeholder'=>'仓库编号'),
        array('type'=>'text','key'=>'sh.name*','text'=>'仓库名称'), 'placeholder'=>'仓库编号'
        ),
    'buttonArray'=>array(
        array('text'=>'添加','buttonId'=>'addButton'),
        ),
    );

$this->loadForm($form_array,$_GET);

//列表显示
$array =array(
    array('key'=>'store_id','type'=>'href','style'=>'width:120px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'store_id','type'=>'','style'=>'text-align:center;width:80px;','text'=>'仓库编号'),
    array('key'=>'name','style'=>'width:260px;','text'=>'仓库名称'),
    array('key'=>'company_name','style'=>'width:260px;','text'=>'所属公司'),
    //array('key'=>'address','style'=>'text-align:left','text'=>'仓库地址'),
    array('key'=>'capacity','style'=>'width:120px;','text'=>'仓库容积'),
    array('key'=>'type','style'=>'width:100px;','text'=>'仓库类型', 'type'=>'map_val', 'map_name'=>'storehouse_type'),
    array('key'=>'status','style'=>'width:80px;text-align:center;','text'=>'状态', 'type'=>'map_val', 'map_name'=>'storehouse_status'),
);

$this->show_table($array,$_data_['data']['data'],"","","table-bordered table-layout", '');

?>
<script type="text/javascript">
    (function() {
        $("#addButton").on('click', function() {
            window.location.href="/storehouse/add";
        });
    })();
</script>