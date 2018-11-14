<?php
$menus=$this->getIndexMenuWithNewUI();

$menus[] = ['text' => $this->pageTitle];
$buttons = [];
if ($this->checkIsCanEdit($data['status'])) {
    $buttons[] = ['text' => '修改', 'attr' => ['onclick' => "edit({$data['apply_id']})"]];
    $buttons[] = ['text' => '提交', 'attr' => ['onclick' => "submit({$data['apply_id']})"]];
}
$this->loadHeaderWithNewUI($menus, $buttons, true);
?>

<?php $this->renderPartial("/pay/new_detailBody", array('apply' => $model)); ?>

<?php include "new_payInfo.php" ?>

<?php $this->renderPartial("/payStop/new_stop", array('apply' => $model)); ?>
<?php
$checkLogs = FlowService::getCheckLog($data['apply_id'], 19);
if (Utility::isNotEmpty($checkLogs))
    $this->renderPartial("/common/new_checkLogList", array("checkLogs" => $checkLogs));
?>


<script>
    function back() {
        location.href = "/<?php echo $this->getId() ?>/";
    }

    function edit(apply_id) {
        location.href = "/<?php echo $this->getId() ?>/edit?id=" + apply_id;
    }

    function submit(apply_id) {
        inc.vueConfirm({
            content: "您确定要提交当前止付信息吗，该操作不可逆？",
            onConfirm: function () {
                var formData = "id=" + apply_id;
                $.ajax({
                    type: "POST",
                    url: "/<?php echo $this->getId() ?>/submit",
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            inc.vueMessage({
                                message: json.data
                            });
                            location.reload();
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
        })
    }
</script>