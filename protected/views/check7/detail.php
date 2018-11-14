<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#detail" data-toggle="tab">入库单信息</a></li>
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
            <?php if (!$this->isExternal) { ?>
                <li class="pull-right">
                    <button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button>
                </li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="detail">
                <?php
                $this->renderPartial("/stockIn/partial/stockInInfo", array('stockIn' => $model));
                ?>
            </div>

            <div class="tab-pane" id="flow">
                <?php
                $checkLogs = FlowService::getCheckLog($model->stock_in_id, $this->businessId);
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