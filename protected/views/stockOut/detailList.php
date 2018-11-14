<section class="content">
    <?php
    $this->renderPartial("/deliveryOrder/partial/deliveryOrderInfo", array('deliveryOrder' => $deliveryOrder,'isShowBackButton'=>true));
    $this->renderPartial("/common/outDetailList", array('outOrders' => $outOrders, 'isShowEditButton' => true));
    ?>
</section>