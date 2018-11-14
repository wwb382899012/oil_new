<?php if(is_array($stockIns) && count($stockIns) > 0): ?>
    <?php foreach($stockIns as $key => $stockIn): ?>
        <div class="z-card<?php echo (isset($is_close_card) && $is_close_card) ? " in-fold" : ""; ?>">
            <div class="content-wrap-title">
                <h3 class="z-card-header">
                    <b><?php echo $stockIn['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE ? $stockIn['store']['name'] : '虚拟库' ?>入库单<!--
                        --><span style="margin-left: 20px; font-weight: normal;">NO.<span><?php echo $stockIn['code'] ?></span></span></b>
                    <?php if(!isset($is_unfold) || false == $is_unfold):?>
                        <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i class="icon icon-shouqizhankai"></i></p>
                    <?php endif;?>
                    <?php if (StockInService::isCanEdit($stockIn['status'])): ?>
                        <div class="pull-right box-tools">
                            <a href="javascript: void 0" class="z-btn-action" onclick="edit(<?php echo $stockIn['stock_in_id'] ?>)">修改</a>&nbsp;
                            <a href="javascript: void 0" class="z-btn-action z-btn-primary" onclick="submit(<?php echo $stockIn['stock_in_id'] ?>)">提交</a>&nbsp;
                        </div>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="z-card-body">
                <div class="busi-detail">
                    <div class="flex-grid form-group">
                        <?php
                        $is_split = StockInService::isVirtualBill($stockIn->is_virtual);
                        $this->renderPartial("/components/new_attachmentsDropdown", array(
                                'id' => $stockIn['stock_in_id'],
                                'map_key'=>'stock_in_attachment_type',
                                'attach_type'=>ConstantMap::STOCK_IN_ATTACH_TYPE,
                                'attachment_type'=>Attachment::C_STOCK_IN,
                                'controller'=>'stockIn',
                            )
                        );
                        ?>
                        <label class="col col-count-2 field flex-grid">
                            <span class="w-fixed line-h--text">入库日期:</span>
                            <span
                                class="form-control-static line-h--text"><?php echo $stockIn['entry_date']; ?></span>
                        </label>
                        <?php if($is_split):?>
                            <label class="col col-count-2 field flex-grid">
                                <span class="w-fixed line-h--text">被平移入库单编号:</span>
                                <span class="form-control-static ellipsis line-h--text">
                                    <a class="text-link"
                                       href="/stockInList/view?id=<?php echo $stockIn->originalOrder->stock_in_id; ?>&t=1"
                                       target="_blank"
                                       title="<?php echo $stockIn->originalOrder->code; ?>"><?php echo $stockIn->originalOrder->code; ?></a>
                                </span>
                            </label>
                        <?php endif;?>
                    </div>
                    <div class="flex-grid form-group">
                        <table class="table table-fixed">
                            <thead>
                            <tr>
                                <th style="width: 80px;">品名</th>
                                <th style="width: 160px;">入库单数量</th>
                                <th style="width: 80px;">换算比例</th>
                                <th style="width: 200px;">备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($stockIn['details'] as $val):?>
                                <?php if($is_split AND 0 == $val->quantity){continue;} ?>
                                <tr>
                                    <td><?php echo $val['goods']['name']; ?></td>
                                    <td>
                                        <?php
                                        $amountInfo = Utility::numberFormatToDecimal($val["quantity"], 4) . Map::$v['goods_unit'][$val['unit']]['name'];
                                        if (!empty($val['sub']) && !empty($val['sub']['unit'])) {
                                            $amountInfo .= '/' . Utility::numberFormatToDecimal($val['sub']['quantity'], 4) . Map::$v['goods_unit'][$val['sub']['unit']]['name'];
                                        }
                                        echo $amountInfo;
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo !empty($val['unit_rate']) ? $val['unit_rate'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo $val['remark']; ?>
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
                            <?php $remark_arr = explode("；作废理由：", $stockIn['remark']); ?>
                            <?php echo (isset($remark_arr[1])) ? $remark_arr[0] : $stockIn['remark']; ?>
                        </span>
                        </label>
                    </div>
                </div>

                <?php
                $status = $stockIn['status']==StockIn::STATUS_SETTLED ? StockIn::STATUS_PASS : $stockIn['status'];
                $this->renderPartial("/common/new_stockInOrOutStatusInfo", array(
                        'isCanShowStatus'=> StockInService::isCanShowStatus($status),
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
    <?php endforeach; ?>
<?php endif; ?>

<script>
    function edit(stock_in_id) {
        location.href = "/stockIn/edit?id=" + stock_in_id;
    }

    function submit(stock_in_id) {
        inc.vueConfirm({content:'您确定要提交当前入库单信息吗，该操作不可逆？',type: 'warning',onConfirm:function(){
            doSubmit(stock_in_id);
        }});
    }

    function doSubmit(stock_in_id) {
        var formData = "id=" + stock_in_id;
        $.ajax({
            type: "POST",
            url: "/stockIn/submit",
            data: formData,
            dataType: "json",
            success: function (json) {
                if (json.state == 0) {
                    inc.vueMessage({duration: 500,type: 'success', message: '操作成功',onClose:function(){
                        location.reload();
                    }});
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
