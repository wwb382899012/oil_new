<div class="z-card"  data-bind="with:settlement">
    <div class="z-card-header content-title">
        <h3 class="pull-left">非货款类应<?php if($desc_type==ConstantMap::SALE_TYPE) echo '收'; else echo '付'; ?>金额</h3>
        <div class="clearfix">
            <a href="javascript: void 0" class="o-btn o-btn-action primary pull-right" data-bind="click:addOtherExpense">增加</a>
        </div>
    </div>
    <div class="z-card-body">
        <form class="form-group">
            <!-- ko foreach:otherExpenseItems -->
            <div class="settlement-divide">
                <div class="clearfix">
                    <a href="javascript: void 0" class="o-btn o-btn-action pull-right" data-bind="click:$parent.removeOtherExpense">删除</a>
                </div>
                <div class="flex-grid form-group">
                    <div class="col field col-count-3">
                        <p class="form-cell-title must-fill">科目 </p>
                        <select class="form-control selectpicker show-menu-arrow" title="请选择费用科目"
                                data-bind="
                                                    optionsCaption:'请选择费用科目',
                                                    optionsText: 'name',
                                                    optionsValue: 'id',
                                                    selectPickerOptions:subjects,
                                                    selectpicker:subject_id,
                                                    valueAllowUnset: true">
                        </select>
                    </div>
                    <div class="col field col-count-3">
                        <p class="form-cell-title must-fill">币种</p>
                        <select class="form-control selectpicker show-menu-arrow" title="请选择币种"
                                data-bind="
                                                    optionsText: 'name',
                                                    optionsValue: 'id',
                                                    selectPickerOptions:$parent.currencies,
                                                    selectpicker:currency,
                                                    valueAllowUnset: true">
                        </select>
                    </div>
                    <div class="col field col-count-3">
                        <p class="form-cell-title must-fill">金额</p>
                        <div class="input-group">
                            <span class="input-group-addon" data-bind="text:currencyIco"></span>
                            <input type="text" class="form-control" data-bind="money:amount">
                        </div>
                    </div>
                </div>
                <div class="flex-grid form-group" data-bind="visible:cnyIsVisible">
                    <div class="col field col-count-3">
                        <p class="form-cell-title must-fill">汇率</p>
                        <div class="input-group">
                            <input type="text" class="form-control" data-bind="value:exchange_rate">
                        </div>
                    </div>
                    <div class="col field col-count-3">
                        <p class="form-cell-title must-fill">人民币金额</p>
                        <div class="input-group">
                            <span class="input-group-addon">￥</span>
                            <input type="text" class="form-control" data-bind="money:amount_cny" readonly="readonly">
                        </div>
                    </div>
                </div>
                <div class="flex-grid form-group">
                    <div class="col field col-count-1 flex-grid align-start">
                        <span class="w-fixed first-line-align">
                            单据:
                        </span>
                        <div class="form-group-custom-upload">
                            <!-- ko component: {
                                         name: "file-upload",
                                         params: {
                                                    status:otherFileStatus,
                                                    controller:"<?php echo $this->getId() ?>",
                                                    fileConfig:<?php echo json_encode($otherFileConfig) ?>,
                                                    uploadFiles:otherFiles,
                                                    fileParams: {
                                                        id:detail_id()
                                                    }
                                                 }
                                     } -->
                            <!-- /ko -->
                        </div>
                    </div>
                </div>
                <div class="flex-grid form-group">
                    <label class="col field col-count-1 flex-grid align-start">
                        <span class="w-fixed  first-line-align">
                            备注:
                        </span>
                        <textarea class="form-control textarea" data-bind="value:remark" rows="1" placeholder="请输入内容"></textarea>
                    </label>
                </div>
                <!-- 提示：结算单据格式支持上传图片，Excel、word、pdf，压缩包格式文件，文件不能超过30M -->
            </div>
            <!-- /ko -->
        </form>
        <div class="total-amount">
            合计人民币总额：￥<span data-bind="moneyText:other_amount"></span>
        </div>
    </div>
</div>