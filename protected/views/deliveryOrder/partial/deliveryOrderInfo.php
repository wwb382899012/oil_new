<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title" style="text-align: center">
            <b><?php echo $deliveryOrder['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE ? '经仓' : '直调' ?>发货单&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo $deliveryOrder['code'] ?></span></span></b>
        </h3>
        <div class="pull-right box-tools">
            <?php if ($deliveryOrder['status'] < DeliveryOrder::STATUS_SUBMIT): ?>
                <button type="button" class="btn btn-sm btn-primary" onclick="edit(<?php echo $deliveryOrder['order_id'] ?>)">修改</button>&nbsp;
                <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo $deliveryOrder['order_id'] ?>)">提交</button>
            <?php endif; ?>
            <?php if(isset($isShowBackButton) && $isShowBackButton): ?>
                &nbsp;<button type="button" class="btn btn-default btn-sm" onclick="back()">返回</button>
            <?php endif;?>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">交易主体</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a target="_blank" href="/corporation/detail?t=1&id=<?php echo $deliveryOrder->corporation->corporation_id?>">
                        <?php echo $deliveryOrder->corporation->name?>
                    </a>
                </p>
            </div>
            <label class="col-sm-2 control-label">销售合同编号</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a title="合同详情" target="_blank" href="/contract/detail/?id=<?php echo  $deliveryOrder->contract->contract_id;?>&t=1">
                        <?php echo $deliveryOrder->contract->contract_code; ?>
                    </a>
                </p>
            </div>
            <label class="col-sm-2 control-label">下游合作方</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a target="_blank" id="" title="合作方详情" href="/partner/detail/?id=<?php echo $deliveryOrder->partner->partner_id;?>&t=1">
                        <?php echo $deliveryOrder->partner->name ?>
                    </a></p>
            </div>
            <label class="col-sm-2 control-label">预计发货日期</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $deliveryOrder['delivery_date'] ?></p>
            </div>
            <?php
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
        <?php if (Utility::isNotEmpty($deliveryOrder->details)): ?>
            <div class="col-sm-12">
                <table class="table table-striped table-bordered table-layout table-condensed table-hover">
                    <thead>
                    <tr>
                        <th style="width:150px;text-align:center">品名</th>
                        <th style="width:120px;text-align:center">合同数量</th>
                        <th style="width:90px;text-align:center">发货数量</th>
                        <th style="width:180px;text-align:center">配货入库单编号</th>
                        <th style="width:100px;text-align:center">配货数量</th>
                        <th style="width:100px;text-align:center">出库</th>
                        <th style="width:100px;text-align:center">总出库数量</th>
                        <th style="width:100px;text-align:center">未出库数量</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($deliveryOrder->details as $val) {
                        $contract = $val->contract;
                        $contractGoods = $val->contractGoods;
                        ?>
                        <tr>
                            <td style="text-align:center;vertical-align: middle;"><?php echo $val->goods->name ?></td>
                            <td style="text-align:center;vertical-align: middle;">
                                <?php
                                if (!empty($contractGoods)) {
                                    echo $contractGoods->quantity . Map::$v['goods_unit'][$contractGoods['unit']]['name'] . '±' . ($contractGoods->more_or_less_rate * 100) . '%';
                                } else {
                                    echo '';
                                }
                                ?>
                            </td>
                            <td style="text-align:center;vertical-align: middle;"><?php echo $val["quantity"] . Map::$v['goods_unit'][$contractGoods->unit_store]['name'] ?></td>
                            <?php if (Utility::isNotEmpty($val->stockDeliveryDetail)):?>
                                <td colspan="5" style="padding: 0;">
                                    <table class="table table-striped table-layout table-bordered table-condensed table-hover" style="margin-bottom: 0px">
                                        <?php foreach ($val->stockDeliveryDetail as $r):?>
                                            <tr>
                                                <td style="width:180px;text-align:center" title="<?php echo $r->stock->stockIn->code ?>">
                                                    <?php
                                                    $code = $r->stock->stockIn->code;
                                                    if (!empty($r->cross_detail_id)) {
                                                        $code .= '<br/>(' . $r->crossStock->crossOrder->cross_code . ')';
                                                    }
                                                    echo "<a target='_blank' href='/stockInList/view/?id=".$r->stock->stockIn->stock_in_id."&t=1'>".$code."</a>";
                                                    ?>
                                                </td>
                                                <td style="width:100px;text-align:center;vertical-align: middle!important;"><?php echo $r->quantity . Map::$v['goods_unit'][$contractGoods->unit_store]['name'] ?></td>
                                                <td style="width:100px;text-align:center;vertical-align: middle!important;" title="<?php echo !empty($r->stock->stockIn->store) ? $r->stock->stockIn->store->name : '虚拟库' ?>"><?php echo !empty($r->stock->stockIn->store) ? $r->stock->stockIn->store->name : '虚拟库' ?></td>
                                                <td style="width:100px;text-align:center;vertical-align: middle!important;">
                                                    <?php 
                                                    $total_out=0;
                                                    if(!empty($deliveryOrder->stockOuts)){
                                                        foreach ($deliveryOrder->stockOuts as $k=>$v){
                                                            if($v['status']==1 || $v['status']==30){//已出库
                                                                if(!empty($v->details)){
                                                                    foreach ($v->details as $m){
                                                                        if($m['stock_detail_id']==$r->stock_detail_id) //t_stock_delivery_detail 的stock_detail_id 和  t_stock_out_detail 的stock_detail_id对应
                                                                            $total_out+=$m['quantity'];
                                                                    }
                                                                }
                                                            }

                                                        }

                                                    }
                                                    echo sprintf("%.4f",$total_out). Map::$v['goods_unit'][$contractGoods->unit_store]['name'];
                                                    ?>
                                                </td>
                                                <td style="width:100px;text-align:center;vertical-align: middle!important;">
                                                    <?php echo sprintf("%.4f",round($r->quantity - $total_out,4)) . Map::$v['goods_unit'][$contractGoods->unit_store]['name'];?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </td>

                            <?php else:?>

                            <?php endif; ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php
            $status = $deliveryOrder['status']>DeliveryOrder::STATUS_PASS ? DeliveryOrder::STATUS_PASS : $deliveryOrder['status'];
            $this->renderPartial("/common/stockInOrOutStatusInfo", array(
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
        <?php endif; ?>
    </div>
</div>
<script>
    function back() {
        <?php
        if (!empty($_GET["url"])) {
            echo 'location.href="' . $this->getBackPageUrl() . '";';
        } else {
            echo "history.back();";
        }
        ?>
        event.stopPropagation();
    }

    function edit(order_id) {
        location.href = "/<?php echo $this->getId() . '/edit'?>?id=" + order_id;
        event.stopPropagation();
    }

    function submit(order_id) {
        layer.confirm("您确定要提交当前发货单信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
            var formData = "id=" + order_id;
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/submit",
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg(json.data, {icon: 6, time: 1000}, function () {
                            location.href = "/<?php echo $this->getId() ?>/";
                        });
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
            layer.close(index);
        });

        event.stopPropagation();
    }
</script>