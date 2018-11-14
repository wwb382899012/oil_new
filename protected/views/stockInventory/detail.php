<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">库存盘点详情</h3>
            <div class="pull-right box-tools">
                <?php if (!$this->isExternal) { ?>
                    <button type="button" class="btn btn-default" onclick="back()">返回</button>
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
                <label class="col-sm-2 control-label">仓库</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
                        <a href="/storehouse/detail/?store_id=<?php echo $data['store_id'] ?>&t=1"><?php echo StorehouseService::getStoreName($data['store_id']) ?></a>
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
            if (Utility::isNotEmpty($stockInventorys)) { ?>
                <h4 class="section-title">历史盘点信息</h4>
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                        <tr>
                            <th style="width:100px;text-align:center">盘点编号</th>
                            <th style="width:100px;text-align:center">盘点日期</th>
                            <th style="width:80px;text-align:center">盘点证明</th>
                            <th style="width:150px;text-align:right">盘点前可用库存/<span data-bind="html:unit_desc"></span>
                            </th>
                            <th style="width:150px;text-align:right">冻结库存/<span data-bind="html:unit_desc"></span>
                            </th>
                            <th style="width:120px;text-align:right">盘点前库存/<span data-bind="html:unit_desc"></span></th>
                            <th style="width:120px;text-align:right">盘点后库存/<span data-bind="html:unit_desc"></span></th>
                            <th style="width:100px;text-align:right">库存损耗/<span data-bind="html:unit_desc"></span></th>
                            <th style="width:80px;text-align:center">损耗分摊</th>
                            <th style="width:80px;text-align:center">状态</th>
                            <th style="width:100px;text-align:center">操作</th>
                        </tr>
                        </thead>
                        <tbody data-bind="foreach: stockInventorys">
                        <tr>
                            <td style="text-align: center" data-bind="html:inventory_id"></td>
                            <td style="text-align: center" data-bind="html:inventory_date"></td>
                            <td style="text-align: center" data-bind="html:attach_link"></td>
                            <td style="text-align: right" data-bind="html:quantity_active"></td>
                            <td style="text-align: right" data-bind="html:quantity_frozen"></td>
                            <td style="text-align: right" data-bind="html:quantity_before"></td>
                            <td style="text-align: right" data-bind="html:quantity"></td>
                            <td style="text-align: right" data-bind="html:quantity_diff"></td>
                            <td style="text-align: center"><a data-bind="click:$parent.viewStockInventoryDetail">查看</a>
                            </td>
                            <td style="text-align: center" data-bind="html:status_desc"></td>
                            <td style="text-align: center">
                                <!-- ko if: status < 10 -->
                                <a data-bind="click:$parent.edit">修改</a>
                                <a data-bind="click:$parent.submit">提交</a>
                                <!-- /ko -->
                                <!-- ko if: status == 20 || status == -1 -->
                                <span data-bind="html:check_detail_link"></span>
                                <!-- /ko -->
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="modal fade draggable-modal" id="stockInventoryDetailModal" tabindex="-1" role="dialog" aria-labelledby="modal">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title" id="partnerRetrieve">损耗分摊明细</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="box box-primary">
                                        <div class="box-body" style="overflow: auto;">
                                            <table style="min-width: 900px;" class="table table-condensed table-hover table-bordered table-layout">
                                                <thead>
                                                <tr>
                                                    <th style='width:160px;text-align:center;'>入库单编号</th>
                                                    <th style="width:120px;text-align:right">盘点前可用库存/<span data-bind="html:unit_desc"></span>
                                                    </th>
                                                    <th style="width:120px;text-align:right">冻结库存/<span data-bind="html:unit_desc"></span>
                                                    </th>
                                                    <th style="width:120px;text-align:right">盘点前库存/<span data-bind="html:unit_desc"></span>
                                                    </th>
                                                    <th style="width:100px;text-align:right">库存损耗/<span data-bind="html:unit_desc"></span>
                                                    </th>
                                                    <th style="width:120px;text-align:right">盘点后可用库存/<span data-bind="html:unit_desc"></span>
                                                    </th>
                                                    <th style="width:120px;text-align:right">盘点后库存/<span data-bind="html:unit_desc"></span>
                                                    </th>
                                                </tr>
                                                </thead>

                                                <tbody data-bind="foreach: detail">
                                                <tr>
                                                    <td style='text-align:center;' data-bind="html:stock_in_link,attr:{title:stock_in_link}"></td>
                                                    <td style='text-align:right;' data-bind="numberFixedText:quantity_active,attr:{title:quantity_active}"></td>
                                                    <td style='text-align:right;' data-bind="numberFixedText:quantity_frozen,attr:{title:quantity_frozen}"></td>
                                                    <td style='text-align:right' data-bind="numberFixedText:quantity_before,attr:{title:quantity_before}"></td>
                                                    <td style='text-align:right;' class="text-red" data-bind="numberFixedText:quantity_diff,attr:{title:quantity_diff}"></td>
                                                    <td style='text-align:right;' data-bind="numberFixedText:quantity_active_after,attr:{title:quantity_active_after}"></td>
                                                    <td style='text-align:right;' data-bind="numberFixedText:quantity,attr:{title:quantity}"></td>
                                                </tr>
                                                </tbody>
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
                <button type="button" class="btn btn-default" onclick="back()">返回</button>
            <?php } ?>
        </div>
    </div>
</section>

<script>
	function back() {
		location.href = "<?php echo $this->getBackPageUrl() ?>";
	}

	function edit() {
		location.href = "/pay/edit/?id=<?php echo $apply['apply_id'] ?>";
	}

	var view;
	$(function () {
		view = new paymentModel(<?php echo json_encode($stockInventorys) ?>);
		ko.applyBindings(view);
		view.unit_desc(<?php echo json_encode($data['unit_desc']) ?>);
	})
	function paymentModel(option) {
		var self = this;
		self.stockInventorys = ko.observableArray(option);
		self.detail = ko.observableArray();
		self.unit_desc = ko.observable('');

		if (self.stockInventorys().length > 0) {
			ko.utils.arrayForEach(self.stockInventorys(), function (item, i) {
				if (item.attach != null && inc.objectToArray(item.attach).length > 0) {
					var attach = item.attach[0];
					self.stockInventorys()[i].attach_link = "<a href='/stockInventory/getFile/?id=" + attach.id + "&fileName=" + attach.name + "' target='_blank' class='btn btn-primary btn-xs'>点击查看</a>";
				} else {
					self.stockInventorys()[i].attach_link = "无";
				}
				if (item.detail_id != 0 && item.detail_id != null && item.detail_id != '') {
					self.stockInventorys()[i].check_detail_link = "<a target='_blank' href='/check18/detail/?detail_id=" + item.detail_id + "'>审核详情</a>";
				} else {
					self.stockInventorys()[i].check_detail_link = '';
				}
			})
		}

		self.viewStockInventoryDetail = function (data) {
			var detail = data.detail;
			if ($.isArray(detail) && detail.length > 0) {
				ko.utils.arrayForEach(detail, function (item, i) {
					detail[i].stock_in_link = '<a target="_blank" title="' + item.stock_in_code + '" href="/stockInList/view/?id=' + item.stock_in_id + '&t=1">' + item.stock_in_code + '</a>';
					detail[i].quantity_active_after = (item.quantity_active - item.quantity_diff).toFixed(2);
				})
			}
			self.detail(detail);
			$("#stockInventoryDetailModal").modal({
				backdrop: true,
				keyboard: false,
				show: true
			});
		};

		self.edit = function (data) {
			location.href = '/<?php echo $this->getId() ?>/edit?id=' + data.inventory_id;
		}

		self.submit = function (data) {
			layer.confirm("您确定要提交当前库存盘点信息进入审核吗，该操作不可逆？", {icon: 3, title: '提示'}, function (index) {
				var formData = {id: data.inventory_id};
				$.ajax({
					type: 'POST',
					url: '/<?php echo $this->getId() ?>/submit',
					data: formData,
					dataType: "json",
					success: function (json) {
						if (json.state == 0) {
							layer.msg("操作成功", {icon: 6, time: 1000}, function () {
								location.reload();
							});
						}
						else {
							layer.alert(json.data, {icon: 5});
						}
					},
					error: function (data) {
						layer.alert("操作失败！" + data.responseText, {icon: 5});
					}
				});

				layer.close(index);
			});
		}

		self.viewCheckDetail = function (data) {
			var formData = {inventory_id: data.inventory_id};
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/getInventoryCheckDetailId',
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {

						layer.msg("操作成功", {icon: 6, time: 1000}, function () {
							location.reload();
						});
					}
					else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});

			layer.close(index);
		}
	}
</script>