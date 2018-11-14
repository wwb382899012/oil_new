<?php
/**
 * Created by youyi000.
 * DateTime: 2017/3/28 14:59
 * Describe：
 */
function checkRowEditAction($row,$self)
{
    $links=array();
    if($row["isCanCheck"])
    {
        $links[]='<a href="/'.$self->getId().'/check?id='.$row["obj_id"].'" title="审核">审核</a>';
    }
    else{
        $links[]='<a href="/'.$self->getId().'/detail?detail_id='.$row["detail_id"].'&check_status='.$row['checkStatus'].'" title="查看详情">详情</a>';
    }
    $s=implode("&nbsp;|&nbsp;",$links);
    return $s;
}

//查询区域
$form_array = array(
    'form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type'=>'text','key'=>'b.cross_code','text'=>'调货处理单编号'),
        array('type'=>'text','key'=>'d.cross_code','text'=>'调货单编号'),
        array('type'=>'select','key'=>'checkStatus','noAll'=>'1','map_name'=>'contract_check_status','text'=>'审核状态'),
        array('type' => 'text', 'key' => 'f.contract_code*', 'text' => '销售合同编号'),
        array('type' => 'text', 'key' => 'scf.code_out*', 'text' => '销售外部合同编号'),
        array('type' => 'text', 'key' => 'e.contract_code*', 'text' => '被调采购合同编号'),
        array('type' => 'text', 'key' => 'bcf.code_out*', 'text' => '被调采购外部合同编号'),
    ),
    'buttonArray'=>array(

    ),
);

//列表显示
$array =array(
    array('key'=>'obj_id','type'=>'href','style'=>'width:80px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'return_code','type'=>'','style'=>'width:200px;text-align:left','text'=>'调货处理单编号'),
    array('key'=>'borrow_code','type'=>'','style'=>'width:220px;text-align:left','text'=>'调货单编号'),
    array('key'=>'sell_contract_code','type'=>'','style'=>'width:180px;text-align:left','text'=>'销售合同编号'),
    array('key' => 'sell_code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '销售外部合同编号'),
    array('key'=>'buy_contract_code','type'=>'','style'=>'width:180px;text-align:left','text'=>'被调采购合同编号'),
    array('key'=>'buy_code_out','type'=>'','style'=>'width:140px;text-align:left','text'=>'被调采购外部合同编号'),
    array('key'=>'goods_name','type'=>'','style'=>'width:100px;text-align:left;','text'=>'品名'),
    array('key'=>'checkStatus','type'=>'map_val','style'=>'width:80px;text-align:center;','text'=>'审核状态','map_name'=>'contract_check_status'),
);

$style = empty($_data_[data]['rows']) ? "min-width:1050px;" : "min-width:1050px;";

$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"",$style,"table-bordered table-layout");


?>