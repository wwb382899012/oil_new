<section class="content" id="content">
    <div class="form-horizontal">
        <?php $this->renderPartial("/stockIn/partial/stockInInfo", array('stockIn' => $model,'isShowBackButton'=>true)); ?>
    </div>

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">审核信息</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">审核意见</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" data-bind="value:remark"></textarea>
                    </div>
                </div>

            </form>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" class="btn btn-success" data-bind="click:doPass,html:passText"></button>
                    <button type="button" class="btn btn-danger" data-bind="click:doBack,html:backText"></button>
                    <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="flow">
                <?php
                $checkLogs = FlowService::getCheckLog($model->stock_in_id, $this->businessId);
                $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status')); ?>
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
        self.items=ko.observableArray();
        self.remark=ko.observable(o.remark).extend({required:true,maxLength:512});
        self.errors = ko.validation.group(self);

        self.passText = ko.observable('通过');
        self.backText = ko.observable('驳回');
        self.rejectText = ko.observable('拒绝');
        self.checkListUrl = ko.observable('/<?php echo $this->getId() ?>/?search[checkStatus]=1');

        self.actionState = 0;
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.formatItems=function(data)
        {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                self.items.push(data[i]);
            }
        };

        self.confirmText="";

        self.doPass = function () {
            self.confirmText="通过";
            self.status(<?php echo Check::CHECK_DONE; ?>);
            self.save();
        };
        self.doBack = function () {
            self.confirmText="驳回";
            self.status(<?php echo Check::CHECK_BACK; ?>);
            self.save();
        };
        self.doReject = function () {
            self.confirmText="拒绝";
            self.status(<?php echo Check::CHECK_REJECT; ?>);
            self.save();
        };

        self.updateButtonText=function(){
            if(self.actionState==1)
            {
                switch (self.status())
                {
                    case <?php echo Check::CHECK_DONE; ?>:
                        self.passText("通过 "+inc.loadingIco);
                        break;
                    case <?php echo Check::CHECK_BACK; ?>:
                        self.backText("驳回 "+inc.loadingIco);
                        break;
                    case <?php echo Check::CHECK_REJECT; ?>:
                        self.rejectText("拒绝 "+inc.loadingIco);
                        break;
                }
            }
            else
            {
                switch (self.status())
                {
                    case <?php echo Check::CHECK_DONE; ?>:
                        self.passText("通过");
                        break;
                    case <?php echo Check::CHECK_BACK; ?>:
                        self.backText("驳回");
                        break;
                    case <?php echo Check::CHECK_REJECT; ?>:
                        self.rejectText("拒绝");
                        break;
                }
            }

        };

        self.save = function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }

            if(self.actionState==1)
                return;

            layer.confirm("您确定要" + self.confirmText + "该信息的审核，该操作不可逆？", {icon: 3, title: '提示'}, function () {

                var formData = {
                    data: {
                        items:self.items(),
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
                            layer.msg('操作成功', {icon: 6, time: 1000}, function () {
                                location.href = "/<?php echo $this->getId() ?>";
                            });
                        } else {
                            self.passText("通过");
                            if(json.state == 1){
                                layer.alert(json.data, {icon: 5,yes:function(){
                                    location.href = self.checkListUrl();
                                }},);
                            }else{
                                layer.alert(json.data);
                            }
                        }
                    },
                    error: function (data) {
                        self.updateButtonText();
                        self.actionState = 0;
                        layer.alert("操作失败：" + data.responseText, {icon: 5});
                    }
                });
            });
        };

        self.back = function () {
            location.href = self.checkListUrl();
        }
    }
</script>