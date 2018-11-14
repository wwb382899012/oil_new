<section>

    <?php

    $this->renderPartial("/common/new_stockNoticeBriefInfo", array('stockNotice'=>$data['stockInBatch'], 'stockNoticeGoods'=>$data['stockInBatch']['items']));

    $stockIns = $data['stockIn'];
    if(is_array($stockIns))
        foreach ($stockIns as $stockIn) {
            $this->renderPartial("/common/new_stockInBriefInfo", array('stockIn'=>$stockIn, 'is_close_card' => true));
        }


    ?>

    <?php
    $menus = [['text' => '结算管理'],['text'=>'入库通知单结算','link'=>'/stockBatchSettlement/'], ['text' => $this->pageTitle]];

    $this->renderPartial("/common/new_settlementDetail", array('settlement'=>$data['settlement'],'type'=>1, 'isCanEdit'=>$data['isCanEdit'],'isCanSubmit'=>$data['isCanSubmit'], 'menus'=>$menus));


    ?>
    <?php
    if(!empty($data['settlement']['settle_id'])) {
        $checkLogs = FlowService::getCheckLog($data['settlement']['batch_id'], 8);
        if (Utility::isNotEmpty($checkLogs)){
            ?>
            <?php
            $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $checkLogs));
            ?>
        <?php }
    }
    ?>

</section>
<script type="text/javascript">

    function edit(batch_id){
        location.href = "/<?php echo $this->getId() ?>/edit?id="+batch_id;
    }

    function submit(batch_id) {
        inc.vueConfirm({
            content: "您确定要提交入库通知单结算信息吗，该操作不可逆？",
            onConfirm: function () {
                var formData = "batch_id=" + batch_id;
                $.ajax({
                    type: "POST",
                    url: "/<?php echo $this->getId() ?>/submit",
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            inc.vueMessage({
                                message: '操作成功', duration:500, onClose: function () {
                                    location.reload();
                                }
                            });
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
