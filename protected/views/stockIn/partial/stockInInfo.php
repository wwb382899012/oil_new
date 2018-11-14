<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title" style="text-align: center">
            <b><?php echo $stockIn->type == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE ? $stockIn->store->name : '虚拟库' ?>入库单&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo $stockIn['code'] ?></span></span></b>
        </h3>
        <div class="pull-right box-tools">
            <?php if (!$this->isExternal && $this->getId() != 'check7') { ?>
                <button type="button" class="btn btn-default btn-sm" onclick="back()">返回</button>
            <?php } ?>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <?php
            $is_split = StockInService::isVirtualBill($stockIn->is_virtual);
            $div_class = $is_split ? "col-sm-2" : "col-sm-4";
            $this->renderPartial("/components/attachmentsDropdown", array(
                    'id' => $stockIn['stock_in_id'],
                    'map_key'=>'stock_in_attachment_type',
                    'attach_type'=>ConstantMap::STOCK_IN_ATTACH_TYPE,
                    'attachment_type'=>Attachment::C_STOCK_IN,
                    'controller'=>'stockIn',
                    'div_class'=> $div_class,
                )
            );
            ?>
            <label class="col-sm-2 control-label">入库日期</label>
            <div class="<?php echo $div_class;?>">
                <p class="form-control-static"><?php echo $stockIn['entry_date'] ?></p>
            </div>
            <?php if($is_split):?>
                <label class="col-sm-2 control-label">被平移入库单编号</label>
                <div class="col-sm-2">
                    <p class="form-control-static"><?php echo $stockIn->originalOrder['code'] ?? "" ?></p>
                </div>
            <?php endif;?>
        </div>
        <?php
        if (Utility::isNotEmpty($stockIn->details)) { ?>
        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th style="width:120px;text-align:center">品名</th>
                        <!--<th style="width:100px;text-align:center">规格</th>-->
                        <th style="width:80px;text-align:center">入库单数量</th>
                        <th style="width:80px;text-align:center">换算比例</th>
                        <th style="width:80px;text-align:center">备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stockIn->details as $val) { ?>
                        <tr>
                            <td style="text-align:center"><?php echo $val->goods->name ?></td>
                            <!--<td style="text-align:center"><?php /*echo !empty($val["goods_describe"]) ? $val["goods_describe"] : '无' */ ?></td>-->
                            <td style="text-align:center">
                                <?php
                                $amountInfo = Utility::numberFormatToDecimal($val["quantity"], 4) . Map::$v['goods_unit'][$val['unit']]['name'];
                                if (!empty($val->sub->unit) && $val['unit'] != $val->sub->unit) {
                                    $amountInfo .= '/' . Utility::numberFormatToDecimal($val["quantity_sub"], 4) . Map::$v['goods_unit'][$val->sub->unit]['name'];
                                }
                                echo $amountInfo;
                                ?>
                            </td>
                            <td style="text-align:center">
                                <?php
                                if ($val['unit'] == $val->sub->unit) {
                                    echo '';
                                } else {
                                    echo $val['unit_rate'];
                                }
                                ?>
                            </td>
                            <td style="text-align:center"><?php echo $val["remark"] ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php
                $status = $stockIn['status']==StockIn::STATUS_SETTLED ? StockIn::STATUS_PASS : $stockIn['status'];
                $this->renderPartial("/common/stockInOrOutStatusInfo", array(
                        'isCanShowStatus'=>StockInService::isCanShowStatus($status),
                        'isInvalid' => StockInService::isInvalid($status),
                        'statusName'=> Map::$v['stock_in_status'][$status],
                        'remark' => $stockIn['remark'],
                        'isCanShowAuditStatus' => StockInService::isCanShowAuditStatus($status),
                        'isShowAuditRemark' => StockInService::isShowAuditRemark($status),
                        'id' => $stockIn['stock_in_id'],
                        'businessIds'=> FlowService::BUSINESS_STOCK_IN_CHECK,
                    )
                );
                ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>