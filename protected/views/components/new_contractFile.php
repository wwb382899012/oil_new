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
                <td style="width:calc(152px - 12px);">
                    <select  class="form-control" title="合同类型"
                            data-bind="
                                    enable:isCanEdit() && is_main()==0,
                                    optionsText: 'name',
                                    optionsValue: 'id',
                                    options:$parent.categories,
                                    value:category">
                    </select>
                </td>
                <td style="width:115px;">
                    <select class=" form-control" title="版本类别"
                            data-bind="
                                    enable:isCanEdit(),
                                    optionsText: 'name',
                                    optionsValue: 'id',
                                    options:$parent.version_types,
                                    value:version_type">
                    </select>
                </td>
                <td style="width:210px;">
                    <input type="text"  class="el-input__inner form-control" placeholder="我方合同编号"
                           data-bind="enable:isCanEdit() && is_main()==0,value:code, attr: {title: code}" onkeydown="inc.stopEnterDefault()">
                </td>
                <td style="width:210px;">
                    <input type="text" class="el-input__inner form-control" placeholder="对方合同编号"
                           data-bind="enable:isCanEdit,value:code_out, attr: {title: code_out}" onkeydown="inc.stopEnterDefault()">
                </td>
                <td style="width:72px;">
                    <span data-bind="text:statusName"></span>
                </td>
                <td style="width:130px;">
                <span class="z-btn-action fileinput-button" data-bind="visible:isUpload" style="vertical-align: middle">
                        <span class="btn-text" data-bind="html:btnText">上传</span>
                        <input type="file" data-bind="fileUpload:true,url:$parent.postUrl,add:addFunction,done:doneFunction"/>
                    </span>
                    <a target="_blank" href="javascript: void 0" style="display: inline-block;overflow: hidden;" class="z-btn-action"  data-bind="visible:(file_url() != '' && file_url() != null),attr: seeBtnAttr">查看</a>
                </td>
                <td style="width: calc(192px - 12px)">
                    <a href="javascript: void 0" class="z-btn-action" data-bind="visible:isCanDelete,click:$parent.del">删除</a>
                    <a href="javascript: void 0" class="z-btn-action z-btn-primary" data-bind="visible:isShowSaveBtn,click:save">保存</a>
                    <a href="javascript: void 0" class="z-btn-action" data-bind="visible:isShowEditBtn,click:edit">修改</a>
                    <a href="javascript: void 0" class="z-btn-action z-btn-primary" data-bind="visible:isShowSubmitBtn,click:submit">提交</a>
                </td>
            </tr>
            <!-- /ko -->
            <tr>
                <td colspan="7" align="right">
                    <a href="javascript: void 0" class="o-btn o-btn-primary action contract-add" data-bind="click:add">新增</a>
                </td>
            </tr>
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

        self.btnText = ko.observable("上传");

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
			inc.vueConfirm({
				content: "您确定要提交当前信息吗，该操作不可逆？", onConfirm: function () {
					if (!self.isValid()) {
						self.errors.showAllMessages();
						return;
					}

					self.status(3);
					self.sendSaveSubmitAjax();
				}
            })
        }

        self.seeBtnAttr=ko.computed(function () {
            if(self.file_url() != '' && self.file_url() != null){
                return { href: self.getReadUrl, title: self.name ,style:'display: inline-block;overflow: hidden; vertical-align: middle'};
            }else{
                return { href: self.getReadUrl, title: self.name, style: 'vertical-align: middle;display: none' };
            }
        }, self);

        self.sendSaveSubmitAjax = function () {
            if (self.file_url() == '' && self.file_url() != null) {
				inc.vueAlert({content: "请上传文件！"});
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
                        inc.vueMessage("操作成功");
                    } else {
                        inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
                    inc.vueAlert({content: "操作失败：" + data.responseText});
                }
            });
        }

        self.edit = function () {
            self.action_status(1);
        }

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

            data.submit();
        }

        self.doneFunction = function (e, data) {
            if (data.result.state == 0) {
                self.file_id(data.result.data);
                self.name(data.result.extra.name);
                self.file_url(data.result.extra.file_url);
                self.status(data.result.extra.status);
                self.type(self.fileConfig().id);
                if (self.code() == "") {
                    self.action_status(1);
                } else {
                    self.action_status(0);
                }
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
            return "/contract/detail/?id=" + self.contract_id + '&t=1';
        }, self);

        self.partnerDetailUrl = ko.computed(function () {
            return "/partner/detail/?id=" + self.partner_id + '&t=1';
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
						inc.vueAlert({content: "获取合同ID出错！"});
                        return;
                    }
                },
                error: function (data) {
					inc.vueAlert({content: "获取合同ID出错！：" + data.responseText});
                    return;
                }
            });
        }

		self.del = function (file) {
			if (file.is_main() == 1 || file.status() >= 3) {
				inc.vueAlert({content: "当前数据不能删除！"});
				return;
			}
			inc.vueConfirm({content:"您确定要删除该合同上传信息吗，该操作不可逆？", type: 'warning', onConfirm:function(){
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
							inc.vueMessage({duration: 500,message: "操作成功", onClose: function () {
									self.items.remove(file);
								}});
						}
						else {
							inc.vueAlert({content: "操作失败"});
						}
					},
					error: function (data) {
						inc.vueAlert({content: "操作失败：" + data.responseText});
					}
				});
			}});
		}
    }

</script>