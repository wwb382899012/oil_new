<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <form class="form-horizontal" role="form" id="mainForm">
                <h4 class="box-title">保理信息</h4>
                <div class="form-group">
                    <label for="return_date" class="col-sm-2 control-label">合同放款日</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().contract_pay_date"></p>
                    </div>
                    <label for="project_code" class="col-sm-2 control-label">合同回款日</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().contract_return_date"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="return_date" class="col-sm-2 control-label">保理对接本金</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().factor_amount"></p>
                    </div>
                    <label for="return_date" class="col-sm-2 control-label">保理对接利息</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().factor_interest"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="return_date" class="col-sm-2 control-label">利率</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().rate"></p>
                    </div>
                    <label for="project_code" class="col-sm-2 control-label">已回款金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().factor_return_amount"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="return_date" class="col-sm-2 control-label">已回款本金</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().factor_return_capital_amount"></p>
                    </div>
                    <label for="project_code" class="col-sm-2 control-label">已回款利息</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().factor_return_interest"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="return_date" class="col-sm-2 control-label">未回款本金</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().factor_balance_capital"></p>
                    </div>
                    <label for="return_date" class="col-sm-2 control-label">上次回款日</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().last_return_date"></p>
                    </div>
                </div>
                <!--<div class="form-group">
                    <label for="return_date" class="col-sm-2 control-label">实际放款日</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:factor().contract_pay_date"></p>
                    </div>
                </div>-->

                <div class="box-header with-border"></div>
                <h4 class="box-title">保理回款信息</h4>
                <div class="form-group">
                    <label for="return_date" class="col-sm-2 control-label">保理还款日
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control input-sm" placeholder="保理还款日" data-bind="date:return_date">
                    </div>
                    <label for="project_code" class="col-sm-2 control-label">回款本息
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">￥</span>
                            <input type="text" class="form-control" placeholder="回款本息" data-bind="money:amount">
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="return_date" class="col-sm-2 control-label">应还本金</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">￥</span>
                            <input type="text" class="form-control" placeholder="应还本金" data-bind="money:capital_amount" disabled>
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                    <label for="project_code" class="col-sm-2 control-label">应还利息</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">￥</span>
                            <input type="text" class="form-control" placeholder="回款本息" data-bind="money:interest" disabled>
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" class="btn btn-primary" placeholder="保存" data-bind="click:save,html:saveBtnText"></button>
                    <button type="button" class="btn btn-danger" placeholder="提交" data-bind="click:submit,html:submitBtnText"></button>
                    <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
		view.formatFactor(<?php echo json_encode($factor) ?>);
		ko.applyBindings(view);
	})

	function ViewModel(option) {
		var defaults = {
			id: 0,
            detail_id: 0,
			factor_id: 0,
			return_date: '',
			amount: 0,
			capital_amount: 0,
			interest: 0,
			actual_interest: 0
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.id = ko.observable(o.id);
		self.detail_id = ko.observable(o.detail_id);
		self.factor_id = ko.observable(o.factor_id);
		self.factor = ko.observableArray();
		self.return_date = ko.observable(o.return_date).extend({
			custom: {
				params: function (v) {
					self.msg = '保理还款日不能小于合同放款日';
					var return_date = new Date(v);
					if (Object.keys(self.factor()).length > 0) {
						var contract_pay_date = new Date(self.factor().contract_pay_date());
						if (self.factor().last_return_date() != '') {
							var last_return_date = new Date(self.factor().last_return_date());
							if (return_date < last_return_date) {
								self.msg = '保理还款日不能小于上次回款日';
								return false;
							}
						} else {
							if (return_date < contract_pay_date) {
								return false;
							}
						}
					}
					return true;
				},
				message: function () {
					return self.msg;
				}
			}
		});
		self.capital_amount = ko.observable(o.capital_amount);
		self.interest = ko.observable(o.interest);
		self.actual_interest = ko.observable(o.actual_interest);
		self.amount = ko.observable(o.amount).extend({
			money: true,
			custom: {
				params: function (v) {
					return parseFloat(v) >= parseFloat(self.actual_interest());
				},
				message: "回款本息不能小于应还利息"
			}
		});
		self.submitBtnText = ko.observable('提交');
		self.saveBtnText = ko.observable('保存');
		self.actionState = 0;
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		};

		self.status = ko.observable(0);

		self.formatFactor = function (data) {
			if (data == null || data == undefined)
				return;

			self.factor(new Factor(data));
		}

		self.return_date.subscribe(function (v) {
			if (self.isValid())
				self.calculateAmount();
		});

		self.amount.subscribe(function (v) {
			self.computeAmount(v);
		});

		self.computeAmount = function (v) {
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/calculateReturnAmount',
				data: {
					"detail_id": self.detail_id(),
					"return_date": self.return_date()
				},
				dataType: "json",
				success: function (json) {
					console.log(json);
					if (json.state == 0) {
						self.actual_interest(json.data.interest);
						if (v > json.data.interest) {
							self.interest(json.data.interest);
							self.capital_amount(v - json.data.interest);
						} else {
							if(self.isValid()) {
								self.interest(v);
								self.capital_amount(0);
                            }
						}

					} else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("回款本息信息获取失败！" + data.responseText, {icon: 5});
				}
			});
		};

		self.calculateAmount = function () {
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/calculateReturnAmount',
				data: {
					"detail_id": self.detail_id(),
					"return_date": self.return_date()
				},
				dataType: "json",
				success: function (json) {
					console.log(json);
					if (json.state == 0) {
						self.amount(json.data.amount);
						self.capital_amount(json.data.capital_amount);
						self.interest(json.data.interest);
						self.actual_interest(json.data.interest);
					} else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("回款本息信息获取失败！" + data.responseText, {icon: 5});
				}
			});
		};

		self.save = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			self.saveBtnText("保存中" + inc.loadingIco);
			self.sendSaveSubmitAjax();
		}

		self.submit = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			layer.confirm("您确定要提交当前保理回款信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
				self.status(1);
				self.submitBtnText("提交中" + inc.loadingIco);
				self.sendSaveSubmitAjax();
				layer.close(index);
			})
		}

		self.sendSaveSubmitAjax = function () {
			if (self.actionState == 1)
				return;
			self.actionState = 1;
			var formData = {"data": inc.getPostData(self, ["saveBtnText", "submitBtnText", "factor", "msg"])};
			console.log(formData);
			$.ajax({
				type: "POST",
				url: "/<?php echo $this->getId() ?>/save",
				data: formData,
				dataType: "json",
				success: function (json) {
					self.actionState = 0;
					self.saveBtnText("保存");
					self.submitBtnText("提交");
					if (json.state == 0) {
						layer.msg('操作成功', {icon: 6, time: 1000}, function () {
							if (self.status() == 1) {
								location.href = "/<?php echo $this->getId() ?>";
							} else {
								location.href = "/<?php echo $this->getId() ?>/detail/?detail_id=" + self.detail_id();
							}
						});
					} else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					self.actionState = 0;
					self.saveBtnText("保存");
					self.submitBtnText("提交");
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});
		}

		self.back = function () {
			history.back();
		}
	}

	function Factor(option) {
		var defaults = {
			contract_pay_date: '',
			contract_return_date: '',
			last_return_date: '',
			factor_amount: 0,
			factor_interest: 0,
			rate: '',
			factor_return_amount: 0,
			factor_return_capital_amount: 0,
			factor_return_interest: 0,
			factor_balance_capital: 0,
			contract_return_date: ''
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.contract_pay_date = ko.observable(o.contract_pay_date);
		self.contract_return_date = ko.observable(o.contract_return_date);
		self.last_return_date = ko.observable(o.last_return_date);
		self.factor_amount = ko.observable(o.factor_amount);
		self.factor_interest = ko.observable(o.factor_interest);
		self.rate = ko.observable(o.rate);
		self.factor_return_amount = ko.observable(o.factor_return_amount);
		self.factor_return_capital_amount = ko.observable(o.factor_return_capital_amount);
		self.factor_return_interest = ko.observable(o.factor_return_interest);
		self.factor_balance_capital = ko.observable(o.factor_balance_capital);
		self.contract_return_date = ko.observable(o.contract_return_date);
	}
</script>