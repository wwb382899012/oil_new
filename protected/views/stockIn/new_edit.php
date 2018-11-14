<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>

<link rel="stylesheet" href="/css/style/addnewproject.css">
<section class="el-container is-vertical">
    <?php
    $menus = [['text' => '入库管理'],['text'=>'添加入库单','link'=>'/stockOut/'], ['text' => $this->pageTitle]];
    $buttons = [];
    $buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText', 'id' => 'saveButton', 'class_abbr'=>'action-default-base']];
    $buttons[] = ['text' => '提交', 'attr' => ['data-bind' => 'click:submit, html:submitBtnText',  'id' => 'submitButton']];
    $this->loadHeaderWithNewUI($menus, $buttons, true);
    ?>

    <div class="card-wrapper">
        <?php
        $this->renderPartial('partial/new_stockNoticeInfo',['stockNotice'=>$stockNotice]);
        $this->renderPartial('partial/new_stockInInfoCard',['stockIns'=>$stockIns,'is_close_card'=>true]);
        ?>
        <div class="z-card">
            <div class="box-header">
                <h3 class="box-title">
                    本次仓库入库单信息
                </h3>
            </div>
            <div class="box-body">
                <form role="form" id="mainForm">
                    <div class="flex-grid form-group">
                        <label for="entry_date" class="col col-count-2 field">
                            <p class="form-cell-title">入库日期<i class="must-logo">*</i></p>
                            <input type="text" class="form-control input-sm date" placeholder="入库日期" data-bind="date:entry_date">
                        </label>
                        <label class="col col-count-2 field">
                            <p class="form-cell-title">仓库<i class="must-logo">*</i></p>
                            <?php if ($stockNotice['type'] == ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER) :?>
                                <select class="form-control selectpicker show-menu-arrow" data-bind="enable:false">
                                        <option value=''>虚拟库</option>
                                </select>
                            <?php else:?>
                                <select class="form-control selectpicker show-menu-arrow" title="请选择仓库" id="store_id" data-bind="selectpicker:store_id,valueAllowUnset: true" data-live-search="true">
                                    <?php foreach ($storehouses as $v) :?>
                                        <option value='<?php echo $v['store_id'];?>'><?php echo $v['name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            <?php endif; ?>
                        </label>
                    </div>

                    <div class="flex-grid form-group">
                        <div class="col col-count-1 field">
                            <?php
                            $attachType = $this->map["stock_in_attachment_type"][ConstantMap::STOCK_IN_ATTACH_TYPE];
                            ?>
                            <p class="form-cell-title"><?php echo $attachType["name"] ?></p>
                            <div class="form-group-custom-upload">
                                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_uploadNew.php"; ?>
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
                    </div>

                    <div class="flex-grid form-group">
                        <div class="col col-count-1 field">
                            <p class="form-cell-title">仓库入库明细<i class="must-logo">*</i></p>
                            <!-- ko component: {
                                 name: "stock-in-goods",
                                 params: {
                                             items: goodsItems
                                             }
                             } -->
                            <!-- /ko -->
                            <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_stockInGoods.php"; ?>
                        </div>
                    </div>

                    <div class="flex-grid">
                        <label class="col col-count-1 field">
                            <p class="form-cell-title">备注</p>
                            <textarea class="w-full" data-bind="value:remark" cols="105" rows="3" name="obj[remark]" placeholder="备注"></textarea>
                        </label>
                    </div>
                </form>
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
                        inc.vueMessage({duration: 500,type: 'success', message: '操作成功',onClose:function(){
                            if (self.isSubmit() == 1) {
                                if (document.referrer === '') {
                                    location.href = "/stockInList/";
                                } else {
                                    history.back();
                                }
                            } else {
                                location.href = "/<?php echo $this->getId() ?>/detail/?id=" + self.batch_id();
                            }
                        }});
					} else {
						self.isSubmit(0);
                        inc.vueAlert({title:  '错误',content: json.data});
					}
				},
				error: function (data) {
					self.actionState = 0;
					self.saveBtnText("保存");
					self.submitBtnText("提交");
					self.isSubmit(0);
                    inc.vueAlert({title:  '错误',content: "操作失败！" + data.responseText});
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
            inc.vueConfirm({content:'您确定要提交当前入库单信息吗，该操作不可逆？',type: 'warning',onConfirm:function(){
                self.isSubmit(1);
                self.submitBtnText("提交中" + inc.loadingIco);
                self.sendSaveSubmitAjax();
            }});
		};

		self.back = function () {
			history.back();
		}
	}
</script>