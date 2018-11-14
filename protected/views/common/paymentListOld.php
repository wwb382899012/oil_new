<?php
$paymentType = isset($paymentType)?$paymentType:'pay_type';
?>
<table class="table table-hover">
    <thead>
        <tr>
            <th style="width:150px;">预计<?php echo $title;?>日期</th>
            <th style="width:150px;text-align: left;"><?php echo $title;?>类别 </th>
            <th style="width:120px; text-align: left;">币种</th>
            <th style="width:200px; text-align: left;">金额</th>
            <th style="text-align: left;">备注</th>
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
            <td>
                <?php echo $payment['remark'] ?>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>