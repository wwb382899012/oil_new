<?php
/**
*   采购,销售明细表
*/

$notTonNum=0;//单位不为吨的个数
foreach($goodArr as $good){
    if($good['unit']!=ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE)
        $notTonNum++;
}

?>
<table class="table table-hover">
    <thead>
        <tr>
            <th width="200px">品名</th>
            <!-- <th width="180px">规格</th> -->
            <th width="180px">计价标的</th>
            <th width="150px"><?php echo $title;?>溢短装比例</th>
            <th width="100px">数量</th>
            <th width="100px">单位</th>
            <?php if($notTonNum>0) :?><th width="100px">单位换算比</th><?php endif; ?>
            <th width="140px"><?php echo $title;?>单价</th>
            <th width="140px" style="text-align: right;"><?php echo $title;?>总价</th>
            <th width="140px" style="text-align: right;"><?php echo $title;?>人民币总价</th>
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
            <td><?php echo $good->goods['name'];?></td>
            <!-- <td><?php echo $good['goods_describe'];?></td> -->
            <td><?php echo $good['refer_target'];?></td>
            <td><?php echo number_format($good['more_or_less_rate']*100, 2);?>%</td>
            <td><?php echo $good['quantity'];?></td>
            <td><?php echo $this->map['goods_unit'][$good['unit']]['name'];?>
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
            <td style="text-align: right;">
                <?php echo $this->map['currency'][$currency]['ico']?>
                <?php echo number_format($good['amount']/100, 2);?>
            </td>
            <td style="text-align: right;">￥<?php echo number_format($good['amount_cny']/100, 2);?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align: center;">合计</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td style="text-align: right;"><?php echo $this->map['currency'][$currency]['ico']?> <?php echo number_format($amount_currency/100, 2);?></td>
            <td style="text-align: right;">￥ <?php echo number_format($sum/100, 2);?></td>
        </tr>
    </tfoot>
</table>

