<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">保理编号取号</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">保理类型<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择保理类型" data-bind="optionsCaption: '请选择保理类型',value: type,valueAllowUnset: true">
                            <?php foreach ($this->map["factor_code_type"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group" data-bind="visible:isShowCorporationSelect">
                    <label for="corporation_id" class="col-sm-2 control-label">交易主体
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择交易主体" data-live-search="true" data-bind="optionsCaption: '请选择交易主体',selectpicker:corporation_id,valueAllowUnset: true">
                            <?php
                            $cors = UserService::getUserSelectedCorporations();
                            foreach ($cors as $v) {
                                echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">编号备注
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
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
		view = new ViewModel();
		ko.applyBindings(view);
	});
	function ViewModel() {
		var self = this;
		self.type = ko.observable(0);
		self.isShowCorporationSelect = ko.computed(function () {
			return ko.unwrap(self.type) == 0;
		}, self);
		self.corporation_id = ko.observable(0).extend({
			custom: {
				params: function (v) {
					if (self.isShowCorporationSelect()) {
						if (v > 0) {
							return true;
						}
						return false;
					}
					return true;
				},
				message: '请选择交易主体'
			}
		});
		self.remark = ko.observable('').extend({required: true});
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
			var formData = {"data": inc.getPostData(self, ["submitBtnText"])};
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
			layer.confirm("您确定要提交当前信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
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