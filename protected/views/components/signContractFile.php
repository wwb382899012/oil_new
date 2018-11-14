<template id='component-template-contract-files'>
    <table class="table table-hover">
        <thead>
        <tr>
            <th colspan="7">
                <form class="form-horizontal">
                    <div class="form-group" style="margin-bottom: 0px;">
                        <div class="col-sm-1">
                            <p class="form-control-static">
                                <span data-bind="text:contractTypeName"></span>
                            </p>
                        </div>
                        <div class="col-sm-2">
                            <p class="form-control-static">
                                <a data-bind="attr:{href:contractDetailUrl}" title="合同详情" target="_blank"><span data-bind="text:contract_code"></span></a>
                            </p>
                        </div>
                        <div class="col-sm-3" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                            <p class="form-control-static">
                                <a data-bind="attr:{href:partnerDetailUrl, title:partner_name}" target="_blank"><span data-bind="text:partner_name"></span></a>
                            </p>
                        </div>
                        <div class="col-sm-2" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                            <p class="form-control-static">
                                <span data-bind="html:amount, attr:{title:amount}"></span>
                            </p>
                        </div>
                        <div class="col-sm-3" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                            <p class="form-control-static">
                                <span data-bind="html:goods, attr:{title:goods}"></span>
                            </p>
                        </div>
                    </div>
                </form>
            </th>
        </tr>
        </thead>
        <tbody>

        <!-- ko foreach: items -->
        <tr>
            <td style="width:200px;" data-bind="html:contract_name"></td>
            <td style="width:200px;" data-bind="html:code"></td>
            <td style="width:150px;" data-bind="html:code_out"></td>
            <td style="width:80px;text-align: center">
                <a target="_blank" class="btn btn-primary btn-xs" data-bind="visible:final_file_id()>0 && final_file_url() != '',attr: { href: getFinalFileReadUrl, title: final_file_name }">查看</a>
            </td>
            <!-- ko if:isShowElectronSignFile -->
            <td style="width:120px;text-align: center">
                <a target="_blank" class="btn btn-primary btn-xs" data-bind="visible:esign_file_id()>0 && esign_file_url() != '',attr: { href: getEsignFileReadUrl, title: esign_file_name }">查看</a>
            </td>
            <!-- /ko -->
            <td style="width:120px; text-align: center" data-bind="text:statusName"></td>
            <!-- ko if:type() == config.electronSignContractFile -->
            <td style="width:200px;">
                <div class="form-inline">
                    <span class="text-red fa fa-asterisk"></span>
                    <input type="text" class="form-control input-sm date" placeholder="合同签订日期" data-bind="date:sign_date,enable:status()<3" onkeydown="inc.stopEnterDefault()">
                </div>
            </td>
            <!-- /ko -->
            <td>
                <span class="btn btn-success fileinput-button btn-xs" data-bind="visible:isUpload">
                    <span class="btn-text" data-bind="html:btnText">选择上传文件</span>
                    <input type="file" data-bind="fileUpload:true,url:$parent.postUrl,add:addFunction,done:doneFunction"/>
                </span>
                <a target="_blank" class="btn btn-primary btn-xs" data-bind="visible:(file_url() != '' && file_url() != null),attr: { href: getReadUrl, title: name }">查看</a>
                <button class="btn btn-warning btn-xs" data-bind="visible:isShowSubmitBtn,click:submit">提交</button>
            </td>
        </tr>
        <!-- /ko -->
        </tbody>
    </table>
</template>
<script>

	ko.components.register('contract-files', {
		template: {element: 'component-template-contract-files'},
		viewModel: contractFilesComponent
	});

	function ContractFile(option) {
		var defaults = {
			file_id: 0,
			project_id: 0,
			contract_id: 0,
			is_main: 0,
			type: 0,
			category: 0,
			status: 0,
			version_type: 0,
			code: "",
			code_out: "",
			name: "",
			action_status: 1,
			file_url: "",
			contract_name: "",
			final_file_url: "",
			final_file_name: "",
			final_file_id: 0,
			esign_file_url: "",
			esign_file_name: "",
			esign_file_id: 0,
			sign_date: ''
		};

		var o = $.extend(defaults, option);
		var self = this;
		self.file_id = ko.observable(o.file_id);
		self.project_id = ko.observable(o.project_id);
		self.contract_id = ko.observable(o.contract_id);
		self.name = ko.observable(o.name);
		self.is_main = ko.observable(o.is_main);
		self.type = ko.observable(o.type);
		self.category = ko.observable(o.category);
		self.version_type = ko.observable(o.version_type);
		self.code = ko.observable(o.code);
		self.code_out = ko.observable(o.code_out);
		self.file_url = ko.observable(o.file_url);
		self.status = ko.observable(o.status);
		self.action_status = ko.observable(o.action_status);
		self.controller = ko.observable(o.controller);
		self.fileConfig = ko.observable(o.fileConfig);
		self.file_status = o.file_status;
		self.cotroller = o.cotroller;
		self.contract_name = ko.observable(o.contract_name);
		self.final_file_url = ko.observable(o.final_file_url);
		self.final_file_name = ko.observable(o.final_file_name);
		self.final_file_id = ko.observable(o.final_file_id);
		self.esign_file_url = ko.observable(o.esign_file_url);
		self.esign_file_name = ko.observable(o.esign_file_name);
		self.esign_file_id = ko.observable(o.esign_file_id);
		self.sign_date = ko.observable(o.sign_date).extend({custom: {
			params: function (v) {
				if (self.type() == config.electronSignContractFile) {
                    if(inc.isEmpty(v)) {
                    	return false;
                    }
                }
                return true;
			},
			message: "请选择合同签订日期"
		}
		});
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		};
		self.btnText = ko.observable("选择上传文件");
		self.getFinalFileReadUrl = ko.computed(function () {
			return "/contractUpload/getFile/?id=" + self.final_file_id() + "&fileName=" + self.final_file_name();
		}, self);

		self.getEsignFileReadUrl = ko.computed(function () {
			return "/electronSign/getFile/?id=" + self.esign_file_id() + "&fileName=" + self.esign_file_name();
		}, self);

		self.getReadUrl = ko.computed(function () {
			return "/" + self.controller() + "/getFile/?id=" + self.file_id() + "&fileName=" + self.name();
		}, self);

		self.isShowSubmitBtn = ko.computed(function () {
			return self.file_url() != null && self.file_url() != '' && self.action_status() == 1 && self.status() < 3;
		}, self);

		self.isUpload = ko.computed(function () {
			return self.status() < 3;
		}, self);

		self.statusName = ko.computed(function () {
			if (self.file_status[self.status().toString()])
				return self.file_status[self.status().toString()];
			else
				return "";
		}, self);

		self.isShowElectronSignFile = ko.computed(function () {
			return self.controller() == 'paperSign';
		}, self)

		self.submit = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}

			layer.confirm("您确定要提交当前信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
				self.action_status(0);
				self.status(3);
				self.sendSaveSubmitAjax();
				layer.close(index);
			})
		}

		self.sendSaveSubmitAjax = function () {
			if (self.file_url() == '') {
				layer.alert("请上传文件！", {icon: 5});
				return;
			}
			var formData = {"data": inc.getPostData(self, ["controller", "fileConfig", "file_status", "action_status", "btnText", "getReadUrl", "isShowSubmitBtn", "isUpload", "statusName", "final_file_url", "final_file_name", "final_file_id", "contract_name", "getFinalFileReadUrl", "esign_file_url", "esign_file_name", "esign_file_id"])};
			console.log(formData);
			$.ajax({
				type: 'POST',
				url: '/' + self.controller() + '/save',
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						layer.msg("操作成功", {icon: 6, time: 1000});
					} else {
						layer.alert("操作失败", {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("操作失败：" + data.responseText, {icon: 5});
				}
			});
		};

		self.setBtnText = function () {
			if (self.file_url() != '' && self.file_url() != null)
				self.btnText("重新上传");
			else
				self.btnText("选择上传文件");
		};

		self.setBtnText();

		self.addFunction = function (e, data) {
			if (!self.isUpload()) {
				layer.alert("当前状态不允许上传", {icon: 5});
				return;
			}
			if (!inc.checkFileType(data.files[0].name, ko.unwrap(self.fileConfig().fileType))) {
				layer.alert("只能上传指定类型的文件：" + ko.unwrap(self.fileConfig().fileType), {icon: 5});
				return;
			}
			if (data.files[0].size > ko.unwrap(self.fileConfig().maxSize * 1024 * 1024)) {
				layer.alert("文件大小超过最大限制：" + ko.unwrap(self.fileConfig().maxSize * 1024 * 1024) + "K", {icon: 5});
				return;
			}
			self.btnText("正在上传文件。。。");
			data.formData = {
				id: ko.unwrap(self.file_id()),
				project_id: ko.unwrap(self.project_id()),
				contract_id: ko.unwrap(self.contract_id()),
				is_main: ko.unwrap(self.is_main()),
				category: ko.unwrap(self.category()),
                code: ko.unwrap(self.code()),
				type: self.fileConfig().id
			};
			;

			data.submit();
		}

		self.doneFunction = function (e, data) {
			if (data.result.state == 0) {
				self.file_id(data.result.data);
				self.name(data.result.extra.name);
				self.file_url(data.result.extra.file_url);
				self.status(data.result.extra.status);
				self.action_status(1);
			} else {
				layer.alert(data.result.data, {icon: 5});
			}
			self.setBtnText();
		}

		self.failFunction = function () {
			layer.alert("上传出错，请稍后重试！", {icon: 5});
			self.setBtnText();
		}

	}
	function contractFilesComponent(params) {
		var self = this;
		self.contract_id = params.contract_id;
		self.contract_code = params.contract_code;
		self.controller = params.controller;
		self.partner_id = params.partner_id;
		self.partner_name = params.partner_name;
		self.amount = params.amount;
		self.goods = params.goods;
		self.postUrl = "/" + self.controller + "/saveFile/";//文件提交地址
		self.fileConfig = params.fileConfig;
		self.file_status = ko.unwrap(params.file_status);

		self.items = ko.observableArray();

		self.contractTypes = {
			"1": "采购合同",
			"2": "销售合同"
		};

		self.contractTypeName = ko.computed(function () {
			return self.contractTypes[ko.unwrap(params.type)];
		}, self);

		if (params.files) {
			var files = ko.unwrap(params.files);
			var d = {
				controller: self.controller,
				file_status: self.file_status,
				fileConfig: self.fileConfig
			};
			for (var i = 0; i < files.length; i++) {
				var obj = new ContractFile($.extend(d, files[i]));
				self.items.push(obj);
			}
		}

		self.contractDetailUrl = ko.computed(function () {
			return "/businessConfirm/detail/?id=" + self.contract_id + '&t=1';
		}, self);

		self.partnerDetailUrl = ko.computed(function () {
			return "/partner/detail/?id=" + self.partner_id +'&t=1';
		}, self);
	}

</script>