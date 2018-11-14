<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<link rel="stylesheet" href="/css/style/addnewproject.css">
<section>
    <?php
    $menus = [['text' => '结算管理'],['text'=>'采购合同结算','link'=>'/buyContractSettlement/'], ['text' => $this->pageTitle]];
    $buttons = [];
    $buttons[] = ['text' => '暂存', 'attr' => ['data-bind' => 'click:tempSave, html:tempSaveBtnText',  'id' => 'tempSaveButton', 'class_abbr'=>'action-default-base']];
    $buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText', 'id' => 'saveButton']];
    $this->loadHeaderWithNewUI($menus, $buttons, true);
    ?>
    <?php
    $this->renderPartial("/sellContractSettlement/new_contractInfo", array('contract'=>$data['contract']));
    ?>
    <div class="z-card">
        <div class="z-card-body">
            <form data-bind="with:settlement">
                <div class="flex-grid">
                    <label class="col col-count-3 field">
                        <p class="form-cell-title must-fill">结算日期:</p>
                        <input type="text" id="settle_date" class="form-control input-sm date" placeholder="请选择时间" data-bind="date:settle_date">
                    </label>
                </div>
            </form>
        </div>
    </div>
    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_uploadNew.php"; ?>
    <!-- ko foreach:settlement.goodsItems -->
    <div class="z-card ">
        <div class="content-title-wrap">
            <h3 class="z-card-header">
                <span data-bind="text:goods_name"></span>应付结算
                <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </h3>
        </div>
        <div class="z-card-body wrap-content">
            <div class="row">
                <form class="col-sm-12">
                    <div class="clearfix content-title" data-bind="visible:diffQuantity()!=0">
                        <h3 class="pull-right text-theme" >待分摊数量：<span data-bind="text:diffQuantity" ></span><span data-bind="text:unit_name"></span></h3>
                    </div>
                    <!-- ko foreach:billItems -->
                    <div class="settlement-divide">
                        <div class="clearfix  content-title">
                            <h3 class="pull-left">入库通知单编号：<span data-bind="html:url" class="text-link"></span></h3>
                        </div>
                        <div class="flex-grid form-group">
                            <div class="col field col-count-3">
                                <p class="form-cell-title">入库单数量</p>
                                <input type="text" class="form-control" data-bind="value: isShowQuantitySub() ? bill_quantity() +  unit_name() + ' / ' + bill_quantity_sub() +  unit_name_sub() : bill_quantity() +  unit_name()" disabled>
                            </div>
                            <div class="col field col-count-3">
                                <p class="form-cell-title must-fill"></span>结算数量</p>
                                <div class="input-group">
                                    <input type="text" class="form-control" data-bind="value:quantity">
                                    <span class="input-group-addon" data-bind="text:unit_name"></span>
                                </div>
                            </div>
                            <div class="col field col-count-3">
                                <p class="form-cell-title">损耗量</p>
                                <input type="text" class="form-control" data-bind="value: quantity_loss() + ' ' +  unit_name()" disabled>
                            </div>
                        </div>
                        <div class="flex-grid form-group">
                            <div class="col field col-count-3">
                                <p class="form-cell-title">结算单价</p>
                                <div class="input-group">
                                    <span class="input-group-addon" data-bind="text:$parent.currencyIco"></span>
                                    <input type="text" class="form-control" data-bind="money:price" disabled>
                                </div>
                            </div>
                            <div class="col field col-count-3">
                                <p class="form-cell-title">结算金额</p>
                                <div class="input-group">
                                    <span class="input-group-addon" data-bind="text:$parent.currencyIco"></span>
                                    <input type="text" class="form-control" data-bind="money:amount" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grid form-group" data-bind="visible:$parent.cnyVisible">
                            <div class="col field col-count-3">
                                <p class="form-cell-title">人民币结算单价</p>
                                <div class="input-group">
                                    <span class="input-group-addon">￥</span>
                                    <input type="text" class="form-control" data-bind="money:price_cny" disabled>
                                </div>
                            </div>
                            <div class="col field col-count-3">
                                <p class="form-cell-title">人民币结算金额</p>
                                <div class="input-group">
                                    <span class="input-group-addon">￥</span>
                                    <input type="text" class="form-control" data-bind="money:amount_cny" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /ko -->
                    <div class="settlement-divide">
                        <h3 class="pull-left content-title">合计</h3>
                        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_goodsSettlementDetail.php"; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /ko -->
    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_otherSettlementDetail.php"; ?>
    <div class="z-card">
        <div class="z-card-body">
            <div class="form-group flex-grid align-start">
                <p class="form-cell-title w-fixed first-line-align">备注:</p>
                <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:settlement.remark"></textarea>
            </div>
            <div>
                应付合计：<span data-bind="moneyText:settlement.amount"></span>元
                <!--                <button class="btn btn-link" style="margin-left: 10px;">查看明细</button>-->
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