<?php 

$order_id = empty($model['order_id'])?0:$model['order_id'];
//发货单
$DeliveryOrderService = new \ddd\application\stock\DeliveryOrderService();
$deliveryOrder = $DeliveryOrderService->getDeliveryOrder($order_id);
$data['deliveryOrder']=$deliveryOrder;
//出库单
$StockOutService = new \ddd\application\stock\StockOutService();
$stockOut = $StockOutService->getStockOutByOrderId($order_id);
$data['stockOut']=$stockOut;
//审核记录
$checkLogs=FlowService::getCheckLog($order_id,10);
$data['checkLogs']=$checkLogs;

//发货单商品结算
$DeliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
$deliveryOrderSettlement=$DeliveryOrderSettlementService->getDeliveryOrderSettlement($order_id);
$data['deliveryOrderBalance']=$deliveryOrderSettlement;

?>
<section class="content" id="content">
    <?php
    $menus = [['text'=>'结算管理'],['text'=>'发货单结算审核','link'=>'/check10/'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], '/check10/');
    ?>
    <div class="card-wrapper">
        <?php 
        $this->renderPartial("/common/new_deliverySettlementHead", array('deliveryOrder'=>$data['deliveryOrder'],'hideBackBtn'=>true));
        $this->renderPartial("/common/new_outSettlementHead", array('outOrders'=>$data['stockOut'], 'is_close_card' => true));
        $this->renderPartial("/common/new_settlementDetail", array('settlement'=>$data['deliveryOrderBalance'],'type'=>3, 'isHiddenBtn'=>true));

        $checkLogs = FlowService::getCheckLog($order_id, $this->businessId);
        $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status'));
        ?>
    </div>
</section>
