<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">商品库存详情</h3>
            <div class="pull-right box-tools">
                <?php if (!$this->isExternal) { ?>
                    <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                <?php } ?>
            </div>
        </div>
        <div class="box-body form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">交易主体</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
                        <a href="/corporation/detail/?id=<?php echo $data['corporation_id'] ?>&t=1"><?php echo Corporation::getCorporationName($data['corporation_id']) ?></a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">品名</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo GoodsService::getSpecialGoodsNames($data['goods_id']) ?></p>
                </div>
            </div>

            <?php
            if (Utility::isNotEmpty($data['details'])) {
                $unit = Map::$v['goods_unit'][$data['unit']]['name'] ?>
                <h4 class="section-title">库存明细</h4>
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                        <tr>
                            <th style="width:200px;text-align:center">仓库名称</th>
                            <th style="width:150px;text-align:right">可用库存/<?php echo $unit ?></th>
                            <th style="width:150px;text-align:right">冻结库存/<?php echo $unit ?></th>
                            <th style="width:120px;text-align:right">当前库存/<?php echo $unit ?></th>
                            <th style="width:80px;text-align:center">入库单明细</th>
                        </tr>
                        </thead>
                        <tbody data-bind="foreach: stockDetails">
                        <tr>
                            <td style="text-align: center" data-bind="html:store_link"></td>
                            <td style="text-align: right" data-bind="numberFixedText:quantity_active"></td>
                            <td style="text-align: right" data-bind="numberFixedText:quantity_frozen"></td>
                            <td style="text-align: right" data-bind="numberFixedText:quantity"></td>
                            <td style="text-align: center"><a data-bind="click:$parent.getStockDetail">查看</a>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="text-align: center;">合计</td>
                            <td style='text-align:right' data-bind="numberFixedText:total_quantity_balance"></td>
                            <td style='text-align:right;' data-bind="numberFixedText:total_quantity_frozen"></td>
                            <td style='text-align:right;' data-bind="numberFixedText:total_stock_quantity"></td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>

                    <div class="modal fade draggable-modal" id="stockDetailModal" tabindex="-1" role="dialog" aria-labelledby="modal">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title"><span data-bind="html:stockModalTitle"></span>&emsp;入库单明细
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <div class="box box-primary">
                                        <div class="box-body" style="overflow: auto;">
                                            <table class="table table-condensed table-hover table-bordered table-layout">
                                                <thead>
                                                <tr>
                                                    <th style='width: 180px;text-align:center;'>入库单</th>
                                                    <th style='width: 120px;text-align:right;'>可用库存/<?php echo $unit ?></th>
                                                    <th style='width: 120px;text-align:right;'>冻结库存/<?php echo $unit ?></th>
                                                    <th style='width: 120px;text-align:right;'>当前库存/<?php echo $unit ?></th>
                                                </tr>
                                                </thead>

                                                <tbody data-bind="foreach: stock_in_detail">
                                                <tr>
                                                    <td style='text-align:center;' data-bind="html:stock_in_link"></td>
                                                    <td style='text-align:right' data-bind="numberFixedText:quantity_active"></td>
                                                    <td style='text-align:right;' data-bind="numberFixedText:quantity_frozen"></td>
                                                    <td style='text-align:right;' data-bind="numberFixedText:quantity_before"></td>
                                                </tr>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td style="text-align: center;">合计</td>
                                                    <td style='text-align:right' data-bind="numberFixedText:sum_quantity_balance"></td>
                                                    <td style='text-align:right;' data-bind="numberFixedText:sum_quantity_frozen"></td>
                                                    <td style='text-align:right;' data-bind="numberFixedText:sum_stock_quantity"></td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">关闭</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="box-footer">
            <?php if (!$this->isExternal) { ?>
                <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
            <?php } ?>
        </div>
    </div>
</section>

<script>
	var view;
	$(function () {
		view = new paymentModel(<?php echo json_encode($data['details']) ?>);
		ko.applyBindings(view);
	})
	function paymentModel(option) {
		var self = this;
		if (option.length > 0) {
			ko.utils.arrayForEach(option, function (item, i) {
				console.log(item);
				if (item.store_name != '' && item.store_name != null) {
					option[i].store_link = '<a target="_blank" href="/storehouse/detail?store_id=' + item.store_id + '&t=1">' + item.store_name + '</a>';
				} else {
					option[i].store_link = '虚拟库';
				}
			})
		}
		self.stockDetails = ko.observableArray(option);
		self.stock_in_detail = ko.observableArray();
		self.corp_name = ko.observable('');
		self.store_name = ko.observable('');
		self.goods_name = ko.observable('');

		self.getStockDetail = function (data) {
			$.ajax({
				type: "POST",
				url: "/stockInventory/getStockDetail",
				data: {
					params: {
						corporationId: data.corporation_id,
						storeId: data.store_id,
						goodsId: data.goods_id,
						unit: data.unit
					}
				},
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						if (Array.isArray(json.data.stock_detail) && json.data.stock_detail.length > 0) {
							ko.utils.arrayForEach(json.data.stock_detail, function (item, i) {
								json.data.stock_detail[i].stock_in_link = '<a target="_blank" title="' + item.stock_in_code + '" href="/stockInList/view/?id=' + item.stock_in_id + '&t=1">' + item.stock_in_code + '</a>';
							});
						}
						self.stock_in_detail(json.data.stock_detail);
						self.corp_name(json.data.corp_name);
						self.store_name(json.data.store_name);
						self.goods_name(json.data.goods_name);
						$('#stockDetailModal').modal({
							backdrop: true,
							keyboard: false,
							show: true
						});
					} else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("获取库存明细失败！" + data.responseText, {icon: 2});
				}
			});
		}

		self.stockModalTitle = ko.computed(function () {
			return self.corp_name() + '-' + self.store_name() + '-' + self.goods_name();
		});

		self.sum_quantity_balance = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.stock_in_detail(), function (item) {
				var value = parseFloat(item.quantity_active);
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(2);
		}, self);

		self.sum_quantity_frozen = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.stock_in_detail(), function (item) {
				var value = parseFloat(item.quantity_frozen);
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(2);
		}, self);

		self.sum_stock_quantity = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.stock_in_detail(), function (item) {
				var value = parseFloat(item.quantity_before);
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(2);
		}, self);

		self.total_quantity_balance = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.stockDetails(), function (item) {
				var value = parseFloat(item.quantity_active);
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(2);
		}, self);

		self.total_quantity_frozen = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.stockDetails(), function (item) {
				var value = parseFloat(item.quantity_frozen);
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(2);
		}, self);

		self.total_stock_quantity = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.stockDetails(), function (item) {
				var value = parseFloat(item.quantity);
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(2);
		}, self);

		self.back = function () {
			location.href = "<?php echo $this->getBackPageUrl() ?>";
		}
	}
</script>