<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<link rel="stylesheet" href="/css/style/addnewproject.css">
<?php
$menus = [];
$buttons = [];
$buttons[] = ['text' => '提交 ', 'attr' => ['data-bind' => 'click:submit']];
$this->loadHeaderWithNewUI($menus, $buttons, true);
?>

<section class="content">
    <div class="card-wrapper">
        <form role="form" id="mainForm">

            <div class="z-card">
                <div class="z-card-part">
                    <h3 class="z-body-header">银行流水录入</h3>
                </div>

                <div class="flex-grid form-group">
                    <label class="col col-count-1 field flex-grid">
                        <p class="form-cell-title w-fixed ">银行流水模板</p>
                        <p class="form-control-static">
                            <a class="text-link"
                               href="/html/%E9%93%B6%E8%A1%8C%E6%B5%81%E6%B0%B4%E6%A8%A1%E6%9D%BF.xlsx">银行流水模板下载</a>
                        </p>
                    </label>
                </div>
                <div class="flex-grid form-group">
                    <div class="col col-count-1 field flex-grid">
                        <?php
                        $attachType = $this->map["bank_flow_file_type"][ConstantMap::STOCK_BATCH_ATTACH_TYPE];
                        ?>
                        <p class="form-cell-title w-fixed must-fill first-line-align">
                            <?php echo $attachType["name"] ?>
                        </p>
                        <div class="form-group-custom-upload">

                            <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_uploadUndeletable.php"; ?>
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
                </div>
                <div class="form-group" id="returnInfos">
                </div>

            </div>
        </form>
    </div>


</section><!--end content-->
<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode(array('temp_id' => $temp_id)) ?>);
        ko.applyBindings(view);
    });

    function ViewModel(option) {
        var defaults = {
            temp_id: 1
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.isSubmit = ko.observable(0);
        self.temp_id = o.temp_id;
        self.fileUploadStatus = ko.observable();
        self.fileUploadStatus.subscribe(function (newValue) {
            if (newValue == 1) {
                $.ajax({
                    data: {
                        temp_id: this.temp_id
                    },
                    url: '/bankFlow/readFile',
                    success: function (data) {
                        $("#returnInfos").html(data);
                    },
                    error: function (data) {
                        inc.vueAlert("操作失败！" + data.responseText);
                    }
                })
            }
        }, self);
        self.sendSaveSubmitAjax = function () {
            if ($("tr.text-red").length > 0) {
                inc.vueAlert({content: '必须全部信息正确才能导入'});
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
                        inc.vueMessage({message: '操作成功'});
                        if (self.isSubmit() == 1) {
                            location.href = "/<?php echo $this->getId() ?>";
                        } else {
                            location.href = "/<?php echo $this->getId() ?>/detail/?id=" + self.contract_id();
                        }
                    } else {
                        self.actionState = 0;
                        self.isSubmit(0);
                        inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
                    self.actionState = 0;
                    self.isSubmit(0);
                    inc.vueAlert("操作失败！" + data.responseText);
                }
            });
        };

        self.submit = function () {
            inc.vueConfirm({
                content: "您确定要提交当前银行流水录入信息吗，该操作不可逆？", onConfirm: function () {
                    self.isSubmit(1);
                    self.sendSaveSubmitAjax();
                }
            });
        };

        self.back = function () {
            history.back();
        }
    }
</script>