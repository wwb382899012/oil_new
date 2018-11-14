<?php
$menus=$this->getIndexMenuWithNewUI();

$menus[] = ['text' => $this->pageTitle];
$buttons = [];
$this->loadHeaderWithNewUI($menus, $buttons, true);
?>
<section class="content sub-container">
    <?php
    if (!empty($this->detailPartialFile))
        $this->renderPartial($this->detailPartialFile, array($this->detailPartialModelName => $model));
    $this->renderPartial("/common/new_checkDetail", array('checkLog' => $checkLog));
    ?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>审核记录</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <?php
        $checkLogs = FlowService::getCheckLogModel($checkLog['obj_id'], $this->businessId);
        $this->renderPartial("/check/new_checkLogs", array('checkLogs' => $checkLogs));
        ?>
    </div>

</section>
<script>
    function back() {
        location.href = "<?php echo $this->getBackPageUrl() ?>";
    }
</script>