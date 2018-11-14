<ul class="item-com">
    <li>
        <label>货款合同编号：</label>
        <p>
            <a href="/contract/detail?id=<?php echo $payClaim->contract->contract_id ?>&t=1"
               title="<?php echo $payClaim->contract->contract_code ?>"
               target="_blank"><?php echo $payClaim->contract->contract_code ?></a>
        </p>
    </li>
    <li>
        <label>货款合同类型：</label>
        <p>
            <?php echo $this->map['buy_sell_type'][$payClaim->contract->type] ?>
        </p>
    </li>
    <li>
        <label>项目编号：</label>
        <p>
            <a href="/project/detail?id=<?php echo $payClaim->project->project_id ?>&t=1"
               title="<?php echo $payClaim->project->project_code ?>"
               target="_blank"><?php echo $payClaim->project->project_code ?></a>
        </p>
    </li>
    <li>
        <label>项目类型：</label>
        <p>
            <?php echo $this->map['project_type'][$payClaim->project->type] ?>
        </p>
    </li>
    <li>
        <label>付款合同类型：</label>
        <p>
            <?php echo $this->map['contract_category'][$payClaim->sub_contract_type] ?>
        </p>
    </li>
    <li>
        <label>付款合同编号：</label>
        <p>
            <?php echo $payClaim->sub_contract_code ?>
        </p>
    </li>
</ul>

<?php if (!empty($payClaim->payClaimDetail)): ?>
    <div class="form-group">
        <table class="table">
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
            <tbody>
            <?php foreach ($payClaim->payClaimDetail as $detail): ?>
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
                    <td>
                        <p class="form-control-static"><?php echo Map::$v['currency'][$detail->paymentPlan->currency]['ico'] . Utility::numberFormatFen2Yuan($detail->paymentPlan->amount) ?></p>
                    </td>
                    <td>
                        <p class="form-control-static"><?php echo Map::$v['currency'][$detail->currency]['ico'] . Utility::numberFormatFen2Yuan($detail->paymentPlan->amount_paid - $detail->amount) ?></p>
                    </td>
                    <td>
                        <p class="form-control-static">
                            <?php
                            echo Map::$v['currency'][$detail->currency]['ico'] . Utility::numberFormatFen2Yuan($detail->paymentPlan->amount - $detail->paymentPlan->amount_paid);
                            ?>
                        </p>
                    </td>
                    <td>
                        <p class="form-control-static"><?php echo Map::$v['currency'][$detail->currency]['ico'] . Utility::numberFormatFen2Yuan($detail->amount) ?></p>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<ul class="item-com">
    <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
        <label>
            认领金额：
        </label>
        <p class="form-control-static"><?php echo Map::$v['currency'][$payClaim->currency]['ico'] . Utility::numberFormatFen2Yuan($payClaim->amount) ?></p>
    </li>
    <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
        <label>备注：</label>
        <p class="form-control-static"><?php echo $payClaim->remark ?></p>
    </li>
</ul>

