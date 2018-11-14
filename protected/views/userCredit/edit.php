<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<script src="/js/jquery.bankInput.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="bank_amount" class="col-sm-2 control-label">活（定）期存款</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <input type="text" class="form-control" id="bank_amount" name="obj[bank_amount]" placeholder="活（定）期存款" data-bind="moneyWan:bank_amount">
                            <span class="input-group-addon">万元</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="jyb_amount" class="col-sm-2 control-label">加油宝理财投资</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <input type="text" class="form-control" id="jyb_amount" name="obj[jyb_amount]" placeholder="加油宝理财投资" data-bind="moneyWan:jyb_amount">
                            <span class="input-group-addon">万元</span>
                        </div>
                    </div>
                </div>

				<?php
				$other_map = $this->map['user_credit_other_json'];
				if (is_array($other_map) && count($other_map) > 0) {
					foreach ($other_map as $key => $row) {
						?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?php echo $row['label'] ?>
                                </label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="<?php echo $row['key'] ?>"
                                           name="obj[other_json][<?php echo $row['key'] ?>]" placeholder="<?php echo $row["label"] ?>"
                                           data-bind="moneyWan:other_json().<?php echo $row['key'] ?>">
                                    <span class="input-group-addon">万元</span>
                                </div>
                            </div>
                        </div>
						<?php
					}
				}
				?>

                <div class="form-group">
                    <label for="credit_amount" class="col-sm-2 control-label">确认额度 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" class="form-control" id="credit_amount" name="obj[credit_amount]" placeholder="个人信用额度" data-bind="moneyWan:credit_amount" disabled>
                            <span class="input-group-addon">万元</span>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <button type="button" id="calculateButton" class="btn btn-primary" data-bind="click:calculate,text:calculateBtnText"></button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="start_time" class="col-sm-2 control-label">生效日期</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="start_time" name="obj[start_time]" placeholder="生效日期" data-bind="value:start_time">
                    </div>
                    <label for="end_time" class="col-sm-2 control-label">失效日期</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="end_time" name="obj[end_time]" placeholder="失效日期" data-bind="value:end_time">
                    </div>
                </div>

                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="remark" name="obj[remark]" placeholder="备注" data-bind="value:remark">
                    </div>
                </div>

				<?php
				if (empty($attachments)) {
					$attachments = SystemUser::getUserAttachments($obj["user_id"], $this->attachmentType);
				}
				$attachmentTypeKey = "user_credit_attachment_type";
				$this->showAttachmentsEditMulti($obj["user_id"], $obj, $attachmentTypeKey, $attachments);
				?>
                <hr/>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="submitButton" class="btn btn-primary" data-bind="click:submit,text:submitBtnText"></button>
                        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[user_id]' data-bind="value:user_id"/>
                        <input type='hidden' name='obj[credit_id]' data-bind="value:credit_id"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($obj) ?>);
		ko.applyBindings(view);
		$("#start_time").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});
		$("#end_time").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});
	});
	function ViewModel(option) {
		var defaults = {
			credit_id: 0,
			user_id: 0,
			bank_amount: 0,
			jyb_amount: 0,
			other_json: "",
			credit_amount: 0,
			start_time: "",
			end_time: "",
			remark: "",
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.credit_id = ko.observable(o.credit_id);
		self.user_id = ko.observable(o.user_id);
		self.bank_amount = ko.observable(o.bank_amount).extend({money: true});
		self.jyb_amount = ko.observable(o.jyb_amount).extend({money: true});
		self.other_json = ko.observable(new OtherModel(option.other_json));
		self.credit_amount = ko.observable(o.credit_amount).extend({money: true, custom: {
			params: function (v) {
				if (v > 0) {
					return true;
				}
				else
					return false;
			},
			message: "确认额度不能为0"
		}});
		self.start_time = ko.observable(o.start_time);
		self.end_time = ko.observable(o.end_time);
		self.remark = ko.observable(o.remark);
		self.actionState = ko.observable(0);
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return (self.other_json().isValid() && self.errors().length === 0);
		}
		self.actionState = ko.observable(0);
		self.calculateBtnText = ko.observable("计算");
		self.submitBtnText = ko.observable("提交");

		self.other_amount = ko.computed(function () {
			return parseFloat(self.other_json().stock()) + parseFloat(self.other_json().equity()) + parseFloat(self.other_json().property()) + parseFloat(self.other_json().vehicle()) + parseFloat(self.other_json().liquid_assets()) + parseFloat(self.other_json().fixed_assets());
		}, self);

		//个人信用额度计算
		self.calculate = function () {
			if (self.actionState() == 1) {
				return;
			}
			self.actionState(1);
			self.calculateBtnText("计算中" + inc.loadingIco);
			self.credit_amount((parseFloat(self.bank_amount()) + parseFloat(self.jyb_amount()) + parseFloat(self.other_amount())) * 8);

			self.actionState(0);
			self.calculateBtnText("计算");
		}

		//提交
		self.submit = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			if (self.actionState() == 1) {
				return;
			}
			self.actionState(1);
			self.submitBtnText = ko.observable("提交" + inc.loadingIco);
			//var formData = $("#mainForm").serialize() + "&obj[credit_amount]=" + self.credit_amount();
			var formData = "data=" + JSON.stringify(ko.toJS(self));

			$.ajax({
				type: "POST",
				url: "/userCredit/submit",
				data: formData,
				dataType: "json",
				success: function (json) {
					self.submitBtnText = ko.observable("提交");
					self.actionState(0);
					if (json.state == 0) {
						location.href = "/userCredit/";
					} else {
						alert(json.data);
					}
				},
				error: function (data) {
					self.buttonText = ko.observable("提交");
					self.actionState(0);
					alert("操作失败！" + data.responseText);
				}
			});
		}

		//返回
		self.back = function () {
			history.back();
		}
	}

	function OtherModel(option) {
		var defaults = {
			stock: 0,
			equity: 0,
			property: 0,
			vehicle: 0,
			liquid_assets: 0,
			fixed_assets: 0,
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.stock = ko.observable(o.stock).extend({money: true});
		self.equity = ko.observable(o.equity).extend({money: true});
		self.property = ko.observable(o.property).extend({money: true});
		self.vehicle = ko.observable(o.vehicle).extend({money: true});
		self.liquid_assets = ko.observable(o.liquid_assets).extend({money: true});
		self.fixed_assets = ko.observable(o.fixed_assets).extend({money: true});
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		}
	}
</script>
