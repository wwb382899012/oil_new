<section class="content" id="content">
    <?php
    $deliveryOrderModel = DeliveryOrder::model()->findByPk($data['obj_id']);
    $this->renderPartial("/deliveryOrder/partial/deliveryOrderInfo", array('deliveryOrder' => $deliveryOrderModel,'isShowBackButton'=>true));
    ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">审核信息</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">审核意见</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" data-bind="value:remark"></textarea>
                    </div>
                </div>

            </form>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" class="btn btn-success" data-bind="click:doPass,html:passText"></button>
                    <button type="button" class="btn btn-danger" data-bind="click:doReject,html:backText"></button>
                    <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
		ko.applyBindings(view);
	});

	function ViewModel(option) {
		var defaults = {
			check_id: 0,
			remark: '',
			status: 0,
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.check_id = o.check_id;
		self.status = ko.observable(o.status);
		self.remark = ko.observable(o.remark).extend({
			custom: {
				params: function (v) {
					return (self.status() != -1 || (v != '' && v != null));
				},
				message: '请填写审核意见'
			},maxLength:512
		});
		self.errors = ko.validation.group(self);
		self.passText = ko.observable('通过');
		self.backText = ko.observable('驳回');
		self.actionState = 0;
		self.isValid = function () {
			return self.errors().length === 0;
		};
		self.doPass = function () {
			self.status(1);
			self.sendApprovalAjax();
		}
		self.doReject = function () {
			self.status(-1);
			self.sendApprovalAjax();
		}
		self.sendApprovalAjax = function () {
			if (self.isValid() && self.actionState == 0) {
				var confirmInfo = '通过该发货单审核';
				if (self.status() == -1) {
					confirmInfo = '驳回该发货单审核';
				}
				layer.confirm("您确定要" + confirmInfo + "，该操作不可逆？", {icon: 3, title: '提示'}, function () {
					var formData = {
						obj: {
							remark: self.remark(),
							check_id: self.check_id,
							checkStatus: self.status(),
						}
					};
					//console.log(formData);
					if (self.status() == -1) {
						self.backText('驳回' + inc.loadingIco);
					} else {
						self.passText('通过' + inc.loadingIco);
					}
					self.actionState = 1;
					$.ajax({
						type: "POST",
						url: "/<?php echo $this->getId() ?>/save",
						data: formData,
						dataType: "json",
						success: function (json) {
							self.actionState = 0;
							self.passText("通过");
							self.backText("驳回");
							if (json.state == 0) {
								layer.msg('操作成功', {icon: 6, time: 1000}, function () {
									location.href = "/<?php echo $this->getId() ?>";
								});
							} else {
								layer.alert(json.data);
							}
						},
						error: function (data) {
							self.actionState = 0;
							self.passText("通过");
							self.backText("驳回");
							layer.alert("操作失败！" + data.responseText, {icon: 5});
						}
					});
				});

			} else {
				self.errors.showAllMessages();
			}
		}

		self.back = function () {
			location.href = "/<?php echo $this->getId() ?>/?search[checkStatus]=1";
		}
	}
</script>

