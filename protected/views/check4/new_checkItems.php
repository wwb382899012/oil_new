<?php if (!empty($contractDetailFile)) {
    include $contractDetailFile;
} ?>
<?php
//$menus = [['text' => '合同管理', 'link' => '/check4/'], ['text' => $this->pageTitle]];
//$buttons = [];
$this->loadHeaderWithNewUI($menus, [], true);
//?>
<input type='hidden' name='obj[project_id]' data-bind="value:project_id"/>
<input type='hidden' name='obj[check_id]' data-bind="value:check_id"/>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面操作</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <?php if (!empty($this->checkDetailFile)) {
                    include $this->checkDetailFile;
                } ?>


                <!-- ko component: {
                            name: 'contract-check-items',
                            params: {
                                        items:contractItems
                                    }
                        } -->
                <!-- /ko -->
                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_contractCheckItems.php"; ?>

                <div class="form-group">
                    <label for="remark" class="col-sm-1 control-label">备注</label>
                    <div class="col-sm-11">
                        <textarea class="form-control" id="remark" name="obj[remark]" rows="3" placeholder="备注"
                                  data-bind="value:remark"></textarea>
                    </div>
                </div>
                <?php if (!empty($this->extraCheckItemFile)) {
                    include $this->extraCheckItemFile;
                } ?>
            </div>

            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="passButton" class="btn-danger" data-bind="click:doSubmit">提交</button>

                        <button type="button" class="btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[project_id]' data-bind="value:project_id"/>
                        <input type='hidden' name='obj[check_id]' data-bind="value:check_id"/>
                    </div>
                </div>

            </div>
        </form>
    </div>
</section>
<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
    });

    function ViewModel(option) {
        var defaults = {
            project_id: 0,
            check_id: 0,
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.project_id = ko.observable(o.project_id);
        self.check_id = ko.observable(o.check_id);

        self.remark = ko.observable(o.remark);//.extend({required:true});
        self.status = ko.observable(o.status);

        self.contractItems = ko.observableArray(<?php echo json_encode($extraCheckItems) ?>);
        // self.contractItems = <?php echo json_encode($this->map['contract_check_items']) ?>;

        // self.items=ko.observableArray();

        self.errors = ko.validation.group(self);
        self.isValid = function () {
            if (self.errors().length === 0) {
                var unValid = ko.utils.arrayFilter(self.contractItems(), function (item) {
                    if (!item.isValid()) {
                        item.errors.showAllMessages();
                        return true;
                    }
                    else
                        return false;
                });
                if (unValid.length > 0)
                    return false;
                else
                    return true;
            }
            else
                return false;
        };

        self.doSubmit = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                $(".validationElement").eq(0).focus();
                return;
            }
            var unCheck = ko.utils.arrayFilter(self.contractItems(), function (item) {
                return item.value() < 0;
            });
            if (unCheck.length > 0) {
                // layer.alert("请全部审核完再提交！", {icon: 5});
                inc.vueAlert({content: "请全部审核完再提交！"});
                return;
            }

            inc.vueConfirm({
                content: "您确定要提交当前信息的审核，该操作不可逆？",
                onConfirm: function () {
                    var rejects = ko.utils.arrayFilter(self.contractItems(), function (item) {
                        return item.value() != 1;
                    });
                    if (rejects.length > 0)
                        self.status(-1);
                    else
                        self.status(1);
                    self.save();

                }
            })
        }

        self.postData = function (data) {
            var contracts = inc.getPostData(data);
            for (var i in contracts) {
                for (var j in contracts[i]) {
                    if (j != 'value' && j != 'name' && j != 'id' && j != 'remark')
                        delete contracts[i][j];
                }
            }
            return contracts;
        }

        self.save = function () {

            var formData = $("#mainForm").serialize();
            formData += "&obj[checkStatus]=" + self.status();
            formData += "&items=" + JSON.stringify(ko.toJS(self.postData(self.contractItems())));
            // formData+="&items="+JSON.stringify(ko.toJS(self.contractItems()));
            // var formData = {"data": inc.getPostData(self)};
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/save/',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if (document.referrer)
                            location.href = document.referrer;
                        else
                            location.href = "<?php echo $this->mainUrl ?>";
                    }
                    else {
                        inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
                    inc.vueAlert({content: "保存失败！" + data.responseText});
                }
            });
        }

        self.back = function () {
            history.back();
        }


    }
</script>