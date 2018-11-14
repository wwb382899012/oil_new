<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/23 11:11
 * Describe：
 */

function checkRowEditAction($row, $self) { 
    $links = array();
    $payApply = PayApplication::model()->findByPk($row['apply_id']);
    if($payApply->isCanClaim($row["status"]))
    {
        $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["apply_id"] . '" title="认领">认领</a>';
    }
    if(bccomp($row['amount_claim'], 0, 2) ===1) {
        $links[] = '<a href="/' . $self->getId() . '/view?apply_id=' . $row["apply_id"] . '" title="查看">查看</a>';
    }


    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

function showPayAmount($row, $self)
{
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($row["amount"]/100,2);
    return $str;
}

function showPaidAmount($row, $self)
{
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($row["amount_paid"]/100,2);
    return $str;
}
function showContractType($row, $self)
{
    $str=$self->map["contract_config"][$row['contract_type']][$row['contract_category']]["name"];
    return $str;
}

function showPayClaimStatus($row) {

}


//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'a.apply_id', 'text' => '付款编号'),
        array('type' => 'text', 'key' => 'a.payee*', 'text' => '收款单位'),
        array('type' => 'select', 'key' => 'claim_status', 'map_name' => 'pay_claim_status', 'text' => '认领状态'),
        array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'su.name', 'text' => '申请人'),
    )
);


//列表显示
$array = array(
    array('key' => 'apply_id,apply_id', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '付款编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/pay/detail/?id={1}">{1}</a>'),
    array('key' => 'amount', 'type' => 'href', 'style' => 'width:120px;text-align:right;', 'text' => '付款金额', 'href_text' => 'showPayAmount'),
    array('key' => 'amount_paid', 'type' => 'href', 'style' => 'width:120px;text-align:right', 'text' => '实付金额', 'href_text' => 'showPaidAmount'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'claim_status', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '认领状态'),
    array('key' => 'payee', 'type' => 'href', 'style' => 'width:120px;text-align:left', 'text' => '收款单位', 'href_text' => '<span title="{1}">{1}</span>'),
    array('key' => 'subject_name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '用途'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '申请人'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:140px;text-align:left', 'text' => '付款申请时间'),
    array('key' => 'project_id', 'type' => 'href', 'style' => 'width:80px;text-align:left;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),

);


$searchArray = ['search_config' => $form_array, 'is_show_reset_button' => true];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);
?>
<script>
    function trash(id) {
		inc.vueConfirm({
			content: "您确定要作废当前信息吗，该操作不可逆？", onConfirm: function() {
				var formData = {id: id};
				$.ajax({
					type: 'POST',
					url: '/pay/trash',
					data: formData,
					dataType: "json",
					success: function (json) {
						if (json.state == 0) {
							inc.vueMessage({duration: 500,message: "操作成功", onClose: function () {
									$("td[value=" + id + "]").parent().remove();
								}
							});
						}
						else {
							inc.vueAlert(json.data);
						}
					},
					error: function (data) {
						inc.vueAlert("操作失败！" + data.responseText);
					}
				});
			}
        });
    }
</script>
