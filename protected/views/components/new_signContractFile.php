<template id='component-template-contract-files'>
    <div class="in-table-wrapper">
        <div class="flex-grid form-group align-between">
            <label class="field flex-grid emphasis">
                <span class="line-h--text cell-title colon text-link" style="color: #3e8cf7;" data-bind="text:contractTypeName"></span>
                <span class="form-control-static line-h--text">
                        <a href="javascript: void 0" class="text-link" title="合同详情" target="_blank" data-bind="attr:{href:contractDetailUrl}"><span data-bind="text:contract_code"></span></a>
                    </span>
            </label>
            <label class="field flex-grid emphasis">
                    <span class="form-control-static line-h--text">
                        <a href="#" class="text-link" target="_blank" data-bind="attr:{href:partnerDetailUrl, title:partner_name}"><span data-bind="text:partner_name"></span></a>
                    </span>
            </label>
            <label class="field flex-grid emphasis">
                <span class="form-control-static line-h--text" data-bind="html:amount, attr:{title:amount}"></span>
            </label>
            <label class="field flex-grid emphasis">
                <span class="form-control-static line-h--text" data-bind="html:goods, attr:{title:goods}"></span>
            </label>
        </div>


        <table class="table table-fixed">
            <tbody>

            <!-- ko foreach: items -->
            <tr>
                <td style="width:calc(152px - 12px);" data-bind="html:new_contract_name"></td>
                <td style="width:115px;" data-bind="html:type_name"></td>
                <td style="width:210px;" data-bind="html:code"></td>
                <td style="width:210px;;" data-bind="html:code_out"></td>
                <td style="width:80px;">
                    <a target="_blank" class="o-btn o-btn-action" data-bind="visible:final_file_id()>0 && final_file_url() != '',attr: { href: getFinalFileReadUrl, title: final_file_name }">查看</a>
                </td>
                <!-- ko if:isShowElectronSignFile -->
                <td style="width:120px;">
                    <a target="_blank" class="o-btn o-btn-action" data-bind="visible:esign_file_id()>0 && esign_file_url() != '',attr: { href: getEsignFileReadUrl, title: esign_file_name }">查看</a>
                </td>
                <!-- /ko -->
                <td style="width:72px;" data-bind="text:statusName"></td>
                <!-- ko if:type() == config.electronSignContractFile -->
                <td style="width:140px;">
                    <input type="text" class="form-control date" placeholder="合同签订日期" data-bind="date:sign_date,enable:status()<3" onkeydown="inc.stopEnterDefault()">
                </td>
                <!-- /ko -->
                <td style="width:130px;">
                    <span class="o-btn o-btn-action fileinput-button" data-bind="visible:isUpload" style="vertical-align: middle">
                        <span class="btn-text" data-bind="html:btnText">选择上传文件</span>
                        <input type="file" data-bind="fileUpload:true,url:$parent.postUrl,add:addFunction,done:doneFunction"/>
                    </span>
                    <a target="_blank" style="vertical-align: middle"
                       class="o-btn o-btn-action" data-bind="visible:(file_url() != '' && file_url() != null),attr: seeBtnAttr">查看</a>
                </td>
                <td style="width: calc(192px - 12px)">
                    <a href="javascript: void 0" class="o-btn o-btn-action primary" data-bind="visible:isShowSubmitBtn,click:submit">提交</a>
                </td>
            </tr>
            <!-- /ko -->
            </tbody>
        </table>
    </div>

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
			sign_date: '',
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
		self.btnText = ko.observable("上传");
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

        self.type_name = ko.computed(function () {
            return o.version_types[o.version_type].name;
        }, self);

        self.new_contract_name = ko.computed(function () {
            return o.categories[o.contract_type][o.category].name;
        }, self);

		self.isShowElectronSignFile = ko.computed(function () {
			return self.controller() == 'paperSign';
		}, self)

		self.submit = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}

			inc.vueConfirm({
				content: "您确定要提交当前信息吗，该操作不可逆？", onConfirm: function () {
					self.action_status(0);
					self.status(3);
					self.sendSaveSubmitAjax();
				}
			})
		}

        self.seeBtnAttr=ko.computed(function () {
            if(self.file_url() != '' && self.file_url() != null){
                return { href: self.getReadUrl, title: self.name ,style:'display: inline-block;overflow: hidden; vertical-align: middle'};
            }else{
                return { href: self.getReadUrl, title: self.name };
            }
        }, self);

		self.sendSaveSubmitAjax = function () {
			if (self.file_url() == '') {
				inc.vueAlert({content: "请上传文件！"});
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
						inc.vueMessage("操作成功");
					} else {
						inc.vueAlert({content: "操作失败"});
					}
				},
				error: function (data) {
					inc.vueAlert({content: "操作失败：" + data.responseText});
				}
			});
		};

		self.setBtnText = function () {
			if (self.file_url() != '' && self.file_url() != null)
				self.btnText("重传");
			else
				self.btnText("上传");
		};

		self.setBtnText();

		self.addFunction = function (e, data) {
			if (!self.isUpload()) {
				inc.vueAlert({content: "当前状态不允许上传"});
				return;
			}
			if (!inc.checkFileType(data.files[0].name, ko.unwrap(self.fileConfig().fileType))) {
				inc.vueAlert({content: "只能上传指定类型的文件：" + ko.unwrap(self.fileConfig().fileType)});
				return;
			}
			if (data.files[0].size > ko.unwrap(self.fileConfig().maxSize * 1024 * 1024)) {
				inc.vueAlert({content: "文件大小超过最大限制：" + ko.unwrap(self.fileConfig().maxSize * 1024 * 1024) + "K"});
				return;
			}
			self.btnText("上传中");
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
				inc.vueAlert({content: data.result.data});
			}
			self.setBtnText();
		}

		self.failFunction = function () {
			inc.vueAlert({content: "上传出错，请稍后重试！"});
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
				fileConfig: self.fileConfig,
                categories:params.categories,
                version_types:params.version_types,
                contract_type:params.type
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