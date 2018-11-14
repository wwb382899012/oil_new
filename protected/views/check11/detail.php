<section class="content">
    <div class="box box-primary">
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <?php 
                    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/cross/head.php";
                    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/cross/tab.php";
                ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">本次调货原因</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $data['reason'] ?></p>
                    </div>
                </div>
                <?php 
                    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/cross/nowDetail.php";
                ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">调货日期</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $data['cross_date'] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">审核意见 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $data['remark'] ?></p>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="button"  class="btn btn-default" onclick="back()">返回</button>
                </div>
            </div>
        </form>
    </div>
</section>
<script type="text/javascript">
	var back = function () {
		history.go(-1);
        // location.href="/check11/";
	}
</script>