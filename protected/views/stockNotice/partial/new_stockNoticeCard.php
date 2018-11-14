<?php if(Utility::isNotEmpty($stockNotices)): ?>
    <?php foreach($stockNotices as $key => $row): ?>
        <div class="z-card<?php echo (isset($is_close_card) && $is_close_card) ? " in-fold" : ""; ?>">
            <div class="content-wrap-title">
                <h3 class="z-card-header">
                    <b><?php echo Map::$v['stock_notice_delivery_type'][$row['type']].'入库通知单' ?>&nbsp;&nbsp;&nbsp;&nbsp;
                        <a target="_blank" href="/stockIn/detail/?id=<?php echo $row['batch_id']; ?>&t=1" title="<?php echo $row['code']; ?>" class="text-link">
                            <span>NO.<span><?php echo $row['code']; ?></span></span>
                        </a>
                        <?php if($row['status'] > StockNotice::STATUS_SETTLE_SUBMIT): ?>
                            <span class="text-red">(已结算)</span>
                        <?php endif; ?>
                    </b>
                    <?php if(!isset($is_unfold) || false == $is_unfold):?>
                        <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i class="icon icon-shouqizhankai"></i></p>
                    <?php endif;?>
                    <?php if ($row['status'] < StockNotice::STATUS_SUBMIT): ?>
                        <div class="pull-right box-tools">
                            <a href="javascript: void 0" class="z-btn-action" onclick="edit(<?php echo $row['batch_id'] ?>)">修改</a>&nbsp;
                            <a type="button" class="z-btn-action z-btn-primary" onclick="submit(<?php echo $row['batch_id'] ?>)">提交</a>&nbsp;
                        </div>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="z-card-body">
                <div class="busi-detail">

                    <?php if('add' == $this->action->getId()):?>
                        <div class="flex-grid form-group">
                            <?php
                            $this->renderPartial("/components/new_attachmentsDropdown", array(
                                    'id' => $row['batch_id'],
                                    'map_key'=>'stock_notice_attachment_type',
                                    'attach_type'=>ConstantMap::STOCK_NOTICE_ATTACH_TYPE,
                                    'attachment_type'=>Attachment::C_STOCK_NOTICE,
                                    'controller'=>'stockNotice',
                                )
                            );
                            ?>
                            <label class="col col-count-2 field flex-grid">
                                <span class="w-fixed line-h--text">入库通知单日期:</span>
                                <span class="form-control-static ellipsis line-h--text"><?php echo $row['batch_date'] ?></span>
                            </label>
                        </div>
                    <?php else:?>
                        <div class="flex-grid form-group">
                            <label class="col col-count-2 field flex-grid">
                                <span class="w-fixed line-h--text">上游合作方:</span>
                                <span class="form-control-static ellipsis line-h--text">
                                    <a class="text-link"
                                       href="/partner/detail/?id=<?php echo $row->contract->partner_id ?>&t=1"
                                       target="_blank"
                                       title="<?php echo $row->contract->partner->name ?>"><?php echo $row->contract->partner->name ?></a>
                                </span>
                            </label>
                            <label class="col col-count-2 field flex-grid">
                                <span class="w-fixed line-h--text">入库通知单日期:</span>
                                <span class="form-control-static ellipsis line-h--text"><?php echo $row['batch_date'] ?></span>
                            </label>
                        </div>

                        <div class="flex-grid form-group">
                            <?php
                            $this->renderPartial("/components/new_attachmentsDropdown", array(
                                    'id' => $row['batch_id'],
                                    'map_key'=>'stock_notice_attachment_type',
                                    'attach_type'=>ConstantMap::STOCK_NOTICE_ATTACH_TYPE,
                                    'attachment_type'=>Attachment::C_STOCK_NOTICE,
                                    'controller'=>'stockNotice',
                                )
                            );
                            ?>
                            <?php if($row->is_virtual):?>
                                <label class="col col-count-2 field flex-grid">
                                    <span class="w-fixed line-h--text">原入库通知单:</span>
                                    <span class="form-control-static ellipsis line-h--text">
                                    <a class="text-link"
                                       href="/stockIn/detail?id=<?php echo $row->originalOrder->batch_id; ?>&t=1"
                                       target="_blank"
                                       title="<?php echo $row->originalOrder->code; ?>"><?php echo $row->originalOrder->code; ?></a>
                                </span>
                                </label>
                            <?php endif;?>
                        </div>
                    <?php endif;?>

                    <?php if(Utility::isNotEmpty($row->details)): ?>
                        <div class="flex-grid form-group">
                            <table class="table table-fixed">
                                <thead>
                                <tr>
                                    <th style="width: 80px;">品名</th>
                                    <th style="width: 160px;">入库通知单数量</th>
                                    <th style="width: 80px;">换算比例</th>
                                    <th style="width: 80px;">仓库</th>
                                    <th style="width: 200px;">备注</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($row->details as $val): ?>
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
                                        <td><?php echo $row['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE ? $val->store->name : '虚拟库'; ?></td>
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
                                class="form-control-static line-h--text flex-grow"><?php echo $row->remark; ?></span>
                        </label>
                    </div>

                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    function edit(batch_id) {
        location.href = "/<?php echo $this->getId() ?>/edit?id=" + batch_id;
    }

    function submit(batch_id) {
        inc.vueConfirm({content:'您确定要提交当前入库通知单信息吗，该操作不可逆？',type: 'warning',onConfirm:function(){
            doSubmit(batch_id);
        }});
    }

    function doSubmit(batch_id){
        var formData = "id=" + batch_id;
        $.ajax({
            type: "POST",
            url: "/<?php echo $this->getId() ?>/submit",
            data: formData,
            dataType: "json",
            success: function (json) {
                if (json.state == 0) {
                    inc.vueMessage({duration: 500,type: 'success', message: '操作成功',onClose:function () {
                        location.reload();
                    }});
                }else {
                    inc.vueAlert({title:  '错误',content: json.data});
                }
            },
            error: function (data) {
                inc.vueAlert({title:  '错误',content: "操作失败！" + data.responseText});
            }
        });
    }

</script>
