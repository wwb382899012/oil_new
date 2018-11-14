<ul class="table-com">
    <li>
        <span>品名</span>
        <span>计费方式</span>
        <span>计费单价/计费单位</span>
        <span>代理手续费率</span>
        <span>代理手续费</span>
    </li>
<?php 
    if(is_array($agentDetails))
    foreach($agentDetails as $detail):?>
    <li>
        <span> <?php echo $detail->goods->name;?></span>
        <span><?php echo $this->map['agent_fee_pay_type'][$detail['type']];?></span>
        <span>￥ <?php echo number_format($detail['price']/100, 2);?>/<?php echo $this->map['goods_unit'][$detail['unit']]['name'];?></span>
        <span><?php echo number_format($detail['fee_rate']*100, 2);?>%</span>
        <span>￥ <?php echo number_format($detail['amount']/100, 2);?></span>
    </li>
    <?php endforeach;?>
</ul>