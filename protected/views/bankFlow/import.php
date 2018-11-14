<?php 
?>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box box-primary">
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="form-group">
                <label class="col-sm-2 control-label">
                    银行流水模板
                </label>
                <div class="col-sm-10">
                    <p class="form-control-static">
                        <a href="/html/%E9%93%B6%E8%A1%8C%E6%B5%81%E6%B0%B4%E6%A8%A1%E6%9D%BF.xlsx">银行流水模板下载</a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <?php
                $attachType = $this->map["bank_flow_file_type"][ConstantMap::STOCK_BATCH_ATTACH_TYPE];
                ?>
                <label class="col-sm-2 control-label">
                    <span class='glyphicon ' data-bind="css:{'glyphicon-ok text-green':fileUploadStatus,' glyphicon-remove text-red':!fileUploadStatus()}"></span>&emsp;
                    <?php echo $attachType["name"] ?></label>
                <div class="col-sm-10">

                    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/uploadUndeletable.php"; ?>
                    <!-- ko component: {
                         name: "file-upload",
                         params: {
                                     status:fileUploadStatus,
                                     controller:"<?php echo $this->getId() ?>",
                                     fileConfig:<?php echo json_encode($attachType) ?>,
                                     files:<?php echo json_encode($attachments[ConstantMap::STOCK_NOTICE_ATTACH_TYPE]); ?>,
                                     baseId: temp_id
                                     }
                     } -->
                    <!-- /ko -->
                </div>
            </div>


            <div class="form-group" id="returnInfos">
            </div>

            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" class="btn btn-danger" data-bind="click:submit">提交</button>
                        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                    </div>
                </div>
            </div>
        </form>
    </div><!--end box box-primary-->

</section><!--end content-->
<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode(array('temp_id'=>$temp_id)) ?>);
        ko.applyBindings(view);
    });
    function ViewModel(option) {
        var defaults = {
            temp_id:1
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.isSubmit = ko.observable(0);
        self.temp_id = o.temp_id;
        self.fileUploadStatus = ko.observable();
        self.fileUploadStatus.subscribe(function(newValue) {
            if(newValue == 1) {
                $.ajax({
                    data: {
                        temp_id:this.temp_id
                    },
                    url:'/bankFlow/readFile',
                    success:function(data) {
                        $("#returnInfos").html(data);
                    },
                    error:function(data) {
                        layer.alert("操作失败！" + data.responseText, {icon: 5});
                    }
                })
            }
        }, self);
        self.sendSaveSubmitAjax = function () {
            if ($("tr.text-red").length>0) {
                layer.alert('必须全部信息正确才能导入');
                return;
            }
            if (self.actionState == 1)
                return;
            self.actionState = 1;
            var formData = {"temp_id": self.temp_id};
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/importSave',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg('操作成功', {icon: 6, time: 1000}, function () {
                            if (self.isSubmit() == 1) {
                                location.href = "/<?php echo $this->getId() ?>";
                            } else {
                                location.href = "/<?php echo $this->getId() ?>/detail/?id=" + self.contract_id();
                            }
                        });
                    } else {
                        self.actionState = 0;
                        self.isSubmit(0);
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    self.actionState = 0;
                    self.isSubmit(0);
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
        };

        self.submit = function () {
            layer.confirm("您确定要提交当前银行流水录入信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
                self.isSubmit(1);
                self.sendSaveSubmitAjax();
                layer.close(index);
            });
        };

        self.back = function () {
            history.back();
        }
    }
</script>