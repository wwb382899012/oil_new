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
   
    <?php $this->renderPartial("deliveryOrderList", array('deliveryOrders'=>$data['contractSettlement']['delivery_orders'])); ?>

    <div class="box"  data-bind="with:settlement">
        <div class="box-header with-border">
            <h3 class="box-title">非货款类应付金额</h3>
        </div>
        <div class="box-body form-horizontal">
            <div class="row">
                <div class="col-sm-12" style="margin-bottom: 10px;">
                    <button class="btn btn-primary btn-sm" data-bind="click:addOtherExpense">增加</button>
                    
                </div>
                <form class="col-sm-12">
                    <!-- ko foreach:otherExpenseItems -->
                    <fieldset style="border: 1px solid #aaa; padding: 10px 8px; border-radius: 5px;">
                        <div class="clearfix"><button class="btn center-block pull-right"><i class="glyphicon glyphicon-trash"></i></button></div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>科目 </label>
                                <div class="col-sm-8 row">
                                    <select class="form-control" title="请选择费用科目"
                                            data-bind="
                                                    optionsText: 'name',
                                                    optionsValue: 'id',
                                                    options:subjects,
                                                    value:subject_id,
                                                    valueAllowUnset: true">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>币种</label>
                                <div class="col-sm-8 row">
                                    <select class="form-control" title="请选择币种"
                                            data-bind="
                                                    optionsText: 'name',
                                                    optionsValue: 'id',
                                                    options:$parent.currencies,
                                                    value:currency,
                                                    valueAllowUnset: true">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>金额</label>
                                <div class="input-group">
                                    <span class="input-group-addon" data-bind="text:currencyIco"></span>
                                    <input type="text" class="form-control" data-bind="money:amount">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" data-bind="visible:cnyIsVisible">
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>汇率</label>
                                <div class="row col-sm-8">
                                    <input type="text" class="form-control" data-bind="value:exchange_rate">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>人民币金额</label>
                                <div class="input-group">
                                    <span class="input-group-addon">￥</span>
                                    <input type="text" class="form-control" data-bind="money:amount">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">单据</label>
                                <div class="col-sm-8">
                                    <!-- ko component: {
                                         name: "file-upload",
                                         params: {
                                                     controller:"<?php echo $this->getId() ?>",
                                                     fileConfig:<?php echo json_encode($otherFileConfig) ?>,
                                                     files:files(),
                                                     baseId:detail_id
                                                 }
                                     } -->
                                    <!-- /ko -->
                                </div>
                            </div>
                            <div class="col-sm-8" style="margin-left: -4px;">
                                <label class="col-sm-2 control-label">备注</label>
                                <textarea class="col-sm-10" data-bind="value:remark"></textarea>
                            </div>
                        </div>
                        提示：结算单据格式支持上传图片，Excel、word、pdf，压缩包格式文件，文件不能超过30M
                    </fieldset>
                    <!-- /ko -->
                    
                </form>
                <div class="total-amount col-sm-12" style="margin-top: 10px">
                    合计人民币总额：<span data-bind="moneyWanText:other_amount"></span>万元
                </div>
            </div>
        </div>
    </div>
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
                应付合计：<span data-bind="moneyWanText:settlement.amount"></span>万元
            </div>
            <div class="pull-right">
                <div class="btn btn-primary" data-bind="click:save;">保存</div>
                <div class="btn btn-warning" data-bind="click:tempSave;">暂存</div>
                <div class="btn btn-default">返回</div>
            </div>

        </div>
    </div>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">审核意见</h3>
        </div>
        <div class="box-body">
            <textarea class="col-sm-12" rows="5"></textarea>
        </div>
    </div>
    <div class="box-footer">
        <div class="pull-left">
            应付合计：<span data-bind="moneyWanText:settlement.amount"></span>万元
            <button class="btn btn-link" style="margin-left: 10px;">查看明细</button>
        </div>
        <div class="pull-right">
            <div class="btn btn-primary" data-bind="click:save;">保存</div>
            <div class="btn btn-warning" data-bind="click:tempSave;">暂存</div>
            <div class="btn btn-default">返回</div>
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

    var view;
    $(function () {
        view=new SettlementViewModel(<?php echo json_encode($data) ?>,'<?php echo $this->getId();  ?>');
        ko.applyBindings(view);
    });



</script>