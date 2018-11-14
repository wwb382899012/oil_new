<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <?php 
                    include "head.php"; 
                    include "tab.php"; 
                ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">调货处理方式</label>
                    <div class="col-sm-4">
                        <!-- <select class="form-control" id="type" name="obj[type]" data-bind="value:type,valueAllowUnset: true"> -->
                        <select class="form-control" id="type" name="obj[type]" data-bind="value:type">
                            <!-- <option value="">请选择调货处理方式</option> -->
                            <?php
                            foreach ($this->map["cross_method_type"] as $key=>$val){
                                echo "<option value='$key'>$val</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <span data-bind="visible:!isShowReturn()">
                        <label class="col-sm-2 control-label">采购合同数量</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="text" class="form-control input-sm" name= "obj[quantity]" placeholder="采购合同数量" data-bind="value:quantity">
                                <span class="input-group-addon" data-bind="html:unit_format"></span>
                            </div>
                        </div>
                    </span>
                </div>
                <span data-bind="visible:isShowReturn">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">本次还货明细 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                                <!-- ko component: {
                                     name: "cross-return",
                                     params: {
                                                 contract_id: contract_id,
                                                 project_id: project_id,
                                                 goods_id: goods_id,
                                                 items: goodsItems
                                                 }
                                 } -->
                                <!-- /ko -->
                        </div>
                        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/crossReturn.php"; ?>
                    </div>
                </span>
                <div class="form-group">
                    <label class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
                <input type="hidden" data-bind="value: contract_code" />
                <input type="hidden" data-bind="value: contract_id" />
                <input type="hidden" data-bind="value: project_id" />
                <input type="hidden" data-bind="value: goods_id" />
                <input type="hidden" data-bind="value: cross_id" />
                <input type="hidden" data-bind="value: buy_id" />
                <input type="hidden" data-bind="value: relation_cross_id" />
                <input type="hidden" data-bind="value: relation_cross_code" />
                <input type="hidden" data-bind="value: corporation_id" />
                <input type="hidden" data-bind="value: partner_id" />
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save, html:saveBtnText"></button>
                        <button type="button" id="submitButton" class="btn btn-danger" data-bind="click:submit, html:submitBtnText"></button>
                        <button type="button" class="btn btn-default history-back" data-bind="click:back">返回</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    

</section>

<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        view.formatGoodsItems(<?php echo json_encode($goodsItems) ?>);
        ko.applyBindings(view);
    });
    function ViewModel(option, order) {
        var defaults = {
            cross_id: 0,
            contract_id: 0,
            contract_code: '',
            buy_id: 0,
            buy_project_id: 0,
            project_id: 0,
            corporation_id: '',
            partner_id: '',
            goods_id: 0,
            order_index: 0,
            quantity: '',
            type: 0,
            relation_cross_id: 0,
            relation_cross_code: "",
            unit_format: "",
            remark: "",
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.buy_id = ko.observable(o.buy_id);
        self.buy_project_id = ko.observable(o.buy_project_id);
        self.cross_id = ko.observable(o.cross_id);
        self.contract_code = ko.observable(o.contract_code);
        self.goods_id = ko.observable(o.goods_id);
        self.contract_id = ko.observable(o.contract_id);
        self.project_id = ko.observable(o.project_id);
        self.corporation_id = ko.observable(o.corporation_id);
        self.partner_id = ko.observable(o.partner_id);
        self.order_index = ko.observable(o.order_index);
        self.relation_cross_id = ko.observable(o.relation_cross_id);
        self.relation_cross_code = ko.observable(o.relation_cross_code);
        self.unit_format = ko.observable(o.unit_format);
        self.type = ko.observable(o.type);
        self.quantity = ko.observable(o.quantity).extend({
            custom:{
                params: function (v) {
                    if(self.type()==<?php echo ConstantMap::ORDER_BACK_TYPE ?> || 
                      (self.type()==<?php echo ConstantMap::ORDER_BUY_TYPE ?> && v>0 &&
                        parseFloat(v)<=parseFloat(<?php echo $crossDetail['total_quantity_out'] ?>)))
                        return true;
                    else
                        return false;
                },
                message: "采购合同数量必须是一个非零正数，且不能超过实际出库数量"
            }
        });
        self.remark = ko.observable(o.remark);
        /*self.isShowReturn = ko.computed(function () {
            if(self.type() == <?php echo ConstantMap::ORDER_BACK_TYPE ?>)
                return true;
            else
                return false;
        });*/
        self.isShowReturn = ko.observable(false);
        if(self.type()==<?php echo ConstantMap::ORDER_BACK_TYPE ?>){
            self.isShowReturn(true);
        }


        self.type.subscribe(function(v){
            if(v==<?php echo ConstantMap::ORDER_BACK_TYPE ?>)
                self.isShowReturn(true);
            else
                self.isShowReturn(false);
        });


        self.goodsItems = ko.observableArray();

        self.formatGoodsItems = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i in data) {
                var obj = new CrossReturn(data[i]);
                self.goodsItems().push(obj);
            }
        };

        self.isSave = ko.observable(1);
        self.actionState = ko.observable(0);
        self.saveBtnText = ko.observable("保存");
        self.submitBtnText = ko.observable("提交");
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };


        //保存
        self.save = function(){
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
            
            self.isSave(1);

            self.pass();
        }

        self.submit = function(){
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            self.isSave(0);

            layer.confirm("您确定要提交当前调货信息，改操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.pass();
                layer.close(index);
            });

        }

        self.pass = function () {
            var filter = ["isShowReturn", "submitBtnText"];
            var quantity_return = 0.0;
            if(self.type()==<?php echo ConstantMap::ORDER_BACK_TYPE ?>){
                if(self.goodsItems().length>0){
                    for(var item in self.goodsItems()){
                        if(self.goodsItems()[item].quantity()==0){
                            layer.alert("还货明细中入库单编号为："+self.goodsItems()[item].stock_code()+"，还货数量为空！", {icon: 5});
                            return;
                        }else if(parseFloat(self.goodsItems()[item].quantity_balance())<parseFloat(self.goodsItems()[item].quantity())){
                            layer.alert("还货明细中入库单编号为："+self.goodsItems()[item].stock_code()+"，还货数量大于可用库存数量！", {icon: 5});
                            return;
                        }
                        quantity_return += parseFloat(self.goodsItems()[item].quantity());
                    }
                    if(parseFloat(quantity_return)>parseFloat(<?php echo $crossDetail['total_quantity_out'] ?>)){
                        layer.alert("还货总数量大于实际出库总数量！", {icon: 5});
                        return;
                    }
                }else{
                    layer.alert("请添加还货明细！", {icon: 5});
                    return;
                }
            }else{
                filter.push("goodsItems");
            }

            
            
            var formData = {"data": inc.getPostData(self,filter)};
            if (self.actionState() == 1)
                return;
            if(self.isSave()==1)
                self.saveBtnText("保存中" + inc.loadingIco);
            else
                self.submitBtnText("提交中" + inc.loadingIco);

            self.actionState(1);
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    if (json.state == 0) {
                        layer.msg("操作成功", {icon: 6, time:1000}, function(){
                            location.href = "/<?php echo $this->getId() ?>/detail/?id=" + json.data + "&contract_id=" + json.extra;
                        });
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                    self.saveBtnText("保存");
                    self.submitBtnText("提交");
                },
                error: function (data) {
                    self.saveBtnText("保存");
                    self.submitBtnText("提交");
                    self.actionState(0);
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
        }

        self.back = function () {
            history.back();
        }
    }


</script>