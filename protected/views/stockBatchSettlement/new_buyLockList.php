<div>
    <ul class="el-button-group form-group">
        <li type="button" class="el-button el-button--default active">
            <a href="#tab1" data-toggle="tab">锁价记录</a>
        </li>
        <li type="button" class="el-button el-button--default">
            <a href="#tab2" data-toggle="tab">转月记录</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <div style="overflow-x: scroll;">
                <table class="table table-nowrap">
                    <thead>
                    <tr>
                        <th style="width:110px;text-align:center">锁价单编号</th>
                        <th style="width:120px;text-align:center">计价标的</th>
                        <th style="width:100px;text-align:center">锁价日期</th>
                        <th style="width:80px;text-align:center">基准价格</th>
                        <th style="width:120px;text-align:center">锁价数量</th>
                        <th style="width:80px;text-align:center">升贴水</th>
                        <th style="width:80px;text-align:center">月价差</th>
                        <th style="width:80px;text-align:center">调期费</th>
                        <th style="width:80px;text-align:center">结算价格</th>
                        <th style="width:120px;text-align:center">结算金额</th>
                        <th style="width:80px;text-align:center">备注</th>
                    </tr>
                    </thead>
                    <?php if(!empty($buyLocks)):
                        $price_base_sum = 0;
                        $quantity_sum = 0;
                        $amount_sum = 0;
                        ?>
                        <tbody>
                        <?php
                        $currency = $this->map['currency'][$buyLocks[0]['currency']]['ico'];
                        $unit     = $this->map['goods_unit'][$buyLocks[0]['unit']]['name'];
                        if(is_array($buyLocks))
                            foreach ($buyLocks as $lock) :
                                if(is_array($lock['lockPriceDetail']))
                                    foreach($lock['lockPriceDetail'] as $details):
                                        $price_base_sum+=$details['price_base'];
                                        $quantity_sum+=$details['quantity'];
                                        $amount_sum+=$details["amount"]*$details["quantity"];
                                        ?>
                                        <tr>
                                            <td style="text-align:left"><?php echo $details["lock_code"] ?></td>
                                            <td style="text-align:left"><?php echo $details["target"]['name'] ?></td>
                                            <td style="text-align:center"><?php echo $details["lock_date"] ?></td>
                                            <td style="text-align:right"><?php echo $currency.' '.number_format($details["price_base"]/100, 2) ?></td>
                                            <td style="text-align:right"><?php echo number_format($details["quantity"], 2).$unit ?></td>
                                            <td style="text-align:right"><?php echo $currency.' '.number_format($details["premium"]/100, 2) ?></td>
                                            <td style="text-align:right"><?php echo $currency.' '.number_format($details["month_spread"]/100, 2) ?></td>
                                            <td style="text-align:right"><?php echo $currency.' '.number_format($details["rollover_fee"]/100, 2) ?></td>
                                            <td style="text-align:right"><?php echo $currency.' '.number_format($details["amount"]/100, 2) ?></td>
                                            <td style="text-align:right"><?php $settle_amount=$details["amount"]*$details["quantity"]; echo $currency.' '.number_format($settle_amount/100, 2); ?></td>
                                            <td style="text-align:left"><?php echo $details["remark"] ?></td>
                                        </tr>
                                    <?php
                                    endforeach;
                            endforeach;?>
                        <tr>
                            <td style="text-align:center"><b>合计</b></td>
                            <td></td>
                            <td></td>
                            <td style="text-align:right" title="平均价格=总结算金额/总锁价数量"><?php echo $currency.' '.number_format($price_base_sum/100, 2) ?></td>
                            <td style="text-align:right"><?php echo number_format($quantity_sum, 2).$unit ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align:right"><?php echo $currency.' '.number_format($amount_sum/100, 2); ?></td>
                            <td></td>
                        </tr>
                        </tbody>
                    <?php endif;?>
                </table>
            </div>
        </div>
        <div class="tab-pane" id="tab2">
            <div style="overflow-x: scroll">
                <table class="table table-nowrap">
                    <thead>
                    <tr>
                        <th style="width:110px;text-align:center">转月编号</th>
                        <th style="width:170px;text-align:center">转月前标的</th>
                        <th style="width:170px;text-align:center">转月后标的</th>
                        <th style="width:110px;text-align:center">转月数量</th>
                        <th style="width:110px;text-align:center">已锁价数量</th>
                        <th style="width:100px;text-align:center">月价差</th>
                        <th style="width:100px;text-align:center">调期费</th>
                        <th style="text-align:center">备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $currency = $this->map['currency'][$rollOvers[0]['currency']]['ico'];
                    $unit     = $this->map['goods_unit'][$rollOvers[0]['lockPrice']['unit']]['name'];
                    if(is_array($rollOvers))
                        foreach ($rollOvers as $v) {
                            $lock_quantity = 0;
                            if(is_array($v['target']['lockPriceDetail'])) {
                                foreach ($v['target']['lockPriceDetail'] as $lockPriceDetail) {
                                    $lock_quantity += $lockPriceDetail['quantity'];
                                }
                            }

                            ?>
                            <tr>
                                <td style="text-align:left"><?php echo $v['rollover_code'] ?></td>
                                <td style="text-align:left"><?php echo $v['oldTarget']['name']  ?></td>
                                <td style="text-align:left"><?php echo $v['target']['name'] ?></td>
                                <td style="text-align:right"><?php echo number_format($v['quantity'], 2).$unit; ?></td>
                                <td style="text-align:right"><?php echo $lock_quantity.$unit; ?></td>
                                <td style="text-align:right"><?php echo $currency.' '.number_format($v["month_spread"]/100, 2) ?></td>
                                <td style="text-align:right"><?php echo $currency.' '.number_format($v["rollover_fee"]/100, 2) ?></td>
                                <td style="text-align:left"><?php echo $v["remark"] ?></td>
                            </tr>
                        <?php }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>