<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>

<link rel="stylesheet" href="/css/style/addnewproject.css">
<section class="el-container is-vertical">

    <?php
    $menus = [['text' => '入库管理'],['text' => '添加入库通知单', 'link' => '/stockNotice/'], ['text' => $this->pageTitle]];
    $buttons = [];
    $buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText',  'id' => 'saveButton', 'class_abbr'=>'action-default-base']];
    $buttons[] = ['text' => '提交', 'attr' => ['data-bind' => 'click:submit, html:submitBtnText',  'id' => 'submitButton']];
    $this->loadHeaderWithNewUI($menus, $buttons, true);
    ?>

    <div class="card-wrapper">
        <?php
        $this->renderPartial('partial/new_contractInfoCard',['contract'=>$contract,'transactions'=>$transactions]);
        $this->renderPartial('partial/new_stockNoticeCard',['stockNotices'=>$stockNotices,'is_close_card'=>true]);
        ?>
        <div class="z-card">
            <h3 class="z-card-header">
                本次入库通知单信息
            </h3>
            <div class="z-card-body">
                <form role="form" id="mainForm">
                    <div class="flex-grid form-group">
                        <label class="col col-count-2 field">
                            <p class="form-cell-title">入库通知单日期<i class="must-logo">*</i></p>
                            <input type="text" class="form-control input-sm date" placeholder="入库通知单日期" data-bind="date:batch_date">
                        </label>
                        <label class="col col-count-2 field">
                            <p class="form-cell-title">发货方式<i class="must-logo">*</i></p>
                            <select class="form-control selectpicker show-menu-arrow" title="请选择发货方式" id="type" name="obj[type]" data-bind="selectpicker:type,valueAllowUnset: true">
                                <?php foreach ($this->map["stock_notice_delivery_type"] as $k => $v) {
                                    echo "<option value='" . $k . "'>" . $v . "</option>";
                                } ?>
                            </select>
                        </label>
                    </div>
                    <div class="flex-grid form-group">
                        <div class="col col-count-1 field">
                            <?php
                            $attachType = $this->map["stock_notice_attachment_type"][ConstantMap::STOCK_NOTICE_ATTACH_TYPE];
                            ?>
                            <p class="form-cell-title">
                            <?php echo $attachType["name"] ?></p>
                            <div class="form-group-custom-upload">
                                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_uploadNew.php"; ?>
                                <!-- ko component: {
                             name: "file-upload",
                             params: {
                                         status:fileUploadStatus,
                                         controller:"<?php echo $this->getId() ?>",
                                         fileConfig:<?php echo json_encode($attachType) ?>,
                                         files:<?php echo json_encode($attachments[ConstantMap::STOCK_NOTICE_ATTACH_TYPE]); ?>,
                                         fileParams: {
                                            id:<?php echo $data['batch_id'] ?>
                                         }
                                     }
                         } -->
                                <!-- /ko -->
                            </div>
                        </div>
                    </div>
                    <div class="flex-grid form-group">
                        <div class="col col-count-1 field">
                            <p class="form-cell-title">入库通知单明细<i class="must-logo">*</i></p>
                            <!-- ko component: {
                             name: "stock-notice-goods",
                             params: {
                                         contract_id: contract_id,
                                         project_id: project_id,
                                         allGoods: goods,
                                         units: units,
                                         items: goodsItems,
                                         type: type,
                                         allStorehouses: allStorehouses,
                                         unit: <?php echo $transactions[0]['unit'] ?>
                                         }
                         } -->
                            <!-- /ko -->
                            <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_StockNoticeGoods.php"; ?>
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
		view.allStorehouses(inc.objectToArray(<?php echo json_encode(Storehouse::getAllActiveStorehouse()) ?>));
		view.units = inc.objectToArray(<?php echo json_encode(array_values($this->map["goods_unit"])); ?>);

		view.formatGoods(<?php echo json_encode($allGoods) ?>);
        view.formatGoodsItems(<?php echo json_encode($stockNoticeGoods) ?>);
		ko.applyBindings(view);

	});
	function ViewModel(option) {
		var defaults = {
			batch_id: 0,
			project_id: 0,
			contract_id: 0,
			type: 1,
			batch_date: (new Date()).format(),
			order_index: 0,
			remark: '',
		};
		var o = $.extend(defaults, option);
		var self = this;

		self.batch_id = ko.observable(o.batch_id);
		self.project_id = ko.observable(o.project_id);
		self.contract_id = ko.observable(o.contract_id);
		self.type = ko.observable(o.type).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择发货方式"
			}
		});
		self.batch_date = ko.observable(o.batch_date).extend({date: true});
		self.order_index = ko.observable(o.order_index);
		self.remark = ko.observable(o.remark);

		self.fileUploadStatus = ko.observable();

		self.allStorehouses = ko.observableArray();
		self.goodsItems = ko.observableArray();
		self.units = [];
		self.goods = ko.observableArray();

		self.formatGoodsItems = function (data) {
			if (data == null || data == undefined)
				return;
			for (var i in data) {
			    data[i]["allGoods"]=self.goods;
				var obj = new StockNoticeGoods(data[i]);

				self.goodsItems().push(obj);
			}
		};

		self.formatGoods = function (data) {
			if (data == null || data == undefined)
				return;

			for (var i = 0; i < data.length; i++) {
                self.goods().push(new Goods(data[i]));
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
			self.stockNoticeGoods = [];
			if (Array.isArray(self.goodsItems()) && self.goodsItems().length > 0) {
				ko.utils.arrayForEach(self.goodsItems(), function (item, i) {
					self.stockNoticeGoods[i] = inc.getPostData(item, ["allGoods", "allStorehouses", "allValidgoods", "goods", "unit_sub_enable", "unit_sub_visible"]);
				});
			}

			return inc.getPostData(self, ["units", "allStorehouses", "goods", "fileUploadStatus", "goodsItems", "submitBtnText", "saveBtnText"]);
		}

		self.sendSaveSubmitAjax = function () {
			if (self.actionState == 1)
				return;
			self.actionState = 1;
			var formData = {"data": self.getPostData()};
			// console.log(formData);
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/save',
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
                        inc.vueMessage({duration: 500,type: 'success', message: '操作成功',onClose:function () {
                            if (self.isSubmit() == 1) {
                                location.href = "/<?php echo $this->getId() ?>";
                            } else {
                                location.href = "/<?php echo $this->getId() ?>/detail/?id=" + self.contract_id();
                            }
                        }});
					} else {
						self.actionState = 0;
						self.saveBtnText("保存");
						self.submitBtnText("提交");
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

            inc.vueConfirm({content:'您确定要提交当前入库通知单信息吗，该操作不可逆？',type: 'warning',onConfirm:function(){
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