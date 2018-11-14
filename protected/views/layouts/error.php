<div class="box box-warning">
    <div class="box-header">
        <h3 class="box-title text-warning"><span class="glyphicon glyphicon-remove-sign"></span>&nbsp;操作出错</h3>
    </div>
    <div class="box-body">
        <div class="alert alert-warning" role="alert"><?php echo $msg ?></div>
    </div>
    <?php if (!$this->isExternal && !empty($backUrl) && $backUrl != "#") { ?>
        <div class="box-footer">
            <button type="button" class="btn btn-default" onclick="back()">返回</button>
        </div>
    <?php } ?>
</div>
<script>
    function back() {
        location.href = "<?php echo $backUrl ?>";
    }
</script>