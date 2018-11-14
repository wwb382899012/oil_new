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
<ul class="table-com">
        <li>
            <span><?php echo $title;?>品名</span>
            <span>计价标的</span>
            <span>溢短装比</span>
            <span>数量</span>
            <?php if($notTonNum>0) :?><span>单位换算比</span><?php endif; ?>
            <span><?php echo $title;?>单价</span>
            <span><?php echo $title;?>总价</span>
            <span><?php echo $title;?>人民币总价</span>
        </li>
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
        <li>
            <span title="<?php echo $good->goods['name'];?>"><?php echo $good->goods['name'];?></span>
            <span title="<?php echo $good['refer_target'];?>"><?php echo $good['refer_target'];?></span>
            <span><?php echo number_format($good['more_or_less_rate']*100, 2);?>%</span>
            <span><?php echo $good['quantity'];?>  <?php echo $this->map['goods_unit'][$good['unit']]['name'];?></span>
            <?php if($notTonNum>0) :?>
                <span>
                    <?php
                    if($good['unit']!=ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE) {
                        echo $this->map['goods_unit'][$good['unit']]['name'] . '/' . ConstantMap::CONTRACT_GOODS_UNIT_CONVERT; ?> = <?php echo $good['unit_convert_rate'];
                    }
                    ?>
                </span>
            <?php endif; ?>
            <span><?php echo $this->map['currency'][$currency]['ico']?><?php echo number_format($good['price']/100, 2);?></span>

            <span>
                <?php echo $this->map['currency'][$currency]['ico']?>
                <?php echo number_format($good['amount']/100, 2);?>
            </span>
            <span>￥<?php echo number_format($good['amount_cny']/100, 2);?></span>
        </li>
    <?php endforeach;?>
        <li class="li-add">
            <p>
                <span>合计人民币总价: </span>
                <span><?php echo number_format($sum/100, 2);?> 元</span>
            </p>
        </li>
    </ul>