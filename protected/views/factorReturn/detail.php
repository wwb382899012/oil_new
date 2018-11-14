<?php
/**
 * Desc: 保理回款明细
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//列表显示
$array = array(
    array('key' => 'id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'return_date', 'type' => '', 'style' => 'width:120px;text-align:center;', 'text' => '实际回款日'),
    array('key' => 'period', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '实际期限（天）'),
    array('key' => 'amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;', 'text' => '回款本息合计（元）'),
    array('key' => 'capital_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;', 'text' => '回款本金（元）'),
    array('key' => 'interest', 'type' => 'amount', 'style' => 'width:120px;text-align:right;', 'text' => '回款利息（元）'),
    array('key' => 'overdue_period', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '逾期时间（天）'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'factor_returned_status', 'style' => 'width:60px;text-align:center', 'text' => '状态'),
);

function getRowActions($row, $self) {
    $links = array();
    if ($row['status'] < FactorReturn::STATUS_SUBMIT) {
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["id"] . '" title="修改">修改</a>';
        $links[] = '<a onclick="submit(' . $row['id'] . ')" title="提交">提交</a>';
    }

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

$this->show_table_nopage($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");
?>
<button type="button" class="btn btn-default pull-right" onclick="back()">返回</button>

<script>
	function back() {
		location.href = '/<?php echo $this->getId() ?>';
	}

	function submit(id) {
		layer.confirm("您确定要提交当前保理回款信息吗，该操作不可逆？", {
			icon: 3,
			'title': '提示'
		}, function (index) {
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/submit',
				data: {
					"data": {
						id: id,
						status: 1
					}
				},
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						layer.msg('操作成功！', {icon: 6, time: 1000}, function () {
                            location.reload();
						});
					}
					else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});
			layer.close(index);
		})
	}
</script>
