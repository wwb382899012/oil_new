<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <?php include "head.php"; ?>
                <?php if(count($rollDetail)>0) include "tab.php";?>
                <!-- <div class="box-header with-border"> -->
                <div class="box-header">
                </div>
                <h4>本次转月信息</h4>
                <?php 
                if($data['lock_type']==ConstantMap::LOCK_PUT_ORDER){ 
                    if(count($noticeArr)>0){ 
                ?>
                <div class="form-group">
                    <label for="category" class="col-sm-2 control-label">选择入库通知单 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-9">
                        <table class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                            <tr>
                                <th style="width:60px;text-align:center">选择</th>
                                <th style="width:150px;text-align:center">入库通知单编号</th>
                                <th style="width:100px;text-align:center">品名</th>
                                <th style="text-align:center" colspan="2" >入库通知单数量</th>
                                <th style="width:120px;text-align:center">已锁价数量</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                                foreach ($noticeArr as $key => $value) {
                            ?>
                            <tr>
                                <td style="text-align:center"><input type='radio' name="optionRadio" id="<?php echo $key ?>" value="<?php echo $value['code'] ?>"></td>
                                <td style="text-align:center"><?php echo $value['code'] ?></td>
                                <td style="text-align:center"><?php echo $data['goods_name'] ?></td>
                                <td style="width:100px;text-align:right" <?php if(empty($value['sub_quantity'])) echo 'colspan="2"'; ?>><?php echo number_format($value['quantity'], 2).$this->map['goods_unit'][$value['unit']]['name'] ?></td>
                                <?php if(!empty($value['sub_quantity'])){ ?><td style="width:100px;text-align:right"><?php echo number_format($value['sub_quantity'], 2).$this->map['goods_unit'][$value['sub_unit']]['name'] ?></td><?php } ?>
                                <td style="text-align:right"><?php echo number_format($value['lock_quantity'], 2).$this->map['goods_unit'][$value['unit']]['name'] ?></td>
                            </tr>
                            <?php 
                                } 
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php 
                    }
                } 
                ?>
                <div class="form-group">
                    <label for="category" class="col-sm-2 control-label">转月前计价标的 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['old_target_name']; ?></p>
                    </div>
                    
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">转月后计价标的 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="target_name" name="obj[target_name]" placeholder="转月后计价标的" data-bind="value:target_name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">转月数量 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4 form-inline">
                        <input type="text" class="form-control" id="quantity" style="width:75%" name="obj[quantity]" placeholder="转月数量" data-bind="value:quantity">
                        <select class="form-control" title="请选择单位" id="unit" name="obj[unit]" data-bind="value:unit,valueAllowUnset: true,disable:isCanSelectUnit">
                            <?php
                            foreach($this->map['goods_unit'] as $v) {
                                echo "<option value='" . $v["id"] . "'>" . $v["name"]. "</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">调期费 <!-- <span class="text-red fa fa-asterisk"></span> --></label>
                    <div class="col-sm-4 form-inline">
                        <select class="form-control" title="请选择币种" id="currency" name="obj[currency]" data-bind="value:currency,valueAllowUnset: true, disable:isCanSelectCurrency ">
                            <?php
                            foreach($this->map['currency'] as $v) {
                                echo "<option value='" . $v["id"] . "'>" . $v["name"].' '.$v["ico"] . "</option>";
                            }?>
                        </select>
                        <input type="text" class="form-control" id="rollover_fee" style="width:65%" name="obj[rollover_fee]" placeholder="调期费" data-bind="money:rollover_fee">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">月价差 <!-- <span class="text-red fa fa-asterisk"></span> --></label>
                    <div class="col-sm-4">
                        <span class="input-group"> 
                            <span class="input-group-addon" data-bind="text:currency_ico"></span>
                            <input type="text" class="form-control" id="month_spread" name="obj[month_spread]" placeholder="月价差" data-bind="money:month_spread">
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
                <input type="hidden" data-bind="value: lock_type" />
                <input type="hidden" data-bind="value: type" />
                <input type="hidden" data-bind="value: lock_id" />
                <input type="hidden" data-bind="value: contract_code" />
                <input type="hidden" data-bind="value: contract_id" />
                <input type="hidden" data-bind="value: project_id" />
                <input type="hidden" data-bind="value: goods_id" />
                <input type="hidden" data-bind="value: order_index" />
                <input type="hidden" data-bind="value: old_target_id" />
                <input type="hidden" data-bind="value: order_code" />
                <input type="hidden" data-bind="value: detail_id" />
                <input type="hidden" data-bind="value: batch_id" />
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-danger" data-bind="click:save, html:saveBtnText"></button>
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
        ko.applyBindings(view);
        
        $("#lock_date").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});
    });
    function ViewModel(option) {
        var defaults = {
            detail_id: 0,
            target_name: '',
            old_target_id: 0,
            quantity: "",
            month_spread: 0,
            rollover_fee: 0,
            currency: 0,
            unit: 0,
            lock_type: 0,
            type: 0,
            lock_id: 0,
            contract_code: '',
            contract_id: 0,
            project_id: 0,
            goods_id: 0,
            order_index: 0,
            order_code: 0,
            batch_id:0,
            remark: "",

        };
        var o = $.extend(defaults, option);
        var self = this;
        self.detail_id = ko.observable(o.detail_id);
        self.old_target_id = ko.observable(o.old_target_id);
        self.target_name = ko.observable(o.target_name).extend({required: {params: true, message: "转月后计价标的不得为空"}});
        // self.lock_date = ko.observable(o.lock_date).extend({required: {params: true, message: "请选择锁价日期"}});
        self.quantity = ko.observable(o.quantity).extend({positiveNumber: true});
        self.month_spread = ko.observable(o.month_spread);
        self.rollover_fee = ko.observable(o.rollover_fee);
        /*self.month_spread = ko.observable(o.month_spread).extend({positiveNumber: true})
        self.rollover_fee = ko.observable(o.rollover_fee).extend({positiveNumber: true});*/
        self.currency = ko.observable(o.currency);
        self.unit = ko.observable(o.unit);
        self.lock_type = ko.observable(o.lock_type);
        self.type = ko.observable(o.type);
        self.contract_code = ko.observable(o.contract_code);
        self.lock_id = ko.observable(o.lock_id);
        self.goods_id = ko.observable(o.goods_id);
        self.contract_id = ko.observable(o.contract_id);
        self.project_id = ko.observable(o.project_id);
        self.order_index = ko.observable(o.order_index);
        self.batch_id = ko.observable(o.batch_id);
        self.remark = ko.observable(o.remark);
        self.order_code = ko.observable(o.order_code);

        var currencies = <?php echo json_encode($this->map['currency']) ?>;

        self.currency_ico=ko.computed(function () {
                return currencies[self.currency()]["ico"];
        },self);

        var targets = <?php echo json_encode($targetArr) ?>;

        self.isDisplayRollover = ko.computed(function () {
                if(targets.length>0 && targets[self.target_id()]["roll_quantity"]>0)
                    return true;
                return false;
        },self);

        var isHaveDetail = <?php $isDisplay=BuyLockService::isHaveLockDetail($data['detail_id']); echo !empty($isDisplay) ? 1 : 0; ?>;
        self.isCanSelectCurrency = ko.computed(function () {
            if(isHaveDetail==1)
                return true;
            return false;
        }, self);

        self.isCanSelectUnit = ko.computed(function () {
            if(<?php echo $data['unit_price'] ?>>0)
                return true;
            return false;
        }, self);


        self.actionState = ko.observable(0);
        self.saveBtnText    = ko.observable("提交");
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };


        //提交
        self.save = function(){
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            if(self.lock_type()==<?php echo ConstantMap::LOCK_PUT_ORDER ?>){
                var order_code  = $("input[name='optionRadio']:checked").val();
                var batch_id    = $("input[name='optionRadio']:checked").attr('id');
                // console.log(order_code);
                if(order_code==undefined){
                    layer.alert("请选择要进行转月的入库通知单", {icon: 5});
                    return;
                }

                self.order_code(order_code);

                self.batch_id(batch_id);
            }

            if($.trim(self.target_name())=="<?php echo $data['old_target_name'] ?>"){
                layer.alert("转月后与转月前计价标的不能相同！", {icon: 5});
                return;
            }

            layer.confirm("您确定要提交当前锁价信息，改操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.submit();
                layer.close(index);
            });

        }

        self.submit = function () {
            var formData = {"data": inc.getPostData(self)};
            if (self.actionState() == 1)
                return;
            
            self.saveBtnText("提交中" + inc.loadingIco);

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
                    self.saveBtnText("提交");
                },
                error: function (data) {
                    self.saveBtnText("提交");
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