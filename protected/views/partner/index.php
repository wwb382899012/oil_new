<?php
$form_array=array('form_url'=>'/partner/',
    'input_array'=>array(
        array('type'=>'text','key'=>'a.name','text'=>'企业名称'),
        array('type'=>'select','key'=>'a.type','map_name'=>'partner_type','text'=>'类别'),
        array('type'=>'select','key'=>'a.runs_state','map_name'=>'runs_state','text'=>'经营状态'),
    ),

);
$array=array(
    array('key'=>'partner_id','type'=>'href','style'=>'width:60px;text-align:center;','text'=>'操作','href_text'=>'operation'),
    array('key'=>'partner_id','type'=>'','style'=>'width:80px;text-align:center','text'=>'企业编号'),
    array('key'=>'name','type'=>'','style'=>'text-align:left;','text'=>'企业名称'),
    array('key'=>'type','type'=>'','style'=>'width:100px;','text'=>'类别'),
    array('key'=>'corporate','type'=>'','text'=>'法人代表','style'=>'width:90px;text-align:center'),
    array('key'=>'registered_capital','type'=>'','text'=>'注册资本','style'=>'width:100px;text-align:right'),
    array('key'=>'start_date','type'=>'','text'=>'成立日期','style'=>'width:100px;text-align:center'),
    array('key'=>'ownership_name','type'=>'','text'=>'企业所有制','style'=>'text-align:left'),
    array('key'=>'runs_state','type'=>'map_val','text'=>'经营状态','map_name'=>'runs_state','style'=>'width:80px;text-align:center'),
);

function operation($row){
    return '<a href="/partner/detail/?id='.$row["partner_id"].'" title="查看详情">详情</a>';
}

$this->loadForm($form_array,$_GET);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");

?>

