<template id='component-template-stock-batch-settlement'>
    <table class="table table-hover">
        <thead>
        <tr>
            <th style="width:120px;">品名</th>
            <th style="width:120px; text-align: left;">入库通知单数量</th>
            <th style="width:120px; text-align: left;">入库单数量</th>
            <th style="width:140px; text-align: left;">结算单位 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:240px; text-align: left;">结算数量 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:200px; text-align: left;">结算单价</th>
            <th style="width:240px; text-align: left;">结算金额</th>
            <th style="width:160px; text-align: left;">损耗量</th>
            <th style="width:200px; text-align: left;">备注</th>
            <th style="width:200px; text-align: left;">锁价记录</th>
        </tr>
        </thead>
        <tbody data-bind="foreach:goods">
        <!-- ko component: {
            name: "stock-batch-settlement-item",
            params: {
                        model: $data
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
            <p class="form-control-static" data-bind="text:batch_quantity() + batch_unit_desc()"></p>
            <p class="form-control-static" data-bind="visible:!singleUnit(), text:batch_quantity_sub() + batch_unit_sub_desc()"></p>
        </td>
        <td>
            <p class="form-control-static" data-bind="text:stock_in_quantity() + stock_in_unit_desc()"></p>
            <p class="form-control-static" data-bind="visible:!singleUnit(), text:stock_in_quantity_sub() + stock_in_unit_sub_desc()"></p>
        </td>
        <td>
            <select class="form-control" title="单位"
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
            <div class='input-group' data-bind="visible:selectMainUnit">
                <input class="form-control" data-bind="value:quantity">
                <span class="input-group-addon" data-bind="html:unit_desc"></span>
            </div>
            <div class='input-group' data-bind="visible:!selectMainUnit()">
                <p class="form-control-static" data-bind="html:quantity() + unit_desc()">
            </div>
            <div class='input-group' data-bind="visible:!selectMainUnit()">
                <input class="form-control" data-bind="value:quantity_sub">
                <span class="input-group-addon" data-bind="html:unit_sub_desc"></span>
            </div>
            <div class='input-group' data-bind="visible:selectMainUnit">
                <p class="form-control-static" data-bind="html:quantity_sub() + unit_sub_desc()">
            </div>
        </td>
        <td data-bind="visible:singleUnit()">
            <div class='input-group'>
                <input class="form-control" data-bind="value:quantity">
                <span class="input-group-addon" data-bind="html:unit_desc"></span>
            </div>
        </td>
        <!--单价-->
        <td data-bind="visible:!singleUnit()">
            <div class='input-group' data-bind="visible:selectMainUnit()">
                <input class="form-control" data-bind="money:price">
                <span class="input-group-addon" data-bind="html:price_unit_str"></span>
            </div>
            <div class='input-group' data-bind="visible:!selectMainUnit()">
                <p class="form-control-static" data-bind="text:price()/100 + '' + price_unit_str">
                    <span class="input-group-addon" data-bind="text:unit_desc"></span>
            </div>
            <div class='input-group' data-bind="visible:!selectMainUnit()">
                <input class="form-control" data-bind="money:price_sub">
                <span class="input-group-addon" data-bind="text:price_unit_sub_str"></span>
            </div>
            <div class='input-group' data-bind="visible:selectMainUnit()">
                <p class="form-control-static" data-bind="text:price_sub()/100 + '' + price_unit_sub_str">
            </div>
        </td>
        <td data-bind="visible:singleUnit()">
            <div class='input-group'>
                <input class="form-control" data-bind="money:price">
                <span class="input-group-addon" data-bind="text:price_unit_sub_str"></span>
            </div>
        </td>
        <!-- 结算金额 -->
        <td>
            <div class='input-group'>
                <span class="input-group-addon" data-bind="text:currency_str"></span>
                <input type="text" class="form-control" placeholder="结算金额" data-bind="money:amount">
            </div>
        </td>
        <!--损耗-->
        <td data-bind="visible:!singleUnit()">
            <div class='input-group'>
                <p class="form-control-static" data-bind="text:quantity_loss() + unit_desc()">
            </div>
            <div class='input-group'>
                <p class="form-control-static" data-bind="text:quantity_loss_sub() + unit_sub_desc()">
            </div>
            <!--<div class='input-group' data-bind="visible:selectMainUnit()">
                <input readonly="readonly" class="form-control" data-bind="value:quantity_loss">
                <span class="input-group-addon" data-bind="text:unit_desc"></span>
            </div>
            <div class='input-group' data-bind="visible:!selectMainUnit()">
                <p class="form-control-static" data-bind="text:quantity_loss() + unit_desc()">
                    <span class="input-group-addon" data-bind="text:unit_desc"></span>
            </div>
            <div class='input-group' data-bind="visible:!selectMainUnit()">
                <input readonly="readonly" class="form-control" data-bind="value:quantity_loss_sub">
                <span class="input-group-addon" data-bind="text:unit_sub_desc"></span>
            </div>
            <div class='input-group' data-bind="visible:selectMainUnit()">
                <p class="form-control-static" data-bind="text:quantity_loss_sub() + unit_sub_desc()">
            </div>-->
        </td>
        <td data-bind="visible:singleUnit()">
            <div class='input-group'>
                <p class="form-control-static" data-bind="text:quantity_loss() + unit_desc()">
            </div>
            <!--<div class='input-group'>
                <input readonly="readonly" class="form-control" data-bind="value:quantity_loss">
                <span class="input-group-addon" data-bind="text:unit_desc"></span>
            </div>-->
        </td>
        <td>
            <input type="text" class="form-control" placeholder="备注" data-bind="value:remark">
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
            <div class="modal-header">
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
			goods: []
		}
		o = $.extend(o, params);
		var self = this;
		self.goods = o.goods;
	}

	function StockSettlementGoods(option) {
		var o = {
			settle_id: 0,
			detail_id: 0,
			project_id: 0,
			contract_id: 0,
			goods_id: 0,
			goods_name: '',
			batch_id: 0,
			quantity: 0,
			quantity_sub: 0,
			unit_rate: 0,
			amount_cny: 0,
			remark: '',
			quantity_loss: 0,
			quantity_loss_sub: 0,
			price: 0,
			price_sub: 0,
			unit: 0,
			unit_sub: 0,
			unit_settle: 0,
			currency: 1,
			remark: ''
		}
		o = $.extend(o, option);
		var self = this;
		self.settle_id = ko.observable(o.settle_id);
		self.batch_id = ko.observable(o.batch_id);
		self.detail_id = ko.observable(o.detail_id);
		self.project_id = ko.observable(o.project_id);
		self.contract_id = ko.observable(o.contract_id);
		self.unit_rate = ko.observable(o.unit_rate);
		self.price = ko.observable(o.price);
		self.price_sub = ko.observable(o.price_sub);
		self.amount_cny = ko.observable(o.amount_cny);
		self.exchange_rate = ko.observable(o.exchange_rate);
		self.currency = ko.observable(o.currency);
		self.quantity_loss = ko.observable(parseFloat(o.quantity_loss));
		self.quantity_loss_sub = ko.observable(parseFloat(o.quantity_loss_sub));
		self.goods_id = ko.observable(o.goods_id);
		self.goods_name = ko.observable(o.goods_name);
		self.unit = ko.observable(o.unit);
		self.unit_sub = ko.observable(o.unit_sub);
		self.init_unit = ko.observable(o.unit);
		self.unit_settle = ko.observable(o.unit_settle);
		self.remark = ko.observable(o.remark);
		self.units = ko.observableArray(inc.objectToArray(o.units));
		self.currencies = o.currencies;
		self.singleUnit = ko.observable(o.singleUnit);
		self.unit_desc = ko.observable(o.unit_desc);
		self.unit_sub_desc = ko.observable(o.unit_sub_desc);
		self.batch_quantity = ko.observable(parseFloat(o.batch_quantity));
		self.batch_unit_desc = ko.observable(o.batch_unit_desc);
		self.batch_quantity_sub = ko.observable(parseFloat(o.batch_quantity_sub));
		self.batch_unit_sub_desc = ko.observable(o.batch_unit_sub_desc);
		self.batch_unit = ko.observable(o.batch_unit);
		self.batch_unit_sub = ko.observable(o.batch_unit_sub);
		self.stock_in_quantity = ko.observable(parseFloat(o.stock_in_quantity));
		self.stock_in_unit_desc = ko.observable(o.stock_in_unit_desc);
		self.stock_in_quantity_sub = ko.observable(parseFloat(o.stock_in_quantity_sub));
		self.stock_in_unit_sub_desc = ko.observable(o.stock_in_unit_sub_desc);
		self.price_unit_str = self.currencies[o.currency]['ico'] + '/' + self.unit_desc();
		self.price_unit_sub_str = self.currencies[o.currency]['ico'] + '/' + self.unit_sub_desc();
		self.selectMainUnit = ko.computed(function () {
			return self.unit() != self.unit_sub();
		}, self);
		self.quantity = ko.observable(parseFloat(o.quantity)).extend({
			custom: {
				params: function (v) {
					if ((!self.singleUnit() && self.selectMainUnit()) || self.singleUnit()) {
						if (v <= 0) {
							return false;
						}
						return true;
					}
					return true;
				},
				message: "请输入一个大于0的数字"
			}
		});

		self.quantity_sub = ko.observable(parseFloat(o.quantity_sub)).extend({
			custom: {
				params: function (v) {
					if (!self.singleUnit() && !self.selectMainUnit()) {
						if (v <= 0) {
							return false;
						}
						return true;
					}
					return true;
				},
				message: "请输入一个大于0的数字"
			}
		});

		self.currency_str = ko.computed(function () {
			return self.currencies[self.currency()]['ico'];
		}, self);

		self.quantity.subscribe(function (v) {
			self.quantity_loss((parseFloat(v) - parseFloat(self.stock_in_quantity())).toFixed(4));
			self.quantity_sub((parseFloat(v) / parseFloat(self.unit_rate())).toFixed(4));
		}, self);
		self.quantity_sub.subscribe(function (v) {
			self.quantity_loss_sub(((parseFloat(v) - parseFloat(self.stock_in_quantity())) / parseFloat(self.unit_rate())).toFixed(4));
			self.quantity((parseFloat(v) * parseFloat(self.unit_rate())).toFixed(4));
		}, self);
		self.price.subscribe(function (v) {
			self.price_sub((v * self.unit_rate()).toFixed(2));
		}, self);
		self.price_sub.subscribe(function (v) {
			self.price((v / self.unit_rate()).toFixed(2));
		}, self);
		self.quantity_loss.subscribe(function (v) {
			self.quantity_loss_sub((parseFloat(v) / parseFloat(self.unit_rate())).toFixed(4));
		}, self);
		self.quantity_loss_sub.subscribe(function (v) {
			self.quantity_loss((parseFloat(v) * parseFloat(self.unit_rate())).toFixed(4));
		}, self);

		self.amount = ko.computed(function () {
			return (parseFloat(self.price()) * parseFloat(self.quantity())).toFixed(0);
		});

		self.amount_cny = ko.computed(function () {
			return (parseFloat(self.amount()) * parseFloat(self.exchange_rate())).toFixed(0);
		})

		self.amount_sub = ko.computed(function () {
			return (parseFloat(self.price_sub()) * parseFloat(self.quantity_sub())).toFixed(0);
		});

		self.openDialog = function () {
			$.ajax({
				data: {
					detail_id: self.detail_id()
				},
				url: '/stockBatchSettlement/ajaxGetBuyLockList',
				method: 'post',
				success: function (res) {
					$("#buy_lock_dialog_body").html(res);
					$("#buy_lock_dialog").modal("show");
				},
				error: function (res) {
					layer.alert("操作失败！" + res.responseText, {icon: 5});
				}
			});
		}
	}

	function stockBatchSettlementComponent(params) {
		var self = this;
		self.model = params.model;
	}
</script>
