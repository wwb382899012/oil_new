<template id='component-template-contract-files'>
    <table class="table table-hover">
        <thead>
        <tr>
            <th colspan="5">
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
                        <div class="col-sm-1">
                            <p class="form-control-static">
                                <button class="btn btn-success btn-xs" data-bind="click:add">新增</button>
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
            <!--<td style="width:300px;text-align: left; display: none;">
                <span data-bind="text:$parent.contract_code"></span>
            </td>-->
            <td style="width:320px;">
                <div class="form-inline">
                    <span class="text-red fa fa-asterisk"></span>
                    <select class="form-control input-sm" title="合同类型"
                            data-bind="
                                    enable:isCanEdit() && is_main()==0,
                                    optionsText: 'name',
                                    optionsValue: 'id',
                                    options:$parent.categories,
                                    value:category">
                    </select>
                    <span class="text-red fa fa-asterisk"></span>
                    <select class="form-control input-sm" title="版本类别"
                            data-bind="
                                    enable:isCanEdit(),
                                    optionsText: 'name',
                                    optionsValue: 'id',
                                    options:$parent.version_types,
                                    value:version_type">
                    </select>
                </div>
            </td>

            <td style="width:200px;">
                <div class="form-inline">
                    <span class="text-red fa fa-asterisk"></span>
                    <input type="text" class="form-control input-sm" placeholder="我方合同编号" data-bind="enable:isCanEdit() && is_main()==0,value:code" onkeydown="inc.stopEnterDefault()">
                </div>
            </td>
            <!--<td style="width:150px;">
                <div class="form-inline">
                    <span class="text-red fa fa-asterisk"></span>
                    <input type="text" class="form-control input-sm" placeholder="我方合同编号" data-bind="enable:isCanEdit() && !is_main(),value:code">
                </div>
            </td>-->
            <td style="width:150px;">
                <input type="text" class="form-control input-sm" placeholder="对方合同编号" data-bind="enable:isCanEdit,value:code_out" onkeydown="inc.stopEnterDefault()">
            </td>
            <td style="width:100px; text-align: center;vertical-align: middle!important;" data-bind="text:statusName">

            </td>
            <td>
                    <span class="z-btn-action fileinput-button" data-bind="visible:isUpload">
                        <span class="btn-text" data-bind="html:btnText">选择上传文件</span>
                        <input type="file" data-bind="fileUpload:true,url:$parent.postUrl,add:addFunction,done:doneFunction"/>
                    </span>
                <a target="_blank" class="z-btn-action" data-bind="visible:(file_url() != '' && file_url() != null),attr: { href: getReadUrl, title: name }">查看</a>
                <a href="javascript: void 0" class="z-btn-action" data-bind="visible:isCanDelete,click:$parent.del">删除</a>
                <a href="javascript: void 0" class="z-btn-action" data-bind="visible:isShowSaveBtn,click:save">保存</a>
                <a href="javascript: void 0" class="z-btn-action" data-bind="visible:isShowEditBtn,click:edit">修改</a>
                <a href="javascript: void 0" class="z-btn-action" data-bind="visible:isShowSubmitBtn,click:submit">提交</a>
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
			file_url: ""
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
		self.code = ko.observable(o.code).extend({required: true});
		self.code_out = ko.observable(o.code_out);
		self.file_url = ko.observable(o.file_url);
		self.status = ko.observable(o.status);
		self.action_status = ko.observable(o.action_status);
		self.controller = ko.observable(o.controller);
		self.fileConfig = ko.observable(o.fileConfig);
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		};
		//self.fileType=o.fileConfig.fileType;//允许上传的文件类型
		//self.maxSize=o.fileConfig.maxSize;//允许上传的文件大小最大值，单位M
		self.file_status = o.file_status;
		self.cotroller = o.cotroller;

		self.btnText = ko.observable("选择上传文件");

		self.getReadUrl = ko.computed(function () {
			return "/" + self.controller() + "/getFile/?id=" + self.file_id() + "&fileName=" + self.name();
		}, self);

		self.isCanEdit = ko.computed(function () {
			return self.action_status() == 1 && self.status() < 3;
		}, self);

		self.isShowEditBtn = ko.computed(function () {
			return self.file_url() != '' && self.file_url() != null && self.action_status() == 0 && self.status() < 3;
		}, self);

		self.isShowSaveBtn = ko.computed(function () {
			return self.file_url() != '' && self.file_url() != null && self.action_status() == 1 && self.status() < 3;
		}, self);

		self.isShowSubmitBtn = ko.computed(function () {
			return self.file_url() != '' && self.file_url() != null && self.action_status() == 0 && self.status() < 3;
		}, self);

		self.isCanDelete = ko.computed(function () {
			return self.is_main() != 1 && self.status() < 3;
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


		self.save = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}

			self.action_status(0);
			self.status(1);
			self.sendSaveSubmitAjax();
		}
		self.submit = function () {
			layer.confirm("您确定要提交当前信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
				if (!self.isValid()) {
					self.errors.showAllMessages();
					return;
				}

				self.status(3);
				self.sendSaveSubmitAjax();
				layer.close(index);
			})
		}

		self.sendSaveSubmitAjax = function () {
			if (self.file_url() == '' && self.file_url() != null) {
				layer.alert("请上传文件！", {icon: 5});
				return;
			}
			var formData = {"data": inc.getPostData(self, ["controller", "fileConfig", "file_status", "action_status", "btnText", "getReadUrl", "isCanEdit", "isShowEditBtn", "isShowSaveBtn", "isShowSubmitBtn", "isCanDelete", "isUpload", "statusName"])};
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
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("操作失败：" + data.responseText, {icon: 5});
				}
			});
		}

		self.edit = function () {
			self.action_status(1);
		}

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

			data.submit();
		}

		self.doneFunction = function (e, data) {
			if (data.result.state == 0) {
				self.file_id(data.result.data);
				self.name(data.result.extra.name);
				self.file_url(data.result.extra.file_url);
				self.status(data.result.extra.status);
				self.type(self.fileConfig().id);
				if(self.code() == "") {
					self.action_status(1);
                } else {
					self.action_status(0);
				}
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
		self.project_id = params.project_id;
		self.contract_id = params.contract_id;
		self.contract_code = params.contract_code;
		self.partner_id = params.partner_id;
		self.partner_name = params.partner_name;
		self.amount = params.amount;
		self.goods = params.goods;
		self.categories = ko.observableArray(inc.objectToArray(params.categories[params.type]));
		self.version_types = ko.observableArray(inc.objectToArray(params.version_types));
		self.controller = params.controller;
		self.postUrl = "/" + self.controller + "/saveFile/";//文件提交地址
		//self.maxSize=params.fileConfig.maxSize*1024*1024;//允许上传的文件大小最大值，单位M
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
			return "/contract/detail/?id=" + self.contract_id +'&t=1';
		}, self);

		self.partnerDetailUrl = ko.computed(function () {
			return "/partner/detail/?id=" + self.partner_id +'&t=1';
		}, self);

		self.add = function () {
			$.ajax({
				type: 'GET',
				url: '/' + self.controller + '/getFileId',
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						var obj = new ContractFile({
							file_id: json.data,
							controller: self.controller,
							file_status: self.file_status,
							contract_id: self.contract_id,
							fileConfig: self.fileConfig,
							project_id: self.project_id
						});
						self.items.push(obj);
					} else {
						layer.alert("获取合同ID出错！", {icon: 5});
						return;
					}
				},
				error: function (data) {
					layer.alert("获取合同ID出错！：" + data.responseText, {icon: 5});
					return;
				}
			});
		}

		self.del = function (file) {
			if (file.is_main() == 1 || file.status() >= 3) {
				layer.alert("当前数据不能删除！", {icon: 5});
				return;
			}
			if (file.file_id() < 1) {
				self.items.remove(file);
				return;
			}

			//调用删除的代码
			var formData = "id=" + file.file_id();

			$.ajax({
				type: 'POST',
				url: '/' + self.controller + '/delFile',
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						layer.msg("操作成功", {icon: 6, time: 1000}, function () {
							self.items.remove(file);
						});
					}
					else {
						layer.alert("操作失败", {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("操作失败：" + data.responseText, {icon: 5});
				}
			});
		}
	}

</script>