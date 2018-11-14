<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2017/4/13 0013
 * Time: 19:23
 */

//查询区域
$form_array = array(
   'form_url' => '/userCredit/',
   'input_array' => array(
           array('type' => 'text', 'key' => 'a.name', 'text' => '姓名')
   )
);

//列表显示
$array = array(
    array('key' => 'user_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowEditAction'),
	array('key' => 'user_id', 'type' => 'href', 'text' => '额度明细', 'style' => 'width:60px;text-align:center', 'href_text' => '<a id="t_{1}" title="查看" href="/userCredit/creditDetail/?user_id={1}" >查看</a>'),
    array('key' => 'user_id', 'type' => '', 'style' => 'width:100px;text-align:center', 'text' => '编号'),
    array('key' => 'user_id,name', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '姓名', 'href_text' => '<a id="t_{1}" target="_blank"  title="查看详细" href="/businessAssistant/detail?user_id={1}&t=1&url=/userCredit/" >{2}</a>'),
//    array('key' => 'name', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '姓名'),
    array('key' => 'credit_amount', 'type' => 'amountWan', 'text' => '常规额度', 'style' => 'width:120px;text-align:right'),
    array('key' => 'use_amount', 'type' => 'amountWan', 'text' => '正占用额度', 'style' => 'width:120px;text-align:right'),
    array('key' => 'frozen_amount', 'type' => 'amountWan', 'text' => '冻结额度', 'style' => 'width:120px;text-align:right'),
    array('key' => 'balance_amount', 'type' => 'amountWan', 'text' => '剩余额度', 'style' => 'width:120px;text-align:right'),
);

function getRowEditAction($row) {
    $html = '';
    if($row['credit_amount'] == 0 && $row['use_amount'] == 0 && $row['balance_amount'] == 0) {
        $html .= '<a href="/userCredit/edit?user_id=' . $row["user_id"] . '" title="录入">录入</a>&nbsp;';
    }
    if(!empty($row['credit_id'])) {
	    $html .= '<a href="/userCredit/detail?credit_id=' . $row["credit_id"] . '" title="查看">查看</a>&nbsp;
                <a href="/userCredit/edit?user_id='. $row['user_id'] .'&credit_id=' . $row["credit_id"] . '" title="调整">调整</a>';
    }

    return $html;
}

$this->loadForm($form_array, $_GET);
$this->show_table($array, $_data_['data'], "", "min-width:900px;");