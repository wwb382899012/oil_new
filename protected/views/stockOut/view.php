<section class="content">
    <div class="form-horizontal">
        <?php $this->renderPartial("/stockOut/partial/stockOutOrderInfo", array(
            'outOrder' => $stockOutOrder,
            'isShowBackButton'=> true,
            'isShowEditButton'=> true,
        ));?>
    </div>
</section>

<?php $this->renderPartial("/stockOut/partial/commonJs",array('isShowBackButton'=> true));?>