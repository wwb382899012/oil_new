<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>本次审核信息</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <ul class="item-com">
        <?php $this->renderPartial("/common/new_checkItemsDetail", array('checkLog' => $checkLog)); ?>

        <li>
            <label>审核人：</label>
            <p><?php echo $checkLog->user->name ?></p>
        </li>
        <li>
            <label>审核时间：</label>
            <p><?php echo $checkLog->check_time ?></p>
        </li>
        <li>
            <label>审核状态：</label>
            <p><?php echo Map::$v["check_status"][$checkLog->check_status] ?></p>
        </li>
        <li>
            <label>审核意见：</label>
            <p><?php echo $checkLog->remark ?></p>
        </li>
    </ul>
</div>

