<?php
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'a.pay_partner*', 'text' => '付款公司'),
        array('type' => 'text', 'key' => 'a.code*', 'text' => '银行流水编号'),
        array('type' => 'text', 'key' => 'a.bank_name*', 'text' => '收款银行'),
        array('type' => 'select', 'key' => 'r.status', 'map_name' => 'receive_confirm_status', 'text' => '状态'),
        array('type' => 'text', 'key' => 'a.account_name*', 'text' => '银行账户名'),
        array('type' => 'date', 'key' => 'a.receive_date>','id'=>'datepicker', 'text' => '收款开始时间'),
        array('type' => 'date', 'key' => 'a.receive_date<','id'=>'datepicker2', 'text' => '收款结束时间'),
        array('type' => 'text', 'key' => 'c.contract_code*', 'text' => '货款合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
    )
);

//列表显示
$array = array(
    array('key' => 'receive_id', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '收款编号'),
    array('key' => 'flow_id', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '系统流水号'),
    array('key' => 'code', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '银行流水编号'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:180px;text-align:left', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" target="_blank" title="交易主体详情" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'bank_name', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '收款银行'),
    array('key' => 'account_name', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '银行账户名'),
    array('key' => 'pay_partner', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '付款公司'),
    array('key' => 'user_name', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '认领人'),
    array('key'=>'receive_status','style'=>'width:100px;','text'=>'状态', 'type'=>'map_val', 'map_name'=>'receive_confirm_status'),
    array('key' => 'project_code', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '项目编号'),
    array('key' => 'project_type', 'type' => 'map_val', 'style' => 'width:80px;text-align:left', 'text' => '项目类型', 'map_name' => 'project_type'),
    array('key' => 'contract_type', 'type' => 'map_val', 'style' => 'width:80px;text-align:left', 'text' => '合同类型', 'map_name' => 'buy_sell_type'),
    array('key' => 'contract_code', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '货款合同编号'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:left', 'text' => '外部合同编号'),
    array('key' => 'sub_contract_code', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '收款合同编号'),
    array('key' => 'subject_name', 'type' => 'text', 'style' => 'width:80px;text-align:left', 'text' => '用途'),
    array('key' => 'currency', 'type' => 'map_val', 'style' => 'width:80px;text-align:left', 'text' => '币种', 'map_name' => 'currency_type'),
    array('key' => 'flow_id', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '认领金额', 'href_text' => 'getRowAmount'),
    array('key' => 'receive_date', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '收款时间'),
    array('key' => 'create_time', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '认领时间'),
    array('key' => 'flow_id', 'type' => 'href', 'style' => 'width:130px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();

    if($row['receive_status']==ReceiveConfirm::STATUS_NEW&&$row['create_user_id']==Utility::getNowUserId()) {
        $links[] = '<a href="/receiveConfirm/edit?id=' . $row["receive_id"] . '" title="修改">修改</a>';
        $links[] = '<a href="javascript:void(0);" onclick="del('.$row['receive_id'].')" title="作废">作废</a>';
    }
    $links[] = '<a href="/receiveConfirm/detail?id=' . $row["receive_id"] . '&back_url=receiveConfirmList" title="查看">查看</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

function getRowAmount($row, $self) {
    return $self->map['currency'][$row['currency']]['ico'] . number_format($row["received_amount"]/100,2);
}

$headerArray = ['is_show_export' => true];
$searchArray = ['search_config' => $form_array];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>


<script>
  var del = function(flow_id) {
	  inc.vueConfirm({
		  content: '是否确认作废本次收款记录?', onConfirm: function() {
			  $.ajax({
				  type: "POST",
				  url: "/receiveConfirm/ajaxDel",
				  data: {
					  id: flow_id
				  },
				  dataType: "json",
				  success: function (json) {
					  if (json.state == 0) {
						  inc.vueMessage({duration: 500,message: "撤回成功", onClose: function () {
								  window.location.reload();
							  }
						  });
					  } else {
						  // layer.alert(json.data, {icon: 5});
						  self.submitBtnText("提交");
						  self.saveBtnText("保存");
						  self.isSubmit(0);
						  inc.vueAlert(json.data);
					  }
				  },
				  error: function (data) {
					  // layer.alert("保存失败！"+data.responseText, {icon: 5});
					  self.submitBtnText("提交");
					  self.saveBtnText("保存");
					  self.isSubmit(0);
					  inc.vueAlert("操作失败！" + data.responseText);
				  }
			  })
		  }
    });
  }
</script>
