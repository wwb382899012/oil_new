<section class="content">
    <?php
    $this->renderPartial("/common/deliveryBriefList", array('deliveryOrder'=>$model->deliveryOrder));

    $this->renderPartial("/stockOut/partial/stockOutOrderInfo", array(
        'outOrder' => $model
    ));
    ?>
</section>

<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="flow">
                <?php
                $checkLogs = FlowService::getCheckLog($model->out_order_id, $this->businessId);
                $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status')); ?>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    var back = function () {
        history.go(-1);
    }
</script>