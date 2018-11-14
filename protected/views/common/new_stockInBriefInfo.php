<div class="z-card<?php echo (isset($is_close_card) && $is_close_card) ? " in-fold" : ""; ?>">
    <div class="content-wrap-title">
        <h3 class="z-card-header">
            <b>
                <?php echo empty($stockIn['store_name']) ? '虚拟库' : $stockIn['store_name'].'仓库'; ?>入库单
                <a class="title-code text-link" href="/stockInList/view/?id=<?php echo $stockIn['stock_in_id'] ?>&t=1" target="_blank">
                    <span>NO.<span><?php echo $stockIn['code'] ?></span>
                    </span>
                </a>
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
                    <span class="form-control-static line-h--text"><?php echo $stockIn['entry_date']; ?></span>
                </label>
            </div>
            <div class="flex-grid form-group">
                <?php
                if (!empty($stockIn['items'])) :
                    ?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="width:80px;">品名</th>
                            <th style="width:80px;">入库单数量</th>
                            <th style="width:60px;">换算比例</th>
                            <th style="width:60px;">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($stockIn['items'] as $v) : ?>
                            <tr>
                                <td style=""><?php echo $v['goods_name'] ?></td>
                                <td style=""><?php echo Utility::numberFormatToDecimal($v["quantity"]["quantity"], 4) ?><?php echo $this->map["goods_unit"][$v["quantity"]["unit"]]['name'] ?>
                                    <?php
                                    if(!empty($v['quantity_sub']['unit']) && $v['quantity']['unit']!=$v['quantity_sub']['unit']) {
                                        echo '/'. Utility::numberFormatToDecimal($v['quantity_sub']['quantity'], 4). $this->map["goods_unit"][$v["quantity_sub"]["unit"]]['name'];
                                    }
                                    ?>
                                </td>
                                <td style=""><?php echo $v["unit_rate"] ?></td>
                                <td style=""><?php echo $v['remark']; ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endif;?>

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
        $status = $stockIn['status']>=StockIn::STATUS_PASS ? StockIn::STATUS_PASS : $stockIn['status'];
        $this->renderPartial("/common/new_stockInOrOutStatusInfo", array(
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

