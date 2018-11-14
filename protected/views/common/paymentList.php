<?php
$paymentType = isset($paymentType)?$paymentType:'pay_type';
?>
<div class="box-body  box-content-custom">
<table class="table table-hover table-hover-custom">
    <thead>
        <tr>
            <th style="width:148px;">预计日期</th>
            <th style="width:226px; "><?php echo $title;?>类别 </th>
            <th style="width:111px;">币种</th>
            <th style="width:192px; ">金额</th>
            <th style="width:368px;">备注</th>
            <!-- <th style="text-align: left;"><?php if($showDates) echo "占用天数"; ?>&nbsp;</th> -->
        </tr>
    </thead>
    <tbody>
    <?php 
    if(is_array($payments))
    foreach($payments as $payment):?>
        <tr>
            <td>
                <?php echo $payment['pay_date'];?>
            </td>
            <td>
                <?php 
                if($payment['expense_type'] != 5)
                    echo $this->map[$paymentType][$payment['expense_type']]['name'];
                else
                    echo $this->map[$paymentType][$payment['expense_type']]['name'] . '--' . $payment['expense_name'];
                ?>
            </td>
            <td>
                <?php echo $this->map['currency'][$payment['currency']]["name"];?>
            </td>
            <td>
                <?php echo $this->map['currency'][$payment['currency']]["ico"];?> <?php echo number_format($payment['amount']/100, 2);?>
            </td>
            <!-- <td>
                <?php
                if($showDates)
                    echo $payment['payment_term'];
                ?>
                &nbsp;
            </td> -->
            <td style="text-align: left">
                <?php echo empty($payment['remark'])?'--':$payment['remark']; ?>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
</div>