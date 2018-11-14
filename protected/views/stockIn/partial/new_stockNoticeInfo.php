<div class="z-card">
    <h3 class="z-card-header">
        <b><?php echo Map::$v['stock_notice_delivery_type'][$stockNotice['type']].'入库通知单' ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <a class="text-link" target="_blank" title="<?php echo $stockNotice['code']; ?>" href="/stockIn/detail/?id=<?php echo $stockNotice['batch_id']; ?>&t=1">
                <span>NO.<span><?php echo $stockNotice['code']; ?></span></span>
            </a>
            <?php if($stockNotice['status'] > StockNotice::STATUS_SETTLE_SUBMIT): ?>
                <span class="text-red">(已结算)</span>
            <?php endif; ?>
        </b>
    </h3>
    <div class="z-card-body">
        <div class="busi-detail">

            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">上游合作方:</span>
                    <span class="form-control-static line-h--text">
                        <a class="text-link"
                           href="/partner/detail/?id=<?php echo $stockNotice->contract->partner_id ?>&t=1"
                           target="_blank"
                           title="<?php echo $stockNotice->contract->partner->name ?>"><?php echo $stockNotice->contract->partner->name ?></a>
                    </span>
                </label>
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">入库通知单日期:</span>
                    <span class="form-control-static line-h--text">
                        <?php echo $stockNotice['batch_date'] ?>
                    </span>
                </label>
            </div>

            <div class="flex-grid form-group">
                <?php
                $this->renderPartial("/components/new_attachmentsDropdown", array(
                        'id' => $stockNotice['batch_id'],
                        'map_key'=>'stock_notice_attachment_type',
                        'attach_type'=>ConstantMap::STOCK_NOTICE_ATTACH_TYPE,
                        'attachment_type'=>Attachment::C_STOCK_NOTICE,
                        'controller'=>'stockNotice',
                    )
                );
                ?>
                <?php if($stockNotice->is_virtual):?>
                    <label class="col col-count-2 field flex-grid">
                        <span class="w-fixed line-h--text">原入库通知单:</span>
                        <span class="form-control-static ellipsis line-h--text">
                                    <a class="text-link"
                                       href="/stockIn/detail?id=<?php echo $stockNotice->originalOrder->batch_id; ?>&t=1"
                                       target="_blank"
                                       title="<?php echo $stockNotice->originalOrder->code; ?>"><?php echo $stockNotice->originalOrder->code; ?></a>
                                </span>
                    </label>
                <?php endif;?>
            </div>

            <?php if(Utility::isNotEmpty($stockNotice->details)): ?>
                <div class="flex-grid form-group">
                    <table class="table table-custom">
                        <thead>
                        <tr>
                            <th>品名</th>
                            <th>入库通知单数量</th>
                            <th>换算比例</th>
                            <th>仓库</th>
                            <th>总入库数量</th>
                            <th>未入库数量</th>
                            <th>结算数量</th>
                            <th>备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($stockNotice->details as $val): ?>
                            <tr>
                                <td><?php echo $val->goods->name; ?></td>
                                <td>
                                    <?php
                                    $amountInfo = Utility::numberFormatToDecimal($val["quantity"], 4).Map::$v['goods_unit'][$val['unit']]['name'];
                                    if(!empty($val->sub) && !empty($val->sub->unit)){
                                        $amountInfo .= '/'.Utility::numberFormatToDecimal($val->sub->quantity, 4).Map::$v['goods_unit'][$val->sub->unit]['name'];
                                    }
                                    echo $amountInfo;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if(!empty($val->sub) && !empty($val->unit_rate)){
                                        echo $val['unit_rate'];
                                    }else{
                                        echo '';
                                    }
                                    ?>
                                </td>
                                <td><?php echo $stockNotice['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE ? $val->store->name : '虚拟库'; ?></td>
                                <td>
                                    <?php
                                    $stockInTotal = StockNoticeService::getTotalStockInQuantity($val['batch_id'], $val['goods_id'], $val['unit']);
                                    echo Utility::numberFormatToDecimal($stockInTotal['quantity'], 4) . $this->map["goods_unit"][$val["unit"]]['name'];
                                    if(!empty($val['sub']["quantity"]) && $val['sub']["unit"] != $val["unit"]) {
                                        echo '/' . Utility::numberFormatToDecimal($stockInTotal['quantity_sub'], 4) . $this->map["goods_unit"][$val['sub']["unit"]]['name'];
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo Utility::numberFormatToDecimal($val['quantity'] - $stockInTotal['quantity'], 4), $this->map["goods_unit"][$val["unit"]]['name'];
                                    if(!empty($val['sub']["quantity"]) && $val['sub']["unit"] != $val["unit"]) {
                                        echo '/' . Utility::numberFormatToDecimal($val['sub']['quantity'] - $stockInTotal['quantity_sub'], 4) . $this->map["goods_unit"][$val['sub']["unit"]]['name'];
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $stockSettlement = StockNoticeService::getTotalSettlementQuantity($val['batch_id'], $val['goods_id'], $val['unit']);
                                    echo $stockSettlement['quantity'], $this->map["goods_unit"][$val["unit"]]['name'];
                                    if(!empty($val['sub']["quantity"]) && $val['sub']["unit"] != $val["unit"]) {
                                        echo '/' . Utility::numberFormatToDecimal($stockSettlement['quantity_sub'], 4) . $this->map["goods_unit"][$val['sub']["unit"]]['name'];
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $val['remark']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="flex-grid form-group">
                <label class="col full-space field flex-grid">
                    <span class="w-fixed line-h--text">备注:</span>
                    <span
                        class="form-control-static line-h--text flex-grow"><?php echo $stockNotice->remark; ?></span>
                </label>
            </div>
        </div>
    </div>
</div>
