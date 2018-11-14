<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#detail" data-toggle="tab">待审核信息</a></li>
            <li><a href="#flow" data-toggle="tab">历史审核记录</a></li>
            <?php if (!$this->isExternal) { ?>
                <li class="pull-right">
                    <button type="button" class="btn btn-sm btn-default" data-bind="click:back">返回</button>
                </li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="detail">
                <div class="box box-default">
                    <form class="form-horizontal" role="form" id="mainForm">
                        <div class="box-body">
                            <?php
                            if (!empty($this->detailPartialFile))
                                $this->renderPartial($this->detailPartialFile, array($this->detailPartialModelName => $model));
                            include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/checkItems.php";
                            ?>
                            <h4 class="section-title">审核信息</h4>

                            <check-items params='items: items'></check-items>
                            <div class="form-group">
                                <label for="type" class="col-sm-2 control-label">审核意见
                                    <span class="text-red fa fa-asterisk"></span></label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" data-bind="value:remark"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="box-footer">
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <?php if ($this->checkButtonStatus["pass"] == 1) { ?>
                                    <button type="button" id="passButton" class="btn btn-success"
                                            data-bind="click:doPass,html:passText">通过
                                    </button>
                                <?php } ?>
                                <?php if ($this->checkButtonStatus["back"] == 1) { ?>
                                    <button type="button" id="checkBackButton" class="btn btn-danger"
                                            data-bind="click:doBack,html:backText">驳回
                                    </button>
                                <?php } ?>
                                <?php if ($this->checkButtonStatus["reject"] == 1) { ?>
                                    <button type="button" id="rejectButton" class="btn btn-danger"
                                            data-bind="click:doReject,html:rejectText">拒绝
                                    </button>
                                <?php } ?>
                                <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="flow">
                <?php
                $checkLogs = FlowService::getCheckLog($data['obj_id'], $this->businessId);
                $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs));
                ?>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        view.formatItems(<?php echo json_encode($items) ?>);
        ko.applyBindings(view);
    });

    function ViewModel(option) {
        var defaults = {
            check_id: 0,
            detail_id: 0,
            remark: ''
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.check_id = o.check_id;
        self.detail_id = o.detail_id;
        self.status = ko.observable(o.status);
        self.items = ko.observableArray();
        self.remark = ko.observable(o.remark).extend({required: true, maxLength: 512});
        self.errors = ko.validation.group(self);

        self.passText = ko.observable('通过');
        self.backText = ko.observable('驳回');
        self.rejectText = ko.observable('拒绝');

        self.actionState = 0;
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.formatItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                self.items.push(data[i]);
            }
        }

        self.confirmText = "";

        self.doPass = function () {
            self.confirmText = "通过";
            self.status(1);
            self.save();
        }
        self.doBack = function () {
            self.confirmText = "驳回";
            self.status(-1);
            self.save();
        }
        self.doReject = function () {
            self.confirmText = "拒绝";
            self.status(0);
            self.save();
        }

        self.updateButtonText = function () {
            if (self.actionState == 1) {
                switch (self.status()) {
                    case 1:
                        self.passText("通过 " + inc.loadingIco);
                        break;
                    case 0:
                        self.backText("驳回 " + inc.loadingIco);
                        break;
                    case -1:
                        self.rejectText("拒绝 " + inc.loadingIco);
                        break;
                }
            }
            else {
                switch (self.status()) {
                    case 1:
                        self.passText("通过");
                        break;
                    case 0:
                        self.backText("驳回");
                        break;
                    case -1:
                        self.rejectText("拒绝");
                        break;
                }
            }

        }

        self.save = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            if (self.actionState == 1)
                return;

            inc.vueConfirm({
                content: "您确定要" + self.confirmText + "该信息的审核，该操作不可逆？",
                onConfirm: function () {

                    var formData = {
                        data: {
                            items: self.items.getValues(),
                            check_id: self.check_id,
                            detail_id: self.detail_id,
                            checkStatus: self.status(),
                            remark: self.remark()
                        }
                    };
                    self.actionState = 1;
                    self.updateButtonText();
                    $.ajax({
                        type: "POST",
                        url: "/<?php echo $this->getId() ?>/save",
                        data: formData,
                        dataType: "json",
                        success: function (json) {
                            self.updateButtonText();
                            self.actionState = 0;
                            if (json.state == 0) {
                                inc.vueMessage({
                                    message: '操作成功'
                                });
                                location.href = "/<?php echo $this->getId() ?>";
                            } else {
                                inc.vueAlert(json.data);
                            }

                        },
                        error: function (data) {
                            self.updateButtonText();
                            self.actionState = 0;
                            inc.vueAlert({content: "操作失败：" + data.responseText});
                        }
                    });
                }
            })
        }

        self.back = function () {
            location.href = "/<?php echo $this->getId() ?>/?search[checkStatus]=1";
        }
    }
</script>
