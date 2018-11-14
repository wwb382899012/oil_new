<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">付款信息</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" id="mainForm">
                <?php
                $this->renderPartial("partial/payInfo", array('factor' => $factor));
                ?>

                <div class="box-header with-border"></div>

                <h4 class="box-title">保理信息</h4>
                <div class="form-group">
                    <label for="corporation_id" class="col-sm-2 control-label">交易主体</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/corporation/detail/?id=<?php echo $factor->corporation_id ?>&t=1" title="<?php echo $factor->corporation->name ?>" target="_blank"><?php echo $factor->corporation->name ?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="entry_date" class="col-sm-2 control-label">对接本金
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">￥</span>
                            <input type="text" class="form-control" placeholder="对接本金" data-bind="money:amount">
                            <span class="input-group-addon" data-bind="moneyChineseText:amount"></span>
                        </div>
                    </div>
                    <label for="entry_date" class="col-sm-2 control-label">利息</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">￥<span data-bind="html:interest()/100"></span></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="return_date" class="col-sm-2 control-label">合同回款时间
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control input-sm" placeholder="合同回款时间" data-bind="date:return_date">
                    </div>
                    <label for="pay_date" class="col-sm-2 control-label">合同放款时间
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control input-sm" placeholder="合同放款时间" data-bind="date:pay_date">
                    </div>
                </div>
                <?php
                $attachTypes = $this->map['factor_attachment_type'];
                ?>
                <!-- ko component: {
                        name: "multi-file-upload",
                        params: {
                            controller:"<?php echo $this->getId() ?>",
                            attachTypes:<?php echo json_encode($attachTypes) ?>,
                            attachs:<?php echo json_encode($attachments); ?>,
                            fileParams: {
                                id: <?php echo $factor['factor_id'] ?>,
                                detail_id: <?php echo $data['detail_id'] ?>,
                                project_id: <?php echo $factor['project_id'] ?>
                            }
                        }
                    } -->
                <!-- /ko -->
                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/multiUpload.php"; ?>

                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save, html:saveBtnText"></button>
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
		view = new ViewModel(<?php echo json_encode($data) ?>);
		ko.applyBindings(view);
		view.balance_amount(<?php echo $factor->amount - $factor->checkingAmount() - $factor->buttedAmount() ?>);
	});
	function ViewModel(option) {
		var defaults = {
			detail_id: 0,
			factor_id: 0,
			apply_id: 0,
			corporation_id: 0,
			project_id: 0,
			contract_id: 1,
			amount: 0,
			rate: 0,
			pay_date: (new Date()).format(),
			return_date: (new Date()).format(),
			remark: '',
			status: 0,
            interest: 0
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.detail_id = ko.observable(o.detail_id);
		self.factor_id = ko.observable(o.factor_id);
		self.apply_id = ko.observable(o.apply_id);
		self.corporation_id = ko.observable(o.corporation_id);
		self.project_id = ko.observable(o.project_id);
		self.contract_id = ko.observable(o.contract_id);
		self.rate = ko.observable(o.rate);
		self.balance_amount = ko.observable(0);
		self.amount = ko.observable(o.amount).extend({
			custom: {
				params: function (v) {
					self.msg = '请输入大于0的数字金额';
					if (!inc.isMoney(v)) {
						return false;
					}
					if (v > ko.unwrap(self.balance_amount)) {
						self.msg = '对接本金不能大于剩余可对接金额';
						return false;
					}

					return true;
				},
				message: function () {
					return self.msg;
				}
			}
		});
		self.pay_date = ko.observable(o.pay_date).extend({date: true});
		self.return_date = ko.observable(o.return_date).extend({
			custom: {
				params: function (v) {
					if (self.pay_date() != '') {
						var startDate = new Date(self.pay_date());
						var endDate = new Date(v);
						if (endDate < startDate) {
							return false;
						} else {
							return true;
						}
					}

					return true;
				},
				message: function () {
					return '合同回款时间不能小于合同放款时间';
				}
			}
		});
		self.remark = ko.observable(o.remark);
		self.status = ko.observable(o.status);

		self.saveBtnText = ko.observable("保存");
		self.submitBtnText = ko.observable("提交");
		self.actionState = 0;
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		};
		self.interest = ko.observable(o.interest);
		self.computeInterest = function () {
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/computeInterest',
				data: {
					"data": {
						"amount": ko.unwrap(self.amount),
						"rate": ko.unwrap(self.rate),
						"start_date": ko.unwrap(self.pay_date),
						"end_date": ko.unwrap(self.return_date)
					}
				},
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						console.log(self.interest);
						self.interest(json.data);
					} else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("利息获取失败！" + data.responseText, {icon: 5});
				}
			});
		};

		self.amount.subscribe(function () {
			self.computeInterest();
		});

		self.pay_date.subscribe(function () {
			if (self.isValid())
				self.computeInterest();
		});

		self.return_date.subscribe(function () {
			if (self.isValid())
				self.computeInterest();
		});

		self.sendSaveSubmitAjax = function () {
			if (self.actionState == 1)
				return;
			self.actionState = 1;
			var formData = {"data": inc.getPostData(self, ["saveBtnText", "submitBtnText"])};
			console.log(formData);
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/save',
				data: formData,
				dataType: "json",
				success: function (json) {
					self.actionState = 0;
					self.saveBtnText("保存");
					self.submitBtnText("提交");
					if (json.state == 0) {
						layer.msg('操作成功', {icon: 6, time: 1000}, function () {
							if (self.status() == 10) {
								location.href = "/<?php echo $this->getId() ?>";
							} else {
								location.href = "/<?php echo $this->getId() ?>/detail/?id=" + self.detail_id();
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
		};

		self.save = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			self.saveBtnText("保存中" + inc.loadingIco);
			self.sendSaveSubmitAjax();
		};

		self.submit = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			layer.confirm("您确定要提交当前保理对接信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
				self.status(10);
				self.submitBtnText("提交中" + inc.loadingIco);
				self.sendSaveSubmitAjax();
				layer.close(index);
			})
		};

		self.back = function () {
			location.href = "/<?php echo $this->getId() ?>";
		}
	}
</script>