<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">交易主体</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><a href="/corporation/detail/?id=<?php echo $contract->corporation_id ?>&t=1" target="_blank"><?php echo $contract->corporation->name ?></a></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">合作方</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><a href="/partner/detail/?id=<?php echo $contract->partner_id ?>&t=1" target="_blank"><?php echo $contract->partner->name ?></a></p>
                    </div>

                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">项目编号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $contract->project->project_code ?></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">项目类型</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $this->map["project_type"][$contract->project->type] ?></p>
                    </div>

                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">合同编号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><a href="/businessConfirm/detail/?id=<?php echo $contract->contract_id ?>&t=1" target="_blank"><?php echo $contract->contract_code ?></a></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">合同类型</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php  echo $this->map["contract_config"][$contract["type"]][$contract['category']]["name"]; ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">合同已实付金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">￥<?php echo Utility::numberFormatFen2Yuan($actual_paid_amount) ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">付款单已实付金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><span data-bind="text:currency_ico"></span><?php echo Utility::numberFormatFen2Yuan($data['amount_paid']) ?></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">付款单未实付金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><span data-bind="text:currency_ico"></span><span  data-bind="moneyText:amount"></span></p>
                    </div>
                </div>
                <h4 class="section-title">付款计划</h4>
                <div class="form-group">
                    <div class="col-sm-offset-1 col-sm-11">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th style="width:60px;">选择</th>
                                <th style="width:80px; text-align: left;">期数</th>
                                <th style="width:120px; text-align: left;">类别</th>
                                <th style="width:180px; text-align: left;">计划付款金额</th>
                                <th style="width:120px; text-align: left;">已申请金额</th>
                                <th style="width:120px; text-align: left;">未申请金额</th>
                                <th style="text-align: left;">本次付款金额</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:plans">
                            <tr>
                                <td>
                                    <input type="checkbox" data-bind="checked:checked,enable:isCanSelected" />
                                </td>
                                <td data-bind="text:payment.period"> </td>
                                <td><span data-bind="text:expenseName"></span><span data-bind="visible: payment.expense_name">--</span><span data-bind="text:payment.expense_name"></span>
                                </td>
                                <td ><span data-bind="text:currency_ico"></span> <span data-bind="moneyText:payment.amount"></span> </td>
                                <td ><span data-bind="text:currency_ico"></span> <span data-bind="moneyText:payment.amount_paid"></span> </td>
                                <td ><span data-bind="text:currency_ico"></span> <span data-bind="moneyText:amount_balance"></span> </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-addon" data-bind="text:$parent.currency_ico"></span>
                                        <input type="text" class="form-control input-sm"  placeholder="金额" data-bind="money:amount">
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <h4 class="section-title">付款信息</h4>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">付款合同信息<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-8">
                        <select class="form-control input-sm" title="付款合同信息"
                                data-bind="value:sub_contract_id">
                            <?php
                            if(is_array($contract->filesBase))
                            {
                                foreach ($contract->filesBase as $v)
                                {
                                    echo "<option value='" . $v["file_id"] . "'>" . Map::$v['contract_file_categories'][$contract['type']][$v['category']]['name']." - ". $v["code"] . "</option>";
                                }
                            }
                             ?>
                        </select>
                    </div>
                    <!--<label for="type" class="col-sm-2 control-label">付款合同编号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name= "data[sub_contract_code]" placeholder="付款合同编号" data-bind="value:sub_contract_code">
                    </div>-->
                </div>

                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">收款单位<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name= "data[payee]" placeholder="收款单位" data-bind="value:payee">
                    </div>
                    <label for="type" class="col-sm-2 control-label">收款账户名<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name= "data[account_name]" placeholder="收款账户名" data-bind="value:account_name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">开户银行<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name= "data[bank]" placeholder="开户银行" data-bind="value:bank">
                    </div>
                    <label for="type" class="col-sm-2 control-label">银行帐号<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name= "data[account]" placeholder="银行帐号" data-bind="value:account">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">用途<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control selectpicker" title="请选择用途" id="subject" name="data[subject_id]" data-live-search="true" data-bind="optionsCaption: '请选择用途',value:subject_id,valueAllowUnset: true">
                            <?php
                            $subjects =SubjectService::getActiveSubjects();
                            foreach ($subjects as $v)
                            {
                                echo "<option value='" . $v["subject_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                    </div>
                    <label for="type" class="col-sm-2 control-label">付款币种<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择币种" id="currency" name="obj[currency]" data-bind="optionsCaption: '请选择币种',value: currency,valueAllowUnset: true">
                            <?php foreach ($this->map["currency_type"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">付款金额<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-addon" data-bind="text:currency_ico"></span>
                            <input type="text" class="form-control"  placeholder="付款金额" data-bind="money:amount,enable:amountEnable">
                            <span class="input-group-addon" data-bind="moneyChineseText:amount"></span>
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">是否对接保理</label>
                    <div class="col-sm-4">
                        <input type="checkbox"  name="obj[is_factoring]" data-bind="checked:is_factoring" />
                        选中为对接保理
                    </div>
                    <label for="type" class="col-sm-2 control-label">保理金额<span class="text-red fa fa-asterisk" data-bind="visible:is_factoring"></span></label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">￥</span>
                            <input type="text" class="form-control" name= "data[amount_factoring]" placeholder="保理金额" data-bind="money:amount_factoring">
                            <span class="input-group-addon" data-bind="moneyChineseText:amount_factoring"></span>
                        </div>
                    </div>
                </div>
                <?php
                if (!empty($factor)) { ?>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">保理对接编号</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $factor->contract_code ?></p>
                        </div>
                        <label for="type" class="col-sm-2 control-label">资金对接编号</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $factor->contract_code_fund ?></p>
                        </div>

                    </div>
                <?php } ?>
                <div class="form-group">
                    <?php
                    $attachType = $this->map["pay_application_attachment_type"][1];
                    $attachments=AttachmentService::getAttachments(Attachment::C_PAY_APPLICATION,$data["apply_id"], 1);
                    ?>
                    <label class="col-sm-2 control-label">
                        <span class='glyphicon ' data-bind="css:{'glyphicon-ok text-green':fileUploadStatus,' glyphicon-remove text-red':!fileUploadStatus()}"></span>&emsp;
                        <?php echo $attachType["name"] ?></label>
                    <div class="col-sm-10">

                        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/upload.php"; ?>
                        <!-- ko component: {
                             name: "file-upload",
                             params: {
                                         status:fileUploadStatus,
                                         controller:"<?php echo $this->getId() ?>",
                                         fileConfig:<?php echo json_encode($attachType) ?>,
                                         files:<?php echo json_encode($attachments[1]); ?>,
                                         fileParams: {
                                            id:<?php echo empty($data['apply_id'])?0:$data['apply_id'] ?>
                                         }
                                     }
                         } -->
                        <!-- /ko -->
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">付款原因<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="remark" name= "data[remark]" rows="3" placeholder="付款原因" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:tempSave,html:buttonText">保存</button>
                        <button type="button" id="saveButton" class="btn btn-warning" data-bind="click:submit,html:submitButtonText">保存并提交</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='data[apply_id]' data-bind="value:apply_id" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script type="text/javascript" src="/js/pages/pay.js?key=20171130"></script>
<script>
    var currencies=<?php echo json_encode($this->map["currency"]); ?>;
    var expenseNames=<?php echo json_encode($this->map["pay_type"]); ?>;
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode($data) ?>);
        view.formatPaymentPlans(<?php echo json_encode($payments) ?>);

        ko.applyBindings(view);
    });



</script>