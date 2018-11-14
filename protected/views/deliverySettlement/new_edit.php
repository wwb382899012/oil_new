<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<link href="/css/style/addnewproject.css?key=20180112" rel="stylesheet" type="text/css">
<section>
    <?php
    $menus = [['text' => '结算管理'],['text'=>'发货单结算','link'=>'/deliverySettlement/'], ['text' => $this->pageTitle]];
    $buttons = [];
    $buttons[] = ['text' => '暂存', 'attr' => ['data-bind' => 'click:tempSave, html:tempSaveBtnText',  'id' => 'tempSaveButton', 'class_abbr'=>'action-default-base']];
    $buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText', 'id' => 'saveButton']];
    $this->loadHeaderWithNewUI($menus, $buttons, true);
    ?>
    <?php
    $this->renderPartial("/common/new_deliverySettlementHead", array('deliveryOrder'=>$data['deliveryOrder']));
    $this->renderPartial("/common/new_outSettlementHead", array('outOrders'=>$data['stockOut'], 'is_close_card' => true));
    ?>

    <div class="z-card">
        <h3 class="z-card-header">
            发货单结算操作
        </h3>
        <div class="z-card-body">
            <form data-bind="with:settlement">
                <div class="flex-grid form-group" style="margin-bottom: 30px;">
                    <label class="col col-count-3 field">
                        <p class="form-cell-title must-fill">结算日期:</p>
                        <input type="text" id="settle_date" class="form-control input-sm date" placeholder="请选择时间" data-bind="date:settle_date">
                    </label>
                </div>
                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_uploadNew.php"; ?>
                <!-- ko foreach:goodsItems -->
                <div class="settlement-divide">
                <h3 class="pull-left content-title"><span data-bind="text:goods_name" ></span></h3>
                    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_goodsSettlementDetail.php"; ?>
                </div>
                <!-- /ko -->
            </form>
        </div>
    </div>
    <div class="modal fade draggable-modal"  id="buy_lock_dialog" tabindex="-1" role="dialog" aria-labelledby="modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header--flex">
                    <h4 class="modal-title">锁价/转月记录</h4>
                    <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></a>
                </div>
                <div class="modal-body" id="buy_lock_dialog_body">
                </div>
                <div class="modal-footer flex-center">
                    <a href="javascript: void 0" role="button" class="o-btn o-btn-primary" data-dismiss="modal">确定</a>
                </div>
            </div>
        </div>
    </div>
    <?php
    // if($data['deliveryOrder']['status']>DeliveryOrder::STATUS_PASS){
    // 	$checkLogs = FlowService::getCheckLog($data['deliveryOrder']['order_id'], 10);
    // 	$this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs));
    // }
    ?>

</section>
<script type="text/javascript" src="/js/pages/settlement.js?key=20180423001"></script>
<script>
    $("div.link").unbind('click').click(function () {
        $(this).next().toggle();
    });
    settlementConfigs.currencies=<?php echo json_encode($this->map["currency"]); ?>;
    settlementConfigs.expenseNames=<?php echo json_encode($this->map["pay_type"]); ?>;
    settlementConfigs.units=<?php echo json_encode($this->map["goods_unit"]) ?>;
    settlementConfigs.taxSubjects=<?php echo json_encode(\ddd\domain\entity\value\Tax::getConfigs()) ?>;
    settlementConfigs.goodsOtherSubjects=<?php echo json_encode(\ddd\domain\entity\value\Expense::getConfigs()) ?>;
    settlementConfigs.otherSubjects=<?php echo json_encode(\ddd\domain\entity\value\OtherFee::getConfigs()) ?>;
    settlementConfigs.controllerName = "<?php echo $this->getId() ?>";

    var view;
    $(function () {
        view=new SettlementViewModel(<?php echo json_encode($data['settlement']) ?>);
        ko.applyBindings(view);
    });


</script>