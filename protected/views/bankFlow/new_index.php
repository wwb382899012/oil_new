<?php
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'bank_flow_status', 'text' => '状态'),
        array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'a.code*', 'text' => '银行流水编号'),
        array('type' => 'text', 'key' => 'a.bank_name*', 'text' => '收款银行'),
        array('type' => 'text', 'key' => 'a.account_name*', 'text' => '银行账户名'),
        array('type' => 'text', 'key' => 'a.pay_partner*', 'text' => '付款公司'),
        array('type' => 'date', 'key' => 'a.receive_date>', 'text' => '收款开始时间','id'=>'startDate'),
        array('type' => 'date', 'key' => 'a.receive_date<', 'text' => '收款结束时间','id'=>'endDate'),
    ),
    /*'buttonArray' => array(
        array('text'=>'添加','buttonId'=>'addButton'),
        array('text'=>'重置','buttonId'=>'resetButton'),
        array('text' => '导入', 'buttonId' => 'import'),
        array('text' => '导出', 'buttonId' => 'export'),
    )*/
);

$buttonArray = [
    ['text' => '录入收款流水',
     'attr' => [
             'id' => 'addButton',
             'onclick' => "location.href='/".$this->getId()."/add'",],
    ],
    ['text' => '导入',
     'attr' => [
         'id' => 'import',
         'onclick' => "location.href='/".$this->getId()."/import'",
         'class_abbr' => 'action-default-base'
        ],
    ]
];
//列表显示
$array = array(
    array('key' => 'status', 'type' => 'map_val', 'style' => 'width:60px;text-align:left', 'text' => '状态', 'map_name' => 'bank_flow_status'),
    array('key' => 'receive_date', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '收款时间'),
    array('key' => 'amount', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '收款金额', 'href_text' => 'getRowAmount'),
    array('key' => 'pay_partner', 'type' => 'text', 'style' => 'width:240px;text-align:left', 'text' => '付款公司'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" target="_blank" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'flow_id', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => 'ID'),
    array('key' => 'code', 'type' => 'text', 'style' => 'width:140px;text-align:left', 'text' => '银行流水编号'),
    array('key' => 'bank_name', 'type' => 'text', 'style' => 'width:120px;text-align:left', 'text' => '收款银行'),
    array('key' => 'account_name', 'type' => 'text', 'style' => 'width:120px;text-align:left', 'text' => '银行账户名'),
    array('key' => 'pay_bank', 'type' => 'text', 'style' => 'width:240px;text-align:left', 'text' => '付款银行'),
    array('key' => 'flow_id', 'type' => 'href', 'style' => 'width:100px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();
    if($row['status']<BankFlow::STATUS_SUBMITED&&$row['create_user_id']==Utility::getNowUserId()) {
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["flow_id"] . '" title="修改">修改</a>';
    } elseif($row['status']==BankFlow::STATUS_SUBMITED&&$row['create_user_id']==Utility::getNowUserId()&&$row['receive_count']==0)  {
        $links[] = '<a href="javascript:void(0);" onclick="rollback('.$row['flow_id'].')" title="撤回">撤回</a>';
    }
    $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["flow_id"] . '" title="查看">查看</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

function getRowAmount($row, $self) {
    return $self->map['currency'][$row['currency']]['ico'] . number_format($row["amount"]/100,2);
}

$headerArray = ['button_config' => $buttonArray, 'is_show_export' => true];
$searchArray = ['search_config' => $form_array, 'is_show_reset_button' => true];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);

?>


<script>
	var rollback = function(flow_id) {
		inc.vueConfirm({
			content: '是否确认撤回本次流水记录?', onConfirm: function() {
				$.ajax({
					type: "POST",
					url: "/<?php echo $this->getId() ?>/ajaxRollback",
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
							self.isSubmit(0);
							inc.vueAlert(json.data);
						}
					},
					error: function (data) {
						// layer.alert("保存失败！"+data.responseText, {icon: 5});
						self.isSubmit(0);
						inc.vueAlert("操作失败！" + data.responseText);
					}
				})
			}
		});
	}
</script>