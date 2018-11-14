<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                本次入库通知单信息
            </h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">入库通知单日期
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control input-sm date" placeholder="入库通知单日期" data-bind="date:batch_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">发货方式
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择发货方式" id="type" name="obj[type]" data-bind="value:type,valueAllowUnset: true">
                            <?php foreach ($this->map["stock_notice_delivery_type"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <?php
                    $attachType = $this->map["stock_notice_attachment_type"][ConstantMap::STOCK_NOTICE_ATTACH_TYPE];
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
                                         files:<?php echo json_encode($attachments[ConstantMap::STOCK_NOTICE_ATTACH_TYPE]); ?>,
                                         fileParams: {
                                            id:<?php echo $data['batch_id'] ?>
                                         }
                                     }
                         } -->
                        <!-- /ko -->
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">入库通知单明细 <span class="text-red fa fa-asterisk"></span></label>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-1 col-sm-11">
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
                                         unit: <?php echo $lading->items[0]['unit'] ?>
                                         }
                         } -->
                        <!-- /ko -->
                    </div>
                </div>
                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/stockNoticeGoods.php"; ?>
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
                        layer.msg('操作成功', {icon: 6, time: 1000}, function () {
                            if (self.isSubmit() == 1) {
                                location.href = "/<?php echo $this->getId() ?>";
                            } else {
                                location.href = "/<?php echo $this->getId() ?>/detail/?id=" + self.contract_id();
                            }
                        });
                    } else {
                        self.actionState = 0;
                        self.saveBtnText("保存");
                        self.submitBtnText("提交");
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
            layer.confirm("您确定要提交当前入库通知单信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
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