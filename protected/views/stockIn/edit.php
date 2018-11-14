<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <?php
    $this->renderPartial("/stockIn/partial/stockNoticeInfo", array('stockNotice'=>$stockNotice));

    if(Utility::isNotEmpty($stockIns))
        foreach ($stockIns as $stockIn) {
            //$this->renderPartial("/common/stockInBriefInfo", array('stockIn'=>$stockIn));
            $this->renderPartial("/common/stockInInfo", array('stockIn'=>$stockIn));
        }
    ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                本次仓库入库单信息
            </h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="form-group">
                    <label for="entry_date" class="col-sm-2 control-label">入库日期
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control input-sm date" placeholder="入库日期" data-bind="date:entry_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="store_id" class="col-sm-2 control-label">仓库
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <?php
                        if ($stockNotice['type'] == ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER) {
                            echo '<p class="form-control-static">虚拟库</p>';
                        } else {
                            ?>
                            <select class="form-control" title="请选择仓库" id="store_id" data-bind="value:store_id,valueAllowUnset: true">
                                <option value="0">请选择仓库</option>
                                <?php foreach ($storehouses as $v) {
                                    echo "<option value='" . $v['store_id'] . "'>" . $v['name'] . "</option>";
                                } ?>
                            </select>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php
                    $attachType = $this->map["stock_in_attachment_type"][ConstantMap::STOCK_IN_ATTACH_TYPE];
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
                                         files:<?php echo json_encode($stockInAttachs[ConstantMap::STOCK_IN_ATTACH_TYPE]); ?>,
                                         fileParams: {
                                            id:<?php echo $data['stock_in_id'] ?>
                                         }
                                     }
                         } -->
                        <!-- /ko -->
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">仓库入库明细 <span class="text-red fa fa-asterisk"></span></label>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-1 col-sm-11">
                        <!-- ko component: {
                             name: "stock-in-goods",
                             params: {
                                         items: goodsItems
                                         }
                         } -->
                        <!-- /ko -->
                    </div>
                </div>
                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/stockInGoods.php"; ?>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-4">
                        <textarea data-bind="value:remark" cols="105" rows="3" name="obj[remark]" placeholder="备注"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save, html:saveBtnText"></button>
                    <button type="button" id="submitButton" class="btn btn-danger" data-bind="click:submit, html:submitBtnText"></button>
                    <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                    <input type='hidden' data-bind="value:batch_id"/>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
		view.formatGoodsItems(<?php echo json_encode($stockInGoods) ?>);
		ko.applyBindings(view);
	});
	function ViewModel(option) {
		var defaults = {
			stock_in_id: 0,
			project_id: 0,
			contract_id: 0,
			batch_id: 0,
			type: 1,
			store_id: 0,
			entry_date: (new Date()).format(),
			order_index: 0,
			remark: '',
		};
		var o = $.extend(defaults, option);
		var self = this;

		self.stock_in_id = ko.observable(o.stock_in_id);
		self.project_id = ko.observable(o.project_id);
		self.contract_id = ko.observable(o.contract_id);
		self.batch_id = ko.observable(o.batch_id);
		self.type = ko.observable(o.type);
		self.store_id = ko.observable(o.store_id).extend({
			custom: {
				params: function (v) {
					if (self.type() == config.stockNoticeTypeByWarehouse) {
						if (v > 0) {
							return true;
						}
						return false;
					}

					return true;
				},
				message: "请选择仓库"
			}
		});
		self.entry_date = ko.observable(o.entry_date).extend({date: true});
		self.order_index = ko.observable(o.order_index);
		self.remark = ko.observable(o.remark);
		self.fileUploadStatus = ko.observable();
		self.goodsItems = ko.observableArray();

		self.formatGoodsItems = function (data) {
			if (data == null || data == undefined)
				return;

			for (var i in data) {
				data[i]['goods_units'] = self.goods_units;
				data[i]['type'] = self.type();
				var obj = new StockInGoods(data[i]);
				self.goodsItems().push(obj);
			}
		};

		self.saveBtnText = ko.observable("保存");
		self.submitBtnText = ko.observable("提交");
		self.isSubmit = ko.observable(0);
		self.actionState = 0;
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		};

		self.getPostData = function () {
			self.stockInDetail = [];
			if (Array.isArray(self.goodsItems()) && self.goodsItems().length > 0) {
				ko.utils.arrayForEach(self.goodsItems(), function (item, i) {
					if (item.unit() == item.unit_sub()) {
						item.unit_sub(item.stock_unit());
					}
					if (item.quantity() > 0) {
						self.stockInDetail.push(inc.getPostData(item, ["goods_name", "showQuantityInput", "unit_desc", "unit_sub_desc", "units", "showUnitRate"]));
					}
				});
			}

			return inc.getPostData(self, ["fileUploadStatus", "goodsItems", "submitBtnText", "saveBtnText"]);
		}

		self.sendSaveSubmitAjax = function () {
			if (self.actionState == 1)
				return;
			self.actionState = 1;
			var formData = {"data": self.getPostData()};
			console.log(formData);
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/save',
				data: formData,
				dataType: "json",
				success: function (json) {
					self.actionState = 0;
					self.saveBtnText("保存");
					self.submitBtnText("提交");
					if (json.state == 0) {
						layer.msg('操作成功', {icon: 6, time: 1000}, function () {
							if (self.isSubmit() == 1) {
								if (document.referrer === '') {
									location.href = "/stockInList/";
								} else {
									history.back();
								}
							} else {
								location.href = "/<?php echo $this->getId() ?>/detail/?id=" + self.batch_id();
							}
						});
					} else {
						self.isSubmit(0);
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					self.actionState = 0;
					self.saveBtnText("保存");
					self.submitBtnText("提交");
					self.isSubmit(0);
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});
		};

		self.save = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			self.saveBtnText("保存中" + inc.loadingIco);
			self.sendSaveSubmitAjax();
		};

		self.submit = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			layer.confirm("您确定要提交当前入库单信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
				self.isSubmit(1);
				self.submitBtnText("提交中" + inc.loadingIco);
				self.sendSaveSubmitAjax();
				layer.close(index);
			})
		};

		self.back = function () {
			history.back();
		}
	}
</script>