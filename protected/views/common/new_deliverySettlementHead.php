
<div class="z-card">
    <h3 class="z-card-header">
        <b>
            <?php echo Map::$v['stock_notice_delivery_type'][$deliveryOrder['type']]?>发货单
            <a class="text-link title-code" href="/deliveryOrder/detail/?id=<?php echo $deliveryOrder['order_id'] ?>&t=1" target="_blank"><span>NO.<span><?php echo $deliveryOrder['code']?></span></span></a>
        </b>
    </h3>
    <div class="z-card-body">
        <div class="busi-detail">
            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">预计发货日期:</span>
                    <span class="form-control-static line-h--text">
                        <?php echo $deliveryOrder['delivery_date']?>
                    </span>
                </label>
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">交易主体:</span>
                    <span class="form-control-static line-h--text">
                        <a target="_blank" class="text-link"  href="/corporation/detail?t=1&id=<?php echo $deliveryOrder['corporation_id']?>">
                            <?php echo $deliveryOrder['corporation_name']?>
                        </a>
                    </span>
                </label>
            </div>
            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">销售合同编号:</span>
                    <span class="form-control-static line-h--text">
                        <a target="_blank" class="text-link" href="/contract/detail/?id=<?php echo $deliveryOrder['contract_id'] ?>&t=1">
                            <?php echo $deliveryOrder['contract_code']; ?>
                        </a>
                    </span>
                </label>
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">下游合作方:</span>
                    <span class="form-control-static line-h--text">
                        <a target="_blank" class="text-link"  href="/partner/detail?t=1&id=<?php echo $deliveryOrder['partner_id']?>">
                            <?php echo $deliveryOrder['partner_name']?>
                        </a>
                    </span>
                </label>
            </div>
            <div class="flex-grid form-group">
                <?php
                    $attachments=AttachmentService::getAttachments(Attachment::C_STOCK_DELIVERY,$deliveryOrder['order_id'], 1);
                    $this->renderPartial("/components/new_attachmentsDropdown", array(
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
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:120px;text-align:center">品名</th>
                            <!-- <th style="width:140px;text-align:center">合同数量</th> -->
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
                                    <td style="" rowspan="<?php echo $countGoods?>">
                                        <?php echo $goodsDetail['goods_name'];?>
                                    </td>
                                <!--<td style="text-align:right;vertical-align: middle;" rowspan="<?php /*echo $countGoods*/?>">
                                       <?php /*echo $detail->contractGoods->quantity*/?>
                                       <?php /*echo $this->map['goods_unit'][$detail->contractGoods['unit']]['name']*/?>
                                       ±10%
                                   </td>-->
                                   <td style="" rowspan="<?php echo $countGoods?>">
                                    <?php echo Utility::numberFormatToDecimal($goodsDetail['quantity']['quantity'],  4)?>
                                    <?php echo $this->map['goods_unit'][$goodsDetail['quantity']['unit']]['name']?>
                                </td>
                                <td style="white-space: nowrap;">
                                    <?php echo "<a target='_blank' class='text-link' href='/stockInList/view/?id=".$order_item['stock_in_id']."&t=1'>".$order_item['code']."</a>";?>
                                </td>
                                <td style="">
                                    <?php echo Utility::numberFormatToDecimal($order_item['stock_delivery_quantity']['quantity'], 4) ?>
                                    <?php echo $this->map['goods_unit'][$order_item['stock_delivery_quantity']['unit']]['name']?>
                                </td>

                                <td style="">
                                    <?php echo empty($order_item['store_name']) ? "虚拟库" : $order_item['store_name'];?>
                                </td>
                                <td style="">
                                    <?php echo Utility::numberFormatToDecimal($order_item['out_quantity']['quantity'], 4) ?>
                                    <?php echo $this->map['goods_unit'][$order_item['out_quantity']['unit']]['name']?>
                                </td>
                                <td style="">
                                    <?php echo Utility::numberFormatToDecimal($order_item['no_out_quantity']['quantity'], 4) ?>
                                    <?php echo $this->map['goods_unit'][$order_item['no_out_quantity']['unit']]['name']?>
                                </td>
                                <td style="">
                                    <?php echo $order_item['remark'] ?>
                                </td>

                            </tr>
                            <?php
                            unset($goodsDetail['delivery_items'][0]);
                            foreach ($goodsDetail['delivery_items'] as $delivery_item){ ?>
                                <tr>
                                    <td style="white-space: nowrap;">
                                        <?php echo "<a target='_blank' class='text-link' href='/stockInList/view/?id=".$delivery_item['stock_in_id']."&t=1'>".$delivery_item['code']."</a>";?>
                                    </td>
                                    <td style="">
                                        <?php echo Utility::numberFormatToDecimal($delivery_item['stock_delivery_quantity']['quantity'], 4) ?>
                                        <?php echo $this->map['goods_unit'][$delivery_item['stock_delivery_quantity']['unit']]['name']?>
                                    </td>

                                    <td style="">
                                        <?php echo empty($delivery_item['store_name']) ? "虚拟库" : $delivery_item['store_name'];?>
                                    </td>
                                    <td style="">
                                        <?php echo Utility::numberFormatToDecimal($delivery_item['out_quantity']['quantity'], 4) ?>
                                        <?php echo $this->map['goods_unit'][$delivery_item['out_quantity']['unit']]['name']?>
                                    </td>
                                    <td style="">
                                        <?php echo Utility::numberFormatToDecimal($delivery_item['no_out_quantity']['quantity'], 4) ?>
                                        <?php echo $this->map['goods_unit'][$delivery_item['no_out_quantity']['unit']]['name']?>
                                    </td>
                                    <td style="">
                                        <?php echo $delivery_item['remark'] ?>
                                    </td>
                                </tr>
                            <?php }
                        }else{ ?>
                            <tr>
                                <td style="">
                                    <?php echo $goodsDetail['goods_name'];?>
                                </td>
                                    <!--<td style="text-align:right;vertical-align: middle;" rowspan="<?php /*echo $countGoods*/?>">
                                       <?php /*echo $detail->contractGoods->quantity*/?>
                                       <?php /*echo $this->map['goods_unit'][$detail->contractGoods['unit']]['name']*/?>
                                       ±10%
                                   </td>-->
                                   <td style="">
                                    <?php echo Utility::numberFormatToDecimal($goodsDetail['quantity']['quantity'], 4)?>
                                    <?php echo $this->map['goods_unit'][$goodsDetail['quantity']['unit']]['name']?>
                                </td>
                                <td style="white-space: nowrap">
                                    <?php echo "<a target='_blank' class='text-link' href='/stockInList/view/?id=" . $order_item['stock_in_id'] . "&t=1'>" . $order_item['code'] . "</a>"; ?>
                                </td>
                                <td style="">
                                    <?php echo Utility::numberFormatToDecimal($order_item['stock_delivery_quantity']['quantity'], 4) ?>
                                    <?php echo $this->map['goods_unit'][$order_item['stock_delivery_quantity']['unit']]['name'] ?>
                                </td>

                                <td style="">
                                    <?php echo empty($order_item['store_name']) ? "虚拟库" : $order_item['store_name']; ?>
                                </td>
                                <td style="">
                                    <?php echo Utility::numberFormatToDecimal($order_item['out_quantity']['quantity'], 4) ?>
                                    <?php echo $this->map['goods_unit'][$order_item['out_quantity']['unit']]['name'] ?>
                                </td>
                                <td style="">
                                    <?php echo Utility::numberFormatToDecimal($order_item['no_out_quantity']['quantity'], 4) ?>
                                    <?php echo $this->map['goods_unit'][$order_item['no_out_quantity']['unit']]['name'] ?>
                                </td>
                                <td style="">
                                    <?php echo $order_item['remark'] ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }?>
                    </tbody>
                </table>
            </div>
            <div class="flex-grid form-group">
                <label class="col full-space field flex-grid">
                    <span class="w-fixed line-h--text">备注:</span>
                    <span class="form-control-static line-h--text flex-grow">
                        <?php echo $deliveryOrder['remark']; ?>
                    </span>
                </label>
            </div>
        </div>
        <div class="flex-grid form-group">
            <label class="col full-space field flex-grid">
                <span class="w-fixed line-h--text">审核状态:</span>
                <span class="form-control-static line-h--text flex-grow">
                    <?php echo $deliveryOrder['status']<DeliveryOrder::STATUS_PASS ? Map::$v['delivery_order_status'][$deliveryOrder['status']] : Map::$v['delivery_order_status'][DeliveryOrder::STATUS_PASS]; ?>
                </span>
            </label>
        </div>
        <div class="flex-grid form-group">
            <label class="col full-space field flex-grid">
                <span class="w-fixed line-h--text">审核意见:</span>
                <span class="form-control-static line-h--text flex-grow">
                    <?php
                    $checkLogs=FlowService::getCheckLog($deliveryOrder['order_id'],9);
                    if(Utility::isNotEmpty($checkLogs))
                        echo $checkLogs[0]['remark'];
                    ?>
                </span>
            </label>
        </div>
        
    </div>
</div>