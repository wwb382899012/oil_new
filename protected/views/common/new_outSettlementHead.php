

<?php if (is_array($outOrders) && count($outOrders)>0){ ?>
    <?php foreach ($outOrders as $key => $outOrder){ ?>
        <div class="z-card<?php echo (isset($is_close_card) && $is_close_card) ? ' in-fold' : ''; ?>">
            <div class="content-wrap-title">
                <h3 class="z-card-header">
                    <b>
                        <?php echo empty($outOrder['store_name']) ? '虚拟库' : $outOrder['store_name']; ?>出库单
                        <a class="title-code text-link" href="/stockOutList/view/?id=<?php echo $outOrder['out_order_id'] ?>&t=1" target="_blank"><span >NO.<span><?php echo $outOrder['code'] ?></span></span></a>
                    </b>
                    <?php if(!isset($is_unfold) || false == $is_unfold):?>
                        <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i class="icon icon-shouqizhankai"></i></p>
                    <?php endif;?>
                </h3>
            </div>
            <div class="z-card-body">
                <div class="busi-detail">
                    <div class="flex-grid form-group">
                        <?php
                        $this->renderPartial("/components/new_attachmentsDropdown", array(
                                'id' => $outOrder['out_order_id'],
                                'map_key'=>'stock_delivery_attachment',
                                'attach_type'=>ConstantMap::STOCK_OUT_ATTACH_TYPE,
                                'attachment_type'=>Attachment::C_STOCK_OUT,
                                'controller'=>'stockOut',
                            )
                        );
                        ?>
                        <label class="col col-count-2 field flex-grid">
                            <span class="w-fixed line-h--text">出库日期:</span>
                            <span class="form-control-static line-h--text"><?php echo $outOrder['out_date'] ?></span>
                        </label>
                    </div>

                    <div class="flex-grid form-group">
                        <?php if (is_array($outOrder['items']) && count($outOrder['items'])>0){?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <!-- <th style="width:200px;text-align:center">销售合同编号</th> -->
                                        <th style="width:150px;text-align:center">品名</th>
                                        <th style="width:200px;text-align:center">配货入库单号</th>
                                        <th style="width:150px;text-align:center">配货数量</th>
                                        <th style="width:150px;text-align:center">本次出库数量</th>
                                        <th style="text-align:left">备注</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($outOrder['items'] as $val){ ?>
                                    <tr>
                                        <!--<td style="text-align:center">
                                            <a title="合同详情" target="_blank" href="/contract/detail/?id=<?php /*echo $val['contract_id']; */?>&t=1">
                                                <?php /*echo $val->contract->contract_code; */?>
                                            </a>
                                        </td>-->
                                        <td style=""><?php echo $val['goods_name'] ?></td>
                                        <td style="white-space: nowrap;">
                                            <a target='_blank' class="text-link" href='/stockInList/view/?id=<?php echo $outOrder['out_order_id'];?>&t=1'>
                                                <?php echo $val['stock_in_code'];?>
                                            </a>
                                        </td>
                                        <td style="">
                                            <?php echo $val['delivery_quantity']['quantity'] ?>
                                            <?php echo $this->map['goods_unit'][$val['delivery_quantity']['unit']]['name']?>
                                        </td>
                                        <td style="">
                                            <?php echo $val['quantity']['quantity'] ?>
                                            <?php echo $this->map['goods_unit'][$val['quantity']['unit']]['name']?>
                                        </td>
                                        <td style="">
                                            <?php echo $val['remark'] ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
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
                $status = $outOrder['status']==StockOutOrder::STATUS_SETTLED ? StockOutOrder::STATUS_SUBMITED : $outOrder['status'];
                $this->renderPartial("/common/new_stockInOrOutStatusInfo", array(
                        'isCanShowStatus'=>StockOutService::isCanShowStatus($status),
                        'isInvalid' => StockOutService::isInvalid($status),
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
    <?php } ?>
<?php } ?>

<!--
变量有：$outOrder，$isShowEditButton，$isShowBackButton，$isNotFirstItem
-->

<?php $this->renderPartial("/stockOut/partial/commonJs");?>