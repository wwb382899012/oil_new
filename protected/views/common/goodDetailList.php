<?php
/**
*   采购,销售明细表
*/

$notTonNum=0;//单位不为吨的数量
foreach($goodArr as $good){
    if($good['unit']!=ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE)
        $notTonNum++;
}

?>
<div class="box-body  box-content-custom">
    
<table class="table table-hover table-hover-custom">
    <thead>
        <tr>
            <th style="width:188px; "><?php echo $title;?>品名</th>
            <th style="width:121px;">计价标的</th>
            <th style="width:123px; ">溢短装比</th>
            <th style="width:125px;">数量</th>
            <?php if($notTonNum>0) :?><th style="width:123px; ">单位换算比</th><?php endif; ?>
            <th style="width:139px;"><?php echo $title;?>单价</th>
            <th style="width:147px; "><?php echo $title;?>总价</th>
            <th style="width:214px; "><?php echo $title;?>人民币总价</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $sum = 0;
        $currency_ico = '';
        $amount_currency = 0;
        if(is_array($goodArr))
        foreach($goodArr as $good):
        $currency = $good['currency'];
        $amount_currency += $good['amount'];
        $sum += $good['amount_cny'];
        ?>
        <tr>
            <!-- <td><?php echo $good['goods_describe'];?></td> -->
            <td><?php echo $good->goods['name'];?></td>
            <td><?php echo $good['refer_target'];?></td>
            <td><?php echo number_format($good['more_or_less_rate']*100, 2);?>%</td>
            <td><?php echo $good['quantity'];?>  <?php echo $this->map['goods_unit'][$good['unit']]['name'];?></td>
            <?php if($notTonNum>0) :?>
                <td>
                    <?php
                    if($good['unit']!=ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE) {
                        echo $this->map['goods_unit'][$good['unit']]['name'] . '/' . ConstantMap::CONTRACT_GOODS_UNIT_CONVERT; ?> = <?php echo $good['unit_convert_rate'];
                    }
                    ?>
                </td>
            <?php endif; ?>
            <td><?php echo $this->map['currency'][$currency]['ico']?><?php echo number_format($good['price']/100, 2);?></td>
            <td >
                <?php echo $this->map['currency'][$currency]['ico']?>
                <?php echo number_format($good['amount']/100, 2);?>
            </td>
            <td >￥<?php echo number_format($good['amount_cny']/100, 2);?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>
<div class="pull-right padding-right-5 margin-top-10">
    <p class="form-control-static form-control-static-custom text-dark-gray">
    合计人民币总价: <?php echo number_format($sum/100, 2);?> 元
    </p>
</div>
</div>

