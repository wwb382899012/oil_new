<ul class="item-com">
    <li>
        <label>货款（外部）合同编号：</label>
        <p class="form-control-static">
            <a href="/businessConfirm/detail?id=<?php echo $receiveConfirm->contract->contract_id ?>&t=1"
               target="_blank"><?php echo $receiveConfirm->contract->contract_code; if(!empty($receiveConfirm->contract_out_code)) echo '('.$receiveConfirm->contract_out_code.')'; ?></a>
        </p>
    </li>
    <li>
        <label>货款合同类型：</label>
        <p class="form-control-static"><?php echo $this->map['buy_sell_type'][$receiveConfirm->contract->type] ?></p>
    </li>
    <li>
        <label>项目编号：</label>
        <p class="form-control-static">
            <a href="/project/detail?id=<?php echo $receiveConfirm->project->project_id ?>&t=1"
               target="_blank"><?php echo $receiveConfirm->project->project_code ?></a>
        </p>
    </li>
    <li>
        <label>项目类型：</label>
        <p class="form-control-static"><?php echo $this->map['project_type'][$receiveConfirm->project->type] ?></p>
    </li>
    <li>
        <label>收款合同类型：</label>
        <p class="form-control-static"><?php echo $this->map['contract_category'][$receiveConfirm->sub_contract_type] ?></p>
    </li>
    <li>
        <label>收款合同编号：</label>
        <p class="form-control-static"><?php echo $receiveConfirm->sub_contract_code ?></p>
    </li>
    <li>
        <label>用途：</label>
        <p class="form-control-static"><?php echo $receiveConfirm->finSubject->name ?></p>
    </li>
</ul>


<?php if (!empty($receiveConfirm->receiveDetail)): ?>
    <div class="form-group">
        <label>收款计划</label>
        <table class="table table-hover table-nowrap" autoscroll>
            <thead>
            <tr>
                <th style="width:120px;">期数</th>
                <th style="width:130px; text-align: left;">预计收款日期</th>
                <th style="width:120px; text-align: left;">收款类别</th>
                <th style="width:100px; text-align: left;">币种</th>
                <th style="width:200px; text-align: left;">金额</th>
                <th style="width:200px; text-align: left;">已收金额</th>
                <th style="width:200px; text-align: left;">未收金额</th>
                <th style="width:200px; text-align: left;">认领金额</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($receiveConfirm->receiveDetail as $detail): ?>
                <tr>
                    <td>
                        <p class="form-control-static"><?php echo $detail->paymentPlan->period ?></p>
                    </td>
                    <td>
                        <p class="form-control-static"><?php echo $detail->paymentPlan->pay_date ?></p>
                    </td>
                    <td>
                        <p class="form-control-static"><?php echo $this->map['proceed_type'][$detail->paymentPlan->expense_type]['name'] ?></p>
                    </td>
                    <td>
                        <p class="form-control-static"><?php echo $this->map['currency'][$detail->paymentPlan->currency]['name'] ?></p>
                    </td>
                    <td>
                        <p class="form-control-static"><?php echo number_format($detail->paymentPlan->amount / 100, 2) ?></p>
                    </td>
                    <td>
                        <p class="form-control-static"><?php echo number_format($detail->paymentPlan->amount_paid / 100, 2) ?></p>
                    </td>
                    <td>
                        <p class="form-control-static"><?php echo number_format(($detail->paymentPlan->amount - $detail->paymentPlan->amount_paid) / 100, 2) ?></p>
                    </td>
                    <td>
                        <p class="form-control-static"><?php echo number_format($detail->amount / 100, 2) ?></p>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>


<ul class="item-com upload-list">
    <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
        <label>
            认领金额：
        </label>
        <p class="form-control-static"><?php echo number_format($receiveConfirm->amount / 100, 2) ?></p>
    </li>
    <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
        <label>备注：</label>
        <p class="form-control-static"><?php echo $receiveConfirm->remark ?></p>
    </li>
    <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
        <label>附件：</label>
        <div>

            <?php
            foreach ($attachments as $v) {
                if (!empty($v[0]["file_url"])) {
                    foreach ($v as $file) {
                        echo '<p class="form-control-static file-item">';
                        echo "<a href='/receiveConfirm/getFile/?id=" . $file["id"] . "&fileName=" . $file['name'] . "'  target='_blank' >" . $file['name'] . "</a>";
                        echo '</p>';
                    }
                } else {
                    echo "无";
                }
            }
            ?>
        </div>

    </li>
</ul>

