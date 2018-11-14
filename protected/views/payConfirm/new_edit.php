<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<script src="/js/jquery.bankInput.js"></script>
<link rel="stylesheet" href="/css/style/addnewproject.css">
<?php
$menus = [['text' => '付款实付', 'link' => '/payConfirm/'], ['text' => $this->pageTitle]];
$buttons = [];
$buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText', 'id' => 'saveButton', 'class_abbr' => 'action-default-base']];
$buttons[] = ['text' => '保存并提交', 'attr' => ['data-bind' => 'click:submit, html:submitBtnText', 'id' => 'submitButton']];
$this->loadHeaderWithNewUI([], $buttons, '/payConfirm/');
?>
<input type='hidden' name='obj[payment_id]' data-bind="value:payment_id"/>
<input type='hidden' name='obj[apply_id]' data-bind="value:apply_id"/>
<input type='hidden' name='obj[currency]' data-bind="value:currency"/>
<input type='hidden' name='obj[balance_amount]' data-bind="value:balance_amount"/>


<section class="el-container is-vertical">
    <?php
    $this->renderPartial("/pay/new_detailBody", array('apply' => $model));
    include "new_payInfo.php"
    ?>
    <div class="card-wrapper">
        <form role="form" id="mainForm">
            <div class="z-card">
                <div class="z-card-part">
                    <h3 class="z-body-header">实际付款信息</h3>
                    <div class="flex-grid form-group">
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title must-fill">实付日期</p>
                            <input type="text" class="form-control date" id="pay_date" name="obj[pay_date]"
                                   placeholder="" data-bind="value:pay_date">
                        </label>
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title must-fill">实付金额</p>
                            <div class="input-group">
                                <span class="input-group-addon" data-bind="html:currency_ico"></span>
                                <input type="text" class="form-control date" id="amount" name="obj[amount]"
                                       placeholder="实付金额" data-bind="money:amount">
                            </div>
                            <p class="form-control-static" data-bind="visible:isDisplay"><span></span>￥ <span
                                        data-bind="moneyText:amount_cny"></span></p>
                        </label>
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">剩余金额</p>
                            <div class="input-group">
                                <span class="input-group-addon" data-bind="html:currency_ico"></span>
                                <input class="form-control date" type="text" disabled
                                       value="<?php echo number_format($data['balance_amount'] / 100, 2) ?>">
                            </div>
                        </label>
                    </div>
                    <div class="flex-grid form-group">
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">付款账户名</p>
                            <select class="form-control selectpicker show-menu-arrow" id="corporation_id"
                                    name="obj[corporation_id]"
                                    data-live-search="true"
                                    data-bind="optionsCaption: '请选择付款账户名',selectpicker:corporation_id,valueAllowUnset: true,enable:false">
                                <option value=''>请选择付款账户名</option>
                                <?php
                                $cors = UserService::getUserSelectedCorporations();
                                foreach ($cors as $v) {
                                    echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                                } ?>
                            </select>
                        </label>
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">实付银行</p>
                            <select class="form-control selectpicker show-menu-arrow" name="obj[bank_name]" id="bank_name"
                                    data-bind="value: bank_name,valueAllowUnset: true
                                 ,options:accounts
                                 ,optionsText: 'bank_name'
                                 ,optionsValue:'account_id'
                                 ,optionsCaption: '请选择实付银行'
                                    ">
                                <option value=''>请选择实付银行</option>

                            </select>
                        </label>
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">银行账号</p>
                            <select class="form-control selectpicker show-menu-arrow" id="account"
                                    name="obj[account_id]"
                                    data-bind="selectpicker: account_id
                                     ,valueAllowUnset: true
                                     ,selectPickerOptions:bankAccounts
                                     ,optionsText: 'account_no'
                                     ,optionsValue:'account_id'
                                     ,optionsCaption: '请选择银行账号'
                                    ">
                                <option value=''>请选择银行账号</option>
                            </select>
                        </label>
                    </div>
                    <div class="flex-grid form-group">
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">银行付款流水号</p>
                            <input type="text" class="form-control date" id="payment_no" name="obj[payment_no]"
                                   placeholder="银行付款流水号" data-bind="value:payment_no">
                        </label>
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">汇率 <span class="must-fill"
                                                                data-bind="visible:isDisplay"></span></p>
                            <input type="text" class="form-control date" id="exchange_rate"
                                   name="obj[exchange_rate]"
                                   placeholder="汇率" data-bind="value:exchange_rate,enable:isCanEditRate">
                        </label>
                    </div>
                    <div class="flex-grid form-group">
                        <div class="col col-count-1 field ">
                            <?php
                            $attachType = $this->map["payment_attachment_type"][11];
                            $attachType['multi']=false;
                            $attachments=AttachmentService::getAttachments(Attachment::C_PAYMENT,$data["payment_id"], 11);
                            ?>
                            <p class="form-cell-title w-fixed">
                                <?php echo $attachType["name"] ?>
                            </p>
                            <div class="form-group-custom-upload">

                                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_uploadNew.php"; ?>
                                <!-- ko component: {
                             name: "file-upload",
                             params: {
                                         status:fileUploadStatus,
                                         controller:"<?php echo $this->getId() ?>",
                                         fileConfig:<?php echo json_encode($attachType) ?>,
                                         files:<?php echo json_encode($attachments[11]); ?>,
                                         fileParams: {
                                            id:<?php echo empty($data['payment_id']) ? 0 : $data['payment_id'] ?>
                                         }
                                         }
                         } -->
                                <!-- /ko -->
                            </div>



                        </div>
                    </div>
                    <div class="flex-grid">
                        <label class="col col-count-1 field ">
                            <p class="form-cell-title w-fixed">备注</p>
                            <textarea class="form-control" id="remark" name="obj[remark]" rows="3" placeholder="备注"
                                      data-bind="value:remark"></textarea>
                        </label>
                    </div>
                </div>
            </div>

    </div>
    </form>
    </div>
</section>

<script>
    var view;
    var upStatus = 0;
    var count = 0;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        view.formatAccounts(<?php echo json_encode($accounts) ?>);
        ko.applyBindings(view);
        $("#pay_date").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});
        // $("#account").bankInput({min: 1, max: 50, deimiter: ' '});

    });

    function ViewModel(option) {
        var defaults = {
            payment_id: 0,
            apply_id: "0",
            pay_date: "",
            amount: 0,
			bank_name: "",
            account_id: 0,
            corporation_id: "",
            exchange_rate: "",
            payment_no: "",
            remark: "",
            currency: 0,
            balance_amount: 0,
            currency_ico: "",
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.payment_id = ko.observable(o.payment_id);
        self.currency = ko.observable(o.currency);
        self.currency_ico = ko.observable(o.currency_ico);
        self.apply_id = ko.observable(o.apply_id);
        self.balance_amount = ko.observable(o.balance_amount);
        self.bank_name = ko.observable(o.bank_name);
        self.exchange_rate = ko.observable(o.exchange_rate).extend({
            custom: {
                params: function (v) {
                    if (self.currency() == 1 || (self.currency() == 2 && v > 0)) {
                        return true;
                    }
                    else
                        return false;
                },
                message: "请填写汇率"
            }
        });

        self.payment_no = ko.observable(o.payment_no);
        self.pay_date = ko.observable(o.pay_date).extend({required: true});
        self.amount = ko.observable(o.amount).extend({
            money: true, custom: {
                params: function (v) {
                    /*if (v>0 &&
                        ((self.currency()==1 && parseFloat(v)<=parseFloat(self.balance_amount())) ||
                        (self.currency()==2 &&
                        parseFloat(self.currnecy_amount())<=parseFloat(self.balance_amount())))) {
                        return true;
                    }*/
                    if (parseInt(v) > 0 && parseInt(v) <= parseInt(self.balance_amount()))
                        return true;
                    else
                        return false;
                },
                message: "实付金额必须填写，且不能大于剩余金额！"
            }
        });

        self.amount_cny = ko.computed(function (v) {
            if (self.exchange_rate() > 0 && self.currency() == 2)
                return (parseFloat(self.amount()) * parseFloat(self.exchange_rate())).toFixed(0);
            else
                return self.amount();
        });

        self.account_id = ko.observable(o.account_id);
        self.corporation_id = ko.observable(o.corporation_id);
        self.remark = ko.observable(o.remark);

        self.corporation_id.subscribe(function (v) {
            self.account_id("");
            if (v > 0)
                self.setAccounts();
            else
                self.accounts([]);
        });

		self.bankAccounts = ko.observableArray();

        self.bank_name.subscribe(function (v) {
			if (v > 0) {
				self.bankAccounts(ko.utils.arrayFilter(self.accounts(), function (account, index) {
                    return account.account_id == v;
				}));
				self.account_id(v);
            } else {
				self.bankAccounts([]);
			}
		});

        self.isCanEditRate = ko.computed(function (v) {
            return self.currency() == 2;
        });

        self.accounts = ko.observableArray();
        self.formatAccounts = function (data) {
            if (data == null || data == undefined) {
                self.bankAccounts([]);
                return;
            } else {
                for (var i in data) {
                    self.accounts().push(data[i]);
                    if (data[i].account_id == self.account_id()) {
                        self.bankAccounts().push(data[i]);
                    }
                }
            }
        };
        self.setAccounts = function () {
            self.accounts([]);
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/getAccounts",
                data: {corporation_id: self.corporation_id()},
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        self.accounts(json.data);
						$('#bank_name').selectpicker('refresh');
					} else {
                        inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
                    inc.vueAlert({content: "获取数据失败：" + data.responseText});
                }
            });
        }

        self.isDisplay = ko.computed(function (v) {
            return self.currency() == 2;
        });


        self.fileUploadStatus = ko.observable();

        self.isSave = ko.observable(1);
        self.actionState = ko.observable(0);
        self.saveBtnText = ko.observable("保存");
        self.submitBtnText = ko.observable("提交");
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        //保存
        self.save = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            self.pass();
        }

        self.submit = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            self.isSave(0);
            inc.vueConfirm({
                content: "您确定要提交当前实付信息，改操作不可逆？",
                onConfirm: function () {
                    self.pass();
                }
            })

        }

        self.pass = function () {
            // console.log(self.doneItems());
            var filter = ["isDisplay", "saveBtnText", "submitBtnText", "isValid", "accounts", "fileUploadStatus"];

            var formData = {"data": inc.getPostData(self, filter)};

            if (self.actionState() == 1)
                return;
            if (self.isSave() == 1)
                self.saveBtnText("保存中" + inc.loadingIco);
            else
                self.submitBtnText("提交中" + inc.loadingIco);

            // console.log(formData);
            // return;

            self.actionState(1);
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    if (json.state == 0) {
                        inc.vueMessage({
                            message: "操作成功"
                        });
                        location.href = "/<?php echo $this->getId() ?>/detail/?id=" + json.data;
                    } else if (json.state == -2) {
                        inc.vueConfirm({
                            content: json.data,
                            onConfirm:function () {
                                location.href = "/payconfirm/";
                            }
                        })
                                // layer.confirm(, {icon: 3, title: '提示', btn: ['确定']}, );
                    } else {
                        inc.vueAlert(json.data);
                    }
                    self.saveBtnText("保存");
                    self.submitBtnText("提交");
                },
                error: function (data) {
                    self.saveBtnText("保存");
                    self.submitBtnText("提交");
                    self.actionState(0);
                    inc.vueAlert("操作失败！" + data.responseText);
                }
            });
        }

        self.back = function () {
            history.back();
        }
    }
</script>