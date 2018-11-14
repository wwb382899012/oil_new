<section class="el-container is-vertical">
    <?php
    $menus = [['text' => '出库管理'],['text' => '新建发货单', 'link' => '/deliveryOrder/'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], '/deliveryOrder/');
    ?>

    <div class="card-wrapper">
        <?php
        $this->renderPartial("partial/new_deliveryOrderInfoCard", array('deliveryOrder'=>$deliveryOrder));
        $this->renderPartial("/stockOut/partial/new_stockOutOrderInfoCard", array('outOrders'=>$deliveryOrder->stockOuts));
        ?>
    </div>
</section>