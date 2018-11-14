<?php if(is_array($outOrders) && count($outOrders) > 0): ?>
    <?php foreach($outOrders as $key => $outOrder): ?>
        <div class="z-card<?php echo (isset($is_close_card) && $is_close_card) ? " in-fold" : ""; ?>">
            <div class="content-wrap-title">
                <h3 class="z-card-header">
                    <?php echo empty($outOrder->details[0]->store->name) ? '虚拟库' : $outOrder->details[0]->store->name; ?>
                    &nbsp;出库单<!--
                    --><span class="title-code">NO.<span><?php echo $outOrder->code; ?></span></span>
                    <?php if(!isset($is_unfold) || false == $is_unfold):?>
                        <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i class="icon icon-shouqizhankai"></i></p>
                    <?php endif;?>
                    <?php if (StockOutService::isCanEdit($outOrder->status)):?>
                        <div class="pull-right box-tools">
                            <a href="javascript: void 0" class="z-btn-action" onclick="editOut(<?php echo $outOrder->out_order_id ?>)">修改</a>&nbsp;
                            <a href="javascript: void 0" class="z-btn-action z-btn-primary" onclick="submitOut(<?php echo $outOrder->out_order_id ?>)">提交</a>&nbsp;
                        </div>
                    <?php endif;?>
                </h3>

            </div>
            <div class="z-card-body">
                <div class="busi-detail">
                    <div class="flex-grid form-group">
                        <?php
                        $is_split = StockOutService::isVirtualBill($outOrder->is_virtual);
                        $this->renderPartial("/components/new_attachmentsDropdown", array(
                                'id' => $outOrder->out_order_id,
                                'map_key'=>'stock_delivery_attachment',
                                'attach_type'=>ConstantMap::STOCK_OUT_ATTACH_TYPE,
                                'attachment_type'=>Attachment::C_STOCK_OUT,
                                'controller'=>'stockOut',
                            )
                        );
                        ?>
                        <label class="col col-count-2 field flex-grid">
                            <span class="w-100 line-h--text">出库日期:</span>
                            <span
                                class="form-control-static ellipsis line-h--text"><?php echo $outOrder->out_date; ?></span>
                        </label>
                        <?php if($is_split):?>
                            <label class="col col-count-2 field flex-grid">
                                <span class="w-100 line-h--text">被平移出库单编号:</span>
                                <span
                                    class="form-control-static ellipsis line-h--text">
                                    <a class="text-link" target="_blank"  href="/stockOutList/view?id=<?php echo $outOrder->originalOrder->out_order_id ?>&t=1"
                                       title="<?php echo $outOrder->originalOrder->code ?>"><?php echo $outOrder->originalOrder->code ?? "" ?></a>
                                </span>
                            </label>
                        <?php endif;?>
                    </div>
                    <div class="flex-grid form-group">
                        <table class="table table-fixed">
                            <thead>
                            <tr>
                                <th style="width: 160px;">销售合同编号</th>
                                <th style="width: 80px;">品名</th>
                                <th style="width: 160px;">配货入库单编号</th>
                                <th style="width: 80px;">配货数量</th>
                                <th style="width: 80px;">本次出库数量</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($outOrder->details as $val): ?>
                                <?php if($is_split):?>
                                    <?php if(0 == $val->quantity){continue;} ?>
                                    <tr>
                                        <td>
                                            <a class="text-link" target="_blank" title="<?php echo $val->contract->contract_code; ?>"
                                               href="/contract/detail/?id=<?php echo $val->contract->contract_id; ?>&t=1">
                                                <?php echo $val->contract->contract_code; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $val->goods->name; ?></td>
                                        <td style="text-align: center">-</td>
                                        <td style="text-align: center">-</td>
                                        <td>
                                            <?php echo $val->quantity; ?>
                                            <?php echo $this->map['goods_unit'][$val->stock->unit]['name']; ?>
                                        </td>
                                    </tr>
                                    <?php continue;?>
                                <?php endif;?>
                                <tr>
                                    <td>
                                        <a class="text-link" target="_blank" title="<?php echo $val->contract->contract_code; ?>"
                                           href="/contract/detail/?id=<?php echo $val->contract->contract_id; ?>&t=1">
                                            <?php echo $val->contract->contract_code; ?>
                                        </a>
                                    </td>
                                    <td><?php echo $val->goods->name; ?></td>
                                    <td>
                                        <a class="text-link" target='_blank' title="<?php echo $val->stock->stockIn->code; ?>"
                                           href='/stockInList/view/?id=<?php echo $val->stock->stockIn->stock_in_id; ?>&t=1'>
                                            <?php echo $val->stock->stockIn->code; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo $val->stockDeliveryDetail->quantity; ?>
                                        <?php echo $this->map['goods_unit'][$val->stock->unit]['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $val->quantity; ?>
                                        <?php echo $this->map['goods_unit'][$val->stock->unit]['name']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex-grid form-group">
                        <label class="col full-space field flex-grid">
                            <span class="w-fixed line-h--text">备注:</span>
                            <span class="form-control-static line-h--text flex-grow">
                            <?php $remark_arr = explode("；作废理由：", $outOrder['remark']); ?>
                            <?php echo (isset($remark_arr[1])) ? $remark_arr[0] : $outOrder['remark']; ?>
                        </span>
                        </label>
                    </div>
                </div>

                <?php
                $status = $outOrder->status==StockOutOrder::STATUS_SETTLED ? StockOutOrder::STATUS_SUBMITED : $outOrder->status;
                $this->renderPartial("/common/new_stockInOrOutStatusInfo", array(
                        'isCanShowStatus'=>StockOutService::isCanShowStatus($status),
                        'isInvalid' => StockOutService::isInvalid($outOrder['status']),
                        'statusName'=> Map::$v['stock_out_status'][$status],
                        'remark' => $outOrder['remark'],
                        'isCanShowAuditStatus' => StockOutService::isCanShowAuditStatus($status),
                        'isShowAuditRemark' => StockOutService::isShowAuditRemark($status),
                        'id' => $outOrder['out_order_id'],
                        'businessIds'=> FlowService::BUSINESS_STOCK_OUT_CHECK,
                    )
                );
                ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    function editOut(id) {
        location.href = "/stockOut/edit?out_order_id=" + id;

        event.stopPropagation();
    }

    function submitOut(id) {
        inc.vueConfirm({content:'您确定要提交当前出库单信息吗，该操作不可逆？',type: 'warning',onConfirm:function(){
            var formData = "id=" + id;
            $.ajax({
                type: "POST",
                url: "/stockOut/submit",
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.vueMessage({duration: 500,type: 'success', message: '操作成功',onClose:function () {
                            location.href = "/<?php echo $this->getId() ?>/";
                        }});
                    } else {
                        inc.vueAlert({title:  '错误',content: json.data});
                    }
                },
                error: function (data) {
                    inc.vueAlert({title:  '错误',content: "操作失败！" + data.responseText});
                }
            });
        }});

        event.stopPropagation();
    }
</script>
