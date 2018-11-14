<template id='component-template-stock-inventory-detail'>
    <table class="table table-hover">
        <thead>
        <tr>
            <th style="width:140px;">入库单编号</th>
            <th style="width:100px; text-align: right;">盘点前库存/<span data-bind="html:unit_desc"></span></th>
            <th style="width:100px; text-align: right;">冻结库存/<span data-bind="html:unit_desc"></span></th>
            <th style="width:100px; text-align: right;">盘点前可用库存/<span data-bind="html:unit_desc"></span></th>
            <th style="width:100px; text-align: left;">库存损耗/<span data-bind="html:unit_desc"></span></th>
            <th style="width:100px; text-align: right;">盘点后可用库存/<span data-bind="html:unit_desc"></span></th>
            <th style="width:100px; text-align: right;">盘点后库存/<span data-bind="html:unit_desc"></span></th>
        </tr>
        </thead>
        <tbody data-bind="foreach:items">
        <!-- ko component: {
            name: "stock-inventory-detail-item",
            params: {
                        model: $data
                        }
        } -->
        <!-- /ko -->
        </tbody>
        <tfoot>
        <tr>
            <td style="text-align: center;">合计</td>
            <td style="text-align: right;" data-bind="html:total_quantity_before"></td>
            <td style="text-align: right;" data-bind="html:total_quantity_frozen"></td>
            <td style="text-align: right;" data-bind="html:total_quantity_active"></td>
            <td class="text-red" style="text-align: right;" data-bind="html:total_quantity_diff"></td>
            <td class="text-red" style="text-align: right;" data-bind="html:total_quantity_active_after"></td>
            <td class="text-red" style="text-align: right;" data-bind="html:total_quantity"></td>
        </tr>
        </tfoot>
    </table>
</template>
<template id='component-template-stock-inventory-detail-item'>
    <tr data-bind="with:model">
        <td data-bind="html:stock_in_link"></td>
        <td style="text-align: right;" data-bind="numberFixedText:quantity_before"></td>
        <td style="text-align: right;" data-bind="numberFixedText:quantity_frozen"></td>
        <td style="text-align: right;" data-bind="numberFixedText:quantity_active"></td>
        <td><input type="text" class="form-control input-sm" placeholder="库存损耗" data-bind="disable:quantity_active()==0,numberFixed:quantity_diff"></td>
        <td style="text-align: right;" data-bind="numberFixedText:quantity_active_after"></td>
        <td style="text-align: right;" data-bind="numberFixedText:quantity"></td>
    </tr>
</template>

<script>
	ko.components.register('stock-inventory-detail-item', {
		template: {element: 'component-template-stock-inventory-detail-item'},
		viewModel: stockInGoodsItemComponent
	});

	ko.components.register('stock-inventory-detail', {
		template: {element: 'component-template-stock-inventory-detail'},
		viewModel: stockInGoodsComponent
	});
	function stockInGoodsComponent(params) {
		var self = this;
		self.items = params.items;
		self.unit_desc = params.unit_desc;

		self.total_quantity_before = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.items(), function (item) {
				var value = parseFloat(ko.unwrap(item.quantity_before));
				if (!isNaN(value) && value !== '') {
					total += value;
				}
			});
			return total.toFixed(4);
		}, self);

		self.total_quantity_frozen = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.items(), function (item) {
				var value = parseFloat(ko.unwrap(item.quantity_frozen));
				if (!isNaN(value) && value !== '') {
					total += value;
				}
			});
			return total.toFixed(4);
		}, self);

		self.total_quantity_active = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.items(), function (item) {
				var value = parseFloat(ko.unwrap(item.quantity_active));
				if (!isNaN(value) && value !== '') {
					total += value;
				}
			});
			return total.toFixed(4);
		}, self);

		self.total_quantity_diff = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.items(), function (item) {
				var value = parseFloat(ko.unwrap(item.quantity_diff));
				if (!isNaN(value) && value !== '') {
					total += value;
				}
			});
			return total.toFixed(4);
		}, self);

		self.total_quantity_active_after = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.items(), function (item) {
				var value = parseFloat(ko.unwrap(item.quantity_active_after));
				if (!isNaN(value) && value !== '') {
					total += value;
				}
			});
			return total.toFixed(4);
		}, self);

		self.total_quantity = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.items(), function (item) {
				var value = parseFloat(ko.unwrap(item.quantity));
				if (!isNaN(value) && value !== '') {
					total += value;
				}
			});
			return total.toFixed(4);
		}, self);
	}

	function StockInventoryDetail(option) {
		var defaults = {
			detail_id: 0,
			inventory_id: 0,
			corporation_id: 0,
			store_id: 0,
			goods_id: 0,
			unit: 0,
			stock_in_id: 0,
			stock_in_code: '',
			type: '',
			quantity_before: 0,
			quantity_diff: 0,
			quantity_active: 0,
			quantity_frozen: 0,
			remark: ''
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.detail_id = ko.observable(o.detail_id);
		self.inventory_id = ko.observable(o.inventory_id);
		self.corporation_id = ko.observable(o.corporation_id);
		self.store_id = ko.observable(o.store_id);
		self.goods_id = ko.observable(o.goods_id);
		self.unit = ko.observable(o.unit);
		self.stock_in_id = ko.observable(o.stock_in_id);
		self.stock_in_code = ko.observable(o.stock_in_code);
		self.type = ko.observable(o.type);
		self.quantity_before = ko.observable(o.quantity_before);
		self.quantity_active = ko.observable(o.quantity_active);
		self.quantity_frozen = ko.observable(o.quantity_frozen);
		self.quantity_diff = ko.observable(o.quantity_diff).extend({
			custom: {
				params: function (v) {
					self.msg = '请输入数字';
					if (isNaN(v) || v === '') {
						return false;
					} else {
						if (parseFloat(v) > parseFloat(self.quantity_active())) {
							self.msg = '库存损耗不能大于可用库存'
							return false;
						}
					}

					return true;
				},
				message: function () {
					return self.msg;
				}
			}
		});
		self.remark = ko.observable(o.remark);

		self.quantity_active_after = ko.computed(function () {
			if (ko.unwrap(self.quantity_diff) !== '' && !isNaN(ko.unwrap(self.quantity_diff))) {
				return (parseFloat(ko.unwrap(self.quantity_active)) - parseFloat(ko.unwrap(self.quantity_diff))).toFixed(4);
			} else {
				return ko.unwrap(self.quantity_active);
			}
		}, self);

		self.quantity = ko.computed(function () {
			if (ko.unwrap(self.quantity_diff) !== '' && !isNaN(ko.unwrap(self.quantity_diff))) {
				return (parseFloat(ko.unwrap(self.quantity_before)) - parseFloat(ko.unwrap(self.quantity_diff))).toFixed(4);
			} else {
				return ko.unwrap(self.quantity_before);
			}
		});

		self.stock_in_link = ko.computed(function () {
			return '<a target="_blank" title="' + self.stock_in_code() + '" href="/stockInList/view/?id=' + self.stock_in_id() + '&t=1">' + self.stock_in_code() + '</a>';
		}, self);
	}

	function stockInGoodsItemComponent(params) {
		var self = this;
		self.model = params.model;
	}
</script>
