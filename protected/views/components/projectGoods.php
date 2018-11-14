<template id='component-template-project-goods'>
    <table class="table table-hover table-hover-custom">
        <thead>
        <tr>
            <th style="width:180px;"><span class="label-custom__span-red">*</span>品名</th>
            <!-- <th style="width:120px; text-align: left;">规格</th> -->
            <th style="width:100px;"><span class="label-custom__span-red">*</span>数量</th>
            <th style="width:100px;"><span class="label-custom__span-red">*</span>单位</th>
            <!-- ko if: isShowPurchasePrice -->
            <th style="width:140px;"><span class="label-custom__span-red">*</span>采购单价</th>
            <!-- /ko -->
            <!-- ko if: isShowSalePrice -->
            <th style="width:140px;"><span class="label-custom__span-red">*</span>销售单价</th>
            <!-- /ko -->
            <!-- ko if: isShowPurchasePrice -->
            <th style="width:160px;">采购总价</th>
            <!-- /ko -->
            <!-- ko if: isShowSalePrice -->
            <th style="width:160px; ">销售总价</th>
            <!-- /ko -->
            <th style="width:100px; ">操作
                <!-- <button class="btn btn-success btn-xs" data-bind="click:add">新增</button> -->
            </th>
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
    </table>
    <div class="add-btn-container">
		<button class="btn  add-btn-custom" data-bind="click:add">+新增商品</button>
  	</div>
</template>
<template id='component-template-project-goods-item'>
    <tr data-bind="with:model">
        <td>
            <select class="form-control input-sm" title="请选择品名" name=""
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'goods_id',
                    options:$parent.allGoods,
                    selectpicker:goods_id,
                    valueAllowUnset: true">
            </select>
        </td>
        <!-- <td>
            <input type="text" class="form-control input-sm" name="" placeholder="规格" data-bind="value:goods_describe">
        </td> -->
        <td>
            <input type="text" class="form-control input-sm" name="" placeholder="数量" data-bind="value:quantity">
        </td>
        <td>
            <select class="form-control input-sm" title="单位" name=""
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:$parent.units,
                    value:unit">
            </select>
        </td>
        <!-- ko if: isShowPurchasePrice -->
        <td>
            <div class="input-group">
                <span class="input-group-addon" data-bind="text:purchase_currency_ico"></span>
                <input type="text" class="form-control input-sm" name="" placeholder="采购单价" data-bind="money:purchase_price">
            </div>
        </td>
        <!-- /ko -->
        <!-- ko if: isShowSalePrice -->
        <td>
            <div class="input-group">
                <span class="input-group-addon" data-bind="text:sell_currency_ico"></span>
                <input type="text" class="form-control input-sm" name="" placeholder="销售单价" data-bind="money:sale_price">
            </div>
        </td>
        <!-- /ko -->
        <!-- ko if: isShowPurchasePrice -->
        <td style="text-align: right;vertical-align: middle!important;">
            <span data-bind="text:purchase_currency_ico"></span>
            <span data-bind="moneyText:purchase_amount"></span>
            <input type='hidden' name='' data-bind="value:purchase_amount"/>
        </td>
        <!-- /ko -->
        <!-- ko if: isShowSalePrice -->
        <td style="text-align: right;vertical-align: middle!important;">
            <span data-bind="text:sell_currency_ico"></span>
            <span data-bind="moneyText:sale_amount"></span>
            <input type='hidden' name='' data-bind="value:sale_amount"/>
        </td>
        <!-- /ko -->
        <td style="text-align: center;">
          <button class="btn btn-xs delete-btn-custom" data-bind="click:$parent.del">删除</button>
        </td>
        <!-- <td>
            <button class="btn btn-danger btn-xs" data-bind="click:$parent.del">删除</button>
        </td> -->
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
			unit: 0,
			purchase_price: 0,
			sale_price: 0,
			purchase_currency: 1,
			sell_currency: 1,
			type: 0,
			buy_sell_type: 0,
			up_partner_id: 0,
			down_partner_id: 0,
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
		self.quantity = ko.observable(o.quantity).extend({positiveNumber: true});
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
