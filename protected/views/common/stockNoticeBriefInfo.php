
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?php echo $this->map['stock_notice_delivery_type'][$stockNotice['type']] ?>入库通知单 &nbsp;&nbsp;&nbsp;&nbsp;<a href="/stockIn/detail/?id=<?php echo $stockNotice['batch_id'] ?>&t=1" target="_blank"><span style="font-size: 16px;">NO.<span><?php echo $stockNotice['batch_code'] ?></span></span></a>
        </h3>
        <div class="pull-right box-tools">
            <?php if(!$this->isExternal && !$hideBackBtn){ ?>
            <button type="button"  class="btn btn-default btn-sm history-back" onclick="back()">返回</button>
            <?php } ?>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">入库通知单日期</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $stockNotice['batch_date'] ?></p>
            </div>
            <label class="col-sm-2 control-label">采购合同编号</label>
            <div class="col-sm-4">
                <p class="form-control-static"><a href="/contract/detail/?id=<?php echo $stockNotice['contract_id'] ?>&t=1" target="_blank" title="<?php echo $stockNotice['contract_code'] ?>"><?php echo $stockNotice['contract_code'] ?></a></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">上游合作方</label>
            <div class="col-sm-4">
                <p class="form-control-static"><a href="/partner/detail/?id=<?php echo $stockNotice['partner_id'] ?>&t=1" target="_blank" title="<?php echo $stockNotice['partner_name'] ?>"><?php echo $stockNotice['partner_name'] ?></a></p>
            </div>
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

        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <?php
                if (!empty($stockNoticeGoods)) :
                    ?>
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                        <tr>
                            <th style="width:100px;text-align:center">品名</th>
                            <th style="width:100px;text-align:center">入库通知单数量</th>
                            <th style="width:100px;text-align:center">换算比例</th>
                            <th style="width:100px;text-align:center">仓库</th>
                            <th style="width:100px;text-align:center">总入库数量</th>
                            <th style="width:100px;text-align:center">未入库数量</th>
<!--                            <th style="width:100px;text-align:center">结算数量</th>-->
                            <th style="width:80px;text-align:center">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($stockNoticeGoods as $v) : ?>
                            <tr>
                                <td style="text-align:center"><?php echo $v['goodsName'] ?></td>
                                <td style="text-align:center">
    
                                <?php echo Utility::numberFormatToDecimal($v["quantity"]['quantity'], 4) ?><?php echo $this->map["goods_unit"][$v["quantity"]['unit']]['name'] ?>
                                <?php if(!empty($v['quantity_sub']["unit"]) && $v['quantity_sub']["unit"] != $v["quantity"]["unit"]) {
                                    echo '/' . Utility::numberFormatToDecimal($v['quantity_sub']['quantity'], 4) . $this->map["goods_unit"][$v['quantity_sub']["unit"]]['name'];
                                }
                                ?>
                                </td>
                                <td style="text-align:center"><?php echo $v["unit_rate"] ?></td>
                                <td style="text-align:center"><?php echo $v['store_name'] ?></td>
                                <td style="text-align:center">
                                <?php
//                                $stockInTotal = StockNoticeService::getTotalStockInQuantity($stockNotice['batch_id'], $v['goods_id'], $v["in_quantity"]['unit']);
                                echo Utility::numberFormatToDecimal($v['in_quantity']['quantity'], 4) . $this->map["goods_unit"][$v["in_quantity"]['unit']]['name'];
                                if(!empty($v['in_quantity_sub']["unit"]) && $v['in_quantity_sub']["unit"] != $v["in_quantity"]["unit"]) {
                                    echo '/' . Utility::numberFormatToDecimal($v['in_quantity_sub']['quantity'], 4) . $this->map["goods_unit"][$v['in_quantity_sub']["unit"]]['name'];
                                }
                                ?>
                                </td>
                                <td style="text-align:center">
                                <?php
                                    echo Utility::numberFormatToDecimal($v['quantity_not']['quantity'], 4), $this->map["goods_unit"][$v['quantity_not']["unit"]]['name'];
                                if(!empty($v['quantity_not_sub']["unit"]) && $v['quantity_not_sub']["unit"] != $v['quantity_not']["unit"]) {
                                    echo '/' . Utility::numberFormatToDecimal($v['quantity_not_sub']['quantity'], 4) . $this->map["goods_unit"][$v['quantity_not_sub']["unit"]]['name'];
                                }
                                ?>
                                </td>
                                <!--<td style="text-align:center">
                                <?php
/*                                $stockSettlement = StockNoticeService::getTotalSettlementQuantity($stockNotice['batch_id'], $v['goods_id'], $v["quantity"]['unit']);
                                echo $stockSettlement['quantity'], $this->map["goods_unit"][$v["unit"]]['name'];
                                if(!empty($v['sub']["quantity"]) && $v['sub']["unit"] != $v["unit"]) {
                                    echo '/' . Utility::numberFormatToDecimal($stockSettlement['quantity_sub'], 4) . $this->map["goods_unit"][$v['sub']["unit"]]['name'];
                                }
                                */?>
                                </td>-->
                                <td style="text-align:center"><?php echo $v["remark"] ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endif;?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">备注</label>
            <div class="col-sm-10">
                <p class="form-control-static">
                <?php
                    echo $stockNotice['remark'];
                ?>
                </p>
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