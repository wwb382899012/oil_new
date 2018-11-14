<?php
if($stockNotice->status != StockNotice::STATUS_NEW):
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            入库单通知单结算
        </h3>
        <?php
        if ($this->getId() == 'stockBatchSettlement' &&StockBatchSettlementService::checkIsCanEdit($stockNotice['batch_id'])) { ?>
            <div class="pull-right box-tools">
                <button type="button" class="btn btn-sm btn-primary" onclick="edit(<?php echo $stockNotice['batch_id'] ?>)">修改</button>&nbsp;
                <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo $stockNotice['batch_id'] ?>)">提交</button>&nbsp;
            </div>
        <?php } ?>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <?php
            $this->renderPartial("/components/attachmentsDropdown", array(
                    'id' => $stockNotice['batch_id'],
                    'map_key'=>'stock_batch_settlement_type',
                    'attach_type'=>ConstantMap::STOCK_BATCH_ATTACH_TYPE,
                    'attachment_type'=>Attachment::C_STOCK_BATCH_SETTLEMENT,
                    'controller'=>'stockBatchSettlement',
                )
            );
            ?>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">结算明细 </label>
            <div class="col-sm-offset-1 col-sm-11">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th style="width:120px;">品名 </th>
                        <th style="width:200px; text-align: left;">入库通知单数量 </th>
                        <th style="width:200px; text-align: left;">入库单数量 </th>
                        <th style="width:100px; text-align: left;">结算单位 </th>
                        <th style="width:200px; text-align: left;">结算数量 </th>
                        <th style="width:200px; text-align: left;">结算单价 </th>
                        <th style="width:200px; text-align: left;">结算金额 </th>
                        <th style="width:200px; text-align: left;">损耗量 </th>
                        <th style="width:200px; text-align: left;">备注</th>
                        <th style="width:200px; text-align: left;">查看锁价记录</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php 
                    if(is_array($stockBatchSettlements))
                    foreach ($stockBatchSettlements as $stockBatchSettlement) :
                        $stockBatchQuantityData = StockNoticeService::getTotalStockNoticeQuantity($stockBatchSettlement['batch_id'], $stockBatchSettlement['goods_id'], $stockBatchSettlement['unit']);
                        $quantity = $stockBatchQuantityData['quantity'];
                        $quantity_sub = $stockBatchQuantityData['quantity_sub'];
                        $stockInQuantityData = StockNoticeService::getTotalStockInQuantity($stockBatchSettlement['batch_id'], $stockBatchSettlement['goods_id'], $stockBatchSettlement['unit']);
                        $stock_in_quantity = $stockInQuantityData['quantity'];
                        $stock_in_quantity_sub = $stockInQuantityData['quantity_sub'];
                        /*if(is_array($stockNoticeGoods))
                        foreach ($stockNoticeGoods as $goods) {
                            if($goods['contract_id'] == $stockBatchSettlement['contract_id'] && $goods['goods_id'] == $stockBatchSettlement['goods_id']) {
                                $quantity += $goods['quantity'];
                                if(!empty($goods['sub'])) {
                                    $quantity_sub += $goods['sub']['quantity'];
                                }
                            }
                        }
                        if(Utility::isNotEmpty($stockInDetail)) {
                            foreach ($stockInDetail as $row) {
                                if($row['contract_id'] == $stockBatchSettlement['contract_id'] && $row['goods_id'] == $stockBatchSettlement['goods_id']) {
                                    $stock_in_quantity += $row['quantity'];
                                    if(!empty($row['sub'])) {
                                        $stock_in_quantity_sub += $row['sub']['quantity'];
                                    }
                                }
                            }
                        }*/
                        $isSingleUnit = empty($stockBatchSettlement['sub']);?>
                    <tr>
                        <td>
                            <p class="form-control-static">
                                <?php echo $stockBatchSettlement['goods']['name']?>
                            </p>
                        </td>
                        <td>
                            <p class="form-control-static">
                                <?php echo Utility::numberFormatToDecimal($quantity, 4)?>
                                <?php echo $this->map['goods_unit'][$stockBatchSettlement['unit']]['name']?>
                            </p>
                            <?php if(!$isSingleUnit):?>
                            <p class="form-control-static">
                                <?php echo Utility::numberFormatToDecimal($quantity_sub, 4)?>
                                <?php echo $this->map['goods_unit'][$stockBatchSettlement['sub']['unit']]['name']?>
                            </p>
                            <?php endif;?>
                        </td>
                        <td>
                            <p class="form-control-static">
                                <?php echo Utility::numberFormatToDecimal($stock_in_quantity, 4)?>
                                <?php echo $this->map['goods_unit'][$stockBatchSettlement['unit']]['name']?>
                            </p>
                            <?php if(!$isSingleUnit):?>
                                <p class="form-control-static">
                                    <?php echo Utility::numberFormatToDecimal($stock_in_quantity_sub,4)?>
                                    <?php echo $this->map['goods_unit'][$stockBatchSettlement['sub']['unit']]['name']?>
                                </p>
                            <?php endif;?>
                        </td>
                        <td>
                            <p class="form-control-static">
                                <?php echo $this->map['goods_unit'][$stockBatchSettlement['unit_settle']]['name']?>
                            </p>
                        </td>
                        <td>
                            <p class="form-control-static">
                                <?php echo $stockBatchSettlement['quantity']?>
                                <?php echo $this->map['goods_unit'][$stockBatchSettlement['unit']]['name']?>
                            </p>
                            <?php if(!$isSingleUnit):?>
                            <p class="form-control-static">
                                <?php echo $stockBatchSettlement['sub']['quantity']?>
                                <?php echo $this->map['goods_unit'][$stockBatchSettlement['sub']['unit']]['name']?>
                            </p>
                            <?php endif;?>
                        </td>
                        <td>
                            <p class="form-control-static">
                                <?php echo Map::$v['currency'][$stockBatchSettlement['currency']]['ico'].number_format($stockBatchSettlement['price']/100, 2);?>
                            </p>
                            <?php if(!$isSingleUnit):?>
                            <p class="form-control-static">
                                <?php echo Map::$v['currency'][$stockBatchSettlement['currency']]['ico'].number_format($stockBatchSettlement['sub']['price']/100, 2);?>
                            </p>
                            <?php endif;?>
                        </td>
                        <td>
                            <p class="form-control-static">
                                <?php echo Map::$v['currency'][$stockBatchSettlement['currency']]['ico'].number_format($stockBatchSettlement['amount_cny']/100, 2);?>
                            </p>
                        </td>
                        <td>
                            <p class="form-control-static">
                                <?php echo $stockBatchSettlement['quantity_loss']?>
                            </p>
                            <?php if(!$isSingleUnit):?>
                            <p class="form-control-static">
                                <?php echo $stockBatchSettlement['sub']['quantity_loss']?>
                            </p>
                            <?php endif;?>
                        </td>
                        <td>
                            <p class="form-control-static">
                                <?php echo $stockBatchSettlement['remark']?>
                            </p>
                        </td>
                        <td>
                            <p class="form-control-static">
                                <a href="javascript:void(0);" onclick="openDialog(<?php echo $stockBatchSettlement['detail_id']?>)">锁价记录</a>
                            </p>
                        </td>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <div class="modal fade in" id="buy_lock_dialog">
                    <div class="modal-dialog" style="width:80%">
                        <div class="modal-content">
                            <div class="modal-header" >
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
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var openDialog = function(detail_id) {
            $.ajax({
                data: {
					detail_id:detail_id
                },
                url:'/stockBatchSettlement/ajaxGetBuyLockList',
                method:'post',
                success:function(res) {
                    $("#buy_lock_dialog_body").html(res);
                    $("#buy_lock_dialog").modal("show");
                },
                error:function(res) {
					layer.alert("操作失败！" + res.responseText, {icon: 5});
                }
            });
        }

	function edit(batch_id) {
		location.href = "/<?php echo $this->getId() ?>/edit?id=" + batch_id;
	}

	function submit(batch_id) {
		layer.confirm("您确定要提交当前入库通知单结算信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
			var formData = "id=" + batch_id;
			$.ajax({
				type: "POST",
				url: "/<?php echo $this->getId() ?>/submit",
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						layer.msg(json.data, {icon: 6, time: 1000}, function () {
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
</script>
<?php
endif;?>
