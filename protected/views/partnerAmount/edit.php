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
                    <label for="name" class="col-sm-2 control-label">企业名称（全称）<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="obj[name]" placeholder="企业名称" data-bind="value:company().name">
                    </div>
                    <!-- <div class="col-sm-1">
                        <button type="button" id="retrieveButton" class="btn btn-primary" data-bind="click:retrieve,html:retrieveBtnText">检索</button>
                    </div> -->
                    <!--<div class="col-sm-1">
                        <a href="http://www.qichacha.com/" title="">企查查详情</a>
                    </div>-->
                </div>
                <div class="form-group">
                    <label for="credit_code" class="col-sm-2 control-label">统一社会信用代码</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="credit_code" name="obj[credit_code]" placeholder="统一社会信用代码" data-bind="value:company().credit_code, disable: isParamsNonEdit">
                    </div>
                </div>
                <div class="form-group">
                    <label for="registration_code" class="col-sm-2 control-label">工商注册号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="registration_code" name="obj[registration_code]" placeholder="工商注册号" data-bind="value:company().registration_code, disable: isParamsNonEdit">
                    </div>
                </div>
                <div class="form-group">
                    <label for="corporate" class="col-sm-2 control-label">法定代表人</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="corporate" name="obj[corporate]" placeholder="法定代表人" data-bind="value:company().corporate">
                    </div>
                </div>
                <div class="form-group">
                    <label for="start_date" class="col-sm-2 control-label">成立日期</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="start_date" name="obj[start_date]" placeholder="成立日期" data-bind="value:company().start_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address" class="col-sm-2 control-label">注册地址</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="address" name="obj[address]" placeholder="注册地址" data-bind="value:company().address">
                    </div>
                </div>
                <div class="form-group">
                    <label for="registration_authority" class="col-sm-2 control-label">登记机关</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="registration_authority" name="obj[registration_authority]" placeholder="登记机关" data-bind="value:company().registration_authority">
                    </div>
                </div>
                <div class="form-group">
                    <label for="registered_capital" class="col-sm-2 control-label">注册资本（万元）</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="registered_capital" name="obj[registered_capital]" placeholder="注册资本（万元）" data-bind="value:company().registered_capital">
                    </div>
                    <label for="paid_up_capital" class="col-sm-2 control-label">实收（万元）</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="paid_up_capital" name="obj[paid_up_capital]" placeholder="实收资本" data-bind="value:company().paid_up_capital">
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_scope" class="col-sm-2 control-label">经营范围</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="business_scope" name="obj[business_scope]" rows="3" placeholder="经营范围" data-bind="value:company().business_scope"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="ownership" class="col-sm-2 control-label">企业所有制</label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择企业所有制" id="ownership" name="obj[ownership]" data-bind="options:ownerships,optionsText:'name',optionsCaption: '请选择企业所有制',value: company().ownership, optionsValue:'id',valueAllowUnset: true">
                            <!--<option value=''>请选择企业所有制</option>
							--><?php
			                /*$ownerships = Ownership::getOwnerships();
							  foreach ($ownerships as $v) {
							  	echo "<option value='" . $v["id"] . "'>" . $v["name"] . "</option>";
							  } */?>
                        </select>
                    </div>
                    <label for="runs_state" class="col-sm-2 control-label">经营状态</label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择经营状态" id="runs_state" name="obj[runs_state]" data-bind="optionsCaption: '请选择经营状态',value: company().runs_state,valueAllowUnset: true">
                            <option value=''>请选择经营状态</option>
							<?php foreach ($this->map["runs_state"] as $k => $v) {
								echo "<option value='" . $k . "'>" . $v . "</option>";
							} ?>
                        </select>
                    </div>
                </div>

                <hr/>
                <div class="form-group">
                    <label for="is_stock" class="col-sm-2 control-label">是否上市</label>
                    <div class="col-sm-4 checkbox">
                        &emsp;&nbsp;<input type="checkbox" id="is_stock" name="obj[is_stock]" data-bind="checked:company().is_stock" style="margin-right:10px;">已上市
                    </div>
                </div>
                <div class="form-group">
                    <label for="stock_code" class="col-sm-2 control-label">上市编号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="stock_code" name="obj[stock_code]" placeholder="上市编号" data-bind="value:company().stock_code">
                    </div>
                </div>
                <div class="form-group">
                    <label for="stock_name" class="col-sm-2 control-label">上市名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="stock_name" name="obj[stock_name]" placeholder="上市名称" data-bind="value:company().stock_name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="stock_type" class="col-sm-2 control-label">上市板块</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="stock_type" name="obj[stock_type]" placeholder="上市板块" data-bind="value:company().stock_type">
                    </div>
                </div>

                <hr/>
                <div class="form-group">
                    <label for="contact_person" class="col-sm-2 control-label">客户联系人 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="contact_person" name="obj[contact_person]" placeholder="客户联系人" data-bind="value:company().contact_person">
                    </div>
                    <label for="contact_phone" class="col-sm-2 control-label">联系方式 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="contact_phone" name="obj[contact_phone]" placeholder="联系方式" data-bind="value:company().contact_phone">
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_type" class="col-sm-2 control-label">企业类型 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <select class="form-control" title="请选择企业类型" id="business_type" name="obj[business_type]" data-bind="optionsCaption: '请选择企业类型',value: company().business_type,valueAllowUnset: true">
							<?php foreach ($this->map["business_type"] as $k => $v) {
								echo "<option value='" . $k . "'>" . $v . "</option>";
							} ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="product" class="col-sm-2 control-label">
                        <span data-bind="visible:company().business_type() == 1">生产产品</span>
                        <span data-bind="visible:company().business_type() == 2">主营产品</span>
                    </label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="product" name="obj[product]" placeholder=""
                               data-bind="value:company().product">
                    </div>
                    <label for="equipment" class="col-sm-2 control-label">
                        <span data-bind="visible:company().business_type() == 1">生产装置</span>
                        <span data-bind="visible:company().business_type() == 2">贸易规模</span>
                    </label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="equipment" name="obj[equipment]" placeholder=""
                               data-bind="value:company().equipment">
                    </div>
                    <label for="production_scale" class="col-sm-2 control-label">
                        <span data-bind="visible:company().business_type() == 1">生产规模</span>
                        <span data-bind="visible:company().business_type() == 2">行业口碑</span>
                    </label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="production_scale" name="obj[production_scale]" placeholder="" data-bind="value:company().production_scale">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">类别 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择类别" id="type" name="obj[type]" data-bind="optionsCaption: '请选择类别',value:type,valueAllowUnset: true">
							<?php
							foreach ($this->map["partner_type"] as $k => $v) {
								echo "<option value='" . $k . "'>" . $v . "</option>";
							}
							?>
                        </select>
                    </div>
                    <label for="user_id" class="col-sm-2 control-label">业务员 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择业务员" id="user_id" name="obj[user_id]" data-bind="optionsCaption: '请选择业务员',value:user_id,valueAllowUnset: true">
                            <option value="0">请选择业务员</option>
                            <?php
                            $users = UserService::getBusinessDirectors();
                            foreach ($users as $v) {
                                echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="trade_info" class="col-sm-2 control-label">历史合作情况 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="trade_info" name="obj[trade_info]" placeholder="历史合作情况" data-bind="value:trade_info">
                    </div>
                </div>
                <div class="form-group">
                    <label for="category" class="col-sm-2 control-label">拟合作产品 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <select multiple="" class="form-control" title="请选择拟合作产品" id="gIds" name="obj[gIds]" data-bind="selectedOptions:gIds,valueAllowUnset: true">
							<?php
							$goods = GoodsService::getAllActiveGoods();
							foreach ($goods as $v) {
								echo "<option value='" . $v["goods_id"] . "'>" . $v["name"] . "</option>";
							} ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="bank_name" class="col-sm-2 control-label">银行名称</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="bank_name" name="obj[bank_name]" placeholder="银行名称" data-bind="value:bank_name">
                    </div>
                    <label for="bank_account" class="col-sm-2 control-label">银行账号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="bank_account" name="obj[bank_account]" placeholder="银行账号" data-bind="value:bank_account">
                    </div>
                </div>
                <div class="form-group">
                    <label for="tax_code" class="col-sm-2 control-label">纳税识别号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="tax_code" name="obj[tax_code]" placeholder="纳税识别号" data-bind="value:company().tax_code">
                    </div>
                    <label for="phone" class="col-sm-2 control-label">电话</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="phone" name="obj[phone]" placeholder="联系电话" data-bind="value:phone">
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name="obj[remark]" rows="3" placeholder="备注" data-bind="value:company().remark"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="custom_level" class="col-sm-2 control-label">商务强制分类</label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择商务强制分类" id="custom_level" name="obj[custom_level]" data-bind="optionsCaption: '请选择商务强制分类',value:custom_level,enable: white_level()==0">
                            <option value="">系统默认</option>
							<?php
							foreach ($this->map["partner_level"] as $k => $v) {
								echo "<option value='" . $k . "'>" . $v . "</option>";
							}
							?>
                        </select>
                    </div>
                    <label for="custom_level" class="col-sm-2 control-label">系统检测分类</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="visible: system_level() > 0" ><span class="text-red" data-bind="text: system_level_desc"></span></p>
                    </div>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="visible: system_level() <= 0" ><span class="text-red"><?php echo $this->map['partner_level'][$data['auto_level']] ?></span></p>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">拟申请额度 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="apply_amount" name="obj[apply_amount]" placeholder="拟申请额度" data-bind="value:apply_amount">
                    </div>
                    <label for="type" class="col-sm-2 control-label">原常规额度</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">￥ <?php echo number_format($data['credit_amount']/100,2) ?></p>
                    </div>
                </div>
            </div><!--end box-border-->
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="checkButton" class="btn btn-primary" data-bind="click:checkLevel,html:checkBtnText,enable: white_level()==0">检查</button>
                        <button type="button" id="tempSaveButton" class="btn btn-primary" data-bind="click:tempSave,html:tempSaveBtnText">暂存</button>
                        <button type="button" id="saveButton" class="btn btn-danger" data-bind="click:pass,html:saveBtnText,visible:isShowSave">提交</button>
                        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[partner_id]' data-bind="value:partner_id"/>
                        <input type='hidden' name='obj[is_temp_save]' data-bind="value:is_temp_save"/>
                    </div>
                </div>
            </div>
        </form>
    </div><!--end box box-primary-->

    <!-- partner retrieve modal -->
    <div class="modal fade draggable-modal" id="partnerModel" tabindex="-1" role="dialog" aria-labelledby="modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="partnerRetrieve">自动检索企业信息</h4>
                </div>
                <div class="modal-body">
                    <div class="box box-primary">
                        <div class="box-body">
                            <form class="search-form">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-addon">企业名称</div>
                                                <input type="text" class="form-control input-sm" name="name" id="search"
                                                       placeholder="企业名称" value=""
                                                       data-bind="textInput:companyKeyWord"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table id="companies" class="table table-condensed table-hover table-bordered table-layout">
                                <thead>
                                <tr>
                                    <th style='text-align:center;'>企业名称</th>
                                    <th style='width: 10%; text-align:center'>法人</th>
                                    <th style='width: 15%; text-align:center;'>成立日期</th>
                                    <th style='width: 10%; text-align:center;'>经营状态</th>
                                    <th style='width: 10%; text-align:center;'>操作</th>
                                </tr>
                                </thead>

                                <tbody id="partnerBody" data-bind="foreach: companies">
                                <tr class="item">
                                    <td style='text-align:left;' data-bind="text:name"></td>
                                    <td style='text-align:center' data-bind="text:corporate"></td>
                                    <td style='text-align:center;' data-bind="text:start_date"></td>
                                    <td style='text-align:center;' data-bind="text:runs_state"></td>
                                    <td style='text-align:center;'><a data-bind="click:function(){$parent.select($index());}">选择</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</section><!--end content-->

<script>
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
		ko.applyBindings(view);
        view.isCanEditName(<?php echo $isCanEditName ?>);
		view.ownerships(<?php echo json_encode(Ownership::getOwnerships()) ?>);
		$("#gIds").selectpicker();
		$("#gIds").selectpicker('val', [<?php echo $data["goods_ids"] ?>]);
		$("#start_date").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});
		$("#bank_account").bankInput({min: 1, max: 50, deimiter: ' '});
	});
	function ViewModel(option) {
		var defaults = {
			partner_id: 0,
			type: 0,//类型
			apply_amount: "",//拟申请额度
			user_id: "0",//业务员
			trade_info: "",//历史合作情况
			goods_ids: "",//拟合作产品
			bank_name: "",//银行名称
			bank_account: "",//银行账号
			phone: "",//电话
			custom_level: "",//商务强制分类
			is_temp_save: 0, //是否暂存
            apply_amount: '',
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.partner_id = ko.observable(o.partner_id);
		self.type=ko.observable(o.type).extend({custom: {
            params: function (v) {
                if (v > -1) {
                    return true;
                }
                else
                    return false;
            },
            message: "请选择类别"
        }
        });
		self.apply_amount = ko.observable(o.apply_amount).extend({money:true,required:true});
		self.user_id = ko.observable(o.user_id).extend({custom: {
            params: function (v) {
                if (v >0 ) {
                    return true;
                }
                else
                    return false;
            },
            message: "请选择业务员"
        }
        });
		self.trade_info = ko.observable(o.trade_info).extend({required: true});
        self.gIds=ko.observable(o.goods_ids).extend({required:{params:true,message:"请选择拟合作产品"}});
		self.bank_name = ko.observable(o.bank_name);
		self.bank_account = ko.observable(o.bank_account);
		self.phone = ko.observable(o.phone);
		self.custom_level = ko.observable(o.custom_level);
        self.auto_level = ko.observable(o.auto_level);
		self.is_temp_save = ko.observable(o.is_temp_save);

		self.system_level = ko.observable(0);
		self.system_level_desc = ko.observable('');
		self.white_level = ko.observable(0);
		self.company = ko.observable(new CompanyModel(option));
		self.companies = ko.observableArray();
		self.companyKeyWord = ko.observable();
		self.actionState = ko.observable(0);
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return (self.company().isValid() && self.errors().length === 0);
		}
        self.isCanEditName = ko.observable(1);
		self.isParamsNonEdit = ko.computed(function () {
			return self.isCanEditName()!=1;
		}, self);
		self.ownerships = ko.observableArray();
		self.retrieveBtnText = ko.observable("检索");
		self.saveBtnText = ko.observable("提交");
		self.tempSaveBtnText = ko.observable("暂存");
		self.checkBtnText = ko.observable("检查");

        self.attachments = ko.observable(<?php echo json_encode(PartnerApplyService::getAttachment($data['partner_id'])) ?>);
        // console.log( Object.keys(self.attachments()).length);
        self.isShowSave = ko.computed(function () {
            if(Object.keys(self.attachments()).length == 0 && (self.custom_level() == 2 || self.custom_level() == 3)) {
            	return 0;
            } else {
            	return 1;
            }
		}, self);

		//企业信息检索
		self.retrieve = function () {
			self.company().name.isModified(true);
			if (!self.company().name.isValid())
				return;
			if (self.actionState() == 1)
				return;

			self.actionState(1);
			self.retrieveBtnText("检索" + inc.loadingIco);
			$.ajax({
				type: "GET",
				url: "/partnerAmount/getCompanies",
				data: {name: self.company().name()},
				dataType: "json",
				success: function (json) {
					self.actionState(0);
					self.retrieveBtnText("检索");
					if (json.state == 0) {
						self.getOwnerships();
						if (json.data.length > 1) {
							self.companies(json.data);
							$("#partnerModel").modal({
								backdrop: true,
								keyboard: false,
								show: true
							});
						}
						else {
//							self.setCompany(new CompanyModel(json.data[0]));
							if (json.data.length == 1) {
								self.setCompany(json.data[0]);
							} else {
								alertModel("企业信息不存在！");
							}
						}
					}
					else {
						alertModel(json.data);
					}
				},
				error: function (data) {
					self.actionState(0);
					self.retrieveBtnText("检索");
					alertModel("检索失败：" + data.responseText);
				}
			});
		}

		//合作方选择
		self.select = function (index) {
			if (index >= self.companies().length || index < 0)
				alertModel("选择有误，请重新选择");

			$("#partnerModel").modal("hide");
			self.setCompany(new CompanyModel(self.companies()[index]));
		}

		self.setCompany = function (company) {
			if (company.hasOwnProperty("is_stock") && company.is_stock == "0") {
				delete company.is_stock;
			}
			if (company.hasOwnProperty("business_type") && company.business_type == "0") {
				company.business_type = 1;
			}
			if (company.hasOwnProperty("contact_person") && company.contact_person == null) {
				delete company.contact_person;
			}
			if (company.hasOwnProperty("contact_phone") && company.contact_phone == null) {
				delete company.contact_phone;
			}

			ko.setObservablesValue(self.company(), company);
			self.checkPartnerInWhite();
		}

		//检查是否在白名单中
        self.checkPartnerInWhite = function () {
			$.ajax({
				type: "GET",
				url: "/partnerAmount/checkInWhite",
				data: {name: self.company().name()},
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						if(json.data !== null) {
							self.white_level(json.data.level);
							if(self.white_level() > 0) {
								self.custom_level(self.white_level());
							}
						}
					}
					else {
						alertModel(json.data);
					}
				},
				error: function (data) {
					alertModel("检查是否在白名单中失败：" + data.responseText);
				}
			});
		}

		//暂存
		self.tempSave = function () {
			self.is_temp_save(1);
			self.save();
		}
        self.pass = function()
        {
            // console.log(self.apply_amount());
            // console.log(<?php echo $data['apply_amount'] ?>);
            if(self.apply_amount() == <?php echo $data['apply_amount'] ?>){
                alertModel('请调整拟申请额度！');
                return;
            }
            if(confirm("您确定要提交当前额度调整信息，该操作不可逆？")){
                self.save();
            }
        }

		//保存
		self.save = function () {
			if (!self.isValid()) {
				// console.log(self.company().errors.showAllMessages());
                // self.company().errors.showAllMessages();
                // console.log(self.company());
                self.company().errors.showAllMessages();
				self.errors.showAllMessages();
				return;
			}
			if (self.actionState() == 1)
				return;

            
			if (self.is_temp_save() == 1) { //执行暂存操作
				self.tempSaveBtnText("暂存中" + inc.loadingIco);
			} else {
                self.saveBtnText("提交中" + inc.loadingIco);
			}

            if(self.system_level()  <= 0 && self.custom_level() <= 0){
                self.checkLevel();
            }

			self.actionState(1);
			var formData=$("#mainForm").serialize()+"&obj[auto_level]="+self.system_level()+"&obj[gIds]="+self.gIds();
			$.ajax({
				type: "POST",
				url: "/partnerAmount/save",
				data: formData,
				dataType: "json",
				success: function (json) {
					self.actionState(0);
					self.saveBtnText("提交");
					self.tempSaveBtnText("暂存");
					if (json.state == 0) {
						self.company().partner_id(json.data);
						if(self.is_temp_save() == 0) { //保存后跳转，暂存不需跳转
							if (document.referrer)
								location.href = document.referrer;
							else
								location.href = "/partnerAmount/detail/?id=<?php echo $data['partner_id'] ?>";
                        }
					}
					else {
						alertModel(json.data);
					}
					self.is_temp_save(0);
				},
				error: function (data) {
					self.actionState(0);
					self.is_temp_save(0);
					self.saveBtnText("提交");
					self.tempSaveBtnText("暂存");
					alertModel("保存失败：" + data.responseText);
				}
			});
		}

		//检查
		self.checkLevel = function () {
			if (!self.isValid()) {
				self.company().errors.showAllMessages();
				self.errors.showAllMessages();
				return;
			}
			if (self.actionState() == 1)
				return;
			self.actionState(1);
			self.checkBtnText("检查" + inc.loadingIco);
			var formData=$("#mainForm").serialize()+"&obj[goods_ids]="+self.gIds();
			$.ajax({
				type: "POST",
				url: "/partnerAmount/checkLevel",
				data: formData,
				dataType: "json",
				success: function (json) {
					self.actionState(0);
					self.checkBtnText("检查");
					if (json.state == 0) {
						self.system_level(json.data['system_level']);
						self.system_level_desc(json.data['level_desc']);
					}
					else {
						alertModel(json.data);
					}
				},
				error: function (data) {
					self.actionState(0);
					self.checkBtnText("检查");
					alertModel("检查失败：" + data.responseText);
				}
			});
		}

		//下一步
        /*self.showAttachUpload = function () {
			location.href = "/partnerAmount/attachments/?partner_id="+self.partner_id();
		}*/

		//返回
		self.back = function () {
			history.back();
		}

		self.companySearch = function () {
			var trs = $("#companies > tbody > tr.item");
			trs.each(function (index, row) {
				var found = false;
				var allCells = $(this).children('td').each(function () {
					var regExp = new RegExp(self.companyKeyWord(), 'i');
					if (regExp.test($(this).text())) {
						found = true;
						return false;
					}
				});
				if (found) $(this).show(); else $(this).hide();
			});
		}

		self.companyKeyWord.subscribe(function (v) {
			self.companySearch();
		});

		self.getOwnerships = function () {
			$.ajax({
				type: "GET",
				url: "/partnerApply/getOwnerships",
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						if (json.data !== null) {
							self.ownerships(json.data);
						}
					}
					else {
						alertModel(json.data);
					}
				},
				error: function (data) {
					alertModel("获取企业所有制失败：" + data.responseText);
				}
			});
		}
	}

	function CompanyModel(option) {
		var defaults = {
			partner_id: 0,
			name: "",//企业名称
			credit_code: "",//统一社会信用代码
			registration_code: "",//工商注册号
			corporate: "",//法人代表
			start_date: "",//成立日期
			address: "",//注册地址
			registration_authority: "",//登记机关
			registered_capital: "",//注册资本
			paid_up_capital: "",//实收资本
			business_scope: "",//经营范围
			ownership: "",//企业所有制
			runs_state: "1",//经营状态
			is_stock: "",//是否上市
			stock_code: "",//上市编号
			stock_name: "",//上市名称
			stock_type: "",//上市板块
			contact_person: "",//客户联系人
			contact_phone: "",//联系方式
			business_type: "1",//企业类型
			product: "",//生成产品
			equipment: "",//生产装置
			production_scale: "",//生产规模
            status: 0, //状态
			tax_code: "",//纳税识别号
			remark: "",//备注
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.partner_id = ko.observable(o.partner_id);
		self.name = ko.observable(o.name).extend({required: true});
		self.credit_code = ko.observable(o.credit_code)
		self.registration_code = ko.observable(o.registration_code)
		self.corporate = ko.observable(o.corporate);
		self.start_date = ko.observable(o.start_date).extend({date: true});
		self.address = ko.observable(o.address);
		self.registration_authority = ko.observable(o.registration_authority);
		self.registered_capital = ko.observable(o.registered_capital);
		self.paid_up_capital = ko.observable(o.paid_up_capital);
		self.business_scope = ko.observable(o.business_scope);
		self.ownership = ko.observable(o.ownership);
		self.runs_state = ko.observable(o.runs_state);
		self.is_stock = ko.observable(o.is_stock);
		self.stock_code = ko.observable(o.stock_code);
		self.stock_name = ko.observable(o.stock_name);
		self.stock_type = ko.observable(o.stock_type);
		self.contact_person = ko.observable(o.contact_person).extend({required: true});
		self.contact_phone = ko.observable(o.contact_phone).extend({required: true, phone: true});
		self.business_type = ko.observable(o.business_type).extend({required: true});
		self.product = ko.observable(o.product);
		self.equipment = ko.observable(o.equipment);
		self.production_scale = ko.observable(o.production_scale);
		self.status = ko.observable(o.status);
		self.tax_code = ko.observable(o.tax_code);
		self.remark = ko.observable(o.remark);
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		}
	}
</script>
