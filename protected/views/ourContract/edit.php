<?php if(!empty($contractDetailFile)) { include $contractDetailFile;} ?>
<section class="content">
    <div class="box box-primary">
        <!-- <div class="box-header with-border">
            <h3 class="box-title">请在下面操作</h3>
            <div class="pull-right box-tools">
                <?php if ($this->checkIsCanEdit($data["status"])) { ?>
                    <button type="button" id="saveButton" class="btn btn-danger" placeholder="提交" data-bind="click:pass,text:buttonText">提交</button>
                <?php } ?>
                <?php if (!$this->isExternal) { ?>
                    <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                <?php } ?>
            </div>
        </div> -->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <!-- <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">项目编号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_id"] ?></a>
                        </p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">上游合作方</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/partner/detail/?id=<?php echo $data["up_partner_id"] ?>&t=1" target="_blank"><?php echo $data["up_name"] ?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">项目名称</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_name"] ?></a>
                        </p>
                    </div>
                
                    <label class="col-sm-2 control-label">下游合作方</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/partner/detail/?id=<?php echo $data["down_partner_id"] ?>&t=1" target="_blank"><?php echo $data["down_name"] ?></a>
                        </p>
                    </div>
                </div> -->
                <div class="form-group ">
                    <label for="type" class="col-sm-2 control-label"><h4>上游放款条件</h4></label>
                    <div class="col-sm-4">
                    </div>
                    <label for="type" class="col-sm-2 control-label"><h4>下游票款顺序</h4></label>
                </div>
                <div class="form-group ">
                    <label for="type" class="col-sm-2 control-label">下游收货确认书 <span class="text-red fa fa-asterisk"></span></label>
                    <!--<div class="col-sm-4">
                        <select class="form-control" id="is_down_receive" name="obj[is_down_receive]" data-bind="value:is_down_receive,valueAllowUnset: true">
                            <option value=''>请选择</option>
                            <?php /*foreach($this->map["confirmation_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }*/ ?>
                        </select>
                    </div>-->
                    <div class="col-sm-4 checkItem">
						<?php for ($i=count($this->map["confirmation_type"])-1; $i>=0; $i--) {
						    $key = array_search($this->map["confirmation_type"][$i],$this->map["confirmation_type"]);
						    ?>
                            <button class="btn btn-sm" data-bind="
                            css:{<?php
							if ($key == 1) {
								echo "'btn-success'";
							} else {
								if ($key == 0) {
									echo "'btn-danger'";
								}
							} ?>:is_down_receive()==<?php echo $key ?>,'btn-default':is_down_receive()!=<?php echo $key ?>},
                            click:<?php echo "function(){isDownReceive(" . $key . ")}"; ?>" style="width:50px"><?php echo $this->map["confirmation_type"][$i] ?></button>&emsp;
						<?php } ?>
                        <input type="hidden" class="form-control" data-bind="value:is_down_receive,attr: { name: 'obj[is_down_receive]'}">
                    </div>
                    <label for="type" class="col-sm-2 control-label">下游票款 <span class="text-red fa fa-asterisk"></span></label>
                    <!--<div class="col-sm-4">
                        <select class="form-control" id="invoice_type" name="obj[invoice_type]" data-bind="value:invoice_type,valueAllowUnset: true">
                            <option value=''>请选择</option>
                            <?php /*foreach($this->map["down_pay_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }*/ ?>
                        </select>
                    </div>-->
                    <div class="col-sm-4 checkItem">
						<?php foreach ($this->map["down_pay_type"] as $k => $v) { ?>
                            <button class="btn" data-bind="
                            css:{<?php
							if ($k == 2) {
								echo "'btn-success'";
							} else {
								if ($k == 1) {
									echo "'btn-danger'";
								}
							} ?>:invoice_type()==<?php echo $k ?>,'btn-default':invoice_type()!=<?php echo $k ?>},
                            click:<?php echo "function(){isInvoiceType(" . $k . ")}";?>" style="width:100px"><?php echo $v ?></button>&emsp;
						<?php } ?>
                        <input type="hidden" class="form-control" data-bind="value:invoice_type,attr: { name: 'obj[invoice_type]'}">
                    </div>
                </div>
                <div class="form-group ">
                    <label for="type" class="col-sm-2 control-label">下游保证金 <span class="text-red fa fa-asterisk"></span></label>
                    <!--<div class="col-sm-4">
                        <select class="form-control" id="is_down_first" name="obj[is_down_first]" data-bind="value:is_down_first" disabled>
                            <option value=''>请选择</option>
                            <?php /*foreach($this->map["first_pay_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }*/ ?>
                        </select>
                    </div>-->
                    <div class="col-sm-4 checkItem">
						<?php for($i=count($this->map["first_pay_type"])-1; $i>=0; $i--) {
                            $key = array_search($this->map["first_pay_type"][$i],$this->map["first_pay_type"]);
						    ?>
                            <button class="btn btn-sm" data-bind="
                            css:{<?php
							if ($key == 1) {
								echo "'btn-success'";
							} else {
								if ($key == 0) {
									echo "'btn-danger'";
								}
							} ?>:is_down_first()==<?php echo $key ?>,'btn-default':is_down_first()!=<?php echo $key ?>},
                            click:<?php echo "function(){isDownFirst(" . $key . ")}";?>" disabled style="width:50px"><?php echo $this->map["first_pay_type"][$i] ?></button>&emsp;
						<?php } ?>
                        <input type="hidden" class="form-control" data-bind="value:is_down_first,attr: { name: 'obj[is_down_first]'}">
                    </div>
                </div>
                <div class="form-group ">
                    <label for="type" class="col-sm-2 control-label">合同双签 <span class="text-red fa fa-asterisk"></span></label>
                    <!--<div class="col-sm-4">
                        <select class="form-control" id="is_contract" name="obj[is_contract]" data-bind="value:is_contract,valueAllowUnset: true">
                            <option value=''>请选择</option>
                            <?php /*foreach($this->map["contract_stamp_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }*/ ?>
                        </select>
                    </div>-->
                    <div class="col-sm-4 checkItem">
						<?php for ($i=count($this->map["contract_stamp_type"])-1; $i>=0; $i--) {
						    $key = array_search($this->map["contract_stamp_type"][$i],$this->map["contract_stamp_type"]);
						    ?>
                            <button class="btn btn-sm" data-bind="
                            css:{<?php
							if ($key == 1) {
								echo "'btn-success'";
							} else {
								if ($key == 0) {
									echo "'btn-danger'";
								}
							} ?>:is_contract()==<?php echo $key ?>,'btn-default':is_contract()!=<?php echo $key ?>},
                            click:<?php echo "function(){isContract(" . $key . ")}";?>" style="width:50px"><?php echo $this->map["contract_stamp_type"][$i] ?></button>&emsp;
						<?php } ?>
                        <input type="hidden" class="form-control" data-bind="value:is_contract,attr: { name: 'obj[is_contract]'}">
                    </div>
                </div>
                <div class="form-group ">
                    <label for="type" class="col-sm-2 control-label">履约保函 <span class="text-red fa fa-asterisk"></span></label>
                    <!--<div class="col-sm-4">
                        <select class="form-control" id="is_bond" name="obj[is_bond]" data-bind="value:is_bond,valueAllowUnset: true">
                            <option value=''>请选择</option>
                            <?php /*foreach($this->map["guarantee_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }*/ ?>
                        </select>
                    </div>-->
                    <div class="col-sm-4 checkItem">
						<?php for ($i=count($this->map["guarantee_type"])-1; $i>=0; $i--) {
						    $key = array_search($this->map["guarantee_type"][$i],$this->map["guarantee_type"]);
						    ?>
                            <button class="btn btn-sm" data-bind="
                            css:{<?php
							if ($key == 1) {
								echo "'btn-success'";
							} else {
								if ($key == 0) {
									echo "'btn-danger'";
								}
							} ?>:is_bond()==<?php echo $key ?>,'btn-default':is_bond()!=<?php echo $key ?>},
                            click:<?php echo "function(){isBond(" . $key . ")}";?>" style="width:50px"><?php echo $this->map["guarantee_type"][$i] ?></button>&emsp;
						<?php } ?>
                        <input type="hidden" class="form-control" data-bind="value:is_bond(),attr: { name: 'obj[is_bond]'}">
                    </div>
                </div>
                <div class="form-group ">
                    <label for="type" class="col-sm-2 control-label">担保协议 <span class="text-red fa fa-asterisk"></span></label>
                    <!--<div class="col-sm-4">
                        <select class="form-control" id="is_guarantee" name="obj[is_guarantee]" data-bind="value:is_guarantee,valueAllowUnset: true">
                            <option value=''>请选择</option>
                            <?php /*foreach($this->map["assure_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }*/ ?>
                        </select>
                    </div>-->
                    <div class="col-sm-4 checkItem">
						<?php for ($i=count($this->map["assure_type"])-1; $i>=0; $i--) {
                            $key = array_search($this->map["assure_type"][$i],$this->map["assure_type"]);
						    ?>
                            <button class="btn btn-sm" data-bind="
                            css:{<?php
							if ($key == 1) {
								echo "'btn-success'";
							} else {
								if ($key == 0) {
									echo "'btn-danger'";
								}
							} ?>:is_guarantee()==<?php echo $key ?>,'btn-default':is_guarantee()!=<?php echo $key ?>},
                            click:<?php echo "function(){isGuarantee(" . $key . ")}";?>" style="width:50px"><?php echo $this->map["assure_type"][$i] ?></button>&emsp;
						<?php } ?>
                        <input type="hidden" class="form-control" data-bind="value:is_guarantee,attr: { name: 'obj[is_guarantee]'}">
                    </div>
                </div>
                <div class="form-group ">
                    <label for="type" class="col-sm-2 control-label">货权转移证明 <span class="text-red fa fa-asterisk"></span></label>
                    <!--<div class="col-sm-4">
                        <select class="form-control" id="is_goods" name="obj[is_goods]" data-bind="value:is_goods,valueAllowUnset: true">
                            <option value=''>请选择</option>
                            <?php /*foreach($this->map["cargo_transfer_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }*/ ?>
                        </select>
                    </div>-->
                    <div class="col-sm-4 checkItem">
						<?php for ($i=count($this->map["cargo_transfer_type"])-1; $i>=0; $i--) {
                            $key = array_search($this->map["cargo_transfer_type"][$i],$this->map["cargo_transfer_type"]);
						    ?>
                            <button class="btn btn-sm" data-bind="
                            css:{<?php
							if ($key == 1) {
								echo "'btn-success'";
							} else {
								if ($key == 0) {
									echo "'btn-danger'";
								}
							} ?>:is_goods()==<?php echo $key ?>,'btn-default':is_goods()!=<?php echo $key ?>},
                            click:<?php echo "function(){isGoods(" . $key . ")}";?>" style="width:50px"><?php echo $this->map["cargo_transfer_type"][$i] ?></button>&emsp;
						<?php } ?>
                        <input type="hidden" class="form-control" data-bind="value:is_goods,attr: { name: 'obj[is_goods]'}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">其他付款条件1</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="pay_remark1" name="obj[pay_remark1]" placeholder="其他付款条件1" data-bind="value:pay_remark1">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">其他付款条件2</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="pay_remark2" name="obj[pay_remark2]" placeholder="其他付款条件2" data-bind="value:pay_remark2">
                    </div>
                </div>
            </div>

            <div class="box-footer">
				<?php if ($this->checkIsCanEdit($data["status"])) { ?>
                    <button type="button" id="saveButton" class="btn btn-danger" placeholder="提交" data-bind="click:pass,text:buttonText">提交</button>
				<?php } ?>
				<?php if (!$this->isExternal) { ?>
                    <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
				<?php } ?>
                <input type='hidden' name='obj[project_id]' data-bind="value:project_id"/>
                <input type='hidden' name='obj[is_down_first]' data-bind="value:is_down_first"/>
            </div>
        </form>
    </div>
</section>
<script>
	var view;
	$(function () {
		var checkItemsLength = $(".checkItem").length;
		view = new ViewModel(<?php echo json_encode($data) ?>, checkItemsLength);
		ko.applyBindings(view);
	});

	function ViewModel(option, checkItemsLength) {
		var defaults = {
			project_id: "0",
			is_down_receive: -1,
			is_down_first: -1,
			invoice_type: -1,
			is_contract: -1,
			is_bond: -1,
			is_guarantee: -1,
			is_goods: -1,
			pay_remark1: "",
			pay_remark2: "",
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.project_id = ko.observable(o.project_id);
		self.is_down_receive = ko.observable(o.is_down_receive).extend({isNotNull: true});
		self.is_down_first = ko.observable(o.is_down_first).extend({isNotNull: true});
		self.invoice_type = ko.observable(o.invoice_type).extend({isNotNull: true});
		self.is_contract = ko.observable(o.is_contract).extend({isNotNull: true});
		self.is_bond = ko.observable(o.is_bond).extend({isNotNull: true});
		self.is_guarantee = ko.observable(o.is_guarantee).extend({isNotNull: true});
		self.is_goods = ko.observable(o.is_goods).extend({isNotNull: true});
		self.pay_remark1 = ko.observable(o.pay_remark1);
		self.pay_remark2 = ko.observable(o.pay_remark2);
		self.buttonText = ko.observable("提交");
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		};

		self.actionState = 0;

		self.pass = function () {
			if (confirm("您确定要提交当前信息，该操作不可逆？")) {
				self.save();
			}
		}
		self.save = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}

			if (self.actionState == 1)
				return;
			self.actionState = 1;
			self.buttonText("提交中。。。");

			var formData = $("#mainForm").serialize();

			$.ajax({
				type: 'POST',
				url: '/ourContract/submit',
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						location.href = "/ourContract/detail?id=<?php echo $data['project_id'] ?>";
					}
					else {
						self.buttonText("提交");
						self.actionState = 0;
						alertModel(json.data);
					}
				},
				error: function (data) {
					self.buttonText("提交");
					self.actionState = 0;
					alertModel(" 保存失败！" + data.responseText);
				}
			});
		}

		self.back = function () {
			history.back();
		}

		self.isDownReceive = function (val) {
			self.is_down_receive(self.int2String(val));
		}

		self.isInvoiceType = function (val) {
			self.invoice_type(self.int2String(val));
		}

		self.isDownFirst = function (val) {
			self.is_down_first(self.int2String(val));
		}

		self.isContract = function (val) {
			self.is_contract(self.int2String(val));
		}

		self.isBond = function (val) {
			self.is_bond(self.int2String(val));
		}

		self.isGuarantee = function (val) {
			self.is_guarantee(self.int2String(val));
		}

		self.isGoods = function (val) {
			self.is_goods(self.int2String(val));
		}

		self.int2String = function (val) {
			var value = val;
			if (typeof value == "string") {
				return value;
			} else {
				return value.toString();
			}
		}
	}
</script>