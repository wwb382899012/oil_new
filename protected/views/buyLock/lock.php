<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <?php include "head.php"; ?>
                <?php if(count($lockDetail)>0 || count($rollDetail)>0) include "tab.php";?>
                <!-- <div class="box-header with-border"> -->
                <div class="box-header">
                </div>
                <h4>本次锁价信息</h4>
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
                                <td style="text-align:center"><input type='radio' name="optionRadio" id="<?php echo $key ?>" value="<?php echo $value['code'] ?>" data-bind='checked: orderCheck'></td>
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
                    <label for="category" class="col-sm-2 control-label">计价标的 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择计价标的" id="target_id" name="obj[target_id]" data-bind="selectpicker:target_id,valueAllowUnset: true">
                            <?php
                            foreach($targetDetail as $v) {
                                echo "<option value='" . $v["target_id"] . "'>" . $v["name"] . "</option>";
                            }?>
                        </select>
                    </div>
                    <span data-bind="visible:isDisplayRollover">
                        <label class="col-sm-2 control-label">本次转月数量</label>
                        <div class="col-sm-4">
                            <p class="form-control-static">
                                <span data-bind="text:calcRoll"></span>
                            </p>
                        </div>
                    </span>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">锁价日期 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="lock_date" name="obj[lock_date]" placeholder="锁价日期" data-bind="value:lock_date">
                    </div>
                    <span data-bind="visible:isDisplayRollover">
                        <label class="col-sm-2 control-label">已锁价数量</label>
                        <div class="col-sm-4">
                            <p class="form-control-static">
                                <span data-bind="text:calcLock"></span>
                            </p>
                        </div>
                    </span>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">基准价格 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4 form-inline">
                        <!-- <span class="input-group-btn" > -->
                        <select class="form-control" title="请选择币种" id="currency" name="obj[currency]" data-bind="value:currency,valueAllowUnset: true, disable:isCanSelectCurrency ">
                            <?php
                            foreach($this->map['currency'] as $v) {
                                echo "<option value='" . $v["id"] . "'>" . $v["name"].' '.$v["ico"] . "</option>";
                            }?>
                        </select>
                        <input type="text" class="form-control" style="width:65%" id="price_base" name="obj[price_base]" placeholder="基准价格" data-bind="money:price_base">
                        <!-- </span> -->
                    </div>
                    <span data-bind="visible:isDisplayRollover">
                        <label class="col-sm-2 control-label">未锁价数量</label>
                        <div class="col-sm-4">
                            <p class="form-control-static">
                                <span data-bind="text:calcBanlance"></span>
                            </p>
                        </div>
                    </span>
                </div>
                <div class="form-group ">
                    <label class="col-sm-2 control-label">锁价数量 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4 form-inline">
                        <input type="text" class="form-control" style="width:75%" id="quantity" name="obj[quantity]" placeholder="锁价数量" data-bind="value:quantity">
                        <select class="form-control" title="请选择单位" id="unit" name="obj[unit]" data-bind="value:unit,valueAllowUnset: true,disable:isCanSelectUnit">
                            <?php
                            foreach($this->map['goods_unit'] as $v) {
                                echo "<option value='" . $v["id"] . "'>" . $v["name"]. "</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">升贴水</label>
                    <div class="col-sm-4">
                        <span class="input-group">
                            <span class="input-group-addon" data-bind="text:currency_ico"></span>
                            <input type="text" class="form-control" id="premium" name="obj[premium]" placeholder="升贴水" data-bind="money:premium">
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">月价差</label>
                    <div class="col-sm-4">
                        <span class="input-group"> 
                            <span class="input-group-addon" data-bind="text:currency_ico"></span>
                            <input type="text" class="form-control" id="month_spread" name="obj[month_spread]" placeholder="月价差" data-bind="money:month_spread">
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">调期费 <!-- <span class="text-red fa fa-asterisk" data-bind="visible:isNeedDisplay"></span> --></label>
                    <div class="col-sm-4">
                        <span class="input-group"> 
                            <span class="input-group-addon" data-bind="text:currency_ico"></span>
                            <input type="text" class="form-control" id="rollover_fee" name="obj[rollover_fee]" placeholder="调期费" data-bind="money:rollover_fee">
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">结算价格</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <span data-bind="text:currency_ico"></span>
                            <span data-bind="moneyText:amount"></span>
                        </p>
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
            target_id: 0,
            lock_date: "",
            price_base: "",
            quantity: "",
            premium: 0,
            month_spread: 0,
            rollover_fee: 0,
            amount: 0,
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
            batch_id: 0,
            remark: "",

        };
        var o = $.extend(defaults, option);
        var self = this;
        self.detail_id = ko.observable(o.detail_id);
        self.target_id = ko.observable(o.target_id).extend({required:true});
        self.lock_date = ko.observable(o.lock_date).extend({required: {params: true, message: "请填写锁价日期"}});
        self.price_base = ko.observable(o.price_base).extend({positiveNumber: true});
        self.quantity = ko.observable(o.quantity).extend({positiveNumber: true});
        self.premium = ko.observable(o.premium);
        self.month_spread = ko.observable(o.month_spread);
        self.rollover_fee = ko.observable(o.rollover_fee);
        /*self.rollover_fee = ko.observable(o.rollover_fee).extend({
        custom:{
            params: function (v) {
                if((<?php echo count($rollDetail)?> > 0 && v > 0) || <?php echo count($rollDetail) ?> < 1 )
                    return true;
                else
                    return false;
            },
            message: "调期费必须输入一个大于0的数值"
        }});*/
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
        self.order_code = ko.observable(o.order_code);
        self.batch_id = ko.observable(o.batch_id);
        self.remark = ko.observable(o.remark);

        self.isNeedDisplay = ko.computed(function () {
                return <?php echo count($rollDetail) ?> > 0;
        },self);

        var currencies = <?php echo json_encode($this->map['currency']) ?>;

        self.currency_ico=ko.computed(function () {
                return currencies[self.currency()]["ico"];
        },self);

        var codes = <?php echo json_encode($codeArr) ?>;
        self.orderCheck=ko.computed(function () {
                if(typeof(codes[self.target_id()])!='undefined' && 
                   codes[self.target_id()].hasOwnProperty("order_code"))
                    return codes[self.target_id()]['order_code'];
                return 0;
        },self);


        self.amount=ko.computed(function () {
                var total = 0;

                if (!isNaN(parseFloat(self.price_base()))) {
                    total += parseFloat(self.price_base());
                }
                if(!isNaN(parseFloat(self.premium()))){
                    total += parseFloat(self.premium());
                }
                if(!isNaN(parseFloat(self.month_spread()))){
                    total += parseFloat(self.month_spread());
                }
                if(!isNaN(parseFloat(self.rollover_fee()))){
                    total += parseFloat(self.rollover_fee());
                }
                return total.toFixed(0);
        },self);

        var targets = <?php echo json_encode($targetArr) ?>;

        self.isDisplayRollover = ko.computed(function () {
                if(typeof(targets[self.target_id()])!='undefined' && 
                   targets[self.target_id()].hasOwnProperty("roll_quantity"))
                    return true;
                return false;
        },self);

        

        var isHaveDetail = <?php $isDisplay=BuyLockService::isHaveLockDetail($data['detail_id']); echo !empty($isDisplay) ? 1 : 0; ?>;
        self.isCanSelectCurrency = ko.computed(function () {
            if(isHaveDetail==1)
                return true;
            return false;
        }, self);
        /*self.isCanSelectCurrency = ko.computed(function () {
            if(<?php echo $data['unit_price'] ?>>0)
                return true;
            return false;
        }, self);*/
        self.isCanSelectUnit = ko.computed(function () {
            if(<?php echo $data['unit_price'] ?>>0)
                return true;
            return false;
        }, self);

        self.calcLock = ko.computed(function () {
            var total = '0';
            if(self.isDisplayRollover()){
                if(targets[self.target_id()]["lock_quantity_format"]!=undefined)
                    total = targets[self.target_id()]["lock_quantity_format"];
                total += "<?php echo $data['unit_name']; ?>";
            }
            return total;
        },self);

        self.calcRoll = ko.computed(function () {
            var total = '';
            if(self.isDisplayRollover()){
                total = targets[self.target_id()]["roll_quantity_format"]+"<?php echo $data['unit_name']; ?>";
            }
            return total;
        },self);

        self.calcBanlance = ko.computed(function () {
            var total = '';
            if(self.isDisplayRollover()){
                total = targets[self.target_id()]["balance_quantity_format"]+"<?php echo $data['unit_name']; ?>";
            }
            return total;
        },self);

        self.target_id.subscribe(function(v){
            if(typeof(targets[self.target_id()])!='undefined' &&
                targets[self.target_id()].hasOwnProperty("month_spread"))
                self.month_spread(targets[self.target_id()]["month_spread"]);
            if(typeof(targets[self.target_id()])!='undefined' &&
                targets[self.target_id()].hasOwnProperty("rollover_fee"))
                self.rollover_fee(targets[self.target_id()]["rollover_fee"]);
        });


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
                // console.log(batch_id);
                if(order_code==undefined){
                    layer.alert("请选择要进行锁价的入库通知单", {icon: 5});
                    return;
                }
                self.order_code(order_code);
                self.batch_id(batch_id);
            }

            if(self.isDisplayRollover() && parseFloat(self.quantity())>parseFloat(targets[self.target_id()]["balance_quantity"])){
                layer.alert("锁价数量不能大于未锁价数量", {icon: 5});
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