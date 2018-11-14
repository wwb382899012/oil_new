<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">入库单库存变化详情</h3>
    </div>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <?php
            if (Utility::isNotEmpty($stockDetail)) {
                foreach ($stockDetail as $key => $row) { ?>
                    <li <?php if ($key == 0) { ?> class="active" <?php } ?>>
                        <a href="#goods_<?php echo $row['goods_id'] ?>" data-toggle="tab"><?php echo $row['goods_name'] ?></a>
                        <input type="hidden" value="<?php echo $row['stock_id'] ?>">
                    </li>
                <?php }
            } ?>
        </ul>
        <div class="tab-content">
            <?php
            if (Utility::isNotEmpty($stockDetail)) {
                foreach ($stockDetail as $key => $row) { ?>
                    <div class="tab-pane <?php if ($key == 0) {
                        echo 'active';
                    } ?>" id="goods_<?php echo $row['goods_id']; ?>">
                        <?php
                        if (Utility::isNotEmpty($row['stock_detail'])) { ?>
                            <table class="table table-striped table-bordered table-condensed table-hover">
                                <thead>
                                <tr>
                                    <th style="width:100px;text-align:center">日期</th>
                                    <th style="width:160px;text-align:center">单据编号</th>
                                    <th style="width:100px;text-align:center">出入库类型</th>
                                    <th style="width:100px;text-align:center">出入库方式</th>
                                    <th style="width:200px;text-align:right">数量</th>
                                    <th style="width:200px;text-align:right">库存数量</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($row['stock_detail'] as $v) { ?>
                                    <tr>
                                        <td style="text-align:center"><?php echo $v['op_date']; ?></td>
                                        <td style="text-align:center">
                                            <?php
                                            if ($v['type'] == StockLog::TYPE_IN) {
                                                $code = '<a href="#">' . $v['code'] . '</a>';
                                            } elseif ($v['type'] == StockLog::TYPE_OUT) {
                                                $code = '<a target="_blank" href="/stockOutList/view?id=' . $v['id'] . '&t=1">' . $v['code'] . '</a>';
                                            }
                                            if ($v['method'] == StockLog::METHOD_STOCK_CHECK) {
                                                $code = '<a target="_blank" href="/stockInventory/detail?corp_id=' . $v['corporation_id'] . '&store_id=' . $v['store_id'] . '&goods_id=' . $v['goods_id'] . '&unit=' . $v['unit'] . '&t=1">' . $v['id'] . '</a>';
                                            }
                                            if (!empty($v['cross_code'])) {
                                                $code .= '(<span class="text-red">' . $v['cross_code'] . '</span>)';
                                            }
                                            echo $code;
                                            ?>
                                        </td>
                                        <td style="text-align:center"><?php echo $v['type_desc'] ?></td>
                                        <td style="text-align:center"><?php echo $v['method_desc'] ?></td>
                                        <td style="text-align:right">
                                            <?php
                                            $quantityDesc = Utility::numberFormatToDecimal($v['quantity'], 4) . Map::$v['goods_unit'][$v['unit']]['name'];
                                            echo $v['quantity'] > 0 ? '+' . $quantityDesc : $quantityDesc;
                                            ?>
                                        </td>
                                        <td style="text-align:right"><?php echo Utility::numberFormatToDecimal($v['quantity_balance'], 4) . Map::$v['goods_unit'][$v['unit']]['name'] ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>

                    </div>
                <?php }
            } ?>
        </div>
    </div>
</div>