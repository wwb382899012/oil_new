<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#detail" data-toggle="tab">保理信息</a></li>
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
            <li class="pull-right">
                <?php
                if ($detail->isCanEdit($detail->status)) { ?>
                    <button type="button" class="btn btn-sm btn-primary" onclick="edit()">修改</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-danger" onclick="submit()">提交</button>&nbsp;
                <?php } ?>
                <?php if (!$this->isExternal) { ?>
                    <button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button>
                <?php } ?>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="detail">
                <div class="box-body">
                    <form class="form-horizontal" role="form" id="mainForm">
                        <?php include "partial/factorInfo.php"; ?>
                    </form>
                </div>
            </div>
            <div class="tab-pane" id="flow">
                <?php
                $checkLogs = FlowService::getCheckLog($detail->detail_id, FlowService::BUSINESS_FACTORING);
                $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status')); ?>
            </div>
        </div>
    </div>
</section>
<script>
	function back() {
		location.href = "/<?php echo $this->getId() ?>";
	}

	function edit() {
		location.href = "/<?php echo $this->getId() ?>/edit/?id=<?php echo $detail->detail_id ?>";
	}

	function submit() {
		layer.confirm("您确定要提交当前保理信息吗，该操作不可逆？", {
			icon: 3,
			'title': '提示'
		}, function (index) {
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/submit',
				data: {
					"data": {
						detail_id: <?php echo $detail->detail_id ?>,
						status: 10
					}
				},
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						layer.msg(json.data, {icon: 6, time: 1000}, function () {
							location.href = '/<?php echo $this->getId() ?>/';
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