<section>
    <?php

    $menus = [['text' => '结算管理'],['text'=>'发货单结算','link'=>'/deliverySettlement/'], ['text' => $this->pageTitle]];

    $this->renderPartial("/common/new_deliverySettlementHead", array('deliveryOrder'=>$data['deliveryOrder']));
    $this->renderPartial("/common/new_outSettlementHead", array('outOrders'=>$data['stockOut'], 'is_close_card' => true));
    $this->renderPartial("/common/new_settlementDetail", array('settlement'=>$data['settlement'],'type'=>3, 'isCanEdit'=>$data['isCanEdit'],'isCanSubmit'=>$data['isCanSubmit'], 'menus'=>$menus));
    ?>

    <?php
    if(!empty($data['settlement']['settle_id'])) {
        $checkLogs = FlowService::getCheckLog($data['settlement']['batch_id'], 10);
        if (Utility::isNotEmpty($checkLogs)){
            ?>
            <div class="z-card">
                <h3 class="z-card-header">
                    审核信息
                </h3>
                <div class="z-card-body">
                    <?php
                    $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $checkLogs));
                    ?>
                </div>
            </div>
        <?php }
    }
    ?>
</section>

<script type="text/javascript">
    function edit(order_id){
        location.href = "/<?php echo $this->getId() ?>/edit?id="+order_id;
    }

    function submit(order_id) {
        inc.vueConfirm({
            content: "您确定要提交发货单结算信息吗，该操作不可逆？",
            onConfirm: function () {
                var formData = "id=" + order_id;
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