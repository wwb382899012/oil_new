<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title" style="text-align: center">
            <b><?php echo Map::$v['stock_notice_delivery_type'][$stockNotice['type']] . '入库通知单' ?>&nbsp;&nbsp;&nbsp;&nbsp;
                <span style="font-size: 16px;">NO.<span class="text-red"><?php echo $stockNotice['code'] ?></span></span>
                <?php if ($stockNotice['status'] > StockNotice::STATUS_SETTLE_SUBMIT) { ?>
                    <span class="text-red">(已结算)</span>
                <?php } ?>
            </b>
        </h3>
        <div class="pull-right box-tools">
            <?php if (!$this->isExternal) { ?>
                <button type="button" class="btn btn-default btn-sm" onclick="back()">返回</button>
            <?php } ?>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">上游合作方</label>
            <div class="col-sm-10">
                <p class="form-control-static"><a href="/partner/detail/?id=<?php echo $stockNotice->contract->partner_id ?>&t=1" target="_blank" title="<?php echo $stockNotice->contract->partner->name ?>"><?php echo $stockNotice->contract->partner->name ?></a></p>
            </div>
        </div>
        <div class="form-group">
            <?php
            $this->renderPartial("/components/attachmentsDropdown", array(
                    'id' => $stockNotice['batch_id'],
                    'map_key'=>'stock_notice_attachment_type',
                    'attach_type'=>ConstantMap::STOCK_NOTICE_ATTACH_TYPE,
                    'attachment_type'=>Attachment::C_STOCK_NOTICE,
                    'controller'=>'stockNotice',
                )
            );
            ?>
            <label class="col-sm-2 control-label">入库通知单日期</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $stockNotice['batch_date'] ?></p>
            </div>
        </div>
        <?php
        if (Utility::isNotEmpty($stockNotice->details)) { ?>
        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th style="width:120px;text-align:center">品名</th>
                        <!--<th style="width:100px;text-align:center">规格</th>-->
                        <th style="width:80px;text-align:center">入库通知单数量</th>
                        <th style="width:80px;text-align:center">换算比例</th>
                        <th style="width:80px;text-align:center">仓库</th>
                        <th style="width:80px;text-align:center">总入库数量</th>
                        <th style="width:80px;text-align:center">未入库数量</th>
                        <th style="width:80px;text-align:center">结算数量</th>
                        <th style="width:80px;text-align:center">备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stockNotice->details as $val) { ?>
                        <tr>
                            <td style="text-align:center"><?php echo $val->goods->name ?></td>
                            <!--<td style="text-align:center"><?php /*echo !empty($val["goods_describe"]) ? $val["goods_describe"] : '无' */?></td>-->
                            <td style="text-align:center">
                                <?php
                                $amountInfo = Utility::numberFormatToDecimal($val["quantity"], 4) . Map::$v['goods_unit'][$val['unit']]['name'];
                                if (!empty($val->sub) && !empty($val->sub->unit)) {
                                    $amountInfo .= '/' . Utility::numberFormatToDecimal($val->sub->quantity, 4) . Map::$v['goods_unit'][$val->sub->unit]['name'];
                                }
                                echo $amountInfo;
                                ?>
                            </td>
                            <td style="text-align:center">
                                <?php
                                if (!empty($val->sub) && !empty($val->unit_rate)) {
                                    echo $val['unit_rate'];
                                } else {
                                    echo '';
                                }
                                ?>
                            </td>
                            <td style="text-align:center"><?php echo $val->store->name ?></td>
                            <td style="text-align:center">
                                <?php
                                $stockInTotal = StockNoticeService::getTotalStockInQuantity($val['batch_id'], $val['goods_id'], $val['unit']);
                                echo Utility::numberFormatToDecimal($stockInTotal['quantity'], 4) . $this->map["goods_unit"][$val["unit"]]['name'];
                                if(!empty($val['sub']["quantity"]) && $val['sub']["unit"] != $val["unit"]) {
                                    echo '/' . Utility::numberFormatToDecimal($stockInTotal['quantity_sub'], 4) . $this->map["goods_unit"][$val['sub']["unit"]]['name'];
                                }
                                ?>
                            </td>
                            <td style="text-align:center">
                                <?php
                                echo Utility::numberFormatToDecimal($val['quantity'] - $stockInTotal['quantity'], 4), $this->map["goods_unit"][$val["unit"]]['name'];
                                if(!empty($val['sub']["quantity"]) && $val['sub']["unit"] != $val["unit"]) {
                                    echo '/' . Utility::numberFormatToDecimal($val['sub']['quantity'] - $stockInTotal['quantity_sub'], 4) . $this->map["goods_unit"][$val['sub']["unit"]]['name'];
                                }
                                ?>
                            </td>
                            <td style="text-align:center">
                                <?php
                                $stockSettlement = StockNoticeService::getTotalSettlementQuantity($val['batch_id'], $val['goods_id'], $val['unit']);
                                echo $stockSettlement['quantity'], $this->map["goods_unit"][$val["unit"]]['name'];
                                if(!empty($val['sub']["quantity"]) && $val['sub']["unit"] != $val["unit"]) {
                                    echo '/' . Utility::numberFormatToDecimal($stockSettlement['quantity_sub'], 4) . $this->map["goods_unit"][$val['sub']["unit"]]['name'];
                                }
                                ?>
                            </td>
                            <td style="text-align:center">
                                <?php echo $val['remark']; ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="form-group">
                    <label class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $stockNotice['remark']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<script>
	function back() {
		location.href = '/<?php echo $this->getId() ?>/';
	}
</script>