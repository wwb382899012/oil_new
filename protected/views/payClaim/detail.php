<?php 
?>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">银行流水</h3>
        </div><!--end box box-header-->
        <div class="form-horizontal" role="form" id="mainForm">
            <div class="form-group">
                <label class="col-sm-2 control-label">认领人</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $payClaim->creator->name ?></p>
                </div>
                <label class="col-sm-2 control-label">可认领金额</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $this->map['currency'][$apply->currency]['ico']?><?php echo number_format(($apply->amount_paid - $apply->amount_claim)/100, 2) ?></p>
                </div>
            </div>
            <?php $this->renderPartial("/pay/detailBody", array('apply'=>$apply)); ?>

        </div>
    </div><!--end box box-primary-->
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">认领详情</h3>
        </div><!--end box box-header-->
        <div class="form-horizontal" role="form" id="mainForm">
            <?php
            $this->renderPartial("/common/payClaimDetail", array('payClaim'=>$payClaim));
            ?>

            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                    <?php if($payClaim->status < ReceiveConfirm::STATUS_SUBMITED):?>
                        <button type="button" class="btn btn-primary" onclick="edit()">编辑</button>
                    <?php endif;?>
                        <button type="button" class="btn btn-default" onclick="back()">返回</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end box box-primary-->
</section><!--end content-->
<script>
    var back=function () {
        window.location.href="/<?php echo $this->getId() ?>/view?apply_id=<?php echo $payClaim->apply_id ?>";
    }
    var edit=function () {
        window.location.href="/<?php echo $this->getId() ?>/edit/?id=<?php echo $payClaim->claim_id?>";
    }
</script>