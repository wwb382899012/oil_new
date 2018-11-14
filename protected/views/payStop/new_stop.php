<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>止付信息</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <?php if (!empty($apply->contract_id) && is_array($apply->details) && count($apply->details) > 0) { ?>

    <table class="table">
        <thead>
        <tr>
            <th style="width:60px; text-align: left;">期数</th>
            <th style="width:100px; text-align: left;">类别</th>
            <th style="width:180px; text-align: left;">计划付款金额</th>
            <th style="width:150px; text-align: left;">已申请金额</th>
            <th style="width:150px; text-align: left;">未申请金额</th>
            <th style="width:150px; text-align: left;">本次付款金额</th>
            <th style="text-align: left;">实付金额</th>
        </tr>
        </thead>
        <?php
        if (is_array($apply->details)) {
            $total_amount = 0;
            foreach ($apply->details as $detail) {
                $total_amount += $detail['amount_paid'];
                ?>
                <tbody>
                <tr>
                    <td>
                        <?php echo $detail->payment['period']; ?>
                    </td>
                    <td>
                        <?php
                        echo $this->map["pay_type"][$detail->payment['expense_type']]['name'];
                        if ($detail->payment['expense_type'] == 5)
                            echo '--' . $detail->payment['expense_name'];
                        ?>
                    </td>
                    <td>
                        <?php echo $this->map['currency'][$detail->payment['currency']]["ico"]; ?><?php echo number_format($detail->payment['amount'] / 100, 2); ?>
                    </td>

                    <td>
                        <?php echo $this->map['currency'][$detail->payment['currency']]["ico"]; ?>
                        <?php
                        $amount = $detail['amount'];
                        if ($apply->status == PayApplication::STATUS_STOP)
                            $amount = $detail['amount_paid'];
                        echo $apply->status >= PayApplication::STATUS_SUBMIT ? number_format(($detail->payment['amount_paid'] - $amount) / 100, 2) : number_format($detail->payment['amount_paid'] / 100, 2);
                        ?>
                    </td>
                    <td>
                        <?php echo $this->map['currency'][$detail->payment['currency']]["ico"]; ?><?php echo number_format(($detail->payment['amount'] - $detail->payment['amount_paid']) / 100, 2); ?>
                    </td>
                    <td>
                        <?php echo $this->map['currency'][$apply['currency']]["ico"]; ?><?php echo number_format($detail['amount'] / 100, 2); ?>
                    </td>
                    <td>
                        <?php echo $this->map['currency'][$apply['currency']]["ico"]; ?><?php echo number_format($detail['amount_paid'] / 100, 2); ?>
                    </td>
                </tr>
                </tbody>
            <?php } ?>
            <tfoot>
            <tr>
                <td colspan="5"></td>
                <td align="right">合计</td>
                <td>
                    <span class="color-emphasis">
                        <?php echo $this->map['currency'][$apply['currency']]["ico"]; ?>
                        <?php echo number_format($total_amount / 100, 2); ?>
                    </span>
                </td>
            </tr>
            </tfoot>
            <?php
        }
        ?>
    </table>
    <?php } ?>
    <ul class="item-com upload-list">
        <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
            <label>
                附件：
            </label>
            <div>
                    <?php

                    $attachments = AttachmentService::getAttachments(Attachment::C_PAYSTOP, $apply->apply_id, 21);
                    if (is_array($attachments) && count($attachments) > 0) {

                        foreach ($attachments as $v) {
                            foreach ($v as $file) {
                                echo '<p class="form-control-static file-item">';
                                echo "<a href='/payStop/getFile/?id=" . $file["id"] . "&fileName=" . $file['name'] . "' title='点击查看' target='_blank' class='text-link'>" . $file['name'] . "</a>";
                                echo '</p>';
                            }
                        }
                    } else {
                        echo "无";
                    }
                    ?>
            </div>

        </li>
        <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
            <label>止付原因：</label>
            <p class="form-control-static"><?php echo $apply->extra->stop_remark; ?></p>
        </li>
    </ul>

</div>
