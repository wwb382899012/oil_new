<script src="/js/bootstrap3-typeahead.min.js" xmlns="http://www.w3.org/1999/html"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <form class="form-horizontal" role="form" id="mainForm">
                <h4 class="box-title">基本信息</h4>
                <div class="form-group">
                    <label class="col-sm-2 control-label">交易主体</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <a href="/corporation/detail/?id=<?php echo $data['corporation_id'] ?>&t=1"><?php echo Corporation::getCorporationName($data['corporation_id']) ?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">仓库</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <a href="/storehouse/detail/?store_id=<?php echo $data['store_id'] ?>&t=1"><?php echo StorehouseService::getStoreName($data['store_id']) ?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">品名</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo GoodsService::getSpecialGoodsNames($data['goods_id']) ?></p>
                    </div>
                </div>

                <div class="box-header with-border"></div>
                <h4 class="box-title">本次盘点信息</h4>
                <div class="form-group">
                    <label for="inventory_date" class="col-sm-2 control-label">盘点日期
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control input-sm date" placeholder="盘点日期" data-bind="date:inventory_date">
                    </div>
                </div>
                <div class="form-group">
                    <?php
                    $attachType = $this->map["stock_inventory_attachment"][ConstantMap::STOCK_INVENTORY_ATTACH_TYPE];
                    ?>
                    <label class="col-sm-2 control-label">
                        <span class='glyphicon ' data-bind="css:{'glyphicon-ok text-green':fileUploadStatus,' glyphicon-remove text-red':!fileUploadStatus()}"></span>&emsp;
                        <?php echo $attachType["name"] ?></label>
                    <div class="col-sm-10">
                        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/upload.php"; ?>
                        <!-- ko component: {
                             name: "file-upload",
                             params: {
                                         status:fileUploadStatus,
                                         controller:"<?php echo $this->getId() ?>",
                                         fileConfig:<?php echo json_encode($attachType) ?>,
                                         files:<?php echo json_encode($attachments[ConstantMap::STOCK_INVENTORY_ATTACH_TYPE]); ?>,
                                         fileParams: {
                                            id:<?php echo $data['inventory_id'] ?>
                                         }
                                         }
                         } -->
                        <!-- /ko -->
                    </div>
                </div>
                <div class="form-group">
                    <label for="quantity_active" class="col-sm-2 control-label">盘点前可用库存/<span data-bind="html:unit_desc"></span></label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="numberFixedText:quantity_active"></p>
                    </div>
                    <label for="quantity_frozen" class="col-sm-2 control-label">冻结库存/<span data-bind="html:unit_desc"></span></label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="numberFixedText:quantity_frozen"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quantity_before" class="col-sm-2 control-label">盘点前库存/<span data-bind="html:unit_desc"></span></label>
                    <div class="col-sm-4">
                        <p class="form-control-static text-red" data-bind="numberFixedText:quantity_before"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quantity" class="col-sm-2 control-label">盘点后库存/<span data-bind="html:unit_desc"></span>
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control input-sm" placeholder="盘点后库存" data-bind="numberFixed:quantity">
                    </div>
                    <label for="quantity_diff" class="col-sm-2 control-label">库存损耗/<span data-bind="html:unit_desc"></span></label>
                    <div class="col-sm-4">
                        <p class="form-control-static text-red" data-bind="numberFixedText:quantity_diff"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">损耗分摊
                        <span class="text-red fa fa-asterisk" data-bind="visible: quantity_diff() > 0"></span></label>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-1 col-sm-11">
                        <!-- ko component: {
                             name: "stock-inventory-detail",
                             params: {
                                         items: inventoryDetail,
                                         unit_desc: unit_desc
                                         }
                         } -->
                        <!-- /ko -->
                    </div>
                </div>
                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/stockInventoryDetail.php"; ?>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save, html:saveBtnText"></button>&emsp;
                    <button type="button" id="submitButton" class="btn btn-danger" data-bind="click:submit, html:submitBtnText"></button>&emsp;
                    <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
		ko.applyBindings(view);
		view.formatInventoryDetail(<?php echo json_encode($stockInventoryDetail) ?>);
	});
	function ViewModel(option) {
		var defaults = {
			inventory_id: 0,
			goods_detail_id: 0,
			corporation_id: 0,
			store_id: 0,
			goods_id: 0,
			unit: 0,
			unit_desc: '',
			inventory_date: (new Date()).format(),
			type: 0,
			quantity_before: 0,
			quantity_diff: 0,
			quantity: 0,
			quantity_active: 0,
			quantity_frozen: 0,
			remark: '',
			status: 0
		};
		var o = $.extend(defaults, option);
		var self = this;

		self.inventory_id = ko.observable(o.inventory_id);
		self.goods_detail_id = ko.observable(o.goods_detail_id);
		self.corporation_id = ko.observable(o.corporation_id);
		self.store_id = ko.observable(o.store_id);
		self.goods_id = ko.observable(o.goods_id);
		self.unit = ko.observable(o.unit);
		self.unit_desc = ko.observable(o.unit_desc);
		self.inventory_date = ko.observable(o.inventory_date).extend({date: true});
		self.type = ko.observable(o.type)
		self.quantity_before = ko.observable(o.quantity_before);
		self.quantity = ko.observable(o.quantity).extend({
			required: true,
			custom: {
				params: function (v) {
					self.msg = '盘点后库存不能小于0';
					if (isNaN(v)) {
						self.msg = '请输入数字';
						return false;
					} else {
						return parseFloat(v) >= 0;
					}
					return true;
				},
				message: function () {
					return self.msg;
				}
			}

		});
		self.quantity_active = ko.observable(o.quantity_active);
		self.quantity_frozen = ko.observable(o.quantity_frozen);
		self.remark = ko.observable(o.remark);
		self.status = ko.observable(o.status);
		self.inventoryDetail = ko.observableArray();

		self.fileUploadStatus = ko.observable();

		self.formatInventoryDetail = function (data) {
			if (data == null || data == undefined)
				return;

			for (var i in data) {
				var obj = new StockInventoryDetail(data[i]);

				self.inventoryDetail().push(obj);
			}
		};
		self.quantity_diff = ko.computed(function () {
			if (ko.unwrap(self.quantity) !== '' && !isNaN(ko.unwrap(self.quantity))) {
				return (parseFloat(ko.unwrap(self.quantity_before)) - parseFloat(ko.unwrap(self.quantity))).toFixed(4);
			}
			return 0;
		}, self);

		self.saveBtnText = ko.observable("保存");
		self.submitBtnText = ko.observable("提交");
		self.actionState = 0;
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		};

		self.checkQuantityDiff = function () {
			if ($.isArray(self.inventoryDetail()) && self.inventoryDetail().length > 0) {
				var total = 0;
				ko.utils.arrayForEach(self.inventoryDetail(), function (item, i) {
					var value = parseFloat(ko.unwrap(item.quantity_diff));
					if (!isNaN(value) && value !== '') {
						total += value;
					}
				});
				if (parseFloat(total) != parseFloat(self.quantity_diff())) {
					return "损耗分摊总库存损耗:" + total.toFixed(4) + " 与库存损耗:" + self.quantity_diff() + "不一致，请检查！";
				}
			} else {
				if (parseFloat(self.quantity_diff()) != 0) {
					return "请填写库存损耗分摊信息！";
				}
			}

			return true;
		}

		self.getPostData = function () {
			self.stockInventoryDetail = [];
			if (Array.isArray(self.inventoryDetail()) && self.inventoryDetail().length > 0) {
				ko.utils.arrayForEach(self.inventoryDetail(), function (item, i) {
					if (parseFloat(ko.unwrap(item.quantity_diff)) != 0) {
						if (parseFloat(ko.unwrap(item.quantity)) != parseFloat(ko.unwrap(item.quantity_before))) {
							if (parseFloat(ko.unwrap(item.quantity)) < parseFloat(ko.unwrap(item.quantity_before))) {
								item.type(2);
							} else {
								item.type(1);
							}
						} else {
							item.type(0);
						}

						self.stockInventoryDetail.push(inc.getPostData(item, ['stock_in_code', 'msg', 'stock_in_link', 'quantity_active_after']));
					}
				});
			}

			return inc.getPostData(self, ["fileUploadStatus", "submitBtnText", "saveBtnText", "inventoryDetail", "unit_desc", "msg"]);
		}

		self.sendSaveSubmitAjax = function () {
			if (self.actionState == 1)
				return;
			self.actionState = 1;
			if (self.status() == 0) {
				self.saveBtnText("保存中" + inc.loadingIco);
			} else {
				self.submitBtnText("提交中" + inc.loadingIco);
			}

			if (parseFloat(ko.unwrap(self.quantity_diff)) != 0) {
				if (parseFloat(self.quantity()) < parseFloat(self.quantity_before())) {
					self.type(2);
				} else {
					self.type(1);
				}
			} else {
				self.type(0);
			}

			var formData = {"data": self.getPostData()};
			console.log(formData);
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/save',
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						layer.msg('操作成功', {icon: 6, time: 1000}, function () {
							if (self.status() == 10) {
								location.href = "/<?php echo $this->getId() ?>";
							} else {
								location.href = "/<?php echo $this->getId() ?>/detail/?corp_id=" + self.corporation_id() + '&store_id=' + self.store_id() + '&goods_id=' + self.goods_id() + '&unit=' + self.unit();
							}
						});
					} else {
						self.actionState = 0;
						self.saveBtnText("保存");
						self.submitBtnText("提交");
						self.status(0);
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					self.actionState = 0;
					self.saveBtnText("保存");
					self.submitBtnText("提交");
					self.status(0);
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});
		};

		self.save = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			var detailCheck = self.checkQuantityDiff();
			if (detailCheck !== true) {
				layer.alert(detailCheck, {icon: 5});
				return;
			}
			self.status(0);
			self.sendSaveSubmitAjax();
		};

		self.submit = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}

			var detailCheck = self.checkQuantityDiff();
			if (detailCheck !== true) {
				layer.alert(detailCheck, {icon: 5});
				return;
			}
			layer.confirm("您确定要提交当前库存盘点信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
				self.status(10);
				self.sendSaveSubmitAjax();
				layer.close(index);
			})
		};

		self.back = function () {
			history.back();
		}
	}
</script>