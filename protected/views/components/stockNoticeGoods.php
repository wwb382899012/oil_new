<template id='component-template-stock-notice-goods'>
    <table class="table table-hover table-validate">
        <thead>
        <tr>
            <th style="width:200px;">品名 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:150px; text-align: left;">入库通知单数量 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:150px; text-align: left;">单位 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:100px; text-align: left;">换算比例</th>
            <th style="width:200px; text-align: left;">仓库 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:200px; text-align: left;">备注</span></th>
            <th style="text-align: left;">
                <button class="btn btn-success btn-xs" data-bind="click:add,visible:isCanAdd">新增</button>
            </th>
        </tr>
        </thead>
        <tbody data-bind="foreach:items">
        <!-- ko component: {
            name: "stock-notice-goods-item",
            params: {
                        model: $data,
                        type:$parent.type,
                        parentItems:$parent.items,
                        allGoods: $parent.allGoods,
                        allStorehouses: $parent.allStorehouses,
                        units:$parent.units
                        }
        } -->
        <!-- /ko -->
        </tbody>
    </table>
</template>
<template id='component-template-stock-notice-goods-item'>
    <tr data-bind="with:model">
        <td>
            <select class="form-control selectpicker show-menu-arrow" title="请选择品名"
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'goods_id',
                    options:allValidgoods,
                    value:goods_id">
            </select>
        </td>
        <td>
            <input type="text" class="el-input__inner" placeholder="数量" data-bind="value:quantity">
            <input type="text" class="el-input__inner" placeholder="数量" data-bind=" visible:unit_sub_visible,value:quantity_sub">
        </td>
        <td>
            <select class="form-control selectpicker show-menu-arrow" title="单位"
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:$parent.units,
                    value:unit" disabled>
            </select>
            <select class="form-control selectpicker show-menu-arrow" title="单位"
                    data-bind="
                    visible:unit_sub_visible,
                    enable:unit_sub_enable,
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:$parent.units,
                    value:unit_sub">
            </select>
        </td>
        <td style="text-align: center;">
            <span data-bind="text:unit_rate"></span>
        </td>
        <td>
            <!-- ko if: type() == 1 -->
            <select class="form-control selectpicker show-menu-arrow" title="请选择仓库"
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'store_id',
                    options:$parent.allStorehouses,
                    selectpicker: store_id,
                    valueAllowUnset: true">
            </select>
            <!-- /ko -->
            <!-- ko if: type() == 2 -->
            <span>虚拟库</span>
            <!-- /ko -->
        </td>
        <td>
            <input type="text" class="el-input__inner" placeholder="备注" data-bind="value:remark" />
        </td>
        <td>
            <button class="btn btn-danger btn-xs" data-bind="click:$parent.del">删除</button>
        </td>
    </tr>
</template>

<script>
	ko.components.register('stock-notice-goods-item', {
		template: {element: 'component-template-stock-notice-goods-item'},
		viewModel: stockNoticeGoodsItemComponent
	});

	ko.components.register('stock-notice-goods', {
		template: {element: 'component-template-stock-notice-goods'},
		viewModel: stockNoticeGoodsComponent
	});
	function stockNoticeGoodsComponent(params) {
		var self = this;
		self.allGoods = params.allGoods;
		self.units = params.units;
		self.items = params.items;
		self.unit = params.unit;
		self.contract_id = params.contract_id;
		self.project_id = params.project_id;
		self.allStorehouses = params.allStorehouses;
		self.type = params.type;

		if (self.allGoods().length > 0) {
			ko.utils.arrayForEach(self.allGoods(), function (item, i) {
				self.allGoods()[i].quantity_sub(ko.unwrap(item.quantity_sub));
				if (self.items().length > 0) {
					var obj = ko.utils.arrayFilter(self.items(), function (g) {
						return g.goods_id() == item.goods_id()
					});
					if (obj) {
						self.allGoods()[i].quantity(ko.unwrap(obj[0].quantity));
						self.allGoods()[i].quantity_sub(ko.unwrap(obj[0].quantity_sub));
						self.allGoods()[i].store_id(ko.unwrap(obj[0].store_id));
						self.allGoods()[i].remark(ko.unwrap(obj[0].remark));
					}
				}
			});
		}

		self.add = function () {
			var obj = new StockNoticeGoods({
				allGoods: self.allGoods,
				unit: self.unit,
				contract_id: self.contract_id,
				project_id: self.project_id
			});
			self.items.push(obj);
		}

		self.isCanAdd = ko.computed(function () {
			return self.items().length < self.allGoods().length;
		}, self);
	}

	function StockNoticeGoods(option) {
		var defaults = {
			type: 0,
			detail_id: 0,
			contract_id: 0,
			project_id: 0,
			batch_id: 0,
			store_id: 0,
			goods_id: 0,
			goods_describe: '',
			quantity_sub: 0,
			unit_sub: 0,
			quantity: 0,
			unit: 1,
			unit_rate: 1,
			remark: ''
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.type = ko.observable(o.type);
		self.detail_id = ko.observable(o.detail_id);
		self.contract_id = ko.observable(o.contract_id);
		self.project_id = ko.observable(o.project_id);
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
		self.goods_describe = ko.observable(o.goods_describe);
		self.unit_sub = ko.observable(o.unit_sub);
		self.quantity_sub = ko.observable(o.quantity_sub).extend({
			custom: {
				params: function (v) {
					if (v && self.unit_sub() > 0) {
						return v > 0;
					}
					return true;
				},
				message: "请输入一个大于0的数字"
			}
		});

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
		self.unit_rate = ko.computed(function () {
			if (self.unit() != self.unit_sub() && self.quantity_sub() != 0) {
				return (self.quantity() / self.quantity_sub()).toFixed(4);
			}
			return 1.0000;
		}, self);
		if (o.allGoods)
			self.allGoods = o.allGoods;
		else
			self.allGoods = ko.observableArray();


		self.remark = ko.observable(o.remark);

		self.setGoodsStatus = function (v, goods_id) {
			if (goods_id == null) {
				goods_id = ko.unwrap(self.goods_id);
			}
			var obj = ko.utils.arrayFirst(self.allGoods(), function (item, index) {
				return ko.unwrap(item.goods_id) == goods_id;
			});
			if (obj)
				obj.status(v);
		}

		self.setGoodsStatus(1);

		self.allValidgoods = ko.computed(function () {
			var gs = ko.utils.arrayFilter(self.allGoods(), function (g) {
				return (g.goods_id() == self.goods_id() || g.status() == 0)
			});
			return gs;
		});

		self.goods = ko.computed(function () {
			return ko.utils.arrayFirst(self.allGoods(), function (item, index) {
				return ko.unwrap(item.goods_id) == ko.unwrap(self.goods_id);
			});
		});

		self.unit_sub_enable = ko.computed(function () {
			var g = self.goods();
			if (g != null) {
				var us = g.unit_sub();
				return !(self.goods_id() > 0 && us > 0);
			}
			return false;
		});

		self.unit_sub_visible = ko.computed(function () {
//			return (self.unit_sub_enable() || self.unit() != self.unit_sub());
			return (self.unit_sub_enable() || self.unit() != self.unit_sub() || self.goods().unit() != self.goods().unit_sub());
		}, self);

		self.goods_id.subscribeChanged(function (newVal, oldVal) {
			self.setGoodsStatus(0, oldVal);
			self.setGoodsStatus(1);
			var obj = ko.utils.arrayFirst(self.allGoods(), function (item, index) {
				return ko.unwrap(item.goods_id) == ko.unwrap(self.goods_id);
			});
			self.unit(ko.unwrap(obj.unit));
			self.unit_sub(ko.unwrap(obj.unit_sub));
			self.quantity(ko.unwrap(obj.quantity));
			self.quantity_sub(ko.unwrap(obj.quantity_sub));
			self.store_id(ko.unwrap(obj.store_id));
			self.remark(ko.unwrap(obj.remark));
		});
	}


	function stockNoticeGoodsItemComponent(params) {
		var self = this;
		self.allGoods = params.allGoods;
		self.allStorehouses = params.allStorehouses;
		self.units = params.units;
		self.model = params.model;
		self.model.type(params.type());
		params.type.subscribe(function (v) {
			self.model.type(v);
		});

		self.del = function (data) {
			if (params.parentItems) {
				data.setGoodsStatus(0);
				params.parentItems.remove(data);
			}

		}
	}

	function Goods(option) {
		var defaults = {
			goods_id: 0,
			name: "",
			unit: 0,
			unit_sub: 0,
			quantity: 0,
			quantity_sub: 0,
			unit_rate: 1,
			store_id: 0,
			remark: 0
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.goods_id = ko.observable(o.goods_id);
		self.name = ko.observable(o.name);
		self.unit = ko.observable(o.unit);
		self.unit_sub = ko.observable(o.unit_sub);
		self.status = ko.observable(0);
		self.quantity = ko.observable(o.quantity);
		self.quantity_sub = ko.observable(o.quantity_sub);
		self.unit_rate = ko.observable(o.unit_rate);
		self.store_id = ko.observable(o.store_id);
		self.remark = ko.observable(o.remark);
	}


</script>
