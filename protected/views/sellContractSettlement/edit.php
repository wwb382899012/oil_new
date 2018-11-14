<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
<?php 
$this->renderPartial("../sellContractSettlement/contractInfo", array('contract'=>$data['contract']));

?>
    <div class="box" data-bind="with:settlement">
        <div class="box-body form-horizontal">
            <label for="settle_date" class="col-sm-2 control-label">结算日期
                <span class="text-red fa fa-asterisk"></span></label>
            <div class="col-sm-3">
                <input type="text" id="settle_date" class="form-control input-sm date" placeholder="请选择时间" data-bind="date:settle_date">
            </div>
        </div>
    </div>
    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/upload.php";  ?>
    <!-- ko foreach:settlement.goodsItems -->
    <div class="box">
        <div class="box-header link with-border">
            <h3 class="box-title"><span data-bind="text:goods_name"></span>应付结算</h3>
        </div>
        <div class="box-body form-horizontal">
            <div class="row">
                <form class="col-sm-12">
                    <fieldset>
                        <legend class="h4 text-primary" data-bind="visible:diffQuantity()!=0">
                            <span data-bind="text:goods_name" ></span>
                            <span class="pull-right text-red" >待分摊数量：<span data-bind="text:diffQuantity" ></span><span data-bind="text:unit_name"></span></span>
                        </legend>
                        <!-- ko foreach:billItems -->
                        <fieldset style="border: 1px solid; padding: 0.35em 0.625em 0.75em;margin-bottom: 15px">
                            <legend class="h4 text-primary" style="border: 0;  width: auto;">发货单编号：<span data-bind="html:url"></span></legend>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">出库单数量</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <span data-bind="text:bill_quantity"></span>
                                            <span data-bind="text:unit_name"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>结算数量</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" data-bind="value:quantity">
                                            <span class="input-group-addon" data-bind="text:unit_name"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">损耗量</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <span data-bind="text:quantity_loss"></span>
                                            <span data-bind="text:unit_name"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算单价</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <span data-bind="text:$parent.currencyIco"></span>
                                            <span data-bind="moneyText:price"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算金额</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <span data-bind="text:$parent.currencyIco"></span>
                                            <span data-bind="moneyText:amount"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" data-bind="visible:$parent.cnyVisible">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">人民币结算单价</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            ￥
                                            <span data-bind="moneyText:price_cny"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">人民币结算金额</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            ￥
                                            <span data-bind="moneyText:amount_cny"></span>
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </fieldset>
                        <!-- /ko -->
                        <fieldset style="border: 1px solid; padding: 0.35em 0.625em 0.75em;margin-bottom: 15px">
                            <legend class="h4" style="border: 0;  width: auto;">合计</legend>
                            <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/goodsSettlementDetail.php"; ?>
                        </fieldset>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <!-- /ko -->
    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/otherSettlementDetail.php"; ?>
    <div class="box box-solid">
        <div class="box-body form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:settlement.remark"></textarea>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <div class="pull-left">
                应付合计：<span data-bind="moneyText:settlement.amount"></span>元
<!--                 <button class="btn btn-link" style="margin-left: 10px;">查看明细</button>-->
            </div>
            <div class="pull-right">
                <div class="btn btn-warning" data-bind="click:save, html:saveBtnText">保存</div>
                <div class="btn btn-primary" data-bind="click:tempSave, html:tempSaveBtnText" >暂存</div>
                <?php if(!$this->isExternal){ ?>
                <div class="btn btn-default" data-bind="click:back">返回</div>
                <?php } ?>
            </div>

        </div>
    </div>
</section>




<script type="text/javascript" src="/js/pages/settlement.js?key=20180423001"></script>
<script>
    settlementConfigs.currencies=<?php echo json_encode($this->map["currency"]); ?>;
    settlementConfigs.expenseNames=<?php echo json_encode($this->map["pay_type"]); ?>;
    settlementConfigs.units=<?php echo json_encode($this->map["goods_unit"]) ?>;
    settlementConfigs.taxSubjects=<?php echo json_encode(\ddd\domain\entity\value\Tax::getConfigs()) ?>;
    settlementConfigs.goodsOtherSubjects=<?php echo json_encode(\ddd\domain\entity\value\Expense::getConfigs()) ?>;
    settlementConfigs.otherExpenseSubjects=<?php echo json_encode(\ddd\domain\entity\value\OtherFee::getConfigs()) ?>;
    settlementConfigs.controllerName = "<?php echo $this->getId() ?>";

    var view;
    $(function () {
        view=new SettlementViewModel(<?php echo json_encode($data['settlement']) ?>);
        ko.applyBindings(view);

        $("div.link").click(function(){
            $(this).next().toggle();
        })
    });



</script>