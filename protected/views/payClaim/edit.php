<script src="/js/bootstrap3-typeahead.min.js"></script>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">付款申请详情</h3>
            <div class="pull-right box-tools"></div>
        </div>
        <div class="box-body form-horizontal">
            <?php $this->renderPartial("/pay/detailBody", array('apply' => $apply)); ?>
            <h4 class="section-title">认领信息</h4>
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">待认领金额</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <span data-bind="text:currency_ico"></span>
                        <?php echo Utility::numberFormatFen2Yuan($apply->amount_paid - $apply->amount_claim); ?>
                    </p>
                </div>
                <label for="type" class="col-sm-2 control-label">已认领金额</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <span data-bind="text:currency_ico"></span>
                        <?php echo Utility::numberFormatFen2Yuan($apply->amount_claim); ?>
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label for="contract_id" class="col-sm-2 control-label">货款合同编号</label>
                <div class="col-sm-4">
                    <select class="form-control" title="请选择货款合同编号" id="contract_id" data-bind="selectpicker: contract_id,valueAllowUnset: true">
                        <option value='0'>请选择货款合同编号</option>
                        <?php
                        $contracts = ContractService::getCorporationContracts($apply->corporation_id);
                        foreach ($contracts as $v) {
                            echo "<option value='" . $v["contract_id"] . "'>" . $v["contract_code"] . "</option>";
                        } ?>
                    </select>
                </div>
                <label class="col-sm-2 control-label">货款合同类型</label>
                <div class="col-sm-4">
                    <p class="form-control-static" data-bind="text:contract_type_desc"></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">项目编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static" data-bind="text: project_code"></p>
                </div>
                <label class="col-sm-2 control-label">项目类型</label>
                <div class="col-sm-4">
                    <p class="form-control-static" data-bind="text:project_type_desc"></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">付款合同类型</label>
                <div class="col-sm-4">
                    <select class="form-control" title="付款合同类型" data-bind="value:sub_contract_type">
                        <option value="0">请选择付款合同类型</option>
                        <?php foreach ($this->map['contract_category'] as $key => $category): ?>
                            <option value="<?php echo $key ?>"><?php echo $category ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <label class="col-sm-2 control-label">付款合同编号</label>
                <div class="col-sm-4">
                    <input class="form-control" data-bind="value:sub_contract_code"/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-11 col-sm-push-1">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="width:60px;">选择</th>
                            <th style="width:80px;">期数</th>
                            <th style="width:140px; text-align: left;">预计收款日期</th>
                            <th style="width:140px; text-align: left;">付款类别</th>
                            <th style="width:180px; text-align: left;">计划付款金额</th>
                            <th style="width:180px; text-align: left;">实付金额</th>
                            <th style="width:180px; text-align: left;">未实付金额</th>
                            <th style="width:240px; text-align: left;">认领金额</th>
                        </tr>
                        </thead>

                        <tbody data-bind="foreach:payment_plans">
                        <tr>
                            <td><input type="checkbox" data-bind="checked:checked"></td>
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
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">认领金额 <span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon" data-bind="text:currency_ico"></span>
                        <input type="text" class="form-control" placeholder="认领金额" data-bind="money:amount,enable:amount_editable">
                        <span class="input-group-addon" data-bind="moneyChineseText:amount"></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-10">
                    <textarea type="text" class="form-control" id="remark" name="obj[remark]" placeholder="备注" data-bind="value:remark"></textarea>
                </div>
            </div>
        </div>

        <div class="box-footer">
            <button type="button" id="submitButton" class="btn btn-danger" data-bind="click:submit, html:submitBtnText"></button>
            <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
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
					self.msg = '请输入一个大于0的数字且小于10^15的数字';
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
							layer.alert(json.data, {icon: 5});
						}
					},
					error: function (data) {
						layer.alert("获取合同信息失败！" + data.responseText, {icon: 5});
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
			layer.confirm("是否确认提交该付款申请认领单?本操作无法撤回", function () {
				self.status(1);
				self.save();
			});
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
						layer.msg("操作成功", {icon: 6, time: 1000}, function () {
							location.href = "/<?php echo $this->getId() ?>/detail?id=" + json.data;
						});
					} else {
						layer.alert(json.data, {icon: 5});
					}
					self.actionState(0);
					self.updateButtonText();
				},
				error: function (data) {
					self.actionState(0);
					self.updateButtonText();
					layer.alert("操作失败！" + data.responseText, {icon: 5});
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
						if(v <= 0) {
							return false;
                        }
                    }
                    return true;
				},
				message: "请输入一个大于0的数字且小于10^15的数字"
			}
		});
	}
</script>