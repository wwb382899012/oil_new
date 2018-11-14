<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#detail" data-toggle="tab">本次审核详情</a></li>
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
            <?php if (!$this->isExternal) { ?>
                <li class="pull-right">
                    <button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button>
                </li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="detail">
                <div class="box box-primary">
                    <form class="form-horizontal" role="form" id="mainForm">
                        <div class="box-body">
                            <?php
                            if(!empty($this->detailPartialFile))
                                $this->renderPartial($this->detailPartialFile, array($this->detailPartialModelName=>$model));
                            $this->renderPartial("/common/checkDetail", array('checkLog'=>$checkLog));
                            ?>
                        </div>
                    </form>
                    <div class="box-footer">
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <?php if(!$this->isExternal){ ?>
                                    <button type="button"  class="btn btn-default" onclick="back()">返回</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="flow">
                <?php
                $checkLogs = FlowService::getCheckLogModel($checkLog['obj_id'], $this->businessId);
                $this->renderPartial("/check/checkLogs", array('checkLogs' => $checkLogs));
                ?>
            </div>
        </div>
    </div>
</section>
<script>
    function back() {
        location.href = "<?php echo $this->getBackPageUrl() ?>";
    }
</script>