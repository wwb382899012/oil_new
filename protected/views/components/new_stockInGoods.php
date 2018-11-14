<template id='component-template-stock-in-goods'>
    <table class="table table-hover table-nowrap">
        <thead>
        <tr>
            <th style="width:100px;">品名 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:60px; text-align: left;">仓库计量单位 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:120px; text-align: left;">入库单数量 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:120px; text-align: left;">换算比例</th>
            <th style="width:200px; text-align: left;">备注</th>
        </tr>
        </thead>
        <tbody data-bind="foreach:items">
        <!-- ko component: {
            name: "stock-in-goods-item",
            params: {
                        model: $data
                        }
        } -->
        <!-- /ko -->
        </tbody>
    </table>
</template>
<template id='component-template-stock-in-goods-item'>
    <tr data-bind="with:model">
        <td>
            <span data-bind="text:goods_name"></span>
            <input type="hidden" data-bind="value:goods_id">
        </td>
        <td>
            <select class="form-control selectpicker show-menu-arrow" title="仓库计量单位"
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:units,
                    value:unit">
            </select>
        </td>
        <td style="padding: 10px !important;">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="数量"
                       data-bind="enable:showQuantityInput,numberFixed:quantity">
                <span class="input-group-addon" data-bind="text:unit_desc"></span>
            </div>
            <div class="input-group" data-bind="visible:showUnitRate()" style="margin-top: 10px;">
                <input type="text" class="form-control" placeholder="数量"
                       data-bind="enable:(!showQuantityInput() && showUnitRate()),numberFixed:quantity_sub">
                <span class="input-group-addon" data-bind="text:unit_sub_desc"></span>
            </div>
        </td>
        <td>
            <span data-bind="text:unit_rate,visible:showUnitRate"></span>
        </td>
        <td>
            <input type="text" class="form-control input-sm" placeholder="备注" data-bind="value:remark">
        </td>
    </tr>
</template>

<script>
    ko.components.register('stock-in-goods-item', {
        template: {element: 'component-template-stock-in-goods-item'},
        viewModel: stockInGoodsItemComponent
    });

    ko.components.register('stock-in-goods', {
        template: {element: 'component-template-stock-in-goods'},
        viewModel: stockInGoodsComponent
    });

    function stockInGoodsComponent(params) {
        var self = this;
        self.items = params.items;
    }

    function StockInGoods(option) {
        var defaults = {
            stock_id: 0,
            contract_id: 0,
            project_id: 0,
            store_id: 0,
            detail_id: 0,
            stock_in_id: 0,
            goods_id: 0,
            goods_name: 0,
            batch_id: 0,
            goods_describe: '',
            quantity_sub: 0,
            unit_sub: 1,
            quantity: 0,
            unit: 1,
            unit_rate: 1,
            remark: '',
            type: 0
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.stock_id = ko.observable(o.stock_id);
        self.contract_id = ko.observable(o.contract_id);
        self.project_id = ko.observable(o.project_id);
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
        self.stock_in_id = ko.observable(o.stock_in_id);
        self.batch_id = ko.observable(o.batch_id);
        self.goods_id = ko.observable(o.goods_id);
        self.detail_id = ko.observable(o.detail_id);
        self.goods_name = ko.observable(o.goods_name);
        self.goods_describe = ko.observable(o.goods_describe);
        self.unit_sub = ko.observable(o.unit_sub);
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
        self.unit_rate = ko.observable(o.unit_rate);
        self.stock_unit = ko.observable(o.stock_unit);
        self.units = o.units;
        self.unit_desc = ko.observable(o.unit_desc);
        self.unit_sub_desc = ko.observable(o.unit_sub_desc);
        self.showQuantityInput = ko.computed(function () {
            return self.unit() == self.stock_unit();
        }, self);

        self.showUnitRate = ko.computed(function () {
            return self.stock_unit() != self.unit_sub();
        }, self);
        self.quantity_sub = ko.observable(o.quantity_sub).extend({
            custom: {
                params: function (v) {
                    if (self.showUnitRate() && self.unit() == self.unit_sub()) {
                        if (parseFloat(v) == v && v >= 0)
                            return true;
                        else
                            return false;
                    }
                    return true
                },
                message: "请输入一个不小于0的数字"
            }
        });
        self.quantity = ko.observable(o.quantity).extend({
            custom: {
                params: function (v) {
                    if (self.unit() == self.stock_unit()) {
                        if (parseFloat(v) == v && v >= 0)
                            return true;
                        else
                            return false;
                    }
                    return true
                },
                message: "请输入一个不小于0的数字"
            }
        });
        self.remark = ko.observable(o.remark);

        self.quantity.subscribe(function (v) {
            if (self.showQuantityInput()) {
                self.quantity_sub(parseFloat(v / self.unit_rate()).toFixed(2));
            }
        });

        self.quantity_sub.subscribe(function (v) {
            if (!self.showQuantityInput()) {
                self.quantity(parseFloat(v * self.unit_rate()).toFixed(2));
            }
        });
    }

    function stockInGoodsItemComponent(params) {
        var self = this;
        self.model = params.model;
    }
</script>
