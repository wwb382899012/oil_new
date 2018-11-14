function ViewModel(option) {
    var defaults = {
        apply_id: 0,
        type: 0,
        corporation_id: 0,
        project_id: 0,
        contract_id: 0,
        sub_contract_id: 0,
        sub_contract_type: 0,
        sub_contract_code: "",
        payee: "",
        bank: "",
        account: "",
        account_name: "",
        subject_id: 0,
        currency: 1,
        is_factoring: 0,
        amount: 0,
        amount_factoring: 0,
        exchange_rate: 1,
        remark: "",
        check_user: 0,
        check_user_validate: 0
    };
    var o = $.extend(defaults, option);
    var self = this;
    self.apply_id = ko.observable(o.apply_id);
    self.check_user = ko.observable(o.check_user);
    self.type = ko.observable(o.type);
    self.project_id = ko.observable(o.project_id);
    self.corporation_id = ko.observable(o.corporation_id).extend({
        custom: {
            params: function (v) {
                return v > 0;
            },
            message: "请选择交易主体"
        }
    });
    self.contract_id = ko.observable(o.contract_id);

    self.sub_contract_id = ko.observable(o.sub_contract_id);
    self.sub_contract_type = ko.observable(o.sub_contract_type);
    self.sub_contract_code = ko.observable(o.sub_contract_code).extend({
        custom: {
            params: function (v) {
                if (self.sub_contract_type() > 0 && (v == null || v == ""))
                    return false;
                return true;
            },
            message: "请填写付款合同编号"
        }
    });
    self.checkUserState = ko.observable(0);
    self.check_user.subscribe(function (v) {
        self.check_user_validate(v);
    });
    self.check_user_validate = ko.observable(o.check_user_validate).extend({
        custom: {
            params: function (v) {
                if (self.checkUserState() == 1 && v <= 0) {
                    return false;
                } else {
                    return true;
                }
            },
            message: "请选择审核人"
        }
    });
    self.hideModal=function(){
        $('#business-check-user-modal').modal('hide');
        self.checkUserState(0);
    }

    self.payee = ko.observable(o.payee).extend({required: true});
    self.bank = ko.observable(o.bank).extend({required: true});
    self.account = ko.observable(o.account).extend({required: true});
    self.account_name = ko.observable(o.account_name).extend({required: true});
    self.subject_id = ko.observable(o.subject_id).extend({
        custom: {
            params: function (v) {
                return v > 0;
            },
            message: "请选择用途"
        }
    });


    self.amount = ko.observable(o.amount).extend({positiveNumber: true});

    self.is_factoring = ko.observable(parseInt(o.is_factoring));
    self.amount_factoring = ko.observable(o.amount_factoring).extend({
        custom: {
            params: function (v) {
                if (self.is_factoring()) {
                    return ($.isNumeric(v) && parseFloat(v) <= parseFloat(self.amount()) && v > 0);
                }
                return true;
            },
            message: "保理金额只能是大于0且小于等于付款金额的数字"
        }
    });

    self.currency = ko.observable(o.currency);
    self.exchange_rate = ko.observable(o.exchange_rate);
    self.status = ko.observable(o.status);
    self.remark = ko.observable(o.remark).extend({required: true});

    self.fileUploadStatus = ko.observable();
    self.plans = ko.observableArray();
    self.projects = ko.observableArray();
    self.details = ko.observableArray();
    self.businessDirectors = ko.observableArray();
    self.allProjects = {};

    self.contracts = ko.observableArray();
    self.allContracts = {};

    self.is_factoring.subscribe(function (v) {
        if (v == true) {
            if (self.amount_factoring() == 0)
                self.amount_factoring(self.amount());
        } else {
            self.amount_factoring(0);
        }
    });

    self.currency.subscribe(function (v) {
        ko.utils.arrayForEach(self.details(), function (item) {
            item.currency(v);
        });
        ko.utils.arrayForEach(self.plans(), function (item) {
            if (item.checked() && item.payment.currency != v) {
                item.checked(0);
            }
        });
    });

    self.detailsAmount = ko.computed(function () {
        var amount = 0;
        ko.utils.arrayForEach(self.details(), function (item) {
            amount += parseFloat(item.amount());
        });
        return amount;
    }).subscribe(function (v) {
        self.amount(v);
    });

    self.selectedPlanAmount = ko.computed(function () {
        var amount = 0;
        ko.utils.arrayForEach(self.plans(), function (item) {
            if (item.checked()) {
                amount += parseFloat(item.amount());
            }
        });
        return amount;
    }).subscribe(function (v) {
        self.amount(v);
    });

    self.amountEnable = ko.computed(function () {
        if (self.type() == config.payApplicationType.multi_contract)
            return false;
        var items = ko.utils.arrayFilter(self.plans(), function (item) {
            return item.checked();
        });
        return items.length == 0;
    }, self);

    self.errors = ko.validation.group(self);
    self.isValid = function () {
        return self.errors().length === 0;
    }

    self.actionState = ko.observable(0);
    self.buttonText = ko.observable("保存");
    self.submitButtonText = ko.observable("保存并提交");

    self.payee.subscribe(function (v) {
        self.payee(v.trim())
        self.getAccount();
    });

    self.tempSave = function () {
        self.status(0);
        self.save();
    }

    self.submit = function () {
        if (!self.isValid()) {
            self.errors.showAllMessages();
            return;
        }
        if (self.needModal()) {
            $('#business-check-user-modal').modal();
            self.checkUserState(1);
        } else {
            inc.vueConfirm({
                content: "您确定要提交当前信息进行审核吗，该操作不可逆？", onConfirm: function () {
                    self.status(10);
                    self.save();
                }
            });
        }
    }

    self.needModal = function () {
        return self.type() != config.payApplicationType.contract && self.type() != config.payApplicationType.sell_contract;
    }

    self.submitCheck = function () {
        self.errors = ko.validation.group(self);
        self.status(10);
        self.save();
    }

    self.corporation_id.subscribe(function (v) {
        var items = ko.utils.arrayFilter(self.businessDirectors(), function (item) {
            return $.inArray(self.corporation_id(),item.corp_ids.split(","))!=-1;
        });
        self.businessDirectors(items);
    });

    self.updateButtonText = function () {

        if (self.actionState() == 1) {
            if (self.status() == 10)
                self.submitButtonText("保存并提交中 " + inc.loadingIco);
            else
                self.buttonText("保存中 " + inc.loadingIco);
        }
        else {
            if (self.status() == 10)
                self.submitButtonText("保存并提交");
            else
                self.buttonText("保存 ");
        }

    }

    self.save = function () {
        if (!self.isValid()) {
            self.errors.showAllMessages();
            return;
        }

        if (self.actionState() == 1)
            return;
        self.actionState(1);
        // return;
        if (self.needModal()) {
            $('#business-check-user-modal').modal('hide');
        }
        self.updateButtonText();
        var formData = self.getPostData();
        $.ajax({
            type: "POST",
            url: "/pay/save",
            data: formData,
            dataType: "json",
            success: function (json) {
                self.buttonText("保存");
                if (json.state == 0) {
                    inc.vueMessage({
                        message: "操作成功"
                    });
                    if (self.status() == 0) {
                        location.href = '/pay/detail?id=' + json.data;
                    } else {
                        location.href = "/pay/";
                    }
                } else {
                    inc.vueAlert(json.data);
                }
                self.actionState(0);
                self.updateButtonText();
            },
            error: function (data) {
                self.buttonText("保存");
                inc.vueAlert("保存失败：" + data.responseText);
                self.actionState(0);
                self.updateButtonText();
            }
        });
    }

    self.submitForCheck = function () {
        if (self.needModal()) {
            $('#business-check-user-modal').modal();
            self.checkUserState(1);
        } else {
            inc.vueConfirm({
                content: "您确定要提交当前信息进行审核吗，该操作不可逆？", onConfirm: function () {
                    self.doSubmitForCheck();
                }
            });
        }
    }

    self.submitUserCheck=function(){
        self.doSubmitForCheck();
    }

    self.doSubmitForCheck = function () {
        if (self.needModal()) {
            self.errors = ko.validation.group(self.check_user_validate);
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
            $('#business-check-user-modal').modal('hide');
        }
        var formData = {id: self.apply_id(),check_user:self.check_user()};
        $.ajax({
            type: 'POST',
            url: '/pay/submit',
            data: formData,
            dataType: "json",
            success: function (json) {
                if (json.state == 0) {
                    inc.vueMessage({
                        message: "操作成功"
                    });
                    location.reload();
                }
                else {
                    inc.vueAlert(json.data);
                }
            },
            error: function (data) {
                inc.vueAlert("操作失败！" + data.responseText);
            }
        });
    }

    self.getPostData = function () {
        if (self.is_factoring() == true) {
            self.is_factoring(1);
        } else {
            self.is_factoring(0);
        }
        var formData = inc.getPostData(self, ["selectedPlanAmount", "detailsAmount", "tempSaveBtnText", "saveBtnText", "plans", "details", "allContracts", "allProjects", "contracts", "projects"]);
        var items = {};
        var n = 0;
        ko.utils.arrayForEach(self.plans(), function (item) {
            if (item.checked()) {
                n++;
                items[item.plan_id()] = {
                    plan_id: item.plan_id(),
                    amount: item.amount()
                };
            }
        });
        if (n > 0)
            formData["items"] = items;

        if (self.type() == config.payApplicationType.multi_contract) {
            var details = {};
            ko.utils.arrayForEach(self.details(), function (item) {
                details[item.contract_id()] = {
                    contract_id: item.contract_id(),
                    project_id: item.project_id(),
                    amount: item.amount()
                };
            });
            formData["details"] = details;
        }
        formData = {data: formData};

        return formData;
    }

    self.back = function () {
        history.back();
    }

    self.formatPaymentPlans = function (data) {
        if (data == null || data == undefined)
            return;
        for (var i in data) {
            var obj = new PaymentPlan(data[i]);
            self.plans.push(obj);
        }
    }

    self.formatDetails = function (data) {
        if (data == null || data == undefined)
            return;
        for (var i in data) {
            var obj = new PayDetail(data[i]);
            self.details.push(obj);
        }
    }

    self.currency_ico = ko.computed(function () {
        if (currencies[self.currency()])
            return currencies[self.currency()]["ico"];
        else
            return "";
    }, self);

    self.isShowFactoring=ko.computed(function () {
        // return (self.type()<config.payApplicationType.multi_contract);
        return false;  //20180713修改，石油系统不在对接保理
    });

    self.isShowProject = ko.computed(function () {
        return (self.type() == config.payApplicationType.project);
    });

    self.isMultiContract = ko.computed(function () {
        return (self.type() == config.payApplicationType.multi_contract);
    });

    self.corporation_id.subscribe(function (v) {
        self.updateData();
    });

    self.updateData = function () {
        if (self.isShowProject())
            self.setProjects();
        if (self.isMultiContract())
            self.setContracts();
    }

    self.setProjects = function () {

        if (self.corporation_id() > 0) {
            if (self.allProjects[self.corporation_id()])
                self.projects(self.allProjects[self.corporation_id()]);
            else {
                self.projects([]);
                $.ajax({
                    type: "POST",
                    url: "/pay/getProjects",
                    data: {corpId: self.corporation_id()},
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            self.allProjects[self.corporation_id()] = json.data;
                            self.projects(json.data);
                        } else {
                            inc.vueAlert(json.data);
                        }
                    },
                    error: function (data) {
                        inc.vueAlert("获取数据失败：" + data.responseText);
                    }
                });
            }
        }
        else {
            self.projects([]);
        }
    }

    self.setContracts = function () {
        if (self.corporation_id() > 0) {
            if (self.allContracts[self.corporation_id()])
                self.contracts(self.allContracts[self.corporation_id()]);
            else {
                $.ajax({
                    type: "POST",
                    url: "/pay/getContracts",
                    data: {corpId: self.corporation_id()},
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            self.allContracts[self.corporation_id()] = json.data;
                            self.contracts(json.data);
                        } else {
                            inc.vueAlert(json.data);
                        }
                    },
                    error: function (data) {
                        inc.vueAlert("获取数据失败：" + data.responseText);
                    }
                });
            }
        }
        else {
            self.projects([]);
        }
    }

    self.addDetail = function () {
        self.details.push(new PayDetail({
            currency: self.currency()
        }));
    }

    self.delDetail = function (data) {
        self.details.remove(data);
    }

    self.initProjects = function (data) {
        self.allProjects[self.corporation_id()] = data;
        self.projects(data);
    }
    self.initContracts = function (data) {
        self.allContracts[self.corporation_id()] = data;
        self.contracts(data);
    }
    self.initBusinessDirectors = function (data) {
        self.businessDirectors(data);
    }

    self.getAccount = function () {
        if (self.payee() == null || self.payee() == "" || !self.payee.isValid())
            return;

        $.ajax({
            type: "POST",
            url: "/pay/getAccount",
            data: {name: self.payee()},
            dataType: "json",
            success: function (json) {
                if (json.state == 0) {
                    if (json.data.bank)
                        self.bank(json.data.bank);
                    if (json.data.account)
                        self.account(json.data.account);
                    if (json.data.account_name)
                        self.account_name(json.data.account_name);
                }
            },
            error: function (data) {
                inc.vueAlert("获取收款人银行帐号出错：" + data.responseText);
            }
        });
    }

}

function PaymentPlan(option) {
    var defaults = {
        plan_id: 0,
        checked: 0,
        pay_amount: 0
    };
    var o = $.extend(defaults, option);
    var self = this;
    self.plan_id = ko.observable(o.plan_id);
    self.checked = ko.observable(o.checked);


    self.payment = option;
    self.amount = ko.observable(o.pay_amount);
    /*.extend({
                custom:{
                    params: function (v) {
                        return  parseFloat(v)<=parseFloat(self.payment.amount)-parseFloat(self.payment.amount_paid)
                    },
                    message: "付款金额不能大于未付款金额"
                }
            });*/

    self.amount_balance = self.payment.amount - self.payment.amount_paid;

    self.currency_ico = ko.computed(function () {
        if (currencies[self.payment.currency])
            return currencies[self.payment.currency]["ico"];
        else
            return "";
    }, self);

    self.expenseName = ko.computed(function () {
        if (expenseNames[self.payment.expense_type])
            return expenseNames[self.payment.expense_type]["name"];
        else
            return "";
    }, self);

    self.isCanSelected = ko.computed(function () {
        return view.currency() == self.payment.currency;
    });
}

function PayDetail(option) {
    var defaults = {
        detail_id: 0,
        project_id: 0,
        contract_id: 0,
        contract_type: 0,
        currency: 1,
        contract_code: "",
        project_code: "",
        amount: 0
    };
    var o = $.extend(defaults, option);
    var self = this;
    self.detail_id = ko.observable(o.detail_id);
    self.project_id = ko.observable(o.project_id);
    self.contract_id = ko.observable(o.contract_id);
    self.contract_type = ko.observable(o.contract_type);

    self.currency = ko.observable(o.currency);
    self.amount = ko.observable(o.amount);

    self.currency_ico = ko.computed(function () {
        if (currencies[self.currency()])
            return currencies[self.currency()]["ico"];
        else
            return "";
    }, self);

    self.contract_code = ko.observable(o.contract_code).extend({required: true});
    self.project_code = ko.observable(o.project_code);

    self.contracts = ko.computed(function () {
        var items = ko.utils.arrayFilter(view.contracts(), function (item) {
            return item.type == self.contract_type();
        });
        return items;
    });

    self.contract_id.subscribe(function (v) {
        self.updateContractInfo(v);
    });

    self.updateContractInfo = function (v) {
        if (v > 0) {
            var item = ko.utils.arrayFirst(view.contracts(), function (item) {
                return item.contract_id == v;
            });
            if (item) {
                self.contract_code(item.contract_code);
                self.project_code(item.project_code);
                self.project_id(item.project_id);
            }
        }
        else {
            self.contract_code("");
            self.project_code("");
            self.project_id(0);
        }
    }

}
