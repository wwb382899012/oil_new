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
                    <label for="partner_id" class="col-sm-2 control-label">企业编号</label>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <a href="/PartnerApply/detail/?id=<?php echo $data['partner_id'] ?>&t=1" target="_blank"><?php echo $data["partner_id"] ?></a>
                        </p>
                    </div>

                    <label for="partner_name" class="col-sm-2 control-label">企业名称</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">
                            <a href="/PartnerApply/detail/?id=<?php echo $data['partner_id'] ?>&t=1" target="_blank"><?php echo $data["partner_name"] ?></a>
                        </p>
                    </div>


                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">类别</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo PartnerApplyService::getPartnerType($data["type"]) ?></p>
                    </div>
                    <label for="apply_amount" class="col-sm-2 control-label">拟申请额度</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo '￥ ' . number_format($data["apply_amount"] / 1000000, 2) ?> 万元</p>
                    </div>
                    <label for="o_credit_amount" class="col-sm-2 control-label">确认额度</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo '￥ ' . number_format($data["o_credit_amount"] / 1000000, 2) ?> 万元</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="auto_level" class="col-sm-2 control-label">系统评级</label>
                    <div class="col-sm-2">
                        <p class="form-control-static">
							<?php
							if (!empty($data["auto_level"])) {
								echo $this->map['partner_level'][$data["auto_level"]];
							} else {
								echo "暂无";
							}
							?>
                        </p>
                    </div>
                    <label for="custom_level" class="col-sm-2 control-label">商务评级</label>
                    <div class="col-sm-2">
                        <p class="form-control-static">
							<?php
							if (!empty($data["custom_level"])) {
								echo $this->map['partner_level'][$data["custom_level"]];
							} else {
								echo "暂无";
							}
							?>
                        </p>
                    </div>

                    <label for="level" class="col-sm-2 control-label">风控评级</label>
                    <div class="col-sm-2">
                        <p class="form-control-static">
							<?php
							if (!empty($data["level"])) {
								echo $this->map['partner_level'][$data["level"]];
							} else {
								echo "暂无";
							}
							?>
                        </p>
                    </div>
                </div>
                <hr/>

                <div class="form-group">
                    <label for="start_time" class="col-sm-2 control-label">风控考察时间<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="start_time" name="obj[start_time]" placeholder="风控考察开始时间" data-bind="value:start_time">
                    </div>
                    <div class="col-sm-1 text-center" style="margin-top: 5px;">至</div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="end_time" name="obj[end_time]" placeholder="风控考察结束时间" data-bind="value:end_time">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">风控考察负责人<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择风控考察负责人" id="main_user_id" name="obj[main_user_id]" data-bind="value:main_user_id,valueAllowUnset:true">
                            <option value="">请选择风控考察负责人</option>
							<?php
							$users = UserService::getRiskUsers();
							foreach ($users as $v) {
								echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
							} ?>
                        </select>
                    </div>
                    <label for="type" class="col-sm-2 control-label">风控考察成员<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select multiple="" class="form-control" title="请选择风控考察成员" id="uIds" name="obj[uIds]" data-bind="selectedOptions:uIds,valueAllowUnset: true">
							<?php
							$users = UserService::getRiskUsers();
							foreach ($users as $v) {
								echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
							} ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address" class="col-sm-2 control-label">风控考察地址<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="address" name="obj[address]" placeholder="风控考察地址" data-bind="value:address">
                    </div>
                </div>
                <div class="form-group">
                    <label for="describe" class="col-sm-2 control-label">货物、资金、发票匹配情况</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="describe" name="obj[describe]" rows="3" placeholder="货物、资金、发票匹配情况" data-bind="value:describe">
                    </div>
                </div>

                <div class="form-group">
                    <label for="credit_amount" class="col-sm-2 control-label">拟授予额度</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="credit_amount" name="obj[credit_amount]" placeholder="拟授予额度" data-bind="moneyWan:credit_amount">
                            <span class="input-group-addon">万元</span>
                        </div>

                    </div>
                </div>
                <hr/>
				<?php
				$content_map = $this->map['partner_risk_content_info'];
				foreach ($content_map as $key => $row) {
					if (is_array($row)) {
						if (count($row) > 1) {
							foreach ($content_map[$key][$data['business_type']] as $k => $v) {
								?>
                                <div class="form-group">
                                    <label class="col-sm-12 control-label"
                                           style="text-align: left"><?php echo $k ?></label>
                                </div>
								<?php
								foreach ($v as $index => $d) {
									?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $d['label'] ?></label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="<?php echo $d['key'] ?>"
                                                   name="obj[content][<?php echo $d['key'] ?>]" placeholder="<?php echo $d["label"] ?>"
                                                   data-bind="value:content().<?php echo $d['key'] ?>">
                                        </div>
                                    </div>
									<?php
								}
							}
						} else {
							foreach ($row as $k => $v) {
								?>
                                <div class="form-group">
                                    <label class="col-sm-12 control-label"
                                           style="text-align: left"><?php echo $k ?></label>
                                </div>
								<?php
								foreach ($v as $index => $d) {
									?>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label"><?php echo $d['label'] ?></label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="<?php echo $d['key'] ?>"
                                                   name="obj[content][<?php echo $d['key'] ?>]" placeholder="<?php echo $d["label"] ?>"
                                                   data-bind="value:content().<?php echo $d['key'] ?>">
                                        </div>
                                    </div>
									<?php
								}
							}
						}
						?>
                        <hr/>
						<?php
					}
				}
				?>

				<?php
				if (empty($partnerRiskAttachments)) {
					$partnerRiskAttachments = PartnerRisk::getPartnerRiskAttachments($data["risk_id"]);
				}
				$attachmentTypeKey = "partner_risk_attachment_type";
				$this->showAttachmentsEditMulti($data["risk_id"], $data, $attachmentTypeKey, $partnerRiskAttachments);
				?>
                <hr/>

                <div class="form-group">
                    <label for="conclusion" class="col-sm-1 control-label">结论</label>
                    <div class="col-sm-11">
                        <textarea class="form-control" id="conclusion" name="obj[conclusion]" rows="3" placeholder="结论"
                                  data-bind="value:conclusion"></textarea>
                    </div>
                </div>
            </div><!--end box-border-->
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:tempSave,html:tempSaveBtnText"></button>
                        <button type="button" id="submitButton" class="btn btn-danger" data-bind="click:save,html:submitBtnText"></button>
                        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[partner_id]' data-bind="value:partner_id"/>
                        <input type='hidden' name='obj[risk_id]' data-bind="value:risk_id"/>
                        <input type='hidden' name='obj[level]' value="<?php echo $data['level'] ?>"/>
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

		$("#start_time").datetimepicker({format:'yyyy-mm-dd',minView:'month'});
		$("#end_time").datetimepicker({format:'yyyy-mm-dd',minView:'month'});
		$("#uIds").selectpicker();
		$("#uIds").selectpicker('val', [<?php echo $data["user_ids"] ?>]);

		$("td[data-type='2003']").find(".file-title").append(" <span class='text-red fa fa-asterisk'></span>");
		if(view.credit_amount() == "") {
			view.credit_amount(<?php echo json_encode($data["o_credit_amount"]) ?>);
        }
	});

	function ViewModel(option) {
		var defaults = {
			risk_id: "",
			partner_id: "", //合作方Id
			start_time: "", //开始时间
			end_time: "", //截止时间
			main_user_id: "", //风控负责人
			user_ids: "", //风控考察成员
			address: "", //风控考察地址
			content: "", //其他信息
			describe: "", //匹配情况
			conclusion: "", //风控结论
			credit_amount: "", //信用额度
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.risk_id = ko.observable(o.risk_id);
		self.partner_id = ko.observable(o.partner_id);
		self.start_time = ko.observable(o.start_time).extend({required: {params: true, message: "请选择风控考察开始时间"}});
		self.end_time = ko.observable(o.end_time).extend({required: {params: true, message: "请选择风控考察结束时间"}});
		self.main_user_id = ko.observable(o.main_user_id).extend({required: {params: true, message: "请选择风控考察负责人"}});
		self.uIds = ko.observable(o.user_ids).extend({required: {params: true, message: "请选择风控考察成员"}});
		self.address = ko.observable(o.address).extend({required: {params: true, message: "请填写风控考察地址"}});
		self.content = ko.observable(o.content);
		self.describe = ko.observable(o.describe);
		self.conclusion = ko.observable(o.conclusion);
		self.credit_amount = ko.observable(o.credit_amount);
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		}
		self.actionState = ko.observable(0);
		self.tempSaveBtnText = ko.observable("暂存");
		self.submitBtnText = ko.observable("提交");
		self.is_temp_save = ko.observable(0);

		//暂存
		self.tempSave = function () {
			self.is_temp_save(1);
			self.save();
		}

		self.save = function(){
           	if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			if (self.actionState() == 1) {
				return;
			}
           if (self.is_temp_save() == 1){
               self.submit();
           }else{
               layer.confirm("您确定要执行当前操作，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                   self.submit();
                   layer.close(index);
               });
           }
        }

		//提交
		self.submit = function () {
			self.actionState(1);

			var formData = $("#mainForm").serialize();
			formData = formData + "&obj[uIds]=" + self.uIds() + "&obj[credit_amount]=" + self.credit_amount();

			if (self.is_temp_save() == 1) {
				self.tempSaveBtnText("暂存中" + inc.loadingIco);
				formData = formData + "&obj[is_temp_save]=" + self.is_temp_save();
			} else {
				self.submitBtnText("提交中" + inc.loadingIco);
			}
			$.ajax({
				type: "POST",
				url: "/partnerRisk/save",
				data: formData,
				dataType: "json",
				success: function (json) {
					self.actionState(0);
					self.tempSaveBtnText("暂存");
					self.submitBtnText("提交");
					if (json.state == 0) {
						inc.showNotice("操作成功");
						if (self.is_temp_save() == 1) {
							location.href = "/partnerRisk/detail/?partner_id=" + self.partner_id();
						} else {
							location.href = "/partnerRisk/";
						}
					} else {
						layer.alert(json.data, {icon: 5});
					}
					self.is_temp_save(0);
				},
				error: function (data) {
					self.actionState(0);
					self.tempSaveBtnText("暂存");
					self.submitBtnText("提交");
					layer.alert("保存失败！" + data.responseText, {icon: 5});
				}
			});
		}

		self.back = function () {
			history.back();
		}
	}
</script>
