<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<!--<section class="content">-->
<?php
$this->renderPartial("/common/stockNoticeBriefInfo", array('stockNotice'=>$data['stockInBatch'], 'stockNoticeGoods'=>$data['stockInBatch']['items']));

$stockIns = $data['stockIn'];
if(is_array($stockIns))
    foreach ($stockIns as $stockIn) {
        $this->renderPartial("/common/stockInBriefInfo", array('stockIn'=>$stockIn));
}
?>

    <div class="box">
        <div class="box-header link with-border">
            <h3 class="box-title">入库通知单结算操作</h3>
        </div>
        <div class="box-body form-horizontal" data-bind="with:settlement">
            <div class="row">
                <form class="col-sm-12">
                    <div class="form-group">
                        <label for="settle_date" class="col-sm-2 control-label">结算日期
                            <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-3">
                            <input type="text" id="settle_date" class="form-control input-sm date" placeholder="请选择时间" data-bind="date:settle_date">
                        </div>
                    </div>
                    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/upload.php";  ?>
                    <!-- ko foreach:goodsItems -->
                    <fieldset>
                        <fieldset style="border: 1px solid; padding: 0.35em 0.625em 0.75em;margin-bottom: 15px">
                            <legend class="h4 text-primary" style="border: 0;  width: auto;"><span data-bind="text:goods_name" ></span></legend>
                            <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/goodsSettlementDetail.php"; ?>
                        </fieldset>
                    </fieldset>
                    <!-- /ko -->
                </form>
            </div>
        </div>
        <div class="box-footer">
            <div class="pull-right">
                <div class="btn btn-warning" data-bind="click:save, html:saveBtnText">保存</div>
                <div class="btn btn-primary" data-bind="click:tempSave, html:tempSaveBtnText" >暂存</div>
                <?php if(!$this->isExternal){ ?>
                <div class="btn btn-default" data-bind="click:back">返回</div>
                <?php } ?>
            </div>
        </div>
        <div class="modal fade in" id="buy_lock_dialog">
            <div class="modal-dialog" style="width:80%">
                <div class="modal-content">
                    <div class="modal-header" >
                        <a class="close" data-dismiss="modal">×</a>
                        <h5>锁价/转月记录</h5>
                    </div>
                    <div class="modal-body" id="buy_lock_dialog_body">
                    </div>
                    <div class="modal-footer">
                        <input type="button" value="&nbsp;关闭&nbsp;" class="btn btn-success btn-sm" data-dismiss="modal">
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--</section>-->

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