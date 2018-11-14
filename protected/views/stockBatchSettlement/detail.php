<?php
$this->renderPartial("/common/stockNoticeBriefInfo", array('stockNotice'=>$data['stockInBatch'], 'stockNoticeGoods'=>$data['stockInBatch']['items']));

$stockIns = $data['stockIn'];
if(is_array($stockIns))
    foreach ($stockIns as $stockIn) {
        $this->renderPartial("/common/stockInBriefInfo", array('stockIn'=>$stockIn));
    }


?>

<!--<section class="content">-->

<!--    <div class="box-body form-horizontal">-->

        <?php
        $this->renderPartial("/common/settlementDetail", array('settlement'=>$data['settlement'],'type'=>1, 'isCanEdit'=>$data['isCanEdit'],'isCanSubmit'=>$data['isCanSubmit']));

        if(!empty($data['settlement']['settle_id'])) {
            $checkLogs = FlowService::getCheckLog($data['settlement']['batch_id'], 8);
            if (Utility::isNotEmpty($checkLogs))
                $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs));
        }
        ?>
<!--</div>-->
<!--    </div>-->
<!--</section>-->

<script type="text/javascript">
    function back() {
       location.href = '/<?php echo $this->getId() ?>/';
        // history.back();
    }

    function edit(batch_id){
        location.href = "/<?php echo $this->getId() ?>/edit?id="+batch_id;
    }

    function submit(batch_id){
        layer.confirm("您确定要提交入库通知单结算信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
            var formData = "batch_id=" + batch_id;
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
            layer.close(index);
        });
    }
</script>
