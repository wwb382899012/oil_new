<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div><!--end box box-header-->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">姓名
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="name" name="data[name]" placeholder="姓名" data-bind="value:name">
                    </div>
                </div>
				<div class="form-group">
                    <label for="name" class="col-sm-2 control-label">编码
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="code" name="data[code]" placeholder="编码" data-bind="value:code">
                    </div>
                </div>
                <div class="form-group">
                    <label for="sex" class="col-sm-2 control-label">性别
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" id="sex" name="data[sex]" data-bind="value:sex">
							<?php foreach ($this->map["gender"] as $k => $v) {
								echo "<option value='" . $k . "'>" . $v . "</option>";
							} ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_code" class="col-sm-2 control-label">身份证号码
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="id_code" name="data[id_code]" placeholder="身份证号码" data-bind="value:id_code">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="col-sm-2 control-label">手机号码 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="phone" name="data[phone]" placeholder="手机号码" data-bind="value:phone">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address" class="col-sm-2 control-label">住址 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="address" name="data[address]" placeholder="住址" data-bind="value:address">
                    </div>
                </div>

                <div class="form-group">
                    <label for="status" class="col-sm-2 control-label">状态 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" id="status" name="data[status]" data-bind="value:status">
							<?php foreach ($this->map["user_status"] as $k => $v) {
								echo "<option value='" . $k . "'>" . $v . "</option>";
							} ?>
                        </select>
                    </div>
                </div>
                <hr>

                <div class="form-group">
                    <label for="contact_person" class="col-sm-2 control-label">紧急联系人</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="contact_person" name="data[contact_person]" placeholder="紧急联系人" data-bind="value:contact_person">
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact_phone" class="col-sm-2 control-label">手机号码</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="contact_phone" name="data[contact_phone]" placeholder="手机号码" data-bind="value:contact_phone">
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact_id_code" class="col-sm-2 control-label">身份证号码</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="contact_id_code" name="data[contact_id_code]" placeholder="身份证号码" data-bind="value:contact_id_code">
                    </div>
                </div>

                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="remark" name= "data[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
                <hr>

				<?php
				if (empty($attachments)) {
//					$attachments = SystemUser::getUserAttachments($data["user_id"], $attachmentType);
					$attachments = SystemUser::getUserAttachments($data["user_id"], $this->attachmentType);
				}
				$attachmentTypeKey = "user_extra_attachment_type";
				$this->showAttachmentsEditMulti($data["user_id"], $data, $attachmentTypeKey, $attachments);
				?>
            </div><!--end box-border-->
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save,html:saveBtnText"></button>
                        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                        <input type="hidden" name="data[user_id]" data-bind="value:user_id"/>
                    </div>
                </div>
            </div>
        </form>
    </div><!--end box box-primary-->
</section><!--end content-->

<script>
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
		ko.applyBindings(view);
		$("td[data-type='2003']").find(".file-title").append(" <span class='text-red fa fa-asterisk'></span>");
	});

	function ViewModel(option) {
		var defaults = {
			user_id: "",
			name: "",
			code: "",
			sex: "",
			id_code: "",
			phone: "",
			address: "",
			status: "",
			contact_person: "",
			contact_phone: "",
			contact_id_code: "",
			remark: "",
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.user_id = ko.observable(o.user_id);
		self.name = ko.observable(o.name).extend({required: {params: true, message: "姓名不能为空"}});
		self.code = ko.observable(o.code).extend({number:{params: true, message: "请输入一个数值"}, minLength: {params: 2, message: "至少输入 {0} 个数字"}});
		self.sex = ko.observable(o.sex).extend({required: {params: true, message: "请选择性别"}});
		self.id_code = ko.observable(o.id_code).extend({required: {params: true, message: "身份证号码不能为空"}, idCard: true});
		self.phone = ko.observable(o.phone).extend({required: {params: true, message: "手机号码不能为空"}, phone: true});
		self.address = ko.observable(o.address).extend({required: {params: true, message: "住址不能为空"}});
		self.status = ko.observable(o.status).extend({required: {params: true, message: "请选择状态"}});
		self.contact_person = ko.observable(o.contact_person);
		self.contact_phone = ko.observable(o.contact_phone).extend({phone: true});
		self.contact_id_code = ko.observable(o.contact_id_code).extend({idCard: true});
		self.remark = ko.observable(o.remark);
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		}
		self.buttonState = ko.observable(0);
		self.saveBtnText = ko.observable("保存");

		//保存
		self.save = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			if (self.buttonState() == 1) {
				return;
			}

			self.buttonState(1);
			self.saveBtnText("保存中" + inc.loadingIco);
			var formData = $("#mainForm").serialize();
			$.ajax({
				type: "POST",
				url: "/businessAssistant/save",
				data: formData,
				dataType: "json",
				success: function (json) {
					self.buttonState(0);
					self.saveBtnText("保存");
					if (json.state == 0) {
						inc.showNotice("保存成功");
						location.href = "/businessAssistant/detail/?user_id=" + self.user_id();
					} else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					self.buttonState(0);
					self.saveBtnText("保存");
					layer.alert("保存失败！" + data.responseText, {icon: 5});
				}
			});
		}

		self.back = function () {
			history.back();
		}
	}
</script>
