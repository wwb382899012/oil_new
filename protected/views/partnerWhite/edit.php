<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="editForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">企业名称（全称）<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="name" name="data[name]" placeholder="企业名称" data-bind="value:name" <?php if (!empty($data['id'])) { ?> disabled <?php } ?>>
                    </div>
                    <div class="col-sm-4">
                        <button type="button" id="retrieveButton" class="btn btn-primary" data-bind="click:retrieve,html:retrieveBtnText">检索</button>
		                <?php
		                $keyNo = PartnerService::getKeyNo($data['name']);
		                if (!empty($keyNo)) {
			                echo '&emsp;&emsp;<a href="http://www.qichacha.com/firm_' . $keyNo . '.shtml" target="_blank" class="btn btn-warning">点击跳转到企查查详情页</a>';
		                } else {
			                ?>
                            <span data-bind="html:gotoQiChaChaText"></span>
			                <?php
		                }
		                ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="corporate" class="col-sm-2 control-label">法定代表人</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="corporate" name="data[corporate]" placeholder="法定代表人" data-bind="value:corporate" <?php if (!empty($data['id'])) { ?> disabled <?php } ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="registered_capital" class="col-sm-2 control-label">注册资本（万元）</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="registered_capital" name="data[registered_capital]" placeholder="注册资本" data-bind="value:registered_capital" <?php if (!empty($data['id'])) { ?> disabled <?php } ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="start_date" class="col-sm-2 control-label">成立日期</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="start_date" name="data[start_date]" placeholder="成立日期" data-bind="value:start_date" <?php if (!empty($data['id'])) { ?> disabled <?php } ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="ownership" class="col-sm-2 control-label">企业所有制</label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择企业所有制" id="ownership" name="data[ownership]" data-bind="options:ownerships,optionsText:'name',optionsCaption: '请选择企业所有制',value: ownership, optionsValue:'id',valueAllowUnset: true" <?php if (!empty($data['id'])) { ?> disabled <?php } ?>>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">企业分级</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="level" name="data[level]" data-bind="value:level">
							<?php foreach ($this->map["partner_level"] as $k => $v) {
								echo "<option value='" . $k . "'>" . $v . "</option>";
							} ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">状态</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="status" name="data[status]" data-bind="value:status">
							<?php foreach ($this->map["partner_white_status"] as $k => $v) {
								echo "<option value='" . $k . "'>" . $v . "</option>";
							} ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">保存</button>
                        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                        <input type="hidden" name="data[id]" data-bind="value:id"/>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- start partner retrieve modal -->
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
                                    <td style='text-align:center;'><a
                                                data-bind="click:function(){$parent.select($index());}">选择</a></td>
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
    <!-- end partner retrieve modal -->
</section>

<script>
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
		ko.applyBindings(view);
		view.ownerships(<?php echo json_encode(Ownership::getOwnerships()) ?>);
		$("#start_date").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});
	});

	function ViewModel(option) {
		var defaults = {
			id: 0,
			name: "",
			corporate: "",
			registered_capital: "",
			start_date: "",
			ownership: 0,
			level: 1,
			status: 1
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.id = ko.observable(o.id);
		self.name = ko.observable(o.name).extend({required: true, maxLength: 128});
		self.corporate = ko.observable(o.corporate);
		self.registered_capital = ko.observable(o.registered_capital);
		self.start_date = ko.observable(o.start_date).extend({date: true});
		self.ownership = ko.observable(o.ownership);
		self.level = ko.observable(o.level);
		self.status = ko.observable(o.status);
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		}

		self.companies = ko.observableArray();
		self.companyKeyWord = ko.observable();
		self.retrieveBtnText = ko.observable("检索");
		self.actionState = ko.observable(0);
		self.ownerships = ko.observableArray();

		self.gotoQiChaChaText = ko.observable("");
		//检索
		self.retrieve = function () {
			self.name.isModified(true);
			if (!self.name.isValid()) {
				return;
			}
			if (self.actionState() == 1) {
				return;
			}

			self.actionState(1);
			self.retrieveBtnText("检索中" + inc.loadingIco);
			$.ajax({
				type: "GET",
				url: "/partnerApply/getCompanies",
				data: {name: self.name()},
				dataType: "json",
				success: function (json) {
					self.actionState(0);
					self.retrieveBtnText("检索");
					if (json.state == 0) {
						self.ownerships(json.data.ownerships);

						if (json.data.partnerInfo.length > 1) {
							self.companies(json.data.partnerInfo);
							$("#partnerModel").modal({
								backdrop: true,
								keyboard: false,
								show: true
							});
						} else {
							if (json.data.partnerInfo.length == 1) {
								self.setPartnerParams(json.data.partnerInfo[0]);
                                self.gotoQiChaCha();
							} else {
								self.gotoQiChaChaText("");
								alertModel("企业信息不存在！");
								self.corporate('');
								self.registered_capital('');
								self.start_date('');
								self.ownership('');
							}
						}
					} else {
						alert(json.data);
					}
				},
				error: function (data) {
					self.actionState(0);
					self.retrieveBtnText("检索");
					alert("检索失败：" + data.responseText);
				}
			})
		}

		self.gotoQiChaCha = function () {
			self.name.isModified(true);
			if (!self.name.isValid()) {
				return;
			}
			$.ajax({
				type: "GET",
				url: "/partnerApply/getKeyNo",
				data: {name: self.name()},
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						if (json.data != "") {
							if ($("#qiChachaLink").length == 0) {
								self.gotoQiChaChaText('&emsp;&emsp;<a href="http://www.qichacha.com/firm_' + json.data + '.shtml" target="_blank" class="btn btn-warning">点击跳转到企查查详情页</a>');
							}
						}
					}
					else {
						alertModel(json.data);
					}
				},
				error: function (data) {
					alertModel("获取企业KeyNo失败：" + data.responseText);
				}
			});
		}

		//企业名搜索
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
		self.companyKeyWord.subscribe(function () {
			self.companySearch();
		});

		//选择
		self.select = function (index) {
			if (index >= self.companies().length || index < 0) {
				alert("选择有误，请重新选择！");
			}

			$("#partnerModel").modal("hide");
			self.setPartnerParams(self.companies()[index]);
			self.gotoQiChaCha();
		}

		self.setPartnerParams = function (company) {
			self.name(company['name']);
			self.corporate(company['corporate']);
			self.registered_capital(company['registered_capital']);
			self.start_date(company['start_date']);
			self.ownership(company['ownership']);
		}

		//保存
		self.buttonText = ko.observable("保存");
		self.save = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			self.buttonText("保存中" + inc.loadingIco);
			var formData = $("#editForm").serialize();
			$.ajax({
				type: "POST",
				url: "/partnerWhite/save",
				data: formData,
				dataType: "json",
				success: function (json) {
					self.buttonText ("保存");
					if (json.state == 0) {
						if (document.referrer) {
							location.href = document.referrer;
						} else {
							location.href = "/partnerWhite/";
						}
					} else {
						alert(json.data);
					}
				},
				error: function (data) {
					self.buttonText("保存");
					alert("保存失败！" + data.responseText);
				}
			});
		}

		self.back = function () {
			history.back();
		}
	}
</script>