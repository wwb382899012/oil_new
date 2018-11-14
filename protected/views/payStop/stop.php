<?php if(!empty($apply->contract_id) && is_array($apply->details) && count($apply->details)>0){
    ?>
    <h4 class="section-title">止付信息</h4>
    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-11">
            <table class="table table-hover">
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
                if(is_array($apply->details)){
                    $total_amount = 0;
                    foreach($apply->details as $detail)
                    {
                        $total_amount += $detail['amount_paid'];
                ?>
                <tbody>
                        <tr>
                            <td>
                                <?php echo $detail->payment['period'];?>
                            </td>
                            <td>
                                <?php
                                echo $this->map["pay_type"][$detail->payment['expense_type']]['name'];
                                if($detail->payment['expense_type'] == 5)
                                    echo '--' . $detail->payment['expense_name'];
                                ?>
                            </td>
                            <td>
                                <?php echo $this->map['currency'][$detail->payment['currency']]["ico"];?> <?php echo number_format($detail->payment['amount']/100, 2);?>
                            </td>

                            <td>
                                <?php echo $this->map['currency'][$detail->payment['currency']]["ico"];?> 
                                <?php
                                    $amount = $detail['amount'];
                                    if($apply->status==PayApplication::STATUS_STOP) 
                                        $amount = $detail['amount_paid'];
                                    echo $apply->status>=PayApplication::STATUS_SUBMIT ? number_format(($detail->payment['amount_paid'] - $amount)/100, 2) : number_format($detail->payment['amount_paid']/100, 2);
                                ?>
                            </td>
                            <td>
                                <?php echo $this->map['currency'][$detail->payment['currency']]["ico"];?> <?php echo number_format(($detail->payment['amount']-$detail->payment['amount_paid'])/100, 2);?>
                            </td>
                            <td>
                                <?php echo $this->map['currency'][$apply['currency']]["ico"];?> <?php echo number_format($detail['amount']/100, 2);?>
                            </td>
                            <td>
                                <?php echo $this->map['currency'][$apply['currency']]["ico"];?> <?php echo number_format($detail['amount_paid']/100, 2);?>
                            </td>
                        </tr>
                </tbody>
                <?php } ?>
                <tfoot>
                    <tr>
                        <td>合计</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <?php echo $this->map['currency'][$apply['currency']]["ico"];?> 
                            <?php echo number_format($total_amount/100, 2);?>
                        </td>
                    </tr>
                </tfoot>
                <?php 
                    } 
                ?>
            </table>
        </div>
    </div>
    <?php
} ?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">附件</label>
    <div class="col-sm-10">
        <p class="form-control-static">
            <?php

            $attachments=AttachmentService::getAttachments(Attachment::C_PAYSTOP,$apply->apply_id,21);
            if(is_array($attachments) && count($attachments)>0)
            {

                foreach ($attachments as $v)
                {
                    foreach ($v as $file)
                    {
                        echo "<a href='/payStop/getFile/?id=" . $file["id"] . "&fileName=" . $file['name'] . "' title='点击查看' target='_blank' class='btn btn-primary btn-xs'>" . $file['name'] . "</a> <br /><br />";
                    }
                }
            }
            else
            {
                echo "无";
            }
            ?>
        </p>
    </div>
</div>
<div class="form-group">
    <label for="remark" class="col-sm-2 control-label">止付原因</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo  $apply->extra->stop_remark; ?></p>
    </div>
</div>