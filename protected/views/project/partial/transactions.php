<div class="form-group">
    <label for="" class="col-sm-2 control-label">交易明细</label>
    <div class="col-sm-10">
        <?php
        if (!empty($transactions)) {
            ?>
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                <tr>
                    <th style="width:120px;text-align:center">品名</th>
                    <!-- <th style="width:100px;text-align:center">规格</th> -->
                    <th style="width:80px;text-align:center">数量</th>
                    <th style="width:80px;text-align:center">数量单位</th>
                    <?php if (!empty($data['up_partner_id'])) { ?>
                        <th style="width:100px;text-align:center">采购单价</th>
                    <?php }
                    if (!empty($data['down_partner_id'])) { ?>
                        <th style="width:100px;text-align:center">销售单价</th>
                    <?php }
                    if (!empty($data['up_partner_id'])) { ?>
                        <th style="width:120px;text-align:center">采购总价</th>
                        <?php
                    }
                    if (!empty($data['down_partner_id'])) { ?>
                        <th style="width:120px;text-align:center">销售总价</th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($transactions as $v) { ?>
                    <tr>
                        <td style="text-align:center"><?php echo $v["goods_name"] ?></td>
                        <!-- <td style="text-align:center"><?php echo $v["goods_describe"] ?></td> -->
                        <td style="text-align:center"><?php echo $v["quantity"] ?></td>
                        <td style="text-align:center"><?php echo $this->map["goods_unit"][$v["unit"]]['name'] ?></td>
                        <?php if (!empty($data['up_partner_id'])) { ?>
                            <td style="text-align:center"><?php echo $this->map["currency"][$v["purchase_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["purchase_price"]) ?></td>
                        <?php }
                        if (!empty($data['down_partner_id'])) { ?>
                            <td style="text-align:center"><?php echo $this->map["currency"][$v["sell_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["sale_price"]) ?></td>
                        <?php }
                        if (!empty($data['up_partner_id'])) { ?>
                            <td style="text-align:center"><?php echo $this->map["currency"][$v["purchase_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["purchase_amount"]) ?></td>
                            <?php
                        }
                        if (!empty($data['down_partner_id'])) { ?>
                            <td style="text-align:center"><?php echo $this->map["currency"][$v["sell_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["sale_amount"]) ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php }
        ?>
    </div>
</div>