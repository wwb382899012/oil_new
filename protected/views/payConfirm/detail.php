<div class="box">
    <div class="box-body form-horizontal">
        <div class="box-header with-border">
            <h3 class="box-title">付款实付详情</h3>
            <div class="pull-right box-tools">
                <?php
                 if ($data['status'] == Payment::STATUS_SAVED) { ?>
                    <button type="button" class="btn btn-sm btn-primary" onclick="edit(<?php echo $data['apply_id'] ?>)">修改</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo $data['payment_id'] ?>)">提交</button>&nbsp;
                 <?php } ?>
                <?php if (!$this->isExternal) { ?>
                    <button type="button" class="btn btn-sm btn-default history-back" onclick="back()">返回</button>
                <?php } ?>
            </div>
        </div>
        <div class="box-body">
            <?php $this->renderPartial("/pay/detailBody", array('apply'=>$model)); ?>

            <?php include "payInfo.php" ?>
        </div>
    </div>

    <div class="box-footer">
        <?php
        if ($data['status'] == Payment::STATUS_SAVED) { ?>
           <button type="button" class="btn btn-primary" onclick="edit(<?php echo $data['apply_id'] ?>)">修改</button>&nbsp;
           <button type="button" class="btn btn-danger" onclick="submit(<?php echo $data['payment_id'] ?>)">提交</button>&nbsp;
        <?php } ?>
        <?php if (!$this->isExternal) { ?>
            <button type="button" class="btn btn-default history-back" onclick="back()">返回</button>
        <?php } ?>
    </div>
</div>


<script>
	function back() {
		location.href="/<?php echo $this->getId() ?>/";
	}

    function edit(apply_id) {
        location.href = "/<?php echo $this->getId() ?>/edit?id=" + apply_id;
    }

    function submit(payment_id) {
        layer.confirm("您确定要提交当前实付信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
            var formData = "id=" + payment_id;
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/submit",
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg(json.data, {icon: 6, time: 1000}, function () {
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
        });
    }
</script>