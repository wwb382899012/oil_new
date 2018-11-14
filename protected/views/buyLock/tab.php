<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <?php if($data['type']==ConstantMap::LOCK_PRICE && count($lockDetail)>0){ ?>
        <li class="active"><a href="#tab1" data-toggle="tab">锁价记录</a></li>
        <?php } ?>
        <?php if(count($rollDetail)>0){ ?>
        <li <?php if($data['type']==ConstantMap::ROLLOVER_MONTH || empty($lockDetail)) echo 'class="active"'; ?>><a href="#tab2" data-toggle="tab">转月记录</a></li>
        <?php } ?>
    </ul>
    <div class="tab-content">
        <?php if($data['type']==ConstantMap::LOCK_PRICE && !empty($lockDetail)){ ?>
        <div class="tab-pane active" id="tab1">
            <div class="box">
                <?php if (Utility::isNotEmpty($lockDetail)) { ?>
                    <div class="box-body no-padding form-horizontal">
                        <table class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                            <tr>
                                <th style="width:110px;text-align:center">锁价单编号</th>
                                <th style="width:110px;text-align:center">计价标的</th>
                                <th style="width:100px;text-align:center">锁价日期</th>
                                <th style="width:100px;text-align:center">基准价格</th>
                                <th style="width:100px;text-align:center">锁价数量</th>
                                <th style="width:80px;text-align:center">升贴水</th>
                                <th style="width:80px;text-align:center">月价差</th>
                                <th style="width:80px;text-align:center">调期费</th>
                                <th style="width:100px;text-align:center">结算价格</th>
                                <th style="width:120px;text-align:center">结算金额</th>
                                <th style="width:80px;text-align:center">备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                                $currency = $this->map['currency'][$lockDetail[0]['currency']]['ico'];
                                $unit     = $this->map['goods_unit'][$lockDetail[0]['unit']]['name'];
                                foreach ($lockDetail as $v) { ?>
                                <tr>
                                    <td style="text-align:left"><?php echo $v["lock_code"] ?></td>
                                    <td style="text-align:left"><?php echo $v["target_name"] ?></td>
                                    <td style="text-align:center"><?php echo $v["lock_date"] ?></td>
                                    <td style="text-align:right"><?php echo $currency.' '.number_format($v["price_base"]/100, 2) ?></td>
                                    <td style="text-align:right"><?php echo number_format($v["quantity"], 2).$unit ?></td>
                                    <td style="text-align:right"><?php echo $currency.' '.number_format($v["premium"]/100, 2) ?></td>
                                    <td style="text-align:right"><?php echo $currency.' '.number_format($v["month_spread"]/100, 2) ?></td>
                                    <td style="text-align:right"><?php echo $currency.' '.number_format($v["rollover_fee"]/100, 2) ?></td>
                                    <td style="text-align:right"><?php echo $currency.' '.number_format($v["amount"]/100, 2) ?></td>
                                    <td style="text-align:right"><?php $settle_amount=$v["amount"]*$v["quantity"]; echo $currency.' '.number_format($settle_amount/100, 2); ?></td>
                                    <td style="text-align:left"><?php echo $v["remark"] ?></td>
                                </tr>
                            <?php } ?>
                                <tr>
                                    <td style="text-align:center"><b>合计</b></td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align:right" title="平均价格=总结算金额/总锁价数量"><?php echo $currency.' '.number_format($data["total_price"]/100, 2) ?></td>
                                    <td style="text-align:right"><?php echo number_format($data["total_quantity"], 2).$unit ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align:right"><?php echo $currency.' '.number_format($data['total_amount']/100, 2); ?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        
        <div class="tab-pane <?php if($data['type']==ConstantMap::ROLLOVER_MONTH || empty($lockDetail)) echo 'active'; ?>" id="tab2">
            <div class="box">
                <?php if (Utility::isNotEmpty($rollDetail)) { ?>
                    <div class="box-body no-padding form-horizontal">
                        <table class="table table-striped table-bordered table-condensed table-hover">
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
                                $currency = $this->map['currency'][$rollDetail[0]['currency']]['ico'];
                                $unit     = $this->map['goods_unit'][$rollDetail[0]['unit']]['name'];
                                foreach ($rollDetail as $v) { ?>
                                <tr>
                                    <td style="text-align:left"><?php echo $v['rollover_code'] ?></td>
                                    <td style="text-align:left"><?php echo $v['old_target_name']  ?></td>
                                    <td style="text-align:left"><?php echo $v['target_name'] ?></td>
                                    <td style="text-align:right"><?php echo number_format($v['quantity'], 2).$unit; ?></td>
                                    <td style="text-align:right"><?php echo number_format($targetArr[$v['target_id']]['lock_quantity'], 2).$unit;  //edit by tiny。  target_id -> old_target_id ?></td>
                                    <td style="text-align:right"><?php echo $currency.' '.number_format($v["month_spread"]/100, 2) ?></td>
                                    <td style="text-align:right"><?php echo $currency.' '.number_format($v["rollover_fee"]/100, 2) ?></td>
                                    <td style="text-align:left"><?php echo $v["remark"] ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
        
    </div>
</div>