<section class="content">
    <?php
    $this->renderPartial("/common/deliveryBriefList", array('deliveryOrder'=>$deliveryOrder));
    $this->renderPartial("/common/outDetailList", array('outOrders'=>$outOrders));
    ?>
</section>