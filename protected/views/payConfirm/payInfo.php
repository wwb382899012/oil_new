<?php if (is_array($payInfo) && count($payInfo) > 0) { ?>
    <div id="payInfoContainer">
        <div class="box-header with-border">
        </div>
        <h4 class="box-title">历史实付记录</h4>
        <table class="table table-bordered">
            <tbody>
            <tr>
                <th style="width: 100px;text-align:center">实付日期</th>
                <th style="width: 150px;text-align:center">实付金额</th>
                <th style="text-align:center">付款账户名</th>
                <th style="width: 100px;text-align:center">开户行</th>
                <th style="width: 100px;text-align:center">银行账号</th>
                <th style="width: 100px;text-align:center">银行流水号</th>
                <th style="width: 80px;text-align:center">汇率</th>
                <th style="width: 80px;text-align:center">放款凭证</th>
                <th style="width: 140px;text-align:center">备注</th>
            </tr>
            <?php foreach ($payInfo as $key => $value) {?>
            <tr>
                <td style="text-align:center"><?php echo $value['pay_date'] ?></td>
                <td style="text-align:right"><?php echo $this->map['currency'][$model->currency]['ico']." ".number_format($value['amount']/100, 2) ?></td>
                <td style="text-align:left"><?php echo $value['corporation_name'] ?></td>
                <td style="text-align:left"><?php echo $value['bank_name'] ?></td>
                <td style="text-align:center"><?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $value['account_no']) ?></td>
                <td style="text-align:center"><?php echo $value['payment_no'] ?></td>
                <td style="text-align:right"><?php echo $value['exchange_rate']>0 ? $value['exchange_rate'] : 0 ?></td>
                <td style="text-align:center">
                    <?php if(!empty($value['file_id'])){ ?>
                        <a class='btn btn-primary btn-xs' href='/payConfirm/getFile/?id=<?php echo $value["file_id"] ?>&fileName=<?php echo $value['file_name'] ?>'  target='_blank'>点击查看</a></td>
                    <?php }else{ ?>
                        无
                    <?php } ?>
                <td style="text-align:left"><?php echo $value['remark'] ?></td>
            </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

<?php } ?>