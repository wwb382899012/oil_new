<?php
/**
 * Desc: 库存盘点列表
 * User: susiehuang
 * Date: 2017/11/14 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'input_array' => array(
       array('type' => 'text', 'key' => 'c.name*', 'text' => '交易主体'),
       array('type' => 'text', 'key' => 's.name*', 'text' => '仓库名称'),
       array('type' => 'text', 'key' => 'g.name*', 'text' => '品名'),
   )
);

//列表显示
$array = array(
    array('key' => 'corporation_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'store_id,store_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '仓库名称', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/storehouse/detail/?store_id={1}&t=1">{2}</a>'),
    array('key' => 'goods_name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '品名'),
    array('key' => 'total_quantity_balance', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '可用库存', 'href_text' => 'showTotalQuantityBalance'),
    array('key' => 'total_quantity_frozen', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '冻结库存', 'href_text' => 'showTotalQuantityFrozen'),
    array('key' => 'total_stock_quantity', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '当前库存', 'href_text' => 'showStockQuantityDetail'),
);

function getRowActions($row, $self) {
    $links = array();
    $newestInventory = StockInventoryService::getNewestCanEditStockInventory($row['corporation_id'], $row['store_id'], $row['goods_id'], $row['unit']);
    if ($newestInventory) {
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $newestInventory . '" title="修改">修改</a>';
    } else {
        if (StockInventoryService::checkIsCanAdd($row['corporation_id'], $row['store_id'], $row['goods_id'], $row['unit'])) {
            $links[] = '<a href="/' . $self->getId() . '/add?corp_id=' . $row["corporation_id"] . '&store_id=' . $row['store_id'] . '&goods_id=' . $row['goods_id'] . '&unit=' . $row['unit'] . '" title="盘点">盘点</a>';
        }
    }
    $links[] = '<a href="/' . $self->getId() . '/detail?corp_id=' . $row["corporation_id"] . '&store_id=' . $row['store_id'] . '&goods_id=' . $row['goods_id'] . '&unit=' . $row['unit'] . '" title="库存盘点详情">详情</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}
function showTotalQuantityBalance($row) {
    return Utility::numberFormatToDecimal($row['total_quantity_balance'], 4).Map::$v['goods_unit'][$row['unit']]['name'];
}

function showTotalQuantityFrozen($row) {
    return Utility::numberFormatToDecimal($row['total_quantity_frozen'], 4).Map::$v['goods_unit'][$row['unit']]['name'];
}

function showStockQuantityDetail($row) {
    return '<a title="库存明细" data-bind="click:function(){getStockDetail('.$row['corporation_id'].',' .$row['store_id']. ','.$row['goods_id'].','.$row['unit'].')}">'.Utility::numberFormatToDecimal(($row['total_quantity_balance'] + $row['total_quantity_frozen']), 4).Map::$v['goods_unit'][$row['unit']]['name'].'</a>';
}

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");
?>
<div class="modal fade draggable-modal" id="stockDetailModal" tabindex="-1" role="dialog" aria-labelledby="modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><span data-bind="html:stockModalTitle"></span>&emsp;库存明细</h4>
            </div>
            <div class="modal-body">
                <div class="box box-primary">
                    <div class="box-body" style="overflow: auto;">
                        <table class="table table-condensed table-hover table-bordered table-layout">
                            <thead>
                            <tr>
                                <th style='width: 180px;text-align:center;'>入库单</th>
                                <th style='width: 120px;text-align:right;'>可用库存/<span data-bind="html:unit_desc"></span></th>
                                <th style='width: 120px;text-align:right;'>冻结库存/<span data-bind="html:unit_desc"></span></th>
                                <th style='width: 120px;text-align:right;'>当前库存/<span data-bind="html:unit_desc"></span></th>
                            </tr>
                            </thead>

                            <tbody data-bind="foreach: stockDetail">
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

<script>
	var view;
	$(function () {
		view = new ViewModel();
		ko.applyBindings(view);
		view.units = inc.objectToArray(<?php echo json_encode(array_values(Map::$v['goods_unit'])) ?>);
	})

	function ViewModel() {
		var self=this;
		self.stockDetail = ko.observableArray();
		self.corp_name = ko.observable('');
		self.store_name = ko.observable('');
		self.goods_name = ko.observable('');
		self.units = [];
		self.unit = ko.observable(0);
		self.unit_desc = ko.observable();

		self.getStockDetail = function (corporationId,storeId,goodsId,unit) {
			self.unit(unit);
			$.ajax({
				type: "POST",
				url: "/<?php echo $this->getId() ?>/getStockDetail",
				data: {
					params:{
                        corporationId: corporationId,
                        storeId: storeId,
                        goodsId: goodsId,
                        unit: unit
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
						self.stockDetail(json.data.stock_detail);
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

        self.unit.subscribe(function (v) {
			if (Array.isArray(self.units) && self.units.length > 0) {
				ko.utils.arrayForEach(self.units, function (item, i) {
					if(v == item.id) {
						self.unit_desc(item.name);
					}
				});
			}
		});

        self.sum_quantity_balance = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.stockDetail(), function(item) {
				var value = parseFloat(item.quantity_active);
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(2);
		}, self);

		self.sum_quantity_frozen = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.stockDetail(), function(item) {
				var value = parseFloat(item.quantity_frozen);
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(2);
		}, self);

		self.sum_stock_quantity = ko.computed(function () {
			var total = 0;
			ko.utils.arrayForEach(self.stockDetail(), function(item) {
				var value = parseFloat(item.quantity_before);
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(2);
		}, self);
	}
</script>