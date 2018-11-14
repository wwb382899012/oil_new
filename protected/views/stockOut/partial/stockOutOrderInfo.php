<!--
变量有：$outOrder，$isShowEditButton，$isShowBackButton，$isNotFirstItem
-->

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title" style="text-align: center">
            <b><?php echo empty($outOrder->details[0]->store->name) ? '虚拟库' : $outOrder->details[0]->store->name; ?>&nbsp;出库单&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo $outOrder->code ?></span></span></b>
        </h3>
        <div class="pull-right box-tools">
            <?php if (isset($isShowEditButton) && $isShowEditButton && StockOutService::isCanEdit($outOrder->status)):?>
                <button type="button" class="btn btn-sm btn-primary" onclick="editOut(<?php echo $outOrder->out_order_id ?>)">修改</button>&nbsp;
                <button type="button" class="btn btn-sm btn-danger" onclick="submitOut(<?php echo $outOrder->out_order_id ?>)">提交</button>&nbsp;
            <?php endif;?>
            <?php if(isset($isShowBackButton) && $isShowBackButton): ?>
                &nbsp;<button type="button" class="btn btn-default btn-sm" onclick="back()">返回</button>
            <?php else:?>
                <?php if(isset($this->map["stock_out_status"][$outOrder["status"]])):?>
                    <div class="pull-right box-tools">
                        <p class="form-control-static form-control-static-custom">
                            <span class="label label-info">
                            <?php echo $this->map["stock_out_status"][$outOrder["status"]];?>
                            </span>
                        </p>
                    </div>
                <?php endif;?>
            <?php endif;?>
        </div>
    </div>
    <div class="box-body <?php echo (isset($isNotFirstItem) && $isNotFirstItem) ? 'hide1' : '' ?> form-horizontal">
        <div class="form-group">
            <?php
            $is_split = StockOutService::isVirtualBill($outOrder->is_virtual);
            $div_class = $is_split ? "col-sm-2" : "col-sm-4";
            $this->renderPartial("/components/attachmentsDropdown", array(
                    'id' => $outOrder->out_order_id,
                    'map_key'=>'stock_delivery_attachment',
                    'attach_type'=>ConstantMap::STOCK_OUT_ATTACH_TYPE,
                    'attachment_type'=>Attachment::C_STOCK_OUT,
                    'controller'=>'stockOut',
                    'div_class'=> $div_class,
                )
            );
            ?>
            <label class="col-sm-1 control-label">出库日期</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $outOrder->out_date ?></p>
            </div>
            <?php if($is_split):?>
                <label class="col-sm-2 control-label">被平移出库单编号</label>
                <div class="col-sm-2">
                    <p class="form-control-static"><?php echo $outOrder->originalOrder['code'] ?? "" ?></p>
                </div>
            <?php endif;?>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <?php if (is_array($outOrder->details) && count($outOrder->details)>0):?>
                    <table class="table table-striped table-bordered table-condensed table-hover table-layout">
                        <thead>
                        <tr>
                            <th style="width:200px;text-align:center">销售合同编号</th>
                            <th style="width:150px;text-align:center">品名</th>
                            <th style="width:200px;text-align:center">配货入库单号</th>
                            <th style="width:150px;text-align:center">配货数量</th>
                            <th style="text-align:left">本次出库数量</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($outOrder->details as $val): ?>
                            <tr>
                                <td style="text-align:center">
                                    <a title="合同详情" target="_blank" href="/contract/detail/?id=<?php echo $val->contract->contract_id; ?>&t=1">
                                        <?php echo $val->contract->contract_code; ?>
                                    </a>
                                </td>
                                <td style="text-align:center"><?php echo $val->goods->name ?></td>
                                <td style="text-align:center">
                                    <a target='_blank' href='/stockInList/view/?id=<?php echo $val->stock->stockIn->stock_in_id;?>&t=1'>
                                        <?php echo $val->stock->stockIn->code;?>
                                    </a>
                                </td>
                                <td style="text-align:center">
                                    <?php echo $val->stockDeliveryDetail->quantity ?>
                                    <?php echo $this->map['goods_unit'][$val->stock->unit]['name']?>
                                </td>
                                <td style="text-align:left;">
                                    <?php echo $val->quantity ?>
                                    <?php echo $this->map['goods_unit'][$val->stock->unit]['name']?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <?php
        $status = $outOrder->status==StockOutOrder::STATUS_SETTLED ? StockOutOrder::STATUS_SUBMITED : $outOrder->status;
        $this->renderPartial("/common/stockInOrOutStatusInfo", array(
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