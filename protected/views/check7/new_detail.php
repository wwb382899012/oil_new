<section class="content" id="content">

    <?php
    $menus = [['text'=>'入库管理'],['text'=>'入库单审核','link'=>'/check7/?search[checkStatus]=2'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], true);
    ?>

    <div class="card-wrapper">
        <?php
        $this->renderPartial("/stockIn/partial/new_stockInInfoCard", array('stockIns' => [$model]));

        $checkLogs = FlowService::getCheckLog($model->stock_in_id, $this->businessId);
        $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status'));
        ?>
    </div>

</section>