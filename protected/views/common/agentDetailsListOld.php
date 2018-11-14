<table class="table table-hover">
    <thead>
        <tr>
            <th style="width:170px;">品名</th>
            <th style="width:120px;text-align: left;">计费方式</th>
            <th style="width:120px; text-align: left;">计费单价/计费单位</th>
            <th style="width:150px; text-align: left;">代理手续费率</th>
            <th style="width:150px; text-align: left;">代理手续费</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    if(is_array($agentDetails))
    foreach($agentDetails as $detail):?>
        <tr>
            <td>
                <?php echo $detail->goods->name;?>
            </td>
            <td>
                <?php echo $this->map['agent_fee_pay_type'][$detail['type']];?>
            </td>
            <td>
                ￥ <?php echo number_format($detail['price']/100, 2);?>/<?php echo $this->map['goods_unit'][$detail['unit']]['name'];?>
            </td>
            <td>
                <?php echo number_format($detail['fee_rate']*100, 2);?>%
            </td>
            <td>
                ￥ <?php echo number_format($detail['amount']/100, 2);?>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>