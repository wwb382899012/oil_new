<style>
    .table{
        width:100%;
    }
    .contract-detail-body-tr>td{
        padding-left: 12px !important;
        padding-right: 12px !important;
    }
</style>
<template id='component-template-project-goods'>
    <table class="table table-hover table-hover-custom">
        <thead>
        <tr>
            <th style="width:14%;">品名<i class="must-logo">*</i>
            </th>
            <th style="width:11%; ">数量<i class="must-logo">*</i>单位<i class="must-logo">*</i>
            </th>
<!--            <th style="width:10%;"></i>-->
<!--            </th>-->
            <th style="width:16%;">单位换算比<i class="must-logo">*</i>
            </th>
            <!-- ko if: isShowPurchasePrice -->
            <th style="width:13%; ">采购单价</span><i class="must-logo">*</i>
            </th>
            <!-- /ko -->
            <!-- ko if: isShowSalePrice -->
            <th style="width:13%;">销售单价</span><i class="must-logo">*</i>
            </th>
            <!-- /ko -->
            <!-- ko if: isShowPurchasePrice -->
            <th style="width:16%; ">采购总价</th>
            <!-- /ko -->
            <!-- ko if: isShowSalePrice -->
            <th style="width:16%; ">销售总价</th>
            <!-- /ko -->
            <th style="width:8%; ">操作</th>

        </tr>
        </thead>
        <tbody data-bind="foreach:items">
        <!-- ko component: {
            name: "project-goods-item",
            params: {
                        allGoods: $parent.allGoods,
                        units: $parent.units,
                        model: $data,
                        parentItems: $parent.items,
                        purchase_currency: $parent.purchase_currency,
                        sell_currency: $parent.sell_currency,
                        type:$parent.type,
                        buy_sell_type:$parent.buy_sell_type,
                        up_partner_id:$parent.up_partner_id,
                        down_partner_id:$parent.down_partner_id
                        }
        } -->
        <!-- /ko -->
        </tbody>
        <!-- 此处这么做是为了保证【新增】按钮始终和上面的删除按钮对齐 -->
        <tfoot>
            <tr>
<!--                <td style="width:14%;"></td>-->
                <td style="width:10%;"></td>
                <td style="width:10%;"></td>
                <td style="width:10%;"></td>
                <!-- ko if: isShowPurchasePrice -->
                <td style="width:14%;"></td>
                <!-- /ko -->
                <!-- ko if: isShowSalePrice -->
                <td style="width:14%;"></td>
                <!-- /ko -->
                <!-- ko if: isShowPurchasePrice -->
                <td style="width:14%;"></td>
                <!-- /ko -->
                <!-- ko if: isShowSalePrice -->
                <td style="width:14%;"></td>
                <!-- /ko -->
                <td style="width:10%;">
                    <a data-bind="click:add" href="javascript: void 0" class="oil-btn" style="width:50px;min-width: unset;">新增</a>
                </td>
            </tr>
        </tfoot>
    </table>
</template>
<template id='component-template-project-goods-item'>
    <tr data-bind="with:model" class="contract-detail-body-tr">
        <td>
                <select data-live-search="true" class="selectpicker show-menu-arrow form-control" title="请选择品名" name=""
                        data-bind="
                optionsText: 'name',
                optionsValue: 'goods_id',
                options:$parent.allGoods,
                selectpicker:goods_id,
                valueAllowUnset: true">
                </select>
            
        </td>
        <td>
            <div style="padding: 5px 0;">
                <input style="margin-bottom: 5px"  type="text" class="form-control" name="" placeholder="数量" data-bind="value:quantity" title="0-1亿"
                       data-orig-title="">
                <span class="validationMessage" style="display: none;"></span>
                <select class="selectpicker show-menu-arrow form-control" title="单位" name=""
                        data-bind="
			    optionsText: 'name',
			    optionsValue: 'id',
                selectPickerOptions:$parent.units,
                selectpicker:{value:unit}
			    ">
                </select>
                <span class="validationMessage" style="display: none;"></span>
            </div>

        </td>
        <td>
            <div class="input-group" data-bind="visible: showUnitConvert">
                <span class="input-group-addon" data-bind="text:unit_convert"></span>
                <input type="text" class="form-control input-sm" name="" placeholder="单位换算比" data-bind="value:unit_convert_rate">
            </div>
            <span class="validationMessage" style="display: none;"></span>
        </td>
        <!-- ko if: isShowPurchasePrice -->
        <td>
            <div class="input-group">
                <span class="input-group-addon" data-bind="text:purchase_currency_ico"></span>
                <input type="text" class="form-control" name="" placeholder="采购单价" data-bind="money:purchase_price">
            </div>
            <!-- <span class="form-control">
            <span data-bind="text:purchase_currency_ico"></span>
          <input type="text" class="form-control" name="" placeholder="￥ 0.00" data-bind="money:purchase_price" title="0-1亿"
            data-orig-title="">
            </span> -->
            <span class="validationMessage" style="display: none;"></span>
        </td>
        <!-- /ko -->
        <!-- ko if: isShowSalePrice -->
        <td>
            <div class="input-group">
                <span class="input-group-addon" data-bind="text:sell_currency_ico"></span>
                <input type="text" class="form-control" name="" placeholder="销售单价" data-bind="money:sale_price">
            </div>
            <!-- <input type="text" class="form-control" name="" placeholder="￥ 0.00" data-bind="money:sale_price" title="0-1亿"
              data-orig-title="">
            <span class="validationMessage" style="display: none;"></span> -->
        </td>
        <!-- /ko -->
        <!-- ko if: isShowPurchasePrice -->
        <td>
<!--            <div class="input-group">-->
<!--                <span class="input-group-addon" data-bind="text:purchase_currency_ico"></span>-->
<!--                <input readonly type="text" class="form-control" name="" placeholder="采购总价" data-bind="money:purchase_amount">-->
<!--            </div>-->
<!--             <span class="form-control">-->
                <span data-bind="text:purchase_currency_ico"></span>
                <span  data-bind="moneyText:purchase_amount"></span>
<!--             </span>-->
             <span class="validationMessage" style="display: none;"></span>
        </td>
        <!-- /ko -->
        <!-- ko if: isShowSalePrice -->
        <td>
<!--            <div class="input-group">-->
<!--                <span class="input-group-addon" data-bind="text:sell_currency_ico"></span>-->
<!--                <input readonly type="text" class="form-control" name="" placeholder="销售总价" data-bind="money:sale_amount">-->
<!--            </div>-->
<!--             <span class="form-control">-->
                <span data-bind="text:sell_currency_ico"></span>
                <span   data-bind="moneyText:sale_amount"></span>
<!--            </span>-->
            <!-- <input readonly type="text" class="form-control" name="" placeholder="￥ 0.00" data-bind="value:sale_amount" title="0-1亿"
              data-orig-title=""> -->
            <span class="validationMessage" style="display: none;"></span>
        </td>
        <!-- /ko -->
        <td>
            <a href="javascript: void 0" class="z-btn-action" data-bind="click:$parent.del">删除</a>
        </td>
    </tr>
</template>

<script>
    ko.components.register('project-goods-item', {
        template: {element: 'component-template-project-goods-item'},
        viewModel: projectGoodsItemComponent
    });

    ko.components.register('project-goods', {
        template: {element: 'component-template-project-goods'},
        viewModel: projectGoodsComponent
    });
    function projectGoodsComponent(params) {
        var self = this;
        self.allGoods = params.allGoods;
        self.units = params.units;
        self.currencies = params.currencies;
        self.items = params.items;
        self.purchase_currency = params.purchase_currency;
        self.sell_currency = params.sell_currency;
        self.type = params.type;
        self.buy_sell_type = params.buy_sell_type;
        self.up_partner_id = params.up_partner_id;
        self.down_partner_id = params.down_partner_id;
        self.add = function () {
            var obj = new ProjectGoods({
                currencies: self.currencies,
                purchase_currency: self.purchase_currency(),
                sell_currency: self.sell_currency(),
            });
            self.items.push(obj);
        }

        /*self.purchase_currency_ico = ko.computed(function () {
         return self.currencies[self.purchase_currency()]["ico"];
         }, self);

         self.sell_currency_ico = ko.computed(function () {
         return self.currencies[self.sell_currency()]["ico"];
         }, self);*/

        self.isShowPurchasePrice = ko.computed(function () {
            if (($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) >= 0 && self.buy_sell_type() == config.firstBuyLastSale) || $.inArray(parseInt(self.type()), config.projectTypeSelfSupport) < 0)
                return true;
            return false;
        }, self);

        self.isShowSalePrice = ko.computed(function () {
            if (($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) >= 0 && self.buy_sell_type() == config.firstSaleLastBuy) || $.inArray(parseInt(self.type()), config.projectTypeSelfSupport) < 0)
                return true;
            return false;
        }, self);

        self.showUnitConvert = ko.computed(function () {
            var num=0;
            for(var j in params.items()){
                if(params.items()[j].unit()!=view.contractGoodsUnitConvertValue())
                    num++;
            }
            if(num>0) {
                return true;
            }
            else {
                return false;
            }

        },self);
    }

    function projectGoodsItemComponent(params) {
        var self = this;
        self.allGoods = params.allGoods;
        self.units = params.units;
        self.model = params.model;
        self.model.purchase_currency(params.purchase_currency());
        self.model.sell_currency(params.sell_currency());
        self.model.type(params.type());
        self.model.buy_sell_type(params.buy_sell_type());
        self.model.up_partner_id(params.up_partner_id());
        self.model.down_partner_id(params.down_partner_id());

        params.purchase_currency.subscribe(function (v) {
            self.model.purchase_currency(v);
        });

        params.sell_currency.subscribe(function (v) {
            self.model.sell_currency(v);
        });

        params.type.subscribe(function (v) {
            self.model.type(v);
        });

        params.buy_sell_type.subscribe(function (v) {
            self.model.buy_sell_type(v);
        });

        params.up_partner_id.subscribe(function (v) {
            self.model.up_partner_id(v);
        });

        params.down_partner_id.subscribe(function (v) {
            self.model.down_partner_id(v);
        });

        self.del = function (data) {
            if (params.parentItems)
                params.parentItems.remove(data);
        };
    }

    function ProjectGoods(option) {
        var defaults = {
            purchase_detail_id: 0,
            sale_detail_id: 0,
            goods_id: 0,
            // goods_describe: "",
            quantity: 0,
            unit: 2,
            purchase_price: 0,
            sale_price: 0,
            purchase_currency: 1,
            sell_currency: 1,
            type: 0,
            buy_sell_type: 0,
            up_partner_id: 0,
            down_partner_id: 0,
            unit_convert_rate:1.0000
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.type = ko.observable(o.type);
        self.buy_sell_type = ko.observable(o.buy_sell_type);
        self.up_partner_id = ko.observable(o.up_partner_id);
        self.down_partner_id = ko.observable(o.down_partner_id);
        self.goods_id = ko.observable(o.goods_id).extend({
            custom: {
                params: function (v) {
                    if (v > 0)
                        return true;
                    else
                        return false;

                },
                message: "请选择品名"
            }
        });
        // self.goods_describe = ko.observable(o.goods_describe);
        self.quantity = ko.observable(o.quantity).extend({positiveNumber: {params: true, message: "0-1亿"}});
        self.unit = ko.observable(o.unit).extend({
            custom: {
                params: function (v) {
                    if (v > 0)
                        return true;
                    else
                        return false;

                },
                message: "请选择单位"
            }
        });
        self.unit.subscribe(function(v){
            if(v==view.contractGoodsUnitConvertValue())
                self.unit_convert_rate('1.0000');
        });
        self.purchase_price = ko.observable(o.purchase_price).extend({
            custom: {
                params: function (v) {
                    if ($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) >= 0 && self.buy_sell_type() == config.firstBuyLastSale) {
                        return parseFloat(v) == v && v > 0;
                    }
                    return true;
                },
                message: "请填写采购单价"
            }
        });
        self.sale_price = ko.observable(o.sale_price).extend({
            custom: {
                params: function (v) {
                    if ($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) >= 0 && self.buy_sell_type() == config.firstSaleLastBuy) {
                        return parseFloat(v) == v && v > 0;
                    }
                    return true;
                },
                message: "请填写销售单价"
            }
        });
        self.purchase_currency = ko.observable(o.purchase_currency);
        self.sell_currency = ko.observable(o.sell_currency);
        self.purchase_amount = ko.computed(function () {
            return (parseFloat(self.purchase_price()) * parseFloat(self.quantity())).toFixed(0);
        }, self);
        self.sale_amount = ko.computed(function () {
            return (parseFloat(self.sale_price()) * parseFloat(self.quantity())).toFixed(0);
        }, self);

        self.currencies = o.currencies;

        self.purchase_currency_ico = ko.computed(function () {
            return self.currencies[self.purchase_currency()]["ico"];
        }, self);

        self.showUnitConvert = ko.computed(function () {
            if(self.unit()== view.contractGoodsUnitConvertValue())
                return false
            else
                return true;
        }, self);

        self.unit_convert = ko.computed(function () {
            for (var i in view.units){
                if(view.units[i].id == self.unit()) {
                    return view.units[i].name + '/' + view.contractGoodsUnitConvert();
                }
            }
        }, self);

        self.unit_convert_rate = ko.observable(o.unit_convert_rate).extend({
            custom: {
                params: function (v) {
                    if (self.showUnitConvert()) {
                        if(v==null || v==''|| v<=0 || isNaN(v))
                            return false;
                    }
                    return true;
                },
                message: "请输入不小于0的数字"
            }
        });

        self.sell_currency_ico = ko.computed(function () {
            return self.currencies[self.sell_currency()]["ico"];
        }, self);

        self.isShowPurchasePrice = ko.computed(function () {
            if (($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) >= 0 && self.buy_sell_type() == config.firstBuyLastSale) || $.inArray(parseInt(self.type()), config.projectTypeSelfSupport) < 0)
                return true;
            return false;
        }, self);

        self.isShowSalePrice = ko.computed(function () {
            if (($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) >= 0 && self.buy_sell_type() == config.firstSaleLastBuy) || $.inArray(parseInt(self.type()), config.projectTypeSelfSupport) < 0)
                return true;
            return false;
        }, self);
    }
</script>
