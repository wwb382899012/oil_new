<div class="box-body  box-content-custom">
    <table class="table table-hover table-hover-custom">
        <thead>
        <tr>
            <th style="width:148px;">占用对象</th>
            <th style="width:226px; ">授信额度</th>
            <th style="width:111px;">实际占用额度</th>
            <th style="width:192px; ">合同占用额度</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><a href="/partner/detail/?id=<?php echo $contract->partner_id ?>&t=1" target="_blank"><?php echo $contract->partner->name; ?></a></td>
            <td><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->partner->credit_amount) ?></td>
            <td><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->partner->usedAmount->used_amount) ?></td>
            <td><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->partner->contractAmount->used_amount) ?></td>
        </tr>
        <?php
        if (!empty($contract->relation_contract_id))
        { ?>
            <tr>
                <td><a href="/partner/detail/?id=<?php echo $contract->relationContract->partner->partner_id ?>&t=1" target="_blank"><?php echo $contract->relationContract->partner->name; ?></a></td>
                <td><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->relationContract->partner->credit_amount) ?></td>
                <td><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->relationContract->partner->usedAmount->used_amount) ?></td>
                <td><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->relationContract->partner->contractAmount->used_amount) ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>