<section class="content" id="content">

    <?php
    $menus = [['text'=>'出库管理'],['text'=>'出库单审核','link'=>'/check20/?search[checkStatus]=2'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], true);
    ?>

    <div class="card-wrapper">
        <?php
        $this->renderPartial("/deliveryOrder/partial/new_deliveryOrderInfoCard", array('deliveryOrder' => $deliveryOrder));
        $this->renderPartial("/stockOut/partial/new_stockOutOrderInfoCard", array('outOrders' => [$model]));

        $checkLogs = FlowService::getCheckLog($model->out_order_id, $this->businessId);
        $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status'));
        ?>
    </div>
</section>