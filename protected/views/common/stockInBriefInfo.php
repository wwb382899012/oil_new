<div class="box box-primary">
    <div class="box-header link with-border">
        <h3 class="box-title">
            <?php echo empty($stockIn['store_name']) ? '虚拟库' : $stockIn['store_name'].'仓库'; ?>入库单 &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="/stockInList/view/?id=<?php echo $stockIn['stock_in_id'] ?>&t=1" target="_blank">
                <span style="font-size: 16px;">NO.<span><?php echo $stockIn['code'] ?></span>
                </span>
            </a>
        </h3>
    </div>
    <div class="box-body hide1 form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">入库日期</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $stockIn['entry_date'] ?></p>
            </div>
            <?php
            $this->renderPartial("/components/attachmentsDropdown", array(
                    'id' => $stockIn['stock_in_id'],
                    'map_key'=>'stock_in_attachment_type',
                    'attach_type'=>ConstantMap::STOCK_IN_ATTACH_TYPE,
                    'attachment_type'=>Attachment::C_STOCK_IN,
                    'controller'=>'stockIn',
                )
            );
            ?>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <?php
                if (!empty($stockIn['items'])) :
                    ?>
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                        <tr>
                            <th style="width:80px;text-align:center">品名</th>
                            <th style="width:80px;text-align:center">入库单数量</th>
                            <th style="width:60px;text-align:center">换算比例</th>
                            <th style="width:60px;text-align:center">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($stockIn['items'] as $v) : ?>
                            <tr>
                                <td style="text-align:center"><?php echo $v['goods_name'] ?></td>
                                <td style="text-align:center"><?php echo Utility::numberFormatToDecimal($v["quantity"]["quantity"], 4) ?><?php echo $this->map["goods_unit"][$v["quantity"]["unit"]]['name'] ?>
                                <?php
                                if(!empty($v['quantity_sub']['unit']) && $v['quantity']['unit']!=$v['quantity_sub']['unit']) {
                                    echo '/'. Utility::numberFormatToDecimal($v['quantity_sub']['quantity'], 4). $this->map["goods_unit"][$v["quantity_sub"]["unit"]]['name'];
                                }
                                ?>
                                </td>
                                <td style="text-align:center"><?php echo $v["unit_rate"] ?></td>
                                <td style="text-align:center"><?php echo $v['remark']; ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endif;?>
                <?php
                $status = $stockIn['status']>=StockIn::STATUS_PASS ? StockIn::STATUS_PASS : $stockIn['status'];
                $this->renderPartial("/common/stockInOrOutStatusInfo", array(
                        'isCanShowStatus'=>StockInService::isCanShowStatus($stockIn['status']),
                        'isInvalid' => StockInService::isInvalid($stockIn['status']),
                        'statusName'=> Map::$v['stock_in_status'][$status],
                        'remark' => $stockIn['remark'],
                        'isCanShowAuditStatus' => StockInService::isCanShowAuditStatus($stockIn['status']),
                        'isShowAuditRemark' => StockInService::isShowAuditRemark($stockIn['status']),
                        'id' => $stockIn['stock_in_id'],
                        'businessIds'=> FlowService::BUSINESS_STOCK_IN_CHECK,
                    )
                );
                ?>
            </div>
        </div>
    </div>
</div>
<script>
    $("div.link").unbind('click').click(function () {
        $(this).next().toggle();
    });
</script>
