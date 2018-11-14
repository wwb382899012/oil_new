<div class="form-group">
    <label class="col-sm-2 control-label">备注</label>
    <div class="col-sm-10">
        <p class="form-control-static">
            <?php $remark_arr = explode("；作废理由：", $remark);?>
            <?php echo ($isInvalid && isset($remark_arr[1])) ? $remark_arr[0] : $remark; ?>
        </p>
    </div>
</div>
<?php if(isset($isCanShowStatus) && $isCanShowStatus):?>
    <hr />
    <div class="form-group">
        <label class="col-sm-2 control-label">状态</label>
        <div class="col-sm-4">
            <p class="form-control-static">
                <?php echo $statusName; ?>
            </p>
        </div>
    </div>
    <?php if(isset($isInvalid) && $isInvalid):?>
        <div class="form-group">
            <label class="col-sm-2 control-label">作废理由</label>
            <div class="col-sm-10">
                <p class="form-control-static">
                    <?php echo isset($remark_arr[1]) ? $remark_arr[1] : $remark; ?>
                </p>
            </div>
        </div>
    <?php endif;?>
<?php endif;?>
<?php if(isset($isCanShowAuditStatus) && $isCanShowAuditStatus):?>
    <hr />
    <div class="form-group">
        <label class="col-sm-2 control-label">审核状态</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?php echo $statusName; ?></p>
        </div>
    </div>
    <?php
    if(isset($isShowAuditRemark) && $isShowAuditRemark): ?>
        <?php $checkLog = FlowService::getCheckLog($id, $businessIds);?>
        <div class="form-group">
            <label class="col-sm-2 control-label">审核意见</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $checkLog[0]['remark']; ?></p>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>