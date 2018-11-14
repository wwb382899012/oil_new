<link rel="stylesheet" type="text/css" href="/css/businessconfirmdetail.css?key=20180112">
<script type="text/javascript" src="/js/resize.js"></script>
<script type="text/javascript" src="/js/clipboard.js"></script>
<section class="content-header">
    <div class="content-header__des">
        <?php echo empty($this->pageTitle)?$this->moduleName:$this->pageTitle ?>
    </div>
</section>
<section class="content sub-container">
          <!-- 详情综述 -->
        <!-- 详情综述 -->
        <?php
            $this->renderPartial("/common/contractDetail", array('contract'=>$contract));
        ?>
        <!-- 提交保存 -->
        <div class="box box-primary sub-container__box sub-container__fixed">
            <div class="box-body">
                <div class="form-group form-group-custom-btn">
                    <div class="btn-contain-custom">
                        <?php if($this->checkIsCanEdit($contract["status"])){ ?>
                        <button type="button" id="saveButton" class="btn btn-contain__submit" onclick="submitForCheck()">提交</button>
                        <button type="button" class="btn btn-contain__default " onclick="edit()">修改</button>
                        <?php } ?>
                        <?php if(!$this->isExternal){ ?>
                        <button type="button" class="btn btn-contain__default " onclick="back()">返回</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- 提交保存 -->
    </section>
<script>
    function back() {
        // location.href = "<?php echo $this->getBackPageUrl() ?>";
        location.href = "/businessConfirm/";
    }

    function edit() {
        <?php if($contract['is_main']==1){ ?>
            location.href = "/businessConfirm/edit/?id=<?php echo $contract['contract_id'] ?>&project_id=<?php echo $contract['project_id'] ?>";
        <?php }else{ ?>
            location.href = "/subContract/edit/?id=<?php echo $contract['contract_id'] ?>";
        <?php } ?>
    }


    function submitForCheck() {
        layer.confirm("您确定要提交当前信息到风控审核吗，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
            var formData = {id: <?php echo $contract['contract_id'] ?>};
            $.ajax({
                type: 'POST',
                url: '/businessConfirm/submit',
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

    function copy() {
        layer.msg('复制成功', {icon: 6, time: 1000});
    }

    $(document).ready(function() {
        var clipboard = new Clipboard('.copy-project-num');
        $("section.content").trigger('resize');
    });
</script>