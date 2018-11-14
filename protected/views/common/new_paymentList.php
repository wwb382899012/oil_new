<ul class="table-com">
    <li>
        <span>预计<?php echo $title;?>日期</span>
        <span><?php echo $title;?>类别</span>
        <span>币种</span>
        <span>金额</span>
        <span>备注</span>
    </li>
    <?php 
    if(is_array($payments))
    foreach($payments as $payment):?>
    <li>
        <span><?php echo $payment['pay_date'];?></span>
        <span>
            <?php 
                if($payment['expense_type'] != 5)
                    echo $this->map[$paymentType][$payment['expense_type']]['name'];
                else
                    echo $this->map[$paymentType][$payment['expense_type']]['name'] . '--' . $payment['expense_name'];
                ?>
        </span>
        <span><?php echo $this->map['currency'][$payment['currency']]["name"];?></span>
        <span><?php echo $this->map['currency'][$payment['currency']]["ico"];?><?php echo number_format($payment['amount']/100, 2);?></span>
        <span title="<?php echo empty($payment['remark'])?'--':$payment['remark']; ?>"><?php echo empty($payment['remark'])?'--':$payment['remark']; ?></span>
    </li>
    <?php endforeach;?>
</ul>