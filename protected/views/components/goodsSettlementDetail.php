<style>
    table select.form-control {
        padding: 0px;
    }
</style>
<div class="clearfix">
    <div class="pull-right">
        <button type="button" class="btn btn-link" data-bind="click:toggleDetail,visible:hasDetail() && isShowDetail()"> 收起明细</button>
        <button type="button" class="btn btn-link" data-bind="click:toggleDetail,visible:hasDetail() && !isShowDetail()"> 展开明细</button>
        <button type="button" class="btn btn-link" data-bind="click:addDetail,visible:!hasDetail()"> 录入明细</button>
        <button type="button" class="btn btn-link" data-bind="click:trashDetail,visible:hasDetail"> 取消录入明细</button>
        <button type="button" class="btn btn-link" data-bind="click:displayLockPriceDetail,visible:isShowLockDetailBtn"> 查看锁价</button>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-4">
        <label class="col-sm-4 control-label"><span data-bind="visible:displayIn">入</span><span data-bind="visible:!displayIn()">出</span>库单数量</label>
        <div class="col-sm-8">
            <p class="form-control-static">
                <span  data-bind="text:bill_quantity"></span>
                <span  data-bind="text:unit_name"></span>
                <span data-bind="visible:isShowQuantitySub">/
                    <span  data-bind="text:bill_quantity_sub"></span>
                    <span  data-bind="text:unit_name_sub"></span>
                </span>
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
                <span  data-bind="text:quantity_loss"></span>
                <span  data-bind="text:unit_name"></span>
            </p>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-4">
        <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>结算单价</label>
        <div class="col-sm-8">
            <div class="input-group">
                <span class="input-group-addon" data-bind="text:currencyIco"></span>
                <input type="text" class="form-control" data-bind="money:price,enable:!hasDetail()">
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>结算金额</label>
        <div class="col-sm-8">
            <div class="input-group">
                <span class="input-group-addon" data-bind="text:currencyIco"></span>
                <input type="text" class="form-control" data-bind="money:amount,enable:!hasDetail()">
            </div>
        </div>
    </div>
    <div class="col-sm-4" data-bind="visible:cnyVisible">
        <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>结算汇率</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" data-bind="value:exchange_rate" readonly="readonly">
        </div>
    </div>
</div>
<div class="form-group" data-bind="visible:cnyVisible">
   <div class="col-sm-4">
        <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>人民币结算单价</label>
        <div class="col-sm-8">
            <div class="input-group">
                <span class="input-group-addon">￥</span>
                <input type="text" class="form-control" data-bind="money:price_cny,enable:!hasDetail()">
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>人民币结算金额</label>
        <div class="col-sm-8">
            <div class="input-group">
                <span class="input-group-addon">￥</span>
                <input type="text" class="form-control" data-bind="money:amount_cny ,enable:!hasDetail()">
            </div>
        </div>
    </div>
   
</div>
<fieldset  data-bind="with:detail,visible:isShowDetail">
    <legend class="h5 text-info">结算明细</legend>
    <div class="form-group col-sm-12">
        <div class="h5"><span class="text-red fa fa-asterisk"></span>贷款金额</div>
        <table class="table table-striped table-bordered">
            <tr>
                <td style="width: 90px;">计价币种</td>
                <td>计价币种货款金额</td>
                <td>汇率</td>
                <td>人民币货款总额</td>
                <td>货款单价</td>
                <td>计税汇率</td>
                <td>计税人民币货款总额</td>
            </tr>
            <tr>
                <td>
                    <select class="form-control" title="请选择计价币种"
                            data-bind="
                                                        optionsText: 'name',
                                                        optionsValue: 'id',
                                                        options:currencies,
                                                        value:currency,
                                                        valueAllowUnset: true,enable:currencyIsCanChange">
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon" data-bind="text:currencyIco"></span>
                        <input type="text" class="form-control" data-bind="money:amount_currency">
                    </div>
                </td>
                <td>
                    <input type="text" class="form-control" data-bind="value:exchange_rate,enable:!isCNY()">
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">￥</span>
                        <input type="text" class="form-control" data-bind="money:amount_goods">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">￥</span>
                        <input type="text" class="form-control" data-bind="money:price_goods">
                    </div>
                </td>
                <td><input type="text" class="form-control" data-bind="value:exchange_rate_tax"></td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">￥</span>
                        <input type="text" class="form-control" data-bind="money:amount_goods_tax">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="form-group col-sm-12">
        <div class="h5">相关税收
            <button class="btn" data-bind="click:addTax">
                <i style="color: #428bca" class="glyphicon glyphicon-plus"></i>
            </button>
        </div>
        <table class="table table-striped table-bordered">
            <tr>
                <td style="width: 90px;">税收名目</td>
                <td>税率</td>
                <td>税收总金额</td>
                <td>税收单价</td>
                <td>备注</td>
                <td>操作</td>
            </tr>
            <!-- ko foreach:taxItems -->
            <tr>
                <td>
                    <select class="form-control" title="请选择税收科目"
                            data-bind="
                                                        optionsText: 'name',
                                                        optionsValue: 'id',
                                                        options:subjects,
                                                        value:subject_id,
                                                        valueAllowUnset: true">
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control" data-bind="percent:rate">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">￥</span>
                        <input type="text" class="form-control" data-bind="money:amount">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">￥</span>
                        <input type="text" class="form-control" data-bind="money:price" readonly="readonly">
                    </div>
                </td>
                <td><input type="text" class="form-control" data-bind="value:remark"></td>
                <td>
                    <button class="btn center-block" data-bind="click:$parent.removeTax"><i class="glyphicon glyphicon-trash"></i></button>
                </td>
            </tr>
            <!-- /ko -->
        </table>
    </div>
    <div class="form-group col-sm-12">
        <div class="h5">其他费用
            <button class="btn" data-bind="click:addOtherExpense">
                <i style="color: #428bca" class="glyphicon glyphicon-plus"></i>
            </button>
        </div>
        <table class="table table-striped table-bordered">
            <tr >
                <td>科目</td>
                <td>费用总额</td>
                <td>费用单价</td>
                <td>备注</td>
                <td>操作</td>
            </tr>
            <!-- ko foreach:otherExpenseItems -->
            <tr>
                <td>
                    <select class="form-control" title="请选择费用科目"
                            data-bind="
                                                        optionsText: 'name',
                                                        optionsValue: 'id',
                                                        options:subjects,
                                                        value:subject_id,
                                                        valueAllowUnset: true">
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">￥</span>
                        <input type="text" class="form-control" data-bind="money:amount">

                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon">￥</span>
                        <input type="text" class="form-control" data-bind="money:price">
                    </div>
                </td>
                <td><input type="text" class="form-control" data-bind="value:remark"></td>
                <td>
                    <button class="btn center-block"  data-bind="click:$parent.removeOtherExpense"><i class="glyphicon glyphicon-trash"></i></button>
                </td>
            </tr>
            <!-- /ko -->
        </table>
    </div>
    <div class="form-group">
        <div class="col-sm-4">
            <label class="col-sm-4 control-label"><span class="text-red fa fa-asterisk"></span>调整金额</label>
            <div class="col-sm-4">
                <select class="form-control selectpicker" data-bind="optionsCaption: '请选择调整类型',value:adjust_type, valueAllowUnset: true">
                    <option value="">请选择类型</option>
                    <option value="1">增加</option>
                    <option value="2">减少</option>
                </select>
            </div>
            <div class="input-group">
                <span class="input-group-addon">￥</span>
                <input type="text" class="form-control" data-bind="money:amount_adjust">
            </div>
        </div>
        <div class="col-sm-8">
            <label class="col-sm-2 control-label" style="width: 16.1%"><span class="text-red fa fa-asterisk" data-bind="visible:reasonAdjustIsRequired"></span>调整原因</label>
            <div class="col-sm-10 row">
                <input type="text" class="form-control" data-bind="value:reason_adjust">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-4">
            <label class="col-sm-4  control-label">总结算数量</label>
            <div class="col-sm-8">
                <p class="form-control-static">
                    <span  data-bind="text:quantity"></span>
                    <span  data-bind="text:$parent.unit_name"></span>
                </p>
            </div>
        </div>
        <div class="col-sm-4">
            <label class="col-sm-4  control-label">人民币结算金额</label>
            <div class="col-sm-8">
                <p class="form-control-static">
                    ￥ <span  data-bind="moneyText:amount"></span>
                </p>
            </div>
        </div>
        <div class="col-sm-4">
            <label class="col-sm-4  control-label">人民币结算单价</label>
            <div class="col-sm-8">
                <p class="form-control-static">
                    ￥ <span  data-bind="moneyText:price"></span>
                </p>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-4">
            <label class="col-sm-4  control-label">确定总结算数量 </label>
            <div class="col-sm-8">
                <p class="form-control-static">
                    <span  data-bind="text:quantity"></span>
                    <span  data-bind="text:$parent.unit_name"></span>
                </p>
            </div>
        </div>
        <div class="col-sm-4">
            <label class="col-sm-4  control-label"><span class="text-red fa fa-asterisk"></span>确定人民币结算金额</label>
            <div class="col-sm-8">
                <div class="input-group">
                    <span class="input-group-addon">￥</span>
                    <input type="text" class="form-control" data-bind="money:amount_actual">

                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <label class="col-sm-4  control-label"><span class="text-red fa fa-asterisk"></span>确定人民币结算单价</label>
            <div class="col-sm-8">
                <div class="input-group">
                    <span class="input-group-addon">￥</span>
                    <input type="text" class="form-control" data-bind="money:price_actual">
                </div>
            </div>
        </div>
    </div>

</fieldset>
<div class="form-group">
    <div class="col-sm-4">
        <div class="form-group">
            <label class="col-sm-4 control-label">
                <span class='glyphicon ' data-bind="css:{'glyphicon-ok text-green':settlementFileStatus,' glyphicon-remove text-red':!settlementFileStatus()}"></span>&emsp;
                结算单据
            </label>
            <div class="col-sm-8">
                <!-- ko component: {
                                            name: "file-upload",
                                            params: {
                                                        status:settlementFileStatus,
                                                        uploadFiles:settleFiles,
                                                        controller:"<?php echo $this->getId() ?>",
                                                        fileConfig:<?php echo json_encode($settleFileConfig) ?>,
                                                        baseId:item_id
                                                    }
                                        } -->
                <!-- /ko -->
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">其他附件</label>
            <div class="col-sm-8">
                <!-- ko component: {
                                            name: "file-upload",
                                            params: {
                                                        controller:"<?php echo $this->getId() ?>",
                                                        fileConfig:<?php echo json_encode($goodsOtherFileConfig) ?>,
                                                        uploadFiles:goodsOtherFiles,
                                                        baseId:item_id
                                                    }
                                        } -->
                <!-- /ko -->
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <label>备注: </label>
        <textarea class="form-control" style="height: 110px;" data-bind="value:remark"></textarea>
    </div>
</div>
提示：结算单据和其他附件格式支持上传图片，Excel、word、pdf，压缩包格式文件，文件不能超过30M