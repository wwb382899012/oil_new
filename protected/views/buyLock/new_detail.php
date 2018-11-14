<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">锁价详情</h3>
            <div class="pull-right box-tools">
                <?php if(!$this->isExternal){ ?>
                    <button type="button"  class="btn btn-default btn-sm" onclick="back()">返回</button>
                <?php } ?>
            </div>
        </div>
        <div class="box-body form-horizontal">
            <?php include "head.php" ?>
            <?php if(count($lockDetail)>0 || count($rollDetail)>0) include "tab.php";?>
        </div>
        <div class="box-footer">
            <?php if(!$this->isExternal){ ?>
                <button type="button"  class="btn btn-default" onclick="back()">返回</button>
            <?php } ?>
        </div>

    </div>
</section>

<script>
    function back() {
        location.href = "/buyLock/";
    }
</script>