<section class="content" id="content">

    <?php
    $menus = [['text'=>'出库管理'],['text'=>'发货单审核','link'=>'/check9/?search[checkStatus]=2'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], true);
    ?>

    <div class="card-wrapper">
        <?php
        $deliveryOrderModel = DeliveryOrder::model()->findByPk($data['obj_id']);
        $this->renderPartial("/deliveryOrder/partial/new_deliveryOrderInfoCard", array('deliveryOrder'=>$deliveryOrderModel));

        $checkLogs = FlowService::getCheckLog($deliveryOrderModel->order_id, $this->businessId);
        $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status'));
        ?>
    </div>

</section>