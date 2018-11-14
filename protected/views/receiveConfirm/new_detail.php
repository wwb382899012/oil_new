<?php
$menus = [['text' => '流水认领', 'link' => '/receiveConfirm/'], ['text' => '详情']];
$buttons = [];
if ($receiveConfirm->status == ReceiveConfirm::STATUS_NEW) {
    $buttons[] = ['text' => '提交', 'attr' => ['onclick' => 'submit()', 'id' => 'editButton']];
    $buttons[] = ['text' => '修改', 'attr' => ['onclick' => 'edit()', 'id' => 'editButton','class_abbr'=>'action-default-base']];
}
$this->loadHeaderWithNewUI([], $buttons, '/receiveConfirm/');
?>

<section class="content">

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>银行流水</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="item-com">
            <li>
                <label>认领人：</label>
                <p><?php echo $receiveConfirm->creator->name ?></p>
            </li>
            <li>
                <label>可认领金额：</label>
                <p class="form-control-static"><?php echo $this->map['currency'][$bankFlow->currency]['ico'] ?><?php echo number_format(($bankFlow->amount - $bankFlow->amount_claim) / 100, 2) ?></p>
            </li>
            <?php
            $this->renderPartial("/common/new_bankFlowDetail", array('bankFlow' => $bankFlow));
            ?>
        </ul>
        <div class="flex-grid">
            <label class="col col-count-1 field flex-grid">
                <p class="form-cell-title w-fixed">备注:</p>
                <span><?php echo $bankFlow->remark ?></span>
            </label>
        </div>
    </div>

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>认领详情</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <?php
        $this->renderPartial("/common/new_receiveConfirmDetail", array('bankFlow' => $bankFlow, 'receiveConfirm' => $receiveConfirm, 'attachments' => $attachs));
        ?>
    </div>

</section>
<script>
    var back_url = "";
    <?php
    if(!empty($_GET['back_url'])) { ?>
    back_url = "<?php echo $_GET['back_url'] ?>";
    <?php } ?>
    var back = function () {
        if (!inc.isEmpty(back_url)) {
            location.href = "/" + back_url + "/"
        } else {
            <?php
            if($receiveConfirm->status >= ReceiveConfirm::STATUS_SUBMITED) { ?>
            location.href = "/<?php echo $this->getId() ?>";
            <?php } else { ?>
            location.href = "/<?php echo $this->getId() ?>/view?flow_id=<?php echo $receiveConfirm->flow_id ?>";
            <?php } ?>
        }
    }
    var edit = function () {
        window.location.href = "/<?php echo $this->getId() ?>/edit/?id=<?php echo $receiveConfirm->receive_id?>";
    }
    var submit = function () {
		inc.vueConfirm({
			content: "是否确认提交流水认领单，本操作无法撤回？", onConfirm: function () {
				$.ajax({
					type: "POST",
					url: "/<?php echo $this->getId() ?>/submit",
					data: {
						id:<?php echo $receiveConfirm->receive_id?>
					},
					dataType: "json",
					success: function (json) {
						if (json.state == 0) {
							inc.vueMessage({
								message: "提交成功"
							});
                            var url = "/<?php echo $this->getId() ?>/detail?id=" + json.data;
                            if (!inc.isEmpty(back_url)) {
                                url = url + "&back_url=" + back_url;
                            }
                            location.href = url;
						} else {
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