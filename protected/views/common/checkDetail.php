<h4 class="section-title">本次审核信息</h4>
<?php $this->renderPartial("/common/checkItemsDetail", array('checkLog'=>$checkLog)); ?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">审核人</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $checkLog->user->name ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">审核时间</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $checkLog->check_time ?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">审核状态</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo Map::$v["check_status"][$checkLog->check_status] ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">审核意见</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $checkLog->remark ?></p>
    </div>
</div>