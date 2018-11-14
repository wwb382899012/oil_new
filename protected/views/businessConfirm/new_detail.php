<link rel="stylesheet" type="text/css" href="/css/businessconfirmdetail.css?key=20180112">
<link rel="stylesheet" type="text/css" href="/newUI/css/business-confirm/detail.css">
<link href="/js/plugins/layer/skin/default/layer.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="/newUI/css/business-confirm/detail.css">
<script type="text/javascript" src="/js/resize.js"></script>
<script type="text/javascript" src="/js/clipboard.js"></script>

<?php
$buttons = [];
if ($this->checkIsCanEdit($contract["status"])) {
    $buttons[] = ['text' => '提交', 'attr' => ['onclick' => 'submitForCheck()', 'id' => 'saveButton']];
    $buttons[] = ['text' => '修改', 'attr' => ['onclick' => 'edit()', 'class_abbr' => 'action-default-base']];
}
$this->loadHeaderWithNewUI([], $buttons, '/businessConfirm/');
?>

<section class="content sub-container">
    <!-- 详情综述 -->
    <!-- 详情综述 -->
    <?php
    $this->renderPartial("/common/new_contractDetail", array('contract' => $contract));
    ?>
</section>
<script>
    function back() {
        // location.href = "<?php echo $this->getBackPageUrl() ?>";
        location.href = "/businessConfirm/";
    }

    function edit() {
        <?php if($contract['is_main'] == 1){ ?>
        location.href = "/businessConfirm/edit/?id=<?php echo $contract['contract_id'] ?>&project_id=<?php echo $contract['project_id'] ?>";
        <?php }else{ ?>
        location.href = "/subContract/edit/?id=<?php echo $contract['contract_id'] ?>";
        <?php } ?>
    }


    function submitForCheck() {
        inc.vueConfirm({
            content: "您确定要提交当前信息到风控审核吗，该操作不可逆？",
            onConfirm: function (index) {
                var formData = {id: <?php echo $contract['contract_id'] ?>};
                $.ajax({
                    type: 'POST',
                    url: '/businessConfirm/submit',
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            inc.vueMessage({
                                message: "操作成功"
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

    function copy() {
        inc.vueMessage('复制成功');
    }

    $(document).ready(function () {
        var clipboard = new Clipboard('.copy-project-num');
        $("section.content").trigger('resize');
    });
</script>