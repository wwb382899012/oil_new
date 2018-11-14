<?php if (is_array($payInfo) && count($payInfo) > 0) { ?>
    <div class="content-wrap" id="payInfoContainer">
        <div class="content-wrap-title">
            <div>
                <p>历史实付记录</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <table class="data-table dataTable stripe hover nowrap table-fixed">
            <thead>
            <tr>
                <th style="width: 120px;text-align:center">实付日期</th>
                <th style="width: 150px;text-align:center">实付金额</th>
                <th style="text-align:center">付款账户名</th>
                <th style="width: 320px;text-align:center">开户行</th>
                <th style="width: 180px;text-align:center">银行账号</th>
                <th style="width: 100px;text-align:center">银行流水号</th>
                <th style="width: 80px;text-align:center">汇率</th>
                <th style="width: 130px;text-align:center">放款凭证</th>
                <th style="width: 140px;text-align:center">备注</th>
            </tr>
            </thead>
            <?php foreach ($payInfo as $key => $value) { ?>
                <tr>
                    <td style="text-align:center"><?php echo $value['pay_date'] ?></td>
                    <td style="text-align:right"><?php echo $this->map['currency'][$model->currency]['ico'] . " " . number_format($value['amount'] / 100, 2) ?></td>
                    <td style="text-align:left"><?php echo $value['corporation_name'] ?></td>
                    <td style="text-align:left"><?php echo $value['bank_name'] ?></td>
                    <td style="text-align:center"><span title="<?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $value['account_no']) ?>"><?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $value['account_no']) ?></span></td>
                    <td style="text-align:center"><?php echo $value['payment_no'] ?></td>
                    <td style="text-align:right"><?php echo $value['exchange_rate'] > 0 ? $value['exchange_rate'] : 0 ?></td>
                    <td style="text-align:center">
                        <?php if (!empty($value['file_id'])){ ?>
                        <!--                    <a href="javascript: void 0" data-toggle="dropdown">-->
                        <!--                        点击查看 <i class="icon icon-xiala icon--shrink"></i>-->
                        <!--                    </a>-->
                        <a href='/payConfirm/getFile/?id=<?php echo $value["file_id"] ?>&fileName=<?php echo $value['file_name'] ?>'
                           target='_blank'>点击查看<i class="icon icon-xiala icon--shrink"></i></a></td>
                    <?php } else { ?>
                        无
                    <?php } ?>
                    <td style="text-align:left"><?php echo $value['remark'] ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>

    <script>
        $(function () {
            page.initDatatables('', {columns: 0})
        });
    </script>
<?php } ?>