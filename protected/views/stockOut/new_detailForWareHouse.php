<section class="el-container is-vertical">

    <?php
    $menus = [['text' => '出库管理'],['text' => '添加出库单', 'link' => '/stockOut/'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], '/stockOut/');
    ?>

    <div class="card-wrapper">
        <?php
        $this->renderPartial("/deliveryOrder/partial/new_deliveryOrderInfoCard", array('deliveryOrder' => $deliveryOrder));
        $this->renderPartial("partial/new_stockOutOrderInfoCard", array('outOrders' => $outOrders));
        ?>
    </div>
</section>