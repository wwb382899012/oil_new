<?php
$menus = [['text' => '付款止付', 'link' => '/payStop/'], ['text' => $this->pageTitle]];
$buttons = [];
if ($data['status'] == Payment::STATUS_SAVED) {
    $buttons[] = ['text' => '修改', 'attr' => ['onclick' => "edit({$data['apply_id']})", 'class_abbr' => 'action-default-base']];
    $buttons[] = ['text' => '提交', 'attr' => ['onclick' => "submit({$data['payment_id']})"]];
}
$this->loadHeaderWithNewUI([], $buttons, '/payConfirm/');
?>

<?php $this->renderPartial("/pay/new_detailBody", array('apply' => $model)); ?>

<?php include "new_payInfo.php" ?>

</div>


<script>
    function back() {
        location.href = "/<?php echo $this->getId() ?>/";
    }

    function edit(apply_id) {
        location.href = "/<?php echo $this->getId() ?>/edit?id=" + apply_id;
    }

    function submit(payment_id) {
        inc.vueConfirm({
            content: "您确定要提交当前实付信息吗，该操作不可逆？",
            onConfirm: function () {
                var formData = "id=" + payment_id;
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