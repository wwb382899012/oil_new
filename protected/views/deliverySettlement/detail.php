<?php
$this->renderPartial("/common/deliverySettlementHead", array('deliveryOrder'=>$data['deliveryOrder']));
$this->renderPartial("/common/outSettlementHead", array('outOrders'=>$data['stockOut']));
?>

<!--<section class="content">-->

<!--    <div class="box-body form-horizontal">-->
        <?php $this->renderPartial("/common/settlementDetail", array('settlement'=>$data['settlement'],'type'=>3, 'isCanEdit'=>$data['isCanEdit'],'isCanSubmit'=>$data['isCanSubmit']));

        if(!empty($data['settlement']['settle_id'])){
            $checkLogs = FlowService::getCheckLog($data['settlement']['order_id'], 10);
            if(Utility::isNotEmpty($checkLogs))
                $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs));
        }
        ?>
<!--    </div>-->
<!--</section>-->

<script type="text/javascript">
    function back() {
        // history.back();
        location.href = /<?php echo $this->getId() ?>/;
    }

    function edit(order_id){
        location.href = "/<?php echo $this->getId() ?>/edit?id="+order_id;
    }


    function submit(order_id){
        layer.confirm("您确定要提交发货单结算信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
            var formData = "id=" + order_id;
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/submit",
                data: formData,
                dataType: "json",
                success: function (json) {
                    if(json.code==0){
                        layer.msg('操作成功', {time: 1000}, function () {
                            location.reload();
                        });
                    }
                    else {
                        layer.alert(json.msg, {icon: 5},function(){
                            location.reload();
                        });
                    }
                },
                error: function (data) {
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });

        });
    }
</script>