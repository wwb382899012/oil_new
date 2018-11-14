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
                    <div class="clearfix"><button class="btn center-block pull-right" data-bind="click:$parent.removeOtherExpense"><i class="glyphicon glyphicon-trash"></i></button></div>
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
                                <input type="text" class="form-control" data-bind="money:amount_cny" readonly="readonly">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <label class="col-sm-4 control-label">
                                <span class='glyphicon ' data-bind="css:{'glyphicon-ok text-green':otherFileStatus,' glyphicon-remove text-red':!otherFileStatus()}"></span>&emsp;
                                单据
                            </label>
                            <div class="col-sm-8">
                                <!-- ko component: {
                                         name: "file-upload",
                                         params: {
                                                     controller:"<?php echo $this->getId() ?>",
                                                     fileConfig:<?php echo json_encode($otherFileConfig) ?>,
                                                     uploadFiles:otherFiles,
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
                合计人民币总额：￥<span data-bind="moneyText:other_amount"></span>
            </div>
        </div>
    </div>
</div>