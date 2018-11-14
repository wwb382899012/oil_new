<?php
$menus=$this->getIndexMenuWithNewUI();

$menus[] = ['text' => $this->pageTitle];
$buttons = [];
if ($payClaim->status < ReceiveConfirm::STATUS_SUBMITED) {
    $buttons[] = ['text' => '修改', 'attr' => ['onclick' => 'edit()']];
}
$this->loadHeaderWithNewUI($menus, $buttons, '/payClaim/');
?>
<section class="content sub-container">

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>银行流水</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="item-com">
            <li>
                <label>认领人：</label>
                <p><?php echo $payClaim->creator->name ?></p>
            </li>
            <li>
                <label>可认领金额：</label>
                <p><?php echo $this->map['currency'][$apply->currency]['ico'] ?><?php echo number_format(($apply->amount_paid - $apply->amount_claim) / 100, 2) ?></p>
            </li>
        </ul>
    </div>
    <?php $this->renderPartial("/pay/new_detailBody", array('apply'=>$apply)); ?>

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>认领详情</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <?php
        $this->renderPartial("/common/new_payClaimDetail", array('payClaim' => $payClaim));
        ?>

    </div>

</section><!--end content-->
<script>
    var back = function () {
        window.location.href = "/<?php echo $this->getId() ?>/view?apply_id=<?php echo $payClaim->apply_id ?>";
    }
    var edit = function () {
        window.location.href = "/<?php echo $this->getId() ?>/edit/?id=<?php echo $payClaim->claim_id?>";
    }
</script>