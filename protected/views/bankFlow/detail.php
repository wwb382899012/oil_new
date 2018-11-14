<?php 
?>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">银行流水</h3>
        </div><!--end box box-header-->
        <div class="form-horizontal" role="form" id="mainForm">
            <?php
            $this->renderPartial("/common/bankFlowDetail", array('bankFlow'=>$bankFlow));
            ?>

            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <?php
                        if($bankFlow->status < BankFlow::STATUS_SUBMITED) { ?>
                            <button type="button" class="btn btn-primary" onclick="edit()">修改</button>
                            <button type="button" class="btn btn-danger" onclick="submit()">提交</button>
                        <?php } ?>
                        <button type="button" class="btn btn-default" onclick="back()">返回</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end box box-primary-->
</section><!--end content-->
<script>
    var back=function () {
        window.location.href="/<?php echo $this->getId() ?>/";
    }

    var submit = function() {
		layer.confirm("您确定要提交当前银行流水信息吗，该操作不可逆？", {
			icon: 3,
			'title': '提示'
		}, function (index) {
			var formData = "id=<?php echo $bankFlow->flow_id?>";
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/submit',
				data: formData,
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
	};

	var edit = function () {
		location.href = "/<?php echo $this->getId() ?>/edit?id=<?php echo $bankFlow->flow_id?>";
	}
</script>