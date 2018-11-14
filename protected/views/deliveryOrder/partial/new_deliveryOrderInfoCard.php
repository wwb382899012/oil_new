<div class="z-card">
    <h3 class="z-card-header">
        <?php echo $deliveryOrder['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE ? '经仓' : '直调' ?>发货单<!--
        --><span class="title-code">NO.<span><?php echo $deliveryOrder['code'] ?></span></span>
        <?php if ($deliveryOrder['status'] < DeliveryOrder::STATUS_SUBMIT): ?>
            <div class="pull-right box-tools">
                <a href="javascript: void 0" onclick="editDeliveryOrder(<?php echo $deliveryOrder['order_id'] ?>)" class="z-btn-action">修改</a>&nbsp;
                <a href="javascript: void 0" onclick="submitDeliveryOrder(<?php echo $deliveryOrder['order_id'] ?>)" class="z-btn-action z-btn-primary">提交</a>
            </div>
        <?php endif; ?>
    </h3>
    <div class="z-card-body">
        <div class="busi-detail">
            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid">
                    <span class="w-fixed line-h--text">交易主体:</span>
                    <span class="form-control-static ellipsis line-h--text">
                                 <a class="text-link" target="_blank"
                                    href="/corporation/detail?id=<?php echo $deliveryOrder->corporation->corporation_id ?>&t=1"
                                    title="<?php echo $deliveryOrder->corporation->name ?>">
                                    <?php echo $deliveryOrder->corporation->name ?>
                                </a>
                            </span>
                </label>
                <label class="col col-count-2 field flex-grid">
                    <span class="w-fixed line-h--text">销售合同编号:</span>
                    <span class="form-control-static ellipsis line-h--text">
                                <a title="<?php echo $deliveryOrder->contract->contract_code; ?>" class="text-link"
                                   target="_blank"
                                   href="/contract/detail/?id=<?php echo $deliveryOrder->contract->contract_id; ?>&t=1">
                                    <?php echo $deliveryOrder->contract->contract_code; ?>
                                </a>
                            </span>
                </label>
            </div>
            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid">
                    <span class="w-fixed line-h--text">下游合作方:</span>
                    <span class="form-control-static ellipsis line-h--text">
                                <a target="_blank" id="" class="text-link"
                                   title="<?php echo $deliveryOrder->partner->name ?>"
                                   href="/partner/detail/?id=<?php echo $deliveryOrder->partner->partner_id; ?>&t=1">
                                    <?php echo $deliveryOrder->partner->name ?>
                                </a>
                            </span>
                </label>
                <label class="col col-count-2 field flex-grid">
                    <span class="w-fixed line-h--text">预计发货日期:</span>
                    <span
                        class="form-control-static ellipsis line-h--text"><?php echo $deliveryOrder['delivery_date']; ?></span>
                </label>
            </div>

            <div class="flex-grid form-group">
                <?php
                $this->renderPartial("/components/new_attachmentsDropdown", array(
                        'id' => $deliveryOrder['order_id'],
                        'map_key'=>'stock_delivery_attachment',
                        'attach_type'=>ConstantMap::STOCK_DELIVERY_ATTACH_TYPE,
                        'attachment_type'=>Attachment::C_STOCK_DELIVERY,
                        'controller'=>'deliveryOrder',
                    )
                );
                ?>
                <?php if($deliveryOrder->is_virtual):?>
                    <label class="col col-count-2 field flex-grid">
                        <span class="w-fixed line-h--text">原发货单编号:</span>
                        <span class="form-control-static ellipsis line-h--text">
                                <a title="<?php echo $deliveryOrder->originalOrder->code; ?>" class="text-link"
                                   target="_blank"
                                   href="/stockOut/list?id=<?php echo $deliveryOrder->originalOrder->order_id; ?>">
                                    <?php echo $deliveryOrder->originalOrder->code; ?>
                                </a>
                            </span>
                    </label>
                <?php endif;?>
            </div>

            <div class="flex-grid form-group">
                <table class="table table-custom table-fixed has-table">
                    <thead>
                    <tr>
                        <th style="width: 10%;">品名</th>
                        <th style="width: 10%;">合同数量</th>
                        <th style="width: 10%;">发货数量</th>
                        <th style="width: 15%;">配货入库单编号</th>
                        <th style="width: 15%;">配货数量</th>
                        <th style="width: 10%;">出库</th>
                        <th style="width: 15%;">总出库数量</th>
                        <th style="width: 15%;">未出库数量</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($deliveryOrder->details as $val): ?>
                        <?php $contract = $val->contract;
                        $contractGoods = $val->contractGoods; ?>
                        <?php if($deliveryOrder->is_virtual):?>
                            <td style="vertical-align: middle;"><?php echo $val->goods->name; ?></td>
                            <td style="vertical-align: middle;">
                                <?php
                                if(!empty($contractGoods)){
                                    echo $contractGoods->quantity.Map::$v['goods_unit'][$contractGoods['unit']]['name'].'±'.($contractGoods->more_or_less_rate * 100).'%';
                                }else{
                                    echo '';
                                }
                                ?>
                            </td>
                            <td class="td-divide" style="text-align: center;">-</td>
                            <td style="text-align: center;">-</td>
                            <td style="text-align: center;">-</td>
                            <td style="text-align: center;">-</td>
                            <td style="text-align: center;">-</td>
                            <td style="text-align: center;">-</td>
                            <?php continue;?>
                        <?php endif;?>
                        <tr>
                            <td style="vertical-align: middle;"><?php echo $val->goods->name; ?></td>
                            <td style="vertical-align: middle;">
                                <?php
                                if(!empty($contractGoods)){
                                    echo $contractGoods->quantity.Map::$v['goods_unit'][$contractGoods['unit']]['name'].'±'.($contractGoods->more_or_less_rate * 100).'%';
                                }else{
                                    echo '';
                                }
                                ?>
                            </td>
                            <td class="td-divide" style="vertical-align: middle;"><?php echo $val["quantity"].Map::$v['goods_unit'][$contractGoods->unit_store]['name']; ?></td>
                            <?php if(Utility::isNotEmpty($val->stockDeliveryDetail)): ?>
                                <td colspan="5" style="padding: 0 !important;">
                                    <table
                                        class="table table-fixed in-table"
                                        style="margin-bottom: 0px">
                                        <?php foreach($val->stockDeliveryDetail as $r): ?>
                                            <tr>
                                                <td style="width: 21.4%;" title="<?php echo $r->stock->stockIn->code; ?>">
                                                    <?php
                                                    $code = $r->stock->stockIn->code;
                                                    if(!empty($r->cross_detail_id)){
                                                        $code .= '<br/>('.$r->crossStock->crossOrder->cross_code.')';
                                                    }
                                                    echo "<a target='_blank' class='text-link' href='/stockInList/view/?id=".$r->stock->stockIn->stock_in_id."&t=1'>".$code."</a>";
                                                    ?>
                                                </td>
                                                <td style="width: 21.4%;"><?php echo $r->quantity.Map::$v['goods_unit'][$contractGoods->unit_store]['name'] ?></td>
                                                <td style="width: 14.4%;" title="<?php echo !empty($r->stock->stockIn->store) ? $r->stock->stockIn->store->name : '虚拟库' ?>"><?php echo !empty($r->stock->stockIn->store) ? $r->stock->stockIn->store->name : '虚拟库' ?></td>
                                                <td style="width: 21.4%;">
                                                    <?php
                                                    $total_out = 0;
                                                    if(!empty($deliveryOrder->stockOuts)){
                                                        foreach($deliveryOrder->stockOuts as $k => $v){
                                                            if($v['status'] == 1 || $v['status'] == 30){//已出库
                                                                if(!empty($v->details)){
                                                                    foreach($v->details as $m){
                                                                        if($m['stock_detail_id'] == $r->stock_detail_id) //t_stock_delivery_detail 的stock_detail_id 和  t_stock_out_detail 的stock_detail_id对应
                                                                            $total_out += $m['quantity'];
                                                                    }
                                                                }
                                                            }

                                                        }
                                                    }
                                                    echo sprintf("%.4f", $total_out).Map::$v['goods_unit'][$contractGoods->unit_store]['name'];
                                                    ?>
                                                </td>
                                                <td style="width: 21.4%;">
                                                    <?php echo sprintf("%.4f", round($r->quantity - $total_out, 4)).Map::$v['goods_unit'][$contractGoods->unit_store]['name']; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="flex-grid form-group">
                <label class="col full-space field flex-grid">
                    <span class="w-fixed line-h--text">备注:</span>
                    <span class="form-control-static line-h--text flex-grow"><?php echo $deliveryOrder['remark'];?></span>
                </label>
            </div>
        </div>
        <?php
        $status = $deliveryOrder['status']>DeliveryOrder::STATUS_PASS ? DeliveryOrder::STATUS_PASS : $deliveryOrder['status'];
        $this->renderPartial("/common/new_stockInOrOutStatusInfo", array(
                'isCanShowStatus'=> DeliveryOrderService::isCanShowStatus($status),
                'statusName'=> Map::$v['delivery_order_status'][$status],
                'remark' => $deliveryOrder['remark'],
                'isCanShowAuditStatus' => DeliveryOrderService::isCanShowAuditStatus($status),
                'isShowAuditRemark' => DeliveryOrderService::isShowAuditRemark($status),
                'id' => $deliveryOrder['order_id'],
                'businessIds'=> FlowService::BUSIONESS_DELIVERY_ORDER_CHECK,
            )
        );
        ?>
    </div>
</div>

<script>
    function editDeliveryOrder(order_id) {
        location.href = "/<?php echo $this->getId() . '/edit'?>?id=" + order_id;
        event.stopPropagation();
    }

    function submitDeliveryOrder(order_id) {
        inc.vueConfirm({content:'您确定要提交当前发货单信息吗，该操作不可逆？',type: 'warning',onConfirm:function(){
            doSubmitDeliveryOrder(order_id);
        }});

        event.stopPropagation();
    }

    function doSubmitDeliveryOrder(order_id) {
        var formData = "id=" + order_id;
        $.ajax({
            type: "POST",
            url: "/<?php echo $this->getId() ?>/submit",
            data: formData,
            dataType: "json",
            success: function (json) {
                if (json.state == 0) {
                    inc.vueMessage({duration: 500,type: 'success', message: '操作成功'});
                    location.href = "/<?php echo $this->getId() ?>/";
                } else {
                    inc.vueAlert({title:  '错误',content: json.data});
                }
            },
            error: function (data) {
                inc.vueAlert({title:  '错误',content: "操作失败！" + data.responseText});
            }
        });
    }
</script>