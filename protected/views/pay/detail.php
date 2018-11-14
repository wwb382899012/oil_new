<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">付款申请详情</h3>
            <div class="pull-right box-tools">
                <?php if($apply->isCanEdit()){ ?>
                    <button type="button" id="submitButton" class="btn btn-danger" onclick="submitForCheck()">提交</button>
                    <button type="button" id="editButton" class="btn btn-primary" onclick="edit()">修改</button>
                <?php } ?>
                <?php if(!$this->isExternal){ ?>
                    <button type="button"  class="btn btn-default" onclick="back()">返回</button>
                <?php } ?>
            </div>
        </div>
        <div class="box-body form-horizontal">
            <?php $this->renderPartial("/pay/detailBody", array('apply'=>$apply)); ?>
            <h4 class="section-title">审核记录</h4>
            <?php

            $checkLogs=FlowService::getCheckLogModel($apply->apply_id,FlowService::BUSINESS_PAY_APPLICATION);
            if(Utility::isNotEmpty($checkLogs))
                $this->renderPartial("/check/checkLogs",array("checkLogs"=>$checkLogs)); ?>
        </div>
        <div class="box-footer">
            <?php if($apply->isCanEdit()){ ?>
                <button type="button" id="submitButton" class="btn btn-danger" onclick="submitForCheck()">提交</button>
                <button type="button" id="editButton" class="btn btn-primary" onclick="edit()">修改</button>
            <?php } ?>
            <?php if(!$this->isExternal){ ?>
                <button type="button"  class="btn btn-default" onclick="back()">返回</button>
            <?php } ?>
        </div>

    </div>


</section>

<script>
    function back() {
        location.href = "/pay/";
    }

    function edit() {
        location.href = "/pay/edit/?id=<?php echo $apply['apply_id'] ?>";
    }


    function submitForCheck() {
        layer.confirm("您确定要提交当前信息进入审核吗，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
            var formData = {id: <?php echo $apply['apply_id'] ?>};
            $.ajax({
                type: 'POST',
                url: '/pay/submit',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg("操作成功", {icon: 6, time:1000}, function(){
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