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
                    <label class="col-sm-2 control-label">本次调货原因 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">本次调货明细 <span class="text-red fa fa-asterisk"></span></label>
                </div>
                <div class="form-group" id="view-div">
                    <div class="col-sm-offset-1 col-sm-11">
                        <!-- ko component: {
                             name: "cross-goods",
                             params: {
                                         contract_id: contract_id,
                                         corporation_id: corporation_id,
                                         project_id: project_id,
                                         goods_id: goods_id,
                                         items: goodsItems
                                         }
                         } -->
                        <!-- /ko -->
                    </div>
                </div>
                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/crossGoods.php"; ?>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">调货日期 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="cross_date" name="obj[cross_date]" placeholder="调货日期" data-bind="value:cross_date">
                    </div>
                </div>
                <input type="hidden" data-bind="value: contract_code" />
                <input type="hidden" data-bind="value: contract_id" />
                <input type="hidden" data-bind="value: project_id" />
                <input type="hidden" data-bind="value: goods_id" />
                <input type="hidden" data-bind="value: detail_id" />
                <input type="hidden" data-bind="value: cross_id" />
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
        $("#cross_date").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});
    });
    function ViewModel(option) {
        var defaults = {
            detail_id: 0,
            cross_id: 0,
            cross_date: "",
            contract_code: '',
            contract_id: 0,
            project_id: 0,
            goods_id: 0,
            order_index: 0,
            corporation_id: 0,
            remark: "",

        };
        var o = $.extend(defaults, option);
        var self = this;
        self.detail_id = ko.observable(o.detail_id);
        self.cross_id = ko.observable(o.cross_id);
        self.cross_date = ko.observable(o.cross_date).extend({required: {params: true, message: "请填写调货日期"}});
        self.contract_code = ko.observable(o.contract_code);
        self.goods_id = ko.observable(o.goods_id);
        self.contract_id = ko.observable(o.contract_id);
        self.corporation_id = ko.observable(o.corporation_id);
        self.project_id = ko.observable(o.project_id);
        self.order_index = ko.observable(o.order_index);
        self.remark = ko.observable(o.remark).extend({required: {params: true, message: "请填写本次调货原因"}});

        self.goodsItems = ko.observableArray();

        self.formatGoodsItems = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i in data) {
                var obj = new CrossGoods(data[i]);
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
            if(self.goodsItems().length>0){
                for(var item in self.goodsItems()){
                    if(self.goodsItems()[item].quantity()==0){
                        layer.alert("入库单编号为："+self.goodsItems()[item].stock_code()+"，预计借货数量为空！", {icon: 5});
                        return;
                    }else if(parseFloat(self.goodsItems()[item].quantity_balance())<parseFloat(self.goodsItems()[item].quantity())){
                        layer.alert("入库单编号为："+self.goodsItems()[item].stock_code()+"，预计借货数量大于可用库存数量！", {icon: 5});
                        return;
                    }
                }
            }else{
                layer.alert("请添加调货明细！", {icon: 5});
                return;
            }
            var formData = {"data": inc.getPostData(self)};
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
                            location.href = "/<?php echo $this->getId() ?>/detail/?id=" + json.data;
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