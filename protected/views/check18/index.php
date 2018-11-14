<?php

/**
 * Desc: 库存盘点审核列表
 * User: susiehuang
 * Date: 2017/11/17 0013
 * Time: 16:56
 */
//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'd.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'e.name*', 'text' => '仓库'),
        array('type' => 'text', 'key' => 'f.name*', 'text' => '品名'),
        array('type' => 'text', 'key' => 'b.inventory_id', 'text' => '盘点编号'),
        array('type' => 'select', 'key' => 'checkStatus', 'noAll' => '1', 'map_name' => 'business_flow_check_status', 'text' => '审核状态'),
    )
);

//列表显示
$array = array(
    array('key' => 'detail_id', 'type' => 'href', 'style' => 'width:60px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowActions'),
    array('key' => 'detail_id', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '审核编号'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'store_id,store_name', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '仓库名称', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/storehouse/detail/?store_id={1}&t=1">{2}</a>'),
    array('key' => 'goods_name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '品名'),
    array('key' => 'inventory_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '盘点编号', 'href_text' => 'showStockInventoryLink'),
    array('key' => 'inventory_date', 'type' => '', 'style' => 'width:100px;text-align:center', 'text' => '盘点日期'),
    array('key' => 'inventory_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '盘点证明', 'href_text' => 'showStockInventoryAttach'),
    array('key' => 'inventory_id', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '盘点前可用库存', 'href_text' => 'showQuantityActive'),
    array('key' => 'inventory_id', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '冻结库存', 'href_text' => 'showQuantityFrozen'),
    array('key' => 'inventory_id', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '盘点前库存', 'href_text' => 'showQuantityBefore'),
    array('key' => 'inventory_id', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '库存损耗', 'href_text' => 'showQuantityDiff'),
    array('key' => 'inventory_id', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '盘点后库存', 'href_text' => 'showQuantity'),
    array('key' => 'inventory_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '损耗分摊', 'href_text' => 'showStockInventoryDetail'),
    array('key' => 'detail_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '审核状态', 'href_text' => 'FlowService::showCheckStatus'),
);

function checkRowActions($row, $self) {
    $links = array();
    if ($row["isCanCheck"]) {
        $links[] = '<a href="/' . $self->getId() . '/check?id=' . $row["obj_id"] . '" title="审核">审核</a>';
    } else {
        $links[] = '<a href="/' . $self->getId() . '/detail?detail_id=' . $row["detail_id"] . '" title="查看详情">查看</a>';
    }
    $s = implode("&nbsp;|&nbsp;", $links);

    return $s;
}

function showStockInventoryLink($row) {
    return '<a target="_blank" id="t_{1}" title="{1}" href="/stockInventory/detail/?corp_id='.$row['corporation_id'].'&store_id='.$row['store_id'].'&goods_id='.$row['goods_id'].'&unit='.$row['unit'].'&t=1">{1}</a>';
}

function showQuantityActive($row) {
    return Utility::numberFormatToDecimal($row['quantity_active'], 4) . Map::$v['goods_unit'][$row['unit']]['name'];
}

function showQuantityFrozen($row) {
    return Utility::numberFormatToDecimal($row['quantity_frozen'], 4) . Map::$v['goods_unit'][$row['unit']]['name'];
}

function showQuantityBefore($row) {
    return Utility::numberFormatToDecimal($row['quantity_before'], 4) . Map::$v['goods_unit'][$row['unit']]['name'];
}

function showQuantityDiff($row) {
    return Utility::numberFormatToDecimal($row['quantity_diff'], 4) . Map::$v['goods_unit'][$row['unit']]['name'];
}

function showQuantity($row) {
    return Utility::numberFormatToDecimal($row['quantity'], 4) . Map::$v['goods_unit'][$row['unit']]['name'];
}

function showStockInventoryDetail($row) {
    return '<a title="损耗分摊明细" data-bind="click:function(){viewStockInventoryDetail(' . $row['goods_detail_id'] . ',' . $row['unit'] . ')}">查看</a>';
}

function showStockInventoryAttach($row, $self) {
    $attachments = StockInventoryService::getAttachments($row['inventory_id']);
    $attach = $attachments[ConstantMap::STOCK_INVENTORY_ATTACH_TYPE];
    if (Utility::isNotEmpty($attach)) {
        return "<a href='/stockInventory/getFile/?id=" . $attach[0]['base_id'] . "&fileName=" . $attach[0]['name'] . "' target='_blank' class='btn btn-primary btn-xs'>点击查看</a>";
    }

    return '无';
}

$style = empty($_data_[data]['rows']) ? "min-width:1050px;" : "min-width:1650px;";

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", $style, "table-bordered table-layout");
?>
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
                                <td style='text-align:right;' data-bind="numberFixedText:quantity_diff,attr:{title:quantity_diff}" class="text-red"></td>
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
<script>
	var view;
	$(function () {
		view = new paymentModel();
		ko.applyBindings(view);
		view.units = inc.objectToArray(<?php echo json_encode(array_values(Map::$v['goods_unit'])) ?>);
	})
	function paymentModel() {
		var self = this;
		self.detail = ko.observableArray();
		self.units = [];
		self.unit = ko.observable(0);
		self.unit_desc = ko.observable();

		self.viewStockInventoryDetail = function (goods_detail_id, unit) {
			self.unit(unit);
			$.ajax({
				type: "POST",
				url: "/stockInventory/getStockInventoryDetail",
				data: {goods_detail_id: goods_detail_id},
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						if (Array.isArray(json.data) && json.data.length > 0) {
							ko.utils.arrayForEach(json.data, function (item, i) {
								json.data[i].stock_in_link = '<a target="_blank" title="' + item.stock_in_code + '" href="/stockInList/view/?id=' + item.stock_in_id + '&t=1">' + item.stock_in_code + '</a>';
								json.data[i].quantity_active_after = (item.quantity_active - item.quantity_diff).toFixed(2);
							});
						}
						self.detail(json.data);
						$('#stockInventoryDetailModal').modal({
							backdrop: true,
							keyboard: false,
							show: true
						});
					} else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("获取损耗分摊明细失败！" + data.responseText, {icon: 2});
				}
			});
		}

		self.unit.subscribe(function (v) {
			if (Array.isArray(self.units) && self.units.length > 0) {
				ko.utils.arrayForEach(self.units, function (item, i) {
					if (v == item.id) {
						self.unit_desc(item.name);
					}
				});
			}
		});
	}
</script>
