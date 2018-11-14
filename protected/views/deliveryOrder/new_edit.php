<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>

<link rel="stylesheet" href="/css/style/addnewproject.css">
<section class="el-container is-vertical">

    <?php
    $menus = [['text' => '出库管理'],['text'=>'新建发货单','link'=>'/deliveryOrder/'], ['text' => $this->pageTitle]];
    $buttons = [];
    $buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText',  'id' => 'saveButton', 'class_abbr'=>'action-default-base']];
    $buttons[] = ['text' => '提交', 'attr' => ['data-bind' => 'click:submit, html:submitBtnText',  'id' => 'submitButton']];
    $this->loadHeaderWithNewUI($menus, $buttons, true);
    ?>

    <div class="card-wrapper">
        <?php
        $isStockNoticeByWarehouse = ($deliveryOrder['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE);
        if (!$isStockNoticeByWarehouse) {
            $this->renderPartial('/stockIn/partial/new_stockInInfoCard',['stockIns'=>[$stockIn]]);
        }
        ?>

        <div class="z-card">
            <h3 class="z-card-header">
                请填写发货信息
            </h3>
            <div class="z-card-body">
                <form role="form" id="mainForm">
                    <div class="flex-grid form-group align-center">
                        <label class="col col-count-2 field flex-grid align-center">
                            <span class="w-fixed line-h--text">交易主体:</span>
                            <span class="form-control-static ellipsis line-h--text">
                                <span data-bind="{html:corporation_name_link}"></span>
                                <?php if (!$isStockNoticeByWarehouse):?>
                                    <a href="javascript: void 0" class="o-btn o-btn-action primary" style="margin-left: 30px;" data-bind="click:getSaleContract">选择销售合同</a>
                                <?php endif;?>
                            </span>
                        </label>
                        <label class="col col-count-2 field flex-grid">
                            <span class="w-fixed line-h--text">销售合同编号:</span>
                            <span class="form-control-static ellipsis line-h--text">
                                <p class="form-control-static" data-bind="{html:contract_code_link}"></p>
                            </span>
                        </label>
                        <label class="col col-count-2 field flex-grid">
                            <span class="w-fixed line-h--text">下游合作方:</span>
                            <span class="form-control-static ellipsis line-h--text">
                                <p class="form-control-static" data-bind="{html:partner_name_link}"></p>
                            </span>
                        </label>
                    </div>

                    <div class="flex-grid form-group">
                        <label class="col col-count-3 field">
                            <p class="form-cell-title">预计发货日期<i class="must-logo">*</i></p>
                            <input type="text" class="form-control input-sm date" placeholder="预计发货日期" data-bind="date:delivery_date">
                        </label>
                    </div>

                    <div class="flex-grid form-group">
                        <div class="col col-count-1 field">
                            <?php
                            $attachType = $this->map["stock_delivery_attachment"][1];
                            $attachments=AttachmentService::getAttachments(Attachment::C_STOCK_DELIVERY,$deliveryOrder["order_id"], 1);
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
                                             files:<?php echo json_encode($attachments[1]); ?>,
                                             fileParams: {
                                                id:<?php echo empty($deliveryOrder['order_id'])?0:$deliveryOrder['order_id'] ?>
                                             }
                                         }
                             } -->
                                <!-- /ko -->
                            </div>
                        </div>
                    </div>

                    <div class="flex-grid form-group">
                        <div class="col col-count-1 field">
                            <p class="form-cell-title">本次发货明细<i class="must-logo">*</i></p>
                            <!-- ko component: {
                                 name: "delivery-order-goods",
                                 params: {
                                             addAllGoodsItemsFunc:addAllGoodsItems,
                                             deleteGoodsItemFunc:deleteGoodsItem,
                                             goodsChangedFunc:goodsChanged,
                                             allGoodsItems:allGoodsItems,
                                             corporation_id: corporation_id,
                                             partner_id: partner_id,
                                             stock_in_id: stock_in_id,
                                             items: goodsItems,
                                             type: type
                                             }
                             } -->
                            <!-- /ko -->
                            <?php $this->renderPartial("/components/new_deliveryOrderDetail");?>
                        </div>
                    </div>

                    <div class="flex-grid">
                        <label class="col col-count-1 field">
                            <p class="form-cell-title">备注</p>
                            <textarea class="w-full" id="remark" data-bind="value:remark" cols="105" rows="3"placeholder="备注"></textarea>
                        </label>
                    </div>

                </form>
            </div>
        </div>

        <!-- ko component: { name: "contrace-list",params: {
                                        addSaleContractFunc:addSaleContract,
                                        saleContractList: saleContractList,
                                        goodsItems:goodsItems,
                                        corporation_id: corporation_id,
                                        partner_id: partner_id,
                                        stock_in_id: stock_in_id,
                                        contract_code:contract_code,
                                        partner_name:partner_name,
                                        corporation_name:corporation_name,
                                        }} --><!-- /ko -->
        <?php $this->renderPartial("/deliveryOrder/partial/new_contractListDialog");?>

    </div>
</section>
<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($deliveryOrder) ?>);
        view.setAllGoodsItems(<?php echo json_encode($allGoodsItems) ?>);
        view.formatGoodsItems(<?php echo json_encode($goodsItems) ?>);
        ko.applyBindings(view);
    });
    function ViewModel(option) {
        var defaults = {
            corporation_id: 0,
            contract_id: 0,
            partner_id: 0,
            order_id: 0,
            stock_in_id: 0,
            //
            type: 0,
            delivery_date: (new Date()).format(),
            remark: '',
        };
        var o = $.extend(defaults, option);
        var self = this;

        //提交的参数
        self.order_id = ko.observable(o.order_id);
        self.type = ko.observable(o.type);
        self.delivery_date = ko.observable(o.delivery_date).extend({date: true});
        self.remark = ko.observable(o.remark);
        //销售合同选择弹窗需要的回带参数
        self.corporation_id = ko.observable(o.corporation_id);
        self.project_id = ko.observable(o.project_id);
        self.contract_id = ko.observable(o.contract_id);
        self.partner_id = ko.observable(o.partner_id);
        self.corporation_name = ko.observable(o.corporation_name);
        self.contract_code = ko.observable(o.contract_code);
        self.partner_name = ko.observable(o.partner_name);
        self.stock_in_id = ko.observable(o.stock_in_id);
        //
        self.contract_code_link = ko.observable("--");
        self.partner_name_link = ko.observable("--");
        self.corporation_name_link = ko.observable("--");
        //

        //所有商品明细
        self.allGoodsItems = ko.observableArray();
        //销售合同商品明细
        self.goodsItems = ko.observableArray();
        //销售合同列表
        self.saleContractList = ko.observableArray();

        //设置链接
        self.setLink = function(){
            var contract_code_link = '<a class="text-link" href="/contract/detail/?id='+ self.contract_id() + '&t=1" target="_blank" title="'+ self.contract_code() +'">'+ self.contract_code() +'</a>';
            var partner_name_link = '<a class="text-link" href="/partner/detail/?id='+ self.partner_id() + '&t=1" target="_blank" title="'+ self.partner_name() +'">'+ self.partner_name() +'</a>';
            var corporation_name_link = '<a class="text-link" href="/corporation/detail/?id='+ self.corporation_id() + '&t=1" target="_blank" title="'+ self.corporation_name() +'">'+ self.corporation_name() +'</a>';

            if(self.contract_id() > 0){
                self.contract_code_link(contract_code_link);
            }
            if(self.partner_id() > 0){
                self.partner_name_link(partner_name_link);
            }
            if(self.corporation_id() > 0){
                self.corporation_name_link(corporation_name_link);
            }
        };
        self.setLink();

        self.formatGoodsItems = function (data) {
            if (data == null || data == undefined)
                return;

            var goodsList = [];
            for (var i in data) {
                var obj = new DeliveryOrderDetail(data[i]);
                goodsList.push(obj);
            }

            self.goodsItems(goodsList);
        };

        self.setAllGoodsItems = function(data){
            self.allGoodsItems(data);
        }

        self.deleteGoodsItem = function (data) {
            if (self.goodsItems){
                self.goodsItems.remove(data);
            }
        }

        self.addAllGoodsItems = function () {
            var goodsList = [];
            var selectedGoogsIds = [];
            ko.utils.arrayForEach(self.goodsItems(), function (item) {
                goodsList.push(item);
                selectedGoogsIds.push(item.goods_id());
            });

            for(var i=0; i< self.allGoodsItems().length; i++){
                var item = self.allGoodsItems()[i];
                if(!selectedGoogsIds.includes(item.goods_id)){
                    goodsList.push(new DeliveryOrderDetail(item));
                    //改成每次只添加一个
                    break;
                }
            }
            self.goodsItems(goodsList);
        };

        /**
         * 商品名称下拉框被改变时触发，进行商品明细的更换
         */
        self.goodsChanged = function(newGoodsId, oldGoodsId){
            if(0 == newGoodsId || 0 == oldGoodsId){
                return;
            }

            //添加
            var newGooods = ko.observable();
            var data = self.allGoodsItems();
            for(var i =0 ;i< data.length;i++){
                if(newGoodsId == data[i].goods_id){
                    newGooods = new DeliveryOrderDetail(data[i]);
                    break;
                }
            }

            //替换旧的,并保存先后顺序
            var goodsItems = self.goodsItems();
            var newGoodsItems = [];
            for(var i =0 ;i < goodsItems.length;i++){
                if(goodsItems[i].goods_id() == oldGoodsId){
                    newGoodsItems[i] = newGooods;
                }else{
                    newGoodsItems[i] = goodsItems[i];
                }
            }

            self.goodsItems.removeAll();
            self.goodsItems(newGoodsItems);
        }

        /**
         * 获取合同列表
         */
        self.getSaleContract = function () {
            $("#contractListModal").modal({
                backdrop: true,
                keyboard: false,
                show: true
            });

            $.ajax({
                type: 'GET',
                url: '/<?php echo $this->getId() ?>/getContractsForDirectTransfer',
                data: {
                    stock_in_id:self.stock_in_id()
                },
                dataType: "json",
                success: function (json) {
                    if (json.state != 0) {
                        return;
                    }

                    var saleContractArray = [];
                    ko.utils.arrayForEach(json.data, function (item) {
                        saleContractArray.push(new SaleContract(item));
                    });
                    self.saleContractList(saleContractArray);
                },
                error: function (data) {
                    inc.vueAlert({title:  '错误',content: "获取销售信息失败！" + data.responseText});
                }
            });
        };

        /**
         * 添加销售合同，同时添加销售明细
         * @param data
         */
        self.addSaleContract = function (data) {
            self.project_id(data.project_id());
            self.contract_id(data.contract_id());
            self.corporation_id(data.corporation_id());
            self.partner_id(data.partner_id());
            self.corporation_name(data.corporation_name());
            self.contract_code(data.contract_code());
            self.partner_name(data.partner_name());
            self.setLink();

            $.ajax({
                type: 'GET',
                url: '/<?php echo $this->getId() ?>/getContractsDetails',
                data: {
                    contract_id:data.contract_id(),
                    stock_in_id:self.stock_in_id()
                },
                dataType: "json",
                success: function (json) {
                    if (json.state != 0) {
                        inc.vueAlert({title:  '错误',content: "获取该销售合同明细失败，请重新选择！"});
                        return;
                    }

                    self.allGoodsItems(json.data);
                    self.formatGoodsItems(json.data);
                    $('#contractListModal').modal('hide');
                },
                error: function (data) {
                    inc.vueAlert({title:  '错误',content: "获取销售信息失败！" + data.responseText});
                }
            });
        };

        self.fileUploadStatus=ko.observable();

        self.saveBtnText = ko.observable("保存");
        self.submitBtnText = ko.observable("提交");
        self.isSubmit = ko.observable(0);
        self.actionState = 0;
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };


        self.getPostData = function () {
            self.deliveryOrderDetail = [];
            if (Array.isArray(self.goodsItems()) && self.goodsItems().length > 0) {
                ko.utils.arrayForEach(self.goodsItems(), function (item, i) {
                    self.deliveryOrderDetail[i] = inc.getPostData(item, ["contract_code", "goods_name", "contract_quantity", "distributed_quantity", "stock_out_quantity", "unit_store_desc", "contract_detail_id", "distribute_detail"]);
                });
            }

            return inc.getPostData(self, ["goodsItems", "submitBtnText", "saveBtnText"]);
        }

        self.sendSaveSubmitAjax = function () {
            if (self.actionState == 1)
                return;
            self.actionState = 1;
            var formData = {"data": self.getPostData()};
            formData.data.saleContractList = undefined;
            formData.data.allGoodsItems = undefined;
            formData.data.contract_code_link = undefined;
            formData.data.partner_name_link = undefined;
            formData.data.corporation_name_link = undefined;

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
                        inc.vueMessage({duration: 500,type: 'success', message: '操作成功',onClose:function () {
                            if (self.isSubmit() == 1) {
                                location.href = "/<?php echo $this->getId() ?>";
                            } else {
                                location.href = "/<?php echo $this->getId() ?>/detail/?id=" + json.data;
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

        self.checkData = function () {
            if(self.contract_id() <= 0){
                inc.vueMessage({type: 'error', message: "请选择销售合同！"});
                return false;
            }
            if(self.goodsItems().length == 0){
                inc.vueMessage({type: 'error', message: "请添加本次发货明细！"});
                return false;
            }
            return true;
        }

        self.save = function () {
            if(!self.checkData()){
                return;
            }

            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            self.saveBtnText("保存中" + inc.loadingIco);
            self.sendSaveSubmitAjax();
        };

        self.submit = function () {
            if(!self.checkData()){
                return;
            }

            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            vue.$confirm('您确定要提交当前发货单信息吗，该操作不可逆？', '提示', {
                type: 'warning'
            }).then(() => {
                self.isSubmit(1);
                self.submitBtnText("提交中" + inc.loadingIco);
                self.sendSaveSubmitAjax();
            });
        };

        self.back = function () {
            history.back();
        }


        function DeliveryOrderDetail(option) {
            var defaults = {
                type: 0,
                detail_id: 0,
                project_id: 0,
                contract_id: 0,
                goods_id: 0,
                quantity: 0,
                contract_code: '',
                goods_name: '',
                contract_quantity: '',
                distributed_quantity: '',
                stock_out_quantity: '',
                unit_store_desc: '',
                contract_detail_id: 0,
                distribute_detail: ''
            };
            var o = $.extend(defaults, option);
            var self = this;
            self.type = ko.observable(o.type);
            self.detail_id = ko.observable(o.detail_id);
            self.project_id = ko.observable(o.project_id);
            self.contract_id = ko.observable(o.contract_id);
            self.goods_id = ko.observable(o.goods_id);
            self.quantity = ko.observable(o.quantity).extend({positiveNumber: {params:true,message:"0-1亿"}});
            self.contract_code = ko.observable(o.contract_code);
            self.goods_name = ko.observable(o.goods_name);
            self.contract_quantity = ko.observable(o.contract_quantity);
            self.distributed_quantity = ko.observable(o.distributed_quantity);
            self.stock_out_quantity = ko.observable(o.stock_out_quantity);
            self.unit_store_desc = ko.observable(o.unit_store_desc);
            self.contract_detail_id = ko.observable(o.contract_detail_id);
            self.distribute_detail = ko.observable(o.distribute_detail);
            self.stock_delivery_detail = ko.observableArray();
            if (option.stock_delivery_detail && Array.isArray(option.stock_delivery_detail) && option.stock_delivery_detail.length > 0) {
                ko.utils.arrayForEach(option.stock_delivery_detail, function (item, i) {
                    item.contract_id = self.contract_id();
                    item.project_id = self.project_id();
                    self.stock_delivery_detail()[i] = new StockDeliveryDetail(item);
                });
            }

            self.quantity.subscribe(function (v) {
                if (self.type() == config.stockNoticeTypeDirectTransfer && self.stock_delivery_detail().length > 0) { //直调
                    self.stock_delivery_detail()[0].quantity(v);
                }
            });
        }


        //配货明细
        function SaleContract(option) {
            var defaults = {
                contract_id: 0,
                contract_code:  '',
                code_out:'',
                project_id: 0,
                project_code:  '',
                project_type: 0,
                partner_id: 0,
                partner_name:  '',
                corporation_id: 0,
                corporation_name:  '',
            };
            var object = $.extend(defaults, option);
            var self = this;

            for(var attrName in defaults){
                self[attrName] = ko.observable(object[attrName]);
            }

            var project_code_link = '<a class="text-link" href="/project/detail/?id='+ self.project_id() + '&t=1" target="_blank" title="'+ self.project_code() +'">'+ self.project_code() +'</a>';
            var contract_code_link = '<a class="text-link" href="/contract/detail/?id='+ self.contract_id() + '&t=1" target="_blank" title="'+ self.contract_code() +'">'+ self.contract_code() +'</a>';
            var partner_name_link = '<a class="text-link" href="/partner/detail/?id='+ self.partner_id() + '&t=1" target="_blank" title="'+ self.partner_name() +'">'+ self.partner_name() +'</a>';
            var corporation_name_link = '<a class="text-link" href="/corporation/detail/?id='+ self.corporation_id() + '&t=1" target="_blank" title="'+ self.corporation_name() +'">'+ self.corporation_name() +'</a>';

            self.project_code_link = ko.observable(project_code_link);
            self.contract_code_link = ko.observable(contract_code_link);
            self.partner_name_link = ko.observable(partner_name_link);
            self.corporation_name_link = ko.observable(corporation_name_link);
        }
    }

    function back()
    {
        history.back();
    }

    document.onkeydown = function (e) {
        var event = e || window.event;
        var code = event.keyCode || event.which || event.charCode;
        if (code == 13) {
            return false;
        }
    }

    function back() {
        history.back();
    }
</script>