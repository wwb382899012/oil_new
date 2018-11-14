<?php
?>
<table class="table table-hover">
    <thead>
        <tr>
            <th style="width:30%;">占用对象</th>
            <th style="width:30%;text-align: left;">占用额度</th>
            <th style="width:30%; text-align: left;">备注</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    if(is_array($quotas))
    foreach($quotas as $quota):
        ?>
        <tr>
            <td>
                <?php echo empty($quota->quotaPartner)?$quota->quotaManager->name:'<a href="/partner/detail/?id=' . $quota->quotaPartner['partner_id'] . '&t=1" target="_blank">' . $quota->quotaPartner['name'] . '</a>';?>
            </td>
            <td>
                ￥ <?php 
                echo number_format($quota->amount/(100*10000), 2);
                ?>万元
            </td>
            <td>
                <?php echo $quota->remark;?>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>