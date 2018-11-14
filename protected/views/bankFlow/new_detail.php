<?php
$buttons = [];
if ($bankFlow->status < BankFlow::STATUS_SUBMITED) {
    $buttons[] = ['text' => '修改 ', 'attr' => ['onclick' => 'edit()','class_abbr'=>'action-default-base']];
    $buttons[] = ['text' => '提交 ', 'attr' => ['onclick' => 'submit()']];
}
$this->loadHeaderWithNewUI([], $buttons, '/bankFlow/');
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

    </div><!--end box box-primary-->
</section><!--end content-->
<script>
    var back = function () {
        window.location.href = "/<?php echo $this->getId() ?>/";
    }

    var submit = function () {
        inc.vueConfirm({
            content: "您确定要提交当前银行流水信息吗，该操作不可逆？",
            onConfirm: function () {
                var formData = "id=<?php echo $bankFlow->flow_id?>";
                $.ajax({
                    type: 'POST',
                    url: '/<?php echo $this->getId() ?>/submit',
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            inc.vueMessage({
                                message: json.data
                            });
                            location.href = '/<?php echo $this->getId() ?>/';
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
    };

    var edit = function () {
        location.href = "/<?php echo $this->getId() ?>/edit?id=<?php echo $bankFlow->flow_id?>";
    }
</script>