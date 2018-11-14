
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title" >
            <b><?php echo Map::$v['stock_notice_delivery_type'][$deliveryOrder['type']] . '发货单' ?>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo "<a href='/deliveryOrder/detail/?id=".$deliveryOrder['order_id']."&t=1' target='_blank'>".$deliveryOrder['code']."</a>" ?></span></span></b>
        </h3>
        <div class="pull-right box-tools">
            <?php if(!$this->isExternal && !$hideBackBtn){ ?>
            <button type="button" class="btn btn-default btn-sm" onclick="back()">返回</button>
            <?php } ?>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">交易主体</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a target="_blank" href="/corporation/detail?t=1&id=<?php echo $deliveryOrder['corporation_id']?>">
                        <?php echo $deliveryOrder['corporation_name']?>
                    </a>
                </p>
            </div>
            <label class="col-sm-2 control-label">销售合同编号</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a title="合同详情" target="_blank" href="/contract/detail/?id=<?php echo $deliveryOrder['contract_id']; ?>&t=1">
                        <?php echo $deliveryOrder['contract_code']; ?>
                    </a>
                </p>
            </div>
            <label class="col-sm-2 control-label">下游合作方</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a target="_blank" href="/partner/detail?t=1&id=<?php echo $deliveryOrder['partner_id']?>">
                        <?php echo $deliveryOrder['partner_name']?>
                    </a>
                </p>
            </div>
            <label class="col-sm-2 control-label">预计发货日期</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $deliveryOrder['delivery_date']?></p>
            </div>
            <?php
            $attachments=AttachmentService::getAttachments(Attachment::C_STOCK_DELIVERY,$deliveryOrder['order_id'], 1);
            $this->renderPartial("/components/attachmentsDropdown", array(
                    'id' => $deliveryOrder['order_id'],
                    'map_key'=>'stock_delivery_attachment',
                    'attach_type'=>ConstantMap::STOCK_DELIVERY_ATTACH_TYPE,
                    'attachment_type'=>Attachment::C_STOCK_DELIVERY,
                    'controller'=>'deliveryOrder',
                )
            );
            ?>
        </div>
        <div class="form-group">
            <!-- <label for="type" class="col-sm-2 control-label">发货明细</label> -->
            <div class="col-sm-12">
            <!-- <div class="col-sm-12"> -->
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th style="width:120px;text-align:center">品名</th>
<!--                        <th style="width:140px;text-align:center">合同数量</th>-->
                        <th style="width:90px;text-align:center">发货数量</th>
                        <th style="width:180px;text-align:center">配货入库单编号</th>
                        <th style="width:100px;text-align:center">配货数量</th>
                        
                        <th style="width:100px;text-align:center">仓库</th>
                        <th style="width:100px;text-align:center">总出库数量</th>
                        <th style="width:100px;text-align:center">未出库数量</th>
                        <th style="width:100px;text-align:center">备注</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    foreach ($deliveryOrder['items'] as $goodsDetail) {
                        $countGoods = count($goodsDetail['delivery_items']);
                        $order_item = $goodsDetail['delivery_items'][0];
                        if($countGoods>1){
                    ?>
                        <tr>
                            <td style="text-align:center;vertical-align: middle;" rowspan="<?php echo $countGoods?>">
                                <?php echo $goodsDetail['goods_name'];?>
                            </td>
                            <!--<td style="text-align:right;vertical-align: middle;" rowspan="<?php /*echo $countGoods*/?>">
                                   <?php /*echo $detail->contractGoods->quantity*/?>
                                   <?php /*echo $this->map['goods_unit'][$detail->contractGoods['unit']]['name']*/?>
                                   ±10%
                               </td>-->
                            <td style="text-align:right;vertical-align: middle;" rowspan="<?php echo $countGoods?>">
                                <?php echo Utility::numberFormatToDecimal($goodsDetail['quantity']['quantity'],  4)?>
                                <?php echo $this->map['goods_unit'][$goodsDetail['quantity']['unit']]['name']?>
                            </td>
                            <td style="text-align:center;vertical-align: middle;">
                                <?php echo "<a target='_blank' href='/stockInList/view/?id=".$order_item['stock_in_id']."&t=1'>".$order_item['code']."</a>";?>
                            </td>
                            <td style="text-align:right;vertical-align: middle;">
                                <?php echo Utility::numberFormatToDecimal($order_item['stock_delivery_quantity']['quantity'], 4) ?>
                                <?php echo $this->map['goods_unit'][$order_item['stock_delivery_quantity']['unit']]['name']?>
                            </td>

                            <td style="text-align:center;vertical-align: middle;">
                                <?php echo empty($order_item['store_name']) ? "虚拟库" : $order_item['store_name'];?>
                            </td>
                            <td style="text-align:right;vertical-align: middle;">
                                <?php echo Utility::numberFormatToDecimal($order_item['out_quantity']['quantity'], 4) ?>
                                <?php echo $this->map['goods_unit'][$order_item['out_quantity']['unit']]['name']?>
                            </td>
                            <td style="text-align:right;vertical-align: middle;">
                                <?php echo Utility::numberFormatToDecimal($order_item['no_out_quantity']['quantity'], 4) ?>
                                <?php echo $this->map['goods_unit'][$order_item['no_out_quantity']['unit']]['name']?>
                            </td>
                            <td style="text-align:right;vertical-align: middle;">
                                <?php echo $order_item['remark'] ?>
                            </td>

                        </tr>
                        <?php
                            unset($goodsDetail['delivery_items'][0]);
                            foreach ($goodsDetail['delivery_items'] as $delivery_item){ ?>
                        <tr>
                            <td style="text-align:center;vertical-align: middle;">
                                <?php echo "<a target='_blank' href='/stockInList/view/?id=".$delivery_item['stock_in_id']."&t=1'>".$delivery_item['code']."</a>";?>
                            </td>
                            <td style="text-align:right;vertical-align: middle;">
                                <?php echo Utility::numberFormatToDecimal($delivery_item['stock_delivery_quantity']['quantity'], 4) ?>
                                <?php echo $this->map['goods_unit'][$delivery_item['stock_delivery_quantity']['unit']]['name']?>
                            </td>

                            <td style="text-align:center;vertical-align: middle;">
                                <?php echo empty($delivery_item['store_name']) ? "虚拟库" : $delivery_item['store_name'];?>
                            </td>
                            <td style="text-align:right;vertical-align: middle;">
                                <?php echo Utility::numberFormatToDecimal($delivery_item['out_quantity']['quantity'], 4) ?>
                                <?php echo $this->map['goods_unit'][$delivery_item['out_quantity']['unit']]['name']?>
                            </td>
                            <td style="text-align:right;vertical-align: middle;">
                                <?php echo Utility::numberFormatToDecimal($delivery_item['no_out_quantity']['quantity'], 4) ?>
                                <?php echo $this->map['goods_unit'][$delivery_item['no_out_quantity']['unit']]['name']?>
                            </td>
                            <td style="text-align:right;vertical-align: middle;">
                                <?php echo $delivery_item['remark'] ?>
                            </td>
                        </tr>
                         <?php }
                        }else{ ?>
                            <tr>
                                <td style="text-align:center;vertical-align: middle;">
                                    <?php echo $goodsDetail['goods_name'];?>
                                </td>
                                <!--<td style="text-align:right;vertical-align: middle;" rowspan="<?php /*echo $countGoods*/?>">
                                   <?php /*echo $detail->contractGoods->quantity*/?>
                                   <?php /*echo $this->map['goods_unit'][$detail->contractGoods['unit']]['name']*/?>
                                   ±10%
                               </td>-->
                                <td style="text-align:right;vertical-align: middle;">
                                    <?php echo Utility::numberFormatToDecimal($goodsDetail['quantity']['quantity'], 4)?>
                                    <?php echo $this->map['goods_unit'][$goodsDetail['quantity']['unit']]['name']?>
                                </td>
                                <td style="text-align:center;vertical-align: middle;">
                                    <?php echo "<a target='_blank' href='/stockInList/view/?id=" . $order_item['stock_in_id'] . "&t=1'>" . $order_item['code'] . "</a>"; ?>
                                </td>
                                <td style="text-align:right;vertical-align: middle;">
                                    <?php echo Utility::numberFormatToDecimal($order_item['stock_delivery_quantity']['quantity'], 4) ?>
                                    <?php echo $this->map['goods_unit'][$order_item['stock_delivery_quantity']['unit']]['name'] ?>
                                </td>

                                <td style="text-align:center;vertical-align: middle;">
                                    <?php echo empty($order_item['store_name']) ? "虚拟库" : $order_item['store_name']; ?>
                                </td>
                                <td style="text-align:right;vertical-align: middle;">
                                    <?php echo Utility::numberFormatToDecimal($order_item['out_quantity']['quantity'], 4) ?>
                                    <?php echo $this->map['goods_unit'][$order_item['out_quantity']['unit']]['name'] ?>
                                </td>
                                <td style="text-align:right;vertical-align: middle;">
                                    <?php echo Utility::numberFormatToDecimal($order_item['no_out_quantity']['quantity'], 4) ?>
                                    <?php echo $this->map['goods_unit'][$order_item['no_out_quantity']['unit']]['name'] ?>
                                </td>
                                <td style="text-align:right;vertical-align: middle;">
                                    <?php echo $order_item['remark'] ?>
                                </td>
                        </tr>
                    <?php
                        }
                    }?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">备注</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $deliveryOrder['remark']; ?></p>
            </div>
        </div>
        <!-- <div class="box-header with-border"></div> -->
        <hr/>
        <div class="form-group">
            <label class="col-sm-2 control-label">审核状态</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $deliveryOrder['status']<DeliveryOrder::STATUS_PASS ? Map::$v['delivery_order_status'][$deliveryOrder['status']] : Map::$v['delivery_order_status'][DeliveryOrder::STATUS_PASS]; ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">审核意见</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <?php
                    $checkLogs=FlowService::getCheckLog($deliveryOrder['order_id'],9);
                    if(Utility::isNotEmpty($checkLogs))
                        echo $checkLogs[0]['remark'];
                    ?>
                </p>
            </div>
        </div>
    </div>
    
    
</div>
<script>
    function back() {
        location.href = /<?php echo $this->getId() ?>/;
    }

</script>