<template id='component-template-stock-batch-settlement'>
    <table class="table table-hover">
        <thead>
        <tr>
            <th style="width:120px;">销售合同 </th>
            <th style="width:120px;">品名 </th>
            <th style="width:120px;">合同数量 </th>
            <th style="width:100px;">溢短装比例 </th>
            <th style="width:200px;">合同单位 </th>
            <th style="width:200px;">累积实际出库数量 </th>
            <th style="width:200px;">累积实际结算数量 </th>
            <th style="width:200px;">本次发货数量 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:200px;">商品配货</th>
        </tr>
        </thead>
        <tbody data-bind="foreach:goods">
        <!-- ko component: {
            name: "stock-batch-settlement-item",
            params: {
                        model: $data,
                        units:$parent.units
                        }
        } -->
        <!-- /ko -->
        </tbody>
    </table>
</template>
<template id='component-template-stock-batch-settlement-item'>
    <tr data-bind="with:model">
        <td>
            <p class="form-control-static" data-bind="text:goods_name"></p>
        </td>
        <td>
            <p class="form-control-static" data-bind="text:quantity+''+unit_str"></p>
            <p class="form-control-static" data-bind="visible:!singleUnit(), text:quantity_sub+''+unit_sub_str"></p>
        </td>
        <td>
            <p class="form-control-static" data-bind="text:quantity_done + '' + unit_str"></p>
            <p class="form-control-static" data-bind="visible:!singleUnit(), text:quantity_done_sub + '' + unit_sub_str"></p>
        </td>
        <td>
            <select class="form-control input-sm" title="单位"
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:units,
                    value:unit,
                    change:changeSelectedUnit">
            </select>
        </td>
        <!--数量-->
        <td data-bind="visible:!singleUnit()">
            <div class='input-group' data-bind="visible:selectMainUnit()">
                <input class="form-control" data-bind="value:quantity_actual">
                <span class="input-group-addon" data-bind="text:unit_str"></span>
            </div>
            <div class='input-group' data-bind="visible:!selectMainUnit()">
                <p class="form-control-static" data-bind="text:quantity_actual() + '' + unit_str">
                <span class="input-group-addon" data-bind="text:unit_str"></span>
            </div>
            <div class='input-group' data-bind="visible:!selectMainUnit()">
                <input class="form-control" data-bind="value:quantity_actual_sub">
                <span class="input-group-addon" data-bind="text:unit_sub_str"></span>
            </div>
            <div class='input-group' data-bind="visible:selectMainUnit()">
                <p class="form-control-static" data-bind="text:quantity_actual_sub() + '' + unit_sub_str">
            </div>
        </td>
        <td data-bind="visible:singleUnit()">
            <div class='input-group'>
                <input class="form-control" data-bind="value:quantity_actual">
                <span class="input-group-addon" data-bind="text:unit_str"></span>
            </div>
        </td>
        <!--单价-->
        <td data-bind="visible:!singleUnit()">
            <div class='input-group' data-bind="visible:selectMainUnit()">
                <input class="form-control" data-bind="value:price">
                <span class="input-group-addon" data-bind="text:price_unit_str"></span>
            </div>
            <div class='input-group' data-bind="visible:!selectMainUnit()">
                <p class="form-control-static" data-bind="text:price() + '' + price_unit_str">
                <span class="input-group-addon" data-bind="text:unit_str"></span>
            </div>
            <div class='input-group' data-bind="visible:!selectMainUnit()">
                <input class="form-control" data-bind="value:price_sub">
                <span class="input-group-addon" data-bind="text:price_unit_sub_str"></span>
            </div>
            <div class='input-group' data-bind="visible:selectMainUnit()">
                <p class="form-control-static" data-bind="text:price_sub() + '' + price_unit_sub_str">
            </div>
        </td>
        <td data-bind="visible:singleUnit()">
            <div class='input-group'>
                <input class="form-control" data-bind="value:price">
                <span class="input-group-addon" data-bind="text:unit_str"></span>
            </div>
        </td>
        <td>
            <div class='input-group'>
                <input type="text" class="form-control input-sm" placeholder="结算金额" data-bind="value:amount">
                <span class="input-group-addon" data-bind="text:currencies_str"></span>
            </div>
        </td>
        <!--损耗-->
        <td data-bind="visible:!singleUnit()">
                <div class='input-group' data-bind="visible:selectMainUnit()">
                        <input class="form-control" data-bind="value:quantity_loss">
                        <span class="input-group-addon" data-bind="text:unit_str"></span>
                </div>
                <div class='input-group' data-bind="visible:!selectMainUnit()">
                        <p class="form-control-static" data-bind="text:quantity_loss() + '' + unit_str">
                        <span class="input-group-addon" data-bind="text:unit_str"></span>
                </div>
                <div class='input-group' data-bind="visible:!selectMainUnit()">
                        <input class="form-control" data-bind="value:quantity_loss_sub">
                        <span class="input-group-addon" data-bind="text:unit_sub_str"></span>
                </div>
                <div class='input-group' data-bind="visible:selectMainUnit()">
                        <p class="form-control-static" data-bind="text:quantity_loss_sub() + '' + unit_sub_str">
                </div>
        </td>
        <td data-bind="visible:singleUnit()">
                <div class='input-group'>
                        <input class="form-control" data-bind="value:quantity_loss">
                        <span class="input-group-addon" data-bind="text:unit_str"></span>
                </div>
        </td>
        <td>
            <input type="text" class="form-control input-sm" placeholder="备注" data-bind="value:remark()">
        </td>
        <td>
            <p class="form-control-static">
                <a href="javascript:void(0);" data-bind="click:openDialog">查看</a>
            </p>
        </td>
    </tr>
</template>
<div class="modal fade in" id="buy_lock_dialog">
    <div class="modal-dialog" style="width:80%">
        <div class="modal-content">
            <div class="modal-header" >
                <a class="close" data-dismiss="modal">×</a>
                <h5>锁价/转月记录</h5>
            </div>
            <div class="modal-body" id="buy_lock_dialog_body">
            </div>
            <div class="modal-footer">
                <input type="button" value="&nbsp;关闭&nbsp;" class="btn btn-success btn-sm" data-dismiss="modal">
            </div>
        </div>
    </div>
</div>
<script>
    ko.components.register('stock-batch-settlement-item', {
        template: {element: 'component-template-stock-batch-settlement-item'},
        viewModel: stockBatchSettlementComponent
    });

    ko.components.register('stock-batch-settlement', {
        template: {element: 'component-template-stock-batch-settlement'},
        viewModel: StockBatchSettlement
    });
    function StockBatchSettlement(params) {
        var o = {
            units:[],
            goods:[]
        }
        o = $.extend(o, params);
        var self = this;
        var units = {}
        self.goods = o.goods;
    }

    function StockSettlementGoods(option) {
        var o = {
            quantity :0,
            display_unit :0,
            quantity_sub :0,
            display_unit_sub :0,
            quantity_actual :0,
            quantity_actual_sub :0,
            quantity_done:0,
            quantity_done_sub:0,
            display_done_unit :0,
            display_done_unit_sub :0,
            goods_id :0,
            goods_name:'',
            contract_id:0,
            batch_id:0,
            unit_rate :0,
            display_price:0,
            amount :0,
            remark :'',
            unit_in_use:[],
            quantity_loss:0,
            quantity_loss_sub:0,
            price:0,
            price_sub:0,
            unit:0,
            unit_sub:0,
            unit_settle:0,
            units:[],
            currencies:{},
            amount_currency:1,
        }
        o = $.extend(o, option);
        console.log(o);
        o.display_unit = parseInt(o.display_unit);
        o.display_unit_sub = parseInt(o.display_unit_sub);
        o.unit = parseInt(o.display_unit);
        o.unit_sub = parseInt(o.display_unit_sub);
        o.unit_settle = parseInt(o.unit);
        var self = this;
        for(var ind in o) {
            self[ind] = o[ind];
        }
        self.unit = ko.observable(o.unit);
        self.remark = ko.observable(o.remark);
        self.units = [];
        o.quantity_sub = (o.quantity_sub==0)?o.quantity:o.quantity_sub;
        o.price = o.price;
        o.price_sub = (o.price / o.unit_rate==0)?o.price:(o.price / o.unit_rate).toFixed(2);
        o.quantity_loss = (o.quantity - o.quantity_done).toFixed(2);
        o.quantity_loss_sub = (o.quantity_loss * o.unit_rate).toFixed(2);
        for(var ind in o.unit_in_use) {
            self.units.push(o.units[o.unit_in_use[ind]]);
        }
        self.currencies = o.currencies;
        self.currencies_str = o.currencies[o.amount_currency]['ico'];
        self.unit_str = o.units[o.display_unit]['name'];
        self.unit_sub_str = o.units[o.display_unit_sub]['name'];
        self.price_unit_str = o.currencies[o.amount_currency]['ico'] + '/' + o.units[o.display_unit]['name'];
        self.price_unit_sub_str = o.currencies[o.amount_currency]['ico'] + '/' + o.units[o.display_unit_sub]['name'];
        self.singleUnit = ko.computed(function() {
            var unit = parseInt(this.display_unit);
            var unit_sub = parseInt(this.display_unit_sub);
            return unit == unit_sub;
        }, self);
        self.unit_settle = ko.observable(o.unit_settle);
        self.selectMainUnit = ko.computed(function() {
            var unit = parseInt(this.unit());
            var unit_settle = parseInt(this.unit_settle());
            return unit == unit_settle;
        }, self);
        self.quantity = o.quantity;
        self.quantity_sub = o.quantity_sub;
        self.quantity_done = o.quantity_done;
        self.quantity_done_sub = o.quantity_done_sub;
        self.quantity_actual = ko.observable(o.quantity_actual).extend({
            custom: {
                params: function (v) {
                    if (v <= 0) {
                        return false;
                    }
                    return true
                },
                message: "请输入一个大于0的数字"
            }
        });
        self.quantity_actual_sub = ko.observable(o.quantity_actual_sub).extend({
            custom: {
                params: function (v) {
                    if (v <= 0) {
                        return false;
                    }
                    return true
                },
                message: "请输入一个大于0的数字"
            }
        });
        self.price = ko.observable(o.price);
        self.price_sub = ko.observable(o.price_sub).extend({
            custom: {
                params: function (v) {
                    if (v <= 0) {
                        return false;
                    }
                    return true
                },
                message: "请输入一个大于0的数字"
            }
        });
        self.amount= ko.observable(o.amount);
        self.quantity_loss = ko.observable(o.quantity_loss);
        self.quantity_loss_sub = ko.observable(o.quantity_loss_sub).extend({
            custom: {
                params: function (v) {
                    if (v <= 0) {
                        return false;
                    }
                    return true
                },
                message: "请输入一个大于0的数字"
            }
        });
        self.goods_id = o.goods_id;
        self.contract_id = o.contract_id;
        self.batch_id = o.batch_id;
        self.unit.subscribe(function(newValue) {
            // updated 方法
            // if(newValue == this.unit) {
            //  this.quantity(this.quantity);
            // } else {
            //  this.quantity(this.quantity_sub);
            // }
            // this.price(0);
            // this.amount(0);
        }, this);
        self.quantity_actual.subscribe(function(newValue) {
            // updated 方法
            if(newValue > 0 && this.price() > 0) {
                this.amount((newValue*this.price()).toFixed(2));
            }
            if(this.selectMainUnit()) {
                this.quantity_actual_sub((newValue / o.unit_rate).toFixed(2));
            }
        }, this);
        self.quantity_actual_sub.subscribe(function(newValue) {
            // updated 方法
            if(newValue > 0 && this.price_sub() > 0) {
                this.amount((newValue*this.price_sub()).toFixed(2));
            }
            if(!this.selectMainUnit()) {
                this.quantity_actual((newValue * o.unit_rate).toFixed(2));
            }
        }, this);
        self.price.subscribe(function(newValue) {
            // updated 方法
            if(newValue > 0 && this.quantity_actual() > 0) {
                this.amount((newValue*this.quantity_actual()).toFixed(2));
            }
            if(this.selectMainUnit()) {
                this.price_sub((newValue * o.unit_rate).toFixed(2));
            }
        }, this);
        self.price_sub.subscribe(function(newValue) {
            // updated 方法
            if(newValue > 0 && this.quantity_actual_sub() > 0) {
                this.amount((newValue*this.quantity_actual_sub()).toFixed(2));
            }
            if(!this.selectMainUnit()) {
                this.price((newValue / o.unit_rate).toFixed(2));
            }
        }, this);
        self.quantity_loss.subscribe(function(newValue) {
            // updated 方法
            if(this.selectMainUnit()) {
                this.quantity_loss_sub((newValue*o.unit_rate).toFixed(2));
            }
        }, this);
        self.quantity_loss_sub.subscribe(function(newValue) {
            // updated 方法
            if(!this.selectMainUnit()) {
                this.quantity_loss((newValue/o.unit_rate).toFixed(2));
            }
        }, this);
        self.openDialog = function() {
            var contract_id = self.contract_id, goods_id = self.goods_id, batch_id = self.batch_id;
            $.ajax({
                data: {
                    contract_id:contract_id,
                    goods_id:goods_id,
                    batch_id:batch_id
                },
                url:'/stockBatchSettlement/ajaxGetBuyLockList',
                method:'post',
                success:function(res) {
                    $("#buy_lock_dialog_body").html(res);
                    $("#buy_lock_dialog").modal("show");
                },
                error:function(res) {
                    $("#buy_lock_dialog").modal("show");
                }
            });
        }
    }

    function stockBatchSettlementComponent(params) {
        var self = this;
        self.allGoods = params.allGoods;
        self.allStorehouses = params.allStorehouses;
        self.units = params.units;
        self.model = params.model;
    }
</script>
