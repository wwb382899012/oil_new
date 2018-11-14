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
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#detail" data-toggle="tab">基本信息</a></li>
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
            <?php if(!$this->isExternal){ ?>
            <li class="pull-right"><button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button></li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="detail">

                <section class="content">
                    <?php 
                    $this->renderPartial("/common/deliverySettlementHead", array('deliveryOrder'=>$data['deliveryOrder'],'hideBackBtn'=>true));
                    $this->renderPartial("/common/outSettlementHead", array('outOrders'=>$data['stockOut']));
                
                    ?>
                    <?php $this->renderPartial("/common/settlementDetail", array('settlement'=>$data['deliveryOrderBalance'],'type'=>3));
                ?>
                </section>
            </div><!--end tab1-->
            <div class="tab-pane" id="flow">
                <?php
                $checkLogs = FlowService::getCheckLog($model['order_id'], $this->businessId);
                $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs));
                ?>
            </div>
        </div>
    </div>
</section>

 
<script>
    function back() {
        location.href = "<?php echo $this->getBackPageUrl() ?>";
    }
</script>
