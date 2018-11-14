<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">付款信息</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="form-group">
                    <label for="apply_id" class="col-sm-2 control-label">付款编号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a target="_blank" href="/pay/detail?id=<?php echo $data->apply_id ?>"><?php echo $data->apply_id ?></a>
                        </p>
                    </div>
                    <label for="project_code" class="col-sm-2 control-label">项目编号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a target="_blank" href="/project/detail?id=<?php echo $data->project_id ?>"><?php echo $data->project->project_code ?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="apply_id" class="col-sm-2 control-label">合同编号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a target="_blank" href="/contract/detail?id=<?php echo $data->contract_id ?>"><?php echo $data->contract->contract_code ?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="corporation_id" class="col-sm-2 control-label">付款申请金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo Map::$v['currency'][$data->payApply->currency]['ico'] . Utility::numberFormatFen2Yuan($data->payApply->amount) ?></p>
                    </div>
                    <label for="corporation_id" class="col-sm-2 control-label">申请保理对接金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo Map::$v['currency'][$data->payApply->currency]['ico'] . Utility::numberFormatFen2Yuan($data->apply_amount) ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="corporation_id" class="col-sm-2 control-label">保理对接编号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data->contract_code ?></p>
                    </div>
                    <label for="corporation_id" class="col-sm-2 control-label">资金对接编号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data->contract_code_fund ?></p>
                    </div>
                </div>
                <div class="box-header with-border"></div>
                <h4 class="box-title">保理对接实际信息</h4>
                <div class="form-group">
                    <label for="entry_date" class="col-sm-2 control-label">实际保理对接金额
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">￥</span>
                            <input type="text" class="form-control" placeholder="实际保理对接金额" data-bind="money:amount">
                            <span class="input-group-addon" data-bind="moneyChineseText:amount"></span>
                        </div>
                    </div>
                    <label for="entry_date" class="col-sm-2 control-label">年化利率
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="年化利率" data-bind="value:rate">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="entry_date" class="col-sm-2 control-label">实际放款时间
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control date" placeholder="实际放款时间" data-bind="date:actual_pay_date">
                        </div>
                    </div>
                </div>
                <!--<div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <p class="form-control-static" data-bind="html:remark"></p>
                    </div>
                </div>-->
            </form>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="submitButton" class="btn btn-danger" data-bind="click:submit, html:submitBtnText"></button>
                    <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($factor) ?>);
		ko.applyBindings(view);
	});
	function ViewModel(option) {
		var defaults = {
			factor_id: 0,
			pay_apply_amount: 0,
			amount: 0,
			rate: 1,
            actual_pay_date: '',
			status: 0,
			remark: ''
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.factor_id = ko.observable(o.factor_id);
		self.pay_apply_amount = ko.observable(o.pay_apply_amount);
		self.amount = ko.observable(o.amount).extend({
			custom: {
				params: function (v) {
					self.msg = '不得为空';
					if (v != '' && v != null) {
						if (v > ko.unwrap(self.pay_apply_amount)) {
							self.msg = '不得超过付款申请金额';
							return false;
						}
						if (v < 0) {
							self.msg = '不得为负';
							return false;
						}
					} else {
						return false;
					}

					return true;
				},
				message: function () {
					return self.msg;
				}
			}
		});
		self.rate = ko.observable(o.rate).extend({required: true});
		self.actual_pay_date = ko.observable(o.actual_pay_date).extend({required: true});
		self.status = ko.observable(o.status);
		self.remark = ko.observable(o.remark);

		self.submitBtnText = ko.observable("提交");
		self.actionState = 0;
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		};

		self.sendSubmitAjax = function () {
			if (self.actionState == 1)
				return;
			self.actionState = 1;
			var formData = {"data": inc.getPostData(self, ["submitBtnText", "msg"])};
			console.log(formData);
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/submit',
				data: formData,
				dataType: "json",
				success: function (json) {
					self.actionState = 0;
					self.submitBtnText("提交");
					if (json.state == 0) {
						layer.msg('操作成功', {icon: 6, time: 1000}, function () {
							location.href = "/<?php echo $this->getId() ?>";
						});
					} else if (json.state == -1) {
                        layer.confirm(json.data, {icon: 3, title: '提示', btn: ['确定']},function(){
                            location.href = "/<?php echo $this->getId() ?>/index";
                        });
                    } else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					self.actionState = 0;
					self.submitBtnText("提交");
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});
		};

		self.submit = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			layer.confirm("您确定要提交当前保理对接款信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
				self.status(3);
				self.submitBtnText("提交中" + inc.loadingIco);
				self.sendSubmitAjax();
				layer.close(index);
			})
		};

		self.back = function () {
			location.href = "/<?php echo $this->getId() ?>";
		}
	}
</script>