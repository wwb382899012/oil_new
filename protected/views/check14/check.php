<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#detail" data-toggle="tab">保理信息</a></li>
        <li><a href="#flow" data-toggle="tab">审核记录</a></li>
        <?php if (!$this->isExternal) { ?>
            <li class="pull-right">
                <button type="button" class="btn btn-sm btn-default" data-bind="click:back">返回</button>
            </li>
        <?php } ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="detail">
            <div class="box box-primary">
                <form class="form-horizontal" role="form" id="mainForm">
                    <div class="box-body">
                        <h4 class="box-title">付款信息</h4>
                        <?php
                        $factorDetailModel = FactorDetail::model()->findByPk($data['obj_id']);
                        $factor = Factor::model()->findByPk($factorDetailModel->factor_id);
                        $attachments = FactoringDetailService::getAttachments($data['obj_id'], $factorDetailModel->factor_id);
                        $this->renderPartial("/factor/partial/factorInfo", array('factor' => $factor, 'detail' => $factorDetailModel, 'attachments' => $attachments));
                        ?>
                        <div class="box-header with-border"></div>
                        <h4 class="box-title">审核信息</h4>
                        <div class="form-group">
                            <label for="type" class="col-sm-2 control-label">审核意见</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="3" data-bind="value:remark"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
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
        </div>

        <div class="tab-pane" id="flow">
            <?php
            $checkLogs = FlowService::getCheckLog($data['obj_id'], $this->businessId);
            $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status')); ?>
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
			}, maxLength: 512
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
				var confirmInfo = '通过';
				if (self.status() == -1) {
					confirmInfo = '驳回';
				}
				layer.confirm("您确定要" + confirmInfo + "该保理信息审核，该操作不可逆？", {icon: 3, title: '提示'}, function () {
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

