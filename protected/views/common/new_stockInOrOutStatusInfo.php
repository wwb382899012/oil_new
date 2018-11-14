<?php if(isset($isCanShowStatus) && $isCanShowStatus): ?>
    <div class="flex-grid form-group">
        <label class="col full-space field flex-grid">
            <span class="w-fixed line-h--text">状态:</span>
            <span class="form-control-static line-h--text flex-grow"><?php echo $statusName; ?></span>
        </label>
    </div>
    <?php if(isset($isInvalid) && $isInvalid): ?>
        <div class="flex-grid form-group">
            <label class="col full-space field flex-grid">
                <span class="w-fixed line-h--text">作废理由:</span>
                <span class="form-control-static line-h--text flex-grow">
                    <?php $remark_arr = explode("；作废理由：", $remark); ?>
                    <?php echo isset($remark_arr[1]) ? $remark_arr[1] : $remark; ?>
                </span>
            </label>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if(isset($isCanShowAuditStatus) && $isCanShowAuditStatus): ?>
    <div class="flex-grid form-group">
        <label class="col full-space field flex-grid">
            <span class="w-fixed line-h--text">审核状态:</span>
            <span class="form-control-static line-h--text flex-grow"><?php echo $statusName; ?></span>
        </label>
    </div>
    <?php if(isset($isShowAuditRemark) && $isShowAuditRemark): ?>
        <?php $checkLog = FlowService::getCheckLog($id, $businessIds); ?>
        <div class="flex-grid form-group">
            <label class="col full-space field flex-grid">
                <span class="w-fixed line-h--text">审核意见:</span>
                <span class="form-control-static line-h--text flex-grow"><?php echo $checkLog[0]['remark']; ?></span>
            </label>
        </div>
    <?php endif; ?>
<?php endif; ?>