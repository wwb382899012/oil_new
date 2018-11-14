<div class="clearfix">
    <div class="pull-right">
        <a href="javascript: void 0" role="button" class="o-btn o-btn-action" data-bind="click:toggleDetail,visible:hasDetail() && isShowDetail()">收起明细</a>
        <a href="javascript: void 0" role="button" class="o-btn o-btn-action" data-bind="click:toggleDetail,visible:hasDetail() && !isShowDetail()">展开明细</a>
        <a href="javascript: void 0" role="button" class="o-btn o-btn-action primary" data-bind="click:addDetail,visible:!hasDetail()">录入明细</a>
        <a href="javascript: void 0" role="button" class="o-btn o-btn-action" data-bind="click:trashDetail,visible:hasDetail">取消录入明细</a>
        <a href="javascript: void 0" role="button" class="o-btn o-btn-action" data-bind="click:displayLockPriceDetail,visible:isShowLockDetailBtn">查看锁价</a>
    </div>
</div>
<div class="flex-grid form-group">
    <label class="col col-count-3 field">
        <p class="form-cell-title">
            <span data-bind="visible:displayIn">入</span><span data-bind="visible:!displayIn()">出</span>库单数量:
        </p>
        <input type="text" class="form-control" data-bind="value: isShowQuantitySub() ? bill_quantity() +  unit_name() + ' / ' + bill_quantity_sub() +  unit_name_sub() : bill_quantity() +  unit_name()" disabled>
<!--        <input type="text" class="form-control" data-bind="value: bill_quantity_sub() +  unit_name_sub(), visible:isShowQuantitySub" disabled>-->
    </label>
    <label class="col field col-count-3">
        <p class="form-cell-title must-fill">结算数量</p>
        <div class="input-group">
            <input type="text" class="form-control" data-bind="value:quantity">
            <span class="input-group-addon" data-bind="text:unit_name"></span>
        </div>
    </label>
    <label class="col field col-count-3">
        <p class="form-cell-title">损耗量</p>
        <input type="text" class="form-control" data-bind="value: quantity_loss() +  unit_name()" disabled>
    </label>
</div>
<div class="flex-grid form-group">
    <label class="col field col-count-3">
        <p class="form-cell-title must-fill">结算单价</p>
        <div class="input-group">
            <span class="input-group-addon" data-bind="text:currencyIco"></span>
            <input type="text" class="form-control" data-bind="money:price,enable:!hasDetail()">
        </div>
    </label>
    <label class="col field col-count-3">
        <p class="form-cell-title must-fill">结算金额</p>
        <div class="input-group">
            <span class="input-group-addon" data-bind="text:currencyIco"></span>
            <input type="text" class="form-control" data-bind="money:amount,enable:!hasDetail()">
        </div>
    </label>
    <label class="col field col-count-3" data-bind="visible:cnyVisible">
        <p class="form-cell-title must-fill">结算汇率</p>
        <input type="text" class="form-control" data-bind="value:exchange_rate" readonly="readonly">
    </label>
</div>
<div class="flex-grid form-group" data-bind="visible:cnyVisible">
    <label class="col field col-count-3">
        <p class="form-cell-title must-fill">人民币结算单价</p>
        <div class="input-group">
            <span class="input-group-addon">￥</span>
            <input type="text" class="form-control" data-bind="money:price_cny,enable:!hasDetail()">
        </div>
    </label>
    <label class="col field col-count-3">
        <p class="form-cell-title must-fill">人民币结算金额</p>
        <div class="input-group">
            <span class="input-group-addon">￥</span>
            <input type="text" class="form-control" data-bind="money:amount_cny ,enable:!hasDetail()">
        </div>
    </label>
</div>
<fieldset  data-bind="with:detail,visible:isShowDetail" class="input-detail form-group">
    <legend>结算明细</legend>
    <div class="form-group">
        <p class="form-cell-title must-fill">贷款金额</p>
        <table class="table table-fixed table-nowrap">
            <thead>
            <tr>
                <th style="width: 110px;">计价币种</th>
                <th>计价币种货款金额</th>
                <th style="width: 110px;">汇率</th>
                <th>人民币货款总额</th>
                <th style="width: 140px;">货款单价</th>
                <th style="width: 110px;">计税汇率</th>
                <th>计税人民币货款总额</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <select class="form-control selectpicker show-menu-arrow" title="请选择"
                            data-bind="
                                                        optionsText: 'name',
                                                        optionsValue: 'id',
                                                        selectPickerOptions:currencies,
                                                        selectpicker:currency, enable:currencyIsCanChange,
                                                        valueAllowUnset: true">
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
            </tbody>
        </table>
    </div>
    <div class="form-group">
        <p class="form-cell-title">相关税收
        </p>
        <table class="table table-fixed table-nowrap">
            <thead>
                <tr>
                    <th style="width: 180px;">税收名目</th>
                    <th>税率</th>
                    <th>税收总金额</th>
                    <th>税收单价</th>
                    <th>备注</th>
                    <th style="width: 80px;">操作</th>
                </tr>
            </thead>
            <tbody>
            <!-- ko foreach:taxItems -->
            <tr>
                <td>
                    <select class="form-control selectpicker show-menu-arrow" title="请选择税收名目"
                            data-bind="
                                                        optionsText: 'name',
                                                        optionsValue: 'id',
                                                        optionsCaption:'请选择税收名目',
                                                        selectPickerOptions:subjects,
                                                        selectpicker:subject_id,
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
                    <a href="javascript: void 0" class="o-btn o-btn-action" data-bind="click:$parent.removeTax">删除</a>
                </td>
            </tr>
            <!-- /ko -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"></td>
                    <td>
                        <a href="javascript: void 0" class="o-btn o-btn-primary action" data-bind="click:addTax">
                            新增
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="form-group">
        <p class="form-cell-title">其他费用
        </p>
        <table class="table table-fixed table-nowrap">
            <thead>
            <tr >
                <th style="width: 180px">科目</th>
                <th>费用总额</th>
                <th>费用单价</th>
                <th>备注</th>
                <th style="width: 80px;">操作</th>
            </tr>
            </thead>
            <tbody>
            <!-- ko foreach:otherExpenseItems -->
            <tr>
                <td>
                    <select class="form-control selectpicker show-menu-arrow" data-live-search="true" title="请选择费用科目"
                            data-bind="
                                                        optionsText: 'name',
                                                        optionsValue: 'id',
                                                        optionsCaption:'请选择费用科目',
                                                        selectPickerOptions:subjects,
                                                        selectpicker:subject_id,
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
                    <a href="javascript: void 0" class="o-btn o-btn-action"  data-bind="click:$parent.removeOtherExpense">删除</a>
                </td>
            </tr>
            <!-- /ko -->
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4"></td>
                <td>
                    <a href="javascript: void 0" class="o-btn o-btn-primary action" data-bind="click:addOtherExpense">
                        新增
                    </a>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
    <div class="o-row form-group">
        <div class="o-col-sm-4">
            <p class="form-cell-title must-fill">调整金额</p>
            <div class="o-row">
                <div class="o-col-sm-4">
                    <select class="form-control selectpicker show-menu-arrow" data-bind="optionsCaption: '请选择调整类型',value:adjust_type, valueAllowUnset: true">
                        <option value="">选择类型</option>
                        <option value="1">增加</option>
                        <option value="2">减少</option>
                    </select>
                </div>
                <div class="o-col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon">￥</span>
                        <input type="text" class="form-control" data-bind="money:amount_adjust">
                    </div>
                </div>
            </div>
        </div>
        <div class="o-col-sm-8">
            <p class="form-cell-title" data-bind="css: {'must-fill': reasonAdjustIsRequired}">调整原因</p>
            <input type="text" class="form-control" data-bind="value:reason_adjust">
        </div>
    </div>
    <div class="flex-grid form-group">
        <label class="col field col-count-3">
            <p class="form-cell-title">总结算数量</p>
            <input type="text" class="form-control" data-bind="value: quantity() +  $parent.unit_name()" disabled>
        </label>
        <label class="col field col-count-3">
            <p class="form-cell-title">人民币结算金额</p>
<!--            <input type="text" class="form-control" data-bind="value: '￥ ' +  amount()" disabled>-->
            <div class="input-group">
                <span class="input-group-addon">￥</span>
                <input type="text" class="form-control" data-bind="money:amount" disabled>
            </div>
        </label>
        <label class="col field col-count-3">
            <p class="form-cell-title">人民币结算单价</p>
<!--            <input type="text" class="form-control" data-bind="value: '￥ ' +  price()" disabled>-->
            <div class="input-group">
                <span class="input-group-addon">￥</span>
                <input type="text" class="form-control" data-bind="money:price" disabled>
            </div>
        </label>
    </div>
    <div class="flex-grid">
        <label class="col field col-count-3">
            <p class="form-cell-title">确定总结算数量</p>
            <input type="text" class="form-control" data-bind="value: quantity() +  $parent.unit_name()" disabled>
        </label>
        <label class="col field col-count-3">
            <p class="form-cell-title">确定人民币结算金额</p>
            <div class="input-group">
                <span class="input-group-addon">￥</span>
                <input type="text" class="form-control" data-bind="money:amount_actual">

            </div>
        </label>
        <label class="col field col-count-3">
            <p class="form-cell-title">确定人民币结算单价</p>
            <div class="input-group">
                <span class="input-group-addon">￥</span>
                <input type="text" class="form-control" data-bind="money:price_actual">
            </div>
        </label>
    </div>

</fieldset>
<div class="flex-grid form-group">
    <div class="col field col-count-1 flex-grid align-start">
        <span class="w-fixed first-line-align">
            结算单据:
        </span>
        <div class="form-group-custom-upload">
            <!-- ko component: {
                                 name: "file-upload",
                                 params: {
                                            status:settlementFileStatus,
                                            uploadFiles:settleFiles,
                                            controller:"<?php echo $this->getId() ?>",
                                            fileConfig:<?php echo json_encode($settleFileConfig) ?>,
                                            fileParams: {
                                               id:item_id()
                                            }
                                         }
                             } -->
            <!-- /ko -->
        </div>
    </div>
</div>
<div class="flex-grid form-group">
    <div class="col field col-count-1 flex-grid align-start">
        <span class="w-fixed first-line-align">
            其他附件:
        </span>
        <div class="form-group-custom-upload">
            <!-- ko component: {
                                            name: "file-upload",
                                            params: {
                                                        controller:"<?php echo $this->getId() ?>",
                                                        fileConfig:<?php echo json_encode($goodsOtherFileConfig) ?>,
                                                        uploadFiles:goodsOtherFiles,
                                                        fileParams: {
                                                            id:item_id()
                                                        }
                                                    }
                                        } -->
            <!-- /ko -->
        </div>
    </div>
</div>
<div class="flex-grid form-group">
    <label class="col field col-count-1 flex-grid align-start">
        <span class="w-fixed first-line-align">
            备注:
        </span>
        <textarea class="form-control" data-bind="value:remark" rows="1" style="padding: 4px 12px;" placeholder="请输入内容"></textarea>
    </label>
</div>
<!-- 提示：结算单据和其他附件格式支持上传图片，Excel、word、pdf，压缩包格式文件，文件不能超过30M -->