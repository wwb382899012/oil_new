
<div class="z-card">
    <h3 class="z-card-header">
        <b>
            <?php echo $this->map['stock_notice_delivery_type'][$stockNotice['type']] ?>入库通知单
            <a class="text-link title-code" href="/stockIn/detail/?id=<?php echo $stockNotice['batch_id'] ?>&t=1" target="_blank"><span>NO.<span><?php echo $stockNotice['batch_code'] ?></span></span></a>
        </b>
    </h3>
    <div class="z-card-body">
        <div class="busi-detail">
            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">入库通知单日期:</span>
                    <span class="form-control-static line-h--text">
                        <?php echo $stockNotice['batch_date'] ?>
                    </span>
                </label>
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">采购合同编号:</span>
                    <span class="form-control-static line-h--text">
                        <a class="text-link" href="/contract/detail/?id=<?php echo $stockNotice['contract_id'] ?>&t=1" target="_blank" title="<?php echo $stockNotice['contract_code'] ?>">
                            <?php echo $stockNotice['contract_code'] ?>
                        </a>
                    </span>
                </label>
            </div>

            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid">
                    <span class="line-h--text w-fixed">上游合作方:</span>
                    <span class="form-control-static line-h--text">
                        <a class="text-link" href="/partner/detail/?id=<?php echo $stockNotice['partner_id'] ?>&t=1" target="_blank" title="<?php echo $stockNotice['partner_name'] ?>">
                            <?php echo $stockNotice['partner_name'] ?>
                        </a>
                    </span>
                </label>
                <?php
                $this->renderPartial("/components/new_attachmentsDropdown", array(
                        'id' => $stockIn['stock_in_id'],
                        'map_key'=>'stock_in_attachment_type',
                        'attach_type'=>ConstantMap::STOCK_IN_ATTACH_TYPE,
                        'attachment_type'=>Attachment::C_STOCK_IN,
                        'controller'=>'stockIn',
                    )
                );
                ?>
            </div>

            <div class="form-group">
                <?php
                if (!empty($stockNoticeGoods)) :
                    ?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="width:100px;">品名</th>
                            <th style="width:100px;">入库通知单数量</th>
                            <th style="width:100px;">换算比例</th>
                            <th style="width:100px;">仓库</th>
                            <th style="width:100px;">总入库数量</th>
                            <th style="width:100px;">未入库数量</th>
                            <!--                            <th style="width:100px;">结算数量</th>-->
                            <th style="width:80px;">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($stockNoticeGoods as $v) : ?>
                            <tr>
                                <td style=""><?php echo $v['goodsName'] ?></td>
                                <td style="">

                                    <?php echo Utility::numberFormatToDecimal($v["quantity"]['quantity'], 4) ?><?php echo $this->map["goods_unit"][$v["quantity"]['unit']]['name'] ?>
                                    <?php if(!empty($v['quantity_sub']["unit"]) && $v['quantity_sub']["unit"] != $v["quantity"]["unit"]) {
                                        echo '/' . Utility::numberFormatToDecimal($v['quantity_sub']['quantity'], 4) . $this->map["goods_unit"][$v['quantity_sub']["unit"]]['name'];
                                    }
                                    ?>
                                </td>
                                <td style=""><?php echo $v["unit_rate"] ?></td>
                                <td style=""><?php echo $v['store_name'] ?></td>
                                <td style="">
                                    <?php
                                    //                                $stockInTotal = StockNoticeService::getTotalStockInQuantity($stockNotice['batch_id'], $v['goods_id'], $v["in_quantity"]['unit']);
                                    echo Utility::numberFormatToDecimal($v['in_quantity']['quantity'], 4) . $this->map["goods_unit"][$v["in_quantity"]['unit']]['name'];
                                    if(!empty($v['in_quantity_sub']["unit"]) && $v['in_quantity_sub']["unit"] != $v["in_quantity"]["unit"]) {
                                        echo '/' . Utility::numberFormatToDecimal($v['in_quantity_sub']['quantity'], 4) . $this->map["goods_unit"][$v['in_quantity_sub']["unit"]]['name'];
                                    }
                                    ?>
                                </td>
                                <td style="">
                                    <?php
                                    echo Utility::numberFormatToDecimal($v['quantity_not']['quantity'], 4), $this->map["goods_unit"][$v['quantity_not']["unit"]]['name'];
                                    if(!empty($v['quantity_not_sub']["unit"]) && $v['quantity_not_sub']["unit"] != $v['quantity_not']["unit"]) {
                                        echo '/' . Utility::numberFormatToDecimal($v['quantity_not_sub']['quantity'], 4) . $this->map["goods_unit"][$v['quantity_not_sub']["unit"]]['name'];
                                    }
                                    ?>
                                </td>
                                <!--<td style="">
                                <?php
                                /*                                $stockSettlement = StockNoticeService::getTotalSettlementQuantity($stockNotice['batch_id'], $v['goods_id'], $v["quantity"]['unit']);
                                                                echo $stockSettlement['quantity'], $this->map["goods_unit"][$v["unit"]]['name'];
                                                                if(!empty($v['sub']["quantity"]) && $v['sub']["unit"] != $v["unit"]) {
                                                                    echo '/' . Utility::numberFormatToDecimal($stockSettlement['quantity_sub'], 4) . $this->map["goods_unit"][$v['sub']["unit"]]['name'];
                                                                }
                                                                */?>
                                </td>-->
                                <td style=""><?php echo $v["remark"] ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endif;?>
            </div>

            <div class="flex-grid form-group">
                <label class="col full-space field flex-grid">
                    <span class="w-fixed line-h--text">备注:</span>
                    <span class="form-control-static line-h--text flex-grow">
                        <?php
                            echo $stockNotice['remark'];
                        ?>
                    </span>
                </label>
            </div>
        </div>
    </div>
</div>

<script>
    function back()
    {
        if( document.referrer === '')
            location.href=/<?php echo $this->getId(); ?>/;
        else
            history.back();
    }
</script>