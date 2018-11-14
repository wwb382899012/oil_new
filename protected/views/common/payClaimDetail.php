
            <div class="form-group">
                <label class="col-sm-2 control-label">货款合同编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><a href="/contract/detail?id=<?php echo $payClaim->contract->contract_id ?>&t=1" title="<?php echo $payClaim->contract->contract_code ?>" target="_blank"><?php echo $payClaim->contract->contract_code ?></a></p>
                </div>
                <label class="col-sm-2 control-label">货款合同类型</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $this->map['buy_sell_type'][$payClaim->contract->type] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">项目编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><a href="/project/detail?id=<?php echo $payClaim->project->project_id ?>&t=1" title="<?php echo $payClaim->project->project_code ?>" target="_blank"><?php echo $payClaim->project->project_code ?></a></p>
                    </select>
                </div>
                <label class="col-sm-2 control-label">项目类型</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $this->map['project_type'][$payClaim->project->type] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">收款合同类型</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $this->map['contract_category'][$payClaim->sub_contract_type] ?></p>
                </div>
                <label class="col-sm-2 control-label">收款合同编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $payClaim->sub_contract_code ?></p>
                </div>
            </div>

            <?php if(!empty($payClaim->payClaimDetail)):?>
                <div class="form-group">
                    <div class="col-sm-11 col-sm-push-1">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th style="width:80px;">期数</th>
                                <th style="width:140px; text-align: left;">预计收款日期</th>
                                <th style="width:140px; text-align: left;">付款类别</th>
                                <th style="width:180px; text-align: left;">计划付款金额</th>
                                <th style="width:180px; text-align: left;">实付金额</th>
                                <th style="width:180px; text-align: left;">未实付金额</th>
                                <th style="width:240px; text-align: left;">认领金额</th>
                            </tr>
                            </thead>
                            <tbody >
                            <?php foreach($payClaim->payClaimDetail as $detail):?>
                                <tr>
                                    <td><p class="form-control-static"><?php echo $detail->paymentPlan->period ?></p></td>
                                    <td><p class="form-control-static"><?php echo $detail->paymentPlan->pay_date ?></p></td>
                                    <td>
                                        <p class="form-control-static">
                                            <?php
                                            $expenseMap = $contract->type == ConstantMap::BUY_TYPE ? Map::$v['pay_type'] : Map::$v['proceed_type'];
                                            $endExpense = end($expenseMap);
                                            echo $detail->paymentPlan->expense_type == $endExpense['id'] ? $detail->paymentPlan->expense_name : $expenseMap[$detail->paymentPlan->expense_type]['name']
                                            ?>
                                        </p>
                                    </td>
                                    <td><p class="form-control-static"><?php echo Map::$v['currency'][$detail->paymentPlan->currency]['ico'] . Utility::numberFormatFen2Yuan($detail->paymentPlan->amount) ?></p></td>
                                    <td><p class="form-control-static"><?php echo Map::$v['currency'][$detail->currency]['ico'] . Utility::numberFormatFen2Yuan($detail->paymentPlan->amount_paid - $detail->amount) ?></p></td>
                                    <td>
                                        <p class="form-control-static">
                                            <?php
                                            echo Map::$v['currency'][$detail->currency]['ico'] . Utility::numberFormatFen2Yuan($detail->paymentPlan->amount - $detail->paymentPlan->amount_paid);
                                            ?>
                                        </p>
                                    </td>
                                    <td><p class="form-control-static"><?php echo Map::$v['currency'][$detail->currency]['ico'] . Utility::numberFormatFen2Yuan($detail->amount) ?></p></td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif;?>


            <div class="form-group">
                <label class="col-sm-2 control-label">认领金额</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo Map::$v['currency'][$payClaim->currency]['ico'] . Utility::numberFormatFen2Yuan($payClaim->amount) ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $payClaim->remark ?></p>
                </div>
            </div>
