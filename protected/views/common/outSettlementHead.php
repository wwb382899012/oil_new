<style type="text/css">
    <!--
    .hide1{display:none;}
    -->
</style>

<?php if (is_array($outOrders) && count($outOrders)>0){ ?>
    <?php foreach ($outOrders as $key => $outOrder){ ?>
        <div class="box box-primary">
            <div class="box-header link with-border">
                <h3 class="box-title" style="text-align: center">
                    <b><?php echo empty($outOrder['store_name']) ? '虚拟库' : $outOrder['store_name']; ?>&nbsp;出库单&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo "<a href='/stockOutList/view/?id=".$outOrder['out_order_id']."&t=1' target='_blank'>".$outOrder['code']."</a>" ?></span></span></b>
                </h3>
                <div class="pull-right box-tools">
                    <p class="form-control-static form-control-static-custom">
                        <span class="label label-info">
                          <?php echo $this->map["stock_out_status"][$outOrder["status"]];?>
                        </span>
                    </p>
                </div>
            </div>
            <div class="box-body <?php echo !empty($key) ? 'hide1' : '' ?> form-horizontal">
                <div class="form-group">
                    <?php
                    $this->renderPartial("/components/attachmentsDropdown", array(
                            'id' => $outOrder['out_order_id'],
                            'map_key'=>'stock_delivery_attachment',
                            'attach_type'=>ConstantMap::STOCK_OUT_ATTACH_TYPE,
                            'attachment_type'=>Attachment::C_STOCK_OUT,
                            'controller'=>'stockOut',
                        )
                    );
                    ?>
                    <label class="col-sm-1 control-label">出库日期</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $outOrder['out_date'] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <?php if (is_array($outOrder['items']) && count($outOrder['items'])>0){?>
                            <table class="table table-striped table-bordered table-condensed table-hover table-layout">
                                <thead>
                                <tr>
<!--                                    <th style="width:200px;text-align:center">销售合同编号</th>-->
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
                                        <td style="text-align:center"><?php echo $val['goods_name'] ?></td>
                                        <td style="text-align:center">
                                            <a target='_blank' href='/stockInList/view/?id=<?php echo $outOrder['out_order_id'];?>&t=1'>
                                                <?php echo $val['stock_in_code'];?>
                                            </a>
                                        </td>
                                        <td style="text-align:center;">
                                            <?php echo $val['delivery_quantity']['quantity'] ?>
                                            <?php echo $this->map['goods_unit'][$val['delivery_quantity']['unit']]['name']?>
                                        </td>
                                        <td style="text-align:center">
                                            <?php echo $val['quantity']['quantity'] ?>
                                            <?php echo $this->map['goods_unit'][$val['quantity']['unit']]['name']?>
                                        </td>
                                        <td style="text-align:left;">
                                            <?php echo $val['remark'] ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
                <?php
                $status = $outOrder['status']==StockOutOrder::STATUS_SETTLED ? StockOutOrder::STATUS_SUBMITED : $outOrder['status'];
                $this->renderPartial("/common/stockInOrOutStatusInfo", array(
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



<script>
    /*$("div.box-header").each(function () {
        $(this).click(function () {
            $(this).next().toggle();
        });
    });*/
    $("div.link").unbind('click').click(function () {
        $(this).next().toggle();
    });
</script>

<?php $this->renderPartial("/stockOut/partial/commonJs");?>