<?php
$form_array = array(
	'form_url' => '/partnerRisk/',
	'input_array' => array(
		array('type' => 'text', 'key' => 'a.name*', 'text' => '企业名称'),
		array('type' => 'select', 'key' => 'a.status', 'map_name' => 'partner_status', 'text' => '合作方状态'),
		array('type' => 'select', 'key' => 'a.type', 'map_name' => 'partner_type', 'text' => '类别&emsp;&emsp;'),
		array('type' => 'select', 'key' => 'risk_status', 'noAll'=>'1', 'map_name' => 'partner_risk_status', 'text' => '状态&emsp;&emsp;'),
		array('type' => 'select', 'key' => 'a.runs_state', 'map_name' => 'runs_state', 'text' => '经营状态&emsp;'),
	)
);
$array = array(
	array('key' => 'partner_id', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowEditAction'),
	array('key' => 'partner_id', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '企业编号'),
	array('key' => 'partner_id,name', 'type' => 'href', 'style' => 'text-align:left;', 'text' => '企业名称', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/partnerApply/detail/?id={1}&t=1" >{2}</a>'),
	array('key' => 'type', 'type' => '', 'style' => 'width:100px;', 'text' => '类别'),
	array('key' => 'corporate', 'type' => '', 'text' => '法人代表', 'style' => 'width:90px;text-align:center'),
	array('key' => 'ownership_name', 'type' => '', 'text' => '企业所有制', 'style' => 'text-align:left'),
	array('key' => 'start_date', 'type' => '', 'text' => '成立日期', 'style' => 'width:100px;text-align:center'),
	array('key' => 'runs_state', 'type' => 'map_val', 'text' => '经营状态', 'map_name' => 'runs_state', 'style' => 'width:80px;text-align:center'),
	array('key' => 'status', 'type' => 'map_val', 'text' => '合作方状态', 'map_name' => 'partner_status', 'style' => 'width:120px;text-align:left'),
	array('key' => 'auto_level', 'type' => 'map_val', 'text' => '系统初审分类', 'map_name' => 'partner_level', 'style' => 'width:100px;text-align:center'),
	array('key' => 'custom_level', 'type' => 'map_val', 'text' => '商务强制分类', 'map_name' => 'partner_level', 'style' => 'width:100px;text-align:center'),
	array('key' => 'apply_amount', 'type' => 'amountWan', 'text' => '拟申请额度', 'style' => 'width:100px;text-align:center'),
	array('key' => 'level', 'type' => 'map_val', 'text' => '风控初审分类', 'map_name' => 'partner_level', 'style' => 'width:100px;text-align:center'),
	array('key' => 'credit_amount', 'type' => 'amountWan', 'text' => '确认额度', 'style' => 'width:100px;text-align:center')
);

function getRowEditAction($row, $self) {
	$links=array();
	if($row["status"]==PartnerApply::STATUS_ON_RISK)
	{
		if($row["risk_status"]==PartnerRisk::STATUS_RISK_NEW)
			$links[]="<a href=\"/partnerRisk/edit?partner_id=".$row["partner_id"] ."&risk_id=".$row["risk_id"] ."\" title=\"修改\">修改</a>";
		else
			$links[]="<a href=\"/partnerRisk/edit?partner_id=".$row["partner_id"] ."\" title=\"添加\">添加</a>";

		$links[]='<a id="i_'. $row["partner_id"] .'" onclick="reject('.$row["partner_id"].')" title="驳回">驳回</a>';
	}
    $links[]='<a href="/partnerRisk/detail/?partner_id=' . $row["partner_id"] . '" title="详情">详情</a>';
	return implode("&nbsp;|&nbsp;",$links);
}

$style = empty($_data_['data']['rows']) ? "min-width:1050px;" : "min-width:1650px;";

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_['data'], "", $style, "table-bordered table-layout");
?>
<script>
	$(function(){

	});

</script>

<script>
    function reject(partner_id) {
		layer.confirm("您确定要执行驳回操作吗？该操作不可逆！", {icon: 3, title: '提示'}, function(index){
			var formData = "partner_id="+partner_id+"&status=<?php echo PartnerRisk::STATUS_RISK_REJECT ?>";
			$.ajax({
				type: "POST",
				url: "/partnerRisk/submit",
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						inc.showNotice("操作成功！");
						$("#i_"+partner_id).parent().parent().remove();
						location.reload();
					} else {
						layer.alert(json.data, {icon: 5})
					}
				},
				error: function (data) {
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});

			layer.close(index);
		});
	}
</script>
