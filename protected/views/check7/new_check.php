<section class="content" id="content">
    <?php
    $menus = [['text'=>'入库管理'],['text'=>'入库单审核','link'=>'/check7/?search[checkStatus]=1'], ['text' => $this->pageTitle]];
    $buttons = [];
    $buttons[] = ['text' => '通过', 'attr' => ['data-bind' => 'click:doPass, html:passText']];
    $buttons[] = ['text' => '驳回', 'attr' => ['data-bind' => 'click:doBack, html:backText', 'class_abbr'=>'action-default-base']];
    $this->loadHeaderWithNewUI($menus, $buttons, true);
    ?>

    <div class="card-wrapper">
        <?php $this->renderPartial("/stockIn/partial/new_stockInInfoCard", array('stockIns' => [$model])); ?>

        <div class="z-card">
            <h3 class="z-card-header">
                审核信息
            </h3>
            <div class="z-card-body">
                <form role="form" id="mainForm">
                    <div class="flex-grid">
                        <label class="col col-count-1 field">
                            <p class="form-cell-title">审核意见</p>
                            <textarea class="form-control" cols="105" rows="3" data-bind="value:remark" placeholder="审核意见"></textarea>
                        </label>
                    </div>
                </form>
            </div>
        </div>

        <?php
        $checkLogs = FlowService::getCheckLog($model->stock_in_id, $this->businessId);
        $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status'));
        ?>
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

            inc.vueConfirm({content:"您确定要" + self.confirmText + "该信息的审核，该操作不可逆？", type: 'warning',onConfirm:function(){
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
                            inc.vueMessage({duration: 500,type: 'success', message: '操作成功',onClose:function(){
                                location.href = "/<?php echo $this->getId() ?>";
                            }});
                        } else {
                            self.passText("通过");
                            if(json.state == 1){
                                inc.vueAlert({content:json.data, onClose: function () {
                                        location.href = self.checkListUrl();
                                    }
                                });
                            }else{
                                inc.vueAlert({title:  '错误',content: json.data});
                            }
                        }
                    },
                    error: function (data) {
                        self.updateButtonText();
                        self.actionState = 0;
                        inc.vueAlert({title:  '错误',content: "操作失败！" + data.responseText});
                    }
                });
            }});
        };

        self.back = function () {
            location.href = self.checkListUrl();
        }
    }
</script>