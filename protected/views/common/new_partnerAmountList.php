<ul class="table-com">
    <li>
        <span>占用对象</span>
        <span>授信额度</span>
        <span>实际占用额度	</span>
        <span>合同占用额度</span>
    </li>
    <li>
        <a title="<?php echo $contract->partner->name; ?>" href="/partner/detail/?id=<?php echo $contract->partner_id ?>&t=1" target="_blank"><?php echo $contract->partner->name; ?></a>
        <span><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->partner->credit_amount) ?></span>
        <span><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->partner->usedAmount->used_amount) ?></span>
        <span><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->partner->contractAmount->used_amount) ?></span>
    </li>
    <?php
        if (!empty($contract->relation_contract_id))
        { ?>
    <li>
        <a title="<?php echo $contract->relationContract->partner->name; ?>" href="/partner/detail/?id=<?php echo $contract->relationContract->partner_id ?>&t=1" target="_blank"><?php echo $contract->relationContract->partner->name; ?></a>
        <span><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->relationContract->partner->credit_amount) ?></span>
        <span><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->relationContract->partner->usedAmount->used_amount) ?></span>
        <span><?php echo '￥'. Utility::numberFormatFen2Yuan($contract->relationContract->partner->contractAmount->used_amount) ?></span>
    </li>
    <?php } ?>
</ul>