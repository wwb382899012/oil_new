<?php
$this->loadHeaderWithNewUI([],[],true);
?>
<style>
    ul.flex-grid>li.active{
        display:none;
    }
</style>
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p style="display: flex; align-items: center;"><i class="icon icon-close-circle-fill" style="vertical-align: top;color:#F56C6C;"></i>&nbsp;操作出错</p>
        </div>
    </div>
    <div>
        <div role="alert" style="background: #fff !important;border: none;margin: 0;"><?php echo $msg ?></div>
    </div>
</div>
<script>
	function back() {
		location.href = "<?php echo $backUrl ?>";
	}
</script>