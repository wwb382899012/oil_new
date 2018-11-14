<script src="/js/bootstrap3-typeahead.min.js"></script>
<?php
$menus=$this->getIndexMenuWithNewUI();

$buttons = [];
$buttons[] = ['text' => '提交 ', 'attr' => ['data-bind' => 'click:submit, html:submitBtnText', 'id' => 'submitButton']];
$this->loadHeaderWithNewUI($menus, $buttons, '/payClaim/');
?>
<section class="content sub-container">
    <?php $this->renderPartial("/pay/new_detailBody", array('apply' => $apply)); ?>

    <div class="z-card">
        <div class="z-card-part">
            <h3 class="z-body-header">认领信息</h3>
            <div class="flex-grid form-group">
                <label for="type" class="col col-count-3 field">
                    <p class="form-cell-title">待认领金额</p>
                    <div class="input-group">
                        <span class="input-group-addon" data-bind="html:currency_ico"></span>
                        <input class="form-control date" type="text" disabled
                               value="<?php echo Utility::numberFormatFen2Yuan($apply->amount_paid - $apply->amount_claim); ?>">
                    </div>
                </label>
                <label for="type" class="col col-count-3 field">
                    <p class="form-cell-title">已认领金额</p>
                    <div class="input-group">
                        <span class="input-group-addon" data-bind="html:currency_ico"></span>
                        <input class="form-control date" type="text" disabled
                               value="<?php echo Utility::numberFormatFen2Yuan($apply->amount_claim); ?>">
                    </div>
                </label>
                <label for="type" class="col col-count-3 field">
                    <p class="form-cell-title">货款合同编号</p>
                    <select class="form-control selectpicker show-menu-arrow" title="请选择货款合同编号" id="contract_id"
                        data-live-search="true"   data-bind="selectpicker: contract_id,valueAllowUnset: true">
                        <option value='0'>请选择货款合同编号</option>
                        <?php
                        $contracts = ContractService::getCorporationContracts($apply->corporation_id);
                        foreach ($contracts as $v) {
                            echo "<option value='" . $v["contract_id"] . "'>" . $v["contract_code"] . "</option>";
                        } ?>
                    </select>
                </label>
            </div>

            <div class="flex-grid form-group">
                <label for="type" class="col col-count-3 field">
                    <p class="form-cell-title">货款合同类型</p>
                    <input class="form-control" type="text" disabled data-bind="value:contract_type_desc">
                </label>
                <label for="type" class="col col-count-3 field">
                    <p class="form-cell-title">项目编号</p>
                    <input class="form-control " disabled type="text" data-bind="value: project_code">
                </label>
                <label for="type" class="col col-count-3 field">
                    <p class="form-cell-title">项目类型</p>
                    <input class="form-control " disabled type="text" data-bind="value: project_type_desc">
                </label>
            </div>

            <div class="flex-grid form-group">
                <label for="type" class="col col-count-3 field">
                    <p class="form-cell-title">付款合同类型</p>
                    <select class="form-control selectpicker show-menu-arrow" title="付款合同类型"
                        data-live-search="true"   data-bind="selectpicker:sub_contract_type">
                        <option value="0">请选择付款合同类型</option>
                        <?php foreach ($this->map['contract_category'] as $key => $category): ?>
                            <option value="<?php echo $key ?>"><?php echo $category ?></option>
                        <?php endforeach ?>
                    </select>
                </label>
                <label for="type" class="col col-count-3 field">
                    <p class="form-cell-title">付款合同编号</p>
                    <input class="form-control" data-bind="value:sub_contract_code"/>
                </label>
            </div>


            <div class="form-group">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th style="width:60px;">选择</th>
                        <th style="width:80px;">期数</th>
                        <th style="width:140px; text-align: left;">预计付款日期</th>
                        <th style="width:140px; text-align: left;">付款类别</th>
                        <th style="width:180px; text-align: left;">计划付款金额</th>
                        <th style="width:180px; text-align: left;">实付金额</th>
                        <th style="width:180px; text-align: left;">未实付金额</th>
                        <th style="width:240px; text-align: left;">认领金额</th>
                    </tr>
                    </thead>

                    <tbody data-bind="foreach:payment_plans">
                    <tr>
                        <td>
                            <p><input style="width: auto" type="checkbox" data-bind="checked:checked"></p>
                        </td>
                        <td><p class="form-control-static" data-bind="html:period"></p></td>
                        <td><p class="form-control-static" data-bind="html:pay_date"></p></td>
                        <td><p class="form-control-static" data-bind="html:expense_type_desc"></p></td>
                        <td><p class="form-control-static"><span data-bind="html:currency_ico"></span>
                                <span data-bind="moneyText:amount_plan"></span></p></td>
                        <td><p class="form-control-static"><span data-bind="html:apply_currency_ico"></span>
                                <span data-bind="moneyText:amount_paid"></span></p></td>
                        <td><p class="form-control-static"><span data-bind="html:apply_currency_ico"></span>
                                <span data-bind="moneyText:amount_plan-amount_paid"></span></p></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon" data-bind="html:apply_currency_ico"></span>
                                <input type="text" class="form-control" data-bind="money:amount">
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex-grid form-group">
                <label class="col col-count-1 field ">
                    <p class="form-cell-title w-fixed">认领金额<span class="must-fill"></span></p>
                    <div class="input-group">
                        <span class="input-group-addon" data-bind="text:currency_ico"></span>
                        <input type="text" class="form-control" placeholder="认领金额"
                               data-bind="money:amount,enable:amount_editable">
                        <span class="input-group-addon" data-bind="moneyChineseText:amount"></span>
                    </div>
                </label>
            </div>
            <div class="flex-grid">
                <label class="col col-count-1 field ">
                    <p class="form-cell-title w-fixed">备注</p>
                    <textarea type="text" class="form-control" rows="3" id="remark" name="obj[remark]" placeholder="备注"
                              data-bind="value:remark"></textarea>
                </label>
            </div>

        </div>
    </div>

</section>
<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($payClaim) ?>);
        ko.applyBindings(view);
    });

    function ViewModel(option) {
        var defaults = {
            claim_id: 0,
            corporation_id: 0,
            project_id: 0,
            contract_id: 0,
            apply_id: 0,
            sub_contract_id: 0,
            sub_contract_type: 0,
            sub_contract_code: '',
            type: 0,
            subject_id: 0,
            amount: 0,
            currency: 1,
            exchange_rate: 1,
            status: 0,
            remark: '',
            amount_claim_balance: 0
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.claim_id = ko.observable(o.claim_id);
        self.corporation_id = ko.observable(o.corporation_id);
        self.project_id = ko.observable(o.project_id);
        self.contract_id = ko.observable(o.contract_id);
        self.apply_id = ko.observable(o.apply_id);
        self.sub_contract_id = ko.observable(o.sub_contract_id);
        self.sub_contract_type = ko.observable(o.sub_contract_type);
        self.sub_contract_code = ko.observable(o.sub_contract_code);
        self.type = ko.observable(o.type);
        self.subject_id = ko.observable(o.subject_id);
        self.amount_claim_balance = ko.observable(o.amount_claim_balance);
        self.amount = ko.observable(o.amount).extend({
            custom: {
                params: function (v) {
                    self.msg = '0-1亿';
                    if (parseFloat(v) > parseFloat(ko.unwrap(self.amount_claim_balance))) {
                        self.msg = '认领金额不能超过待认领金额';
                        return false;
                    } else {
                        if (parseFloat(v) <= 0) {
                            return false;
                        }
                    }
                    return true;
                },
                message: function () {
                    return self.msg;
                }
            }
        });
        self.currency = ko.observable(o.currency);
        self.exchange_rate = ko.observable(o.exchange_rate);
        self.status = ko.observable(o.status);
        self.remark = ko.observable(o.remark);
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        var currencies = <?php echo json_encode($this->map['currency']) ?>;
        self.currency_ico = currencies[self.currency()]['ico'];
        self.submitBtnText = ko.observable('提交');

        self.payment_plans = ko.observableArray();
        self.contract_type_desc = ko.observable('');
        self.project_code = ko.observable('');
        self.project_type_desc = ko.observable('');
        self.contract_id.subscribe(function (v) {
            self.payment_plans([]);
            if (!inc.isEmpty(v)) {
                $.ajax({
                    url: '/payClaim/getContractById',
                    data: {contract_id: v},
                    method: 'post',
                    dataType: 'json',
                    success: function (json) {
                        if (json.state == 0) {
                            self.contract_type_desc(json.data.contract_type_desc);
                            self.project_code(json.data.project_code);
                            self.project_type_desc(json.data.project_type_desc);
                            self.project_id(json.data.project_id);
                            if (json.data.contract_type == 1 && $.isArray(json.data.payment_plans) && json.data.payment_plans.length > 0) {
                                var payments = [];
                                for (var i in json.data.payment_plans) {
                                    json.data.payment_plans[i]['currencies'] = currencies;
                                    json.data.payment_plans[i]['apply_currency'] = self.currency();
                                    payments.push(new PaymentModel(json.data.payment_plans[i]));
                                }
                                self.payment_plans(payments);
                            }
                        } else {
                            inc.vueAlert(json.data);
                        }
                    },
                    error: function (data) {
                        inc.vueAlert({content: "获取合同信息失败！" + data.responseText});
                    }
                });
            }
        }, self);

        self.amount_editable = ko.computed(function () {
            if (self.payment_plans().length > 0) {
                var items = ko.utils.arrayFilter(self.payment_plans(), function (item) {
                    return item.checked();
                });
                return items.length == 0;
            }
            return true;
        }, self);

        self.actionState = ko.observable(0);

        self.selectedPlanAmount = ko.computed(function () {
            var amount = 0;
            if (self.payment_plans().length > 0) {
                ko.utils.arrayForEach(self.payment_plans(), function (item) {
                    if (item.checked()) {
                        amount += parseFloat(item.amount());
                    }
                });
            }

            return amount;
        }).subscribe(function (v) {
            self.amount(v);
        });

        self.amount_cny = ko.computed(function () {
            return (parseFloat(self.amount()) * parseFloat(self.exchange_rate())).toFixed(0);
        }, self);

        self.submit = function () {
            inc.vueConfirm({
                content: "是否确认提交该付款申请认领单，本操作无法撤回？",
                onConfirm: function () {
                    self.status(1);
                    self.save();
                }
            })
        };

        self.getPostData = function () {
            var formData = inc.getPostData(self, ['amount_claim_balance', 'amount_editable', 'contract_type_desc', 'msg', 'payment_plans', 'project_code', 'project_type_desc', 'selectedPlanAmount', 'submitBtnText']);
            var items = {};
            var n = 0;
            ko.utils.arrayForEach(self.payment_plans(), function (item) {
                if (item.checked()) {
                    n++;
                    items[item.plan_id] = {
                        plan_id: item.plan_id,
                        amount: item.amount()
                    };
                }
            });
            if (n > 0)
                formData["items"] = items;

            formData = {data: formData};
            return formData;
        }

        self.save = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
            if (self.actionState() == 1)
                return;
            self.actionState(1);
            self.updateButtonText();
            var formData = self.getPostData();
            console.log(formData);
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/save",
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.vueMessage({
                            message: "操作成功"
                        });
                        location.href = "/<?php echo $this->getId() ?>/detail?id=" + json.data;
                    } else {
                        inc.vueAlert(json.data);
                    }
                    self.actionState(0);
                    self.updateButtonText();
                },
                error: function (data) {
                    self.actionState(0);
                    self.updateButtonText();
                    inc.vueAlert("操作失败！" + data.responseText);
                }
            });
        }

        self.updateButtonText = function () {
            if (self.actionState() == 1) {
                if (self.status() == 1)
                    self.submitBtnText("提交中 " + inc.loadingIco);
                /*else
                 self.buttonText("保存中 " + inc.loadingIco);*/
            } else {
                if (self.status() == 1)
                    self.submitBtnText("提交");
                /*else
                 self.buttonText("保存 ");*/
            }
        }

        self.back = function () {
            location.href = "/<?php echo $this->getId() ?>/";
        }

        self.sub_contract_code.subscribe(function (v) {

        })
    }

    var PaymentModel = function (params) {
        var self = this;
        self.plan_id = params.plan_id;
        self.contract_id = params.contract_id;
        self.period = params.period;
        self.pay_date = params.pay_date;
        self.amount_plan = params.amount;
        self.amount_paid = params.amount_paid;
        self.expense_type_desc = params.expense_type_desc;
        self.currency_ico = params.currencies[params.currency]['ico'];
        self.apply_currency_ico = params.currencies[params.apply_currency]['ico'];
        self.checked = ko.observable(0);
        self.amount = ko.observable(0).extend({
            custom: {
                params: function (v) {
                    if (self.checked() == true) {
                        if (v <= 0) {
                            return false;
                        }
                    }
                    return true;
                },
                message: "0-1亿"
            }
        });
    }
</script>