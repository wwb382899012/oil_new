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
                        <select class="form-control selectpicker" title="请选择交易主体" id="corporation_id" name="data[corporation_id]" data-live-search="true" data-bind="optionsCaption: '请选择交易主体',value:corporation_id,valueAllowUnset: true">
                            <?php
                            $cors = UserService::getUserSelectedCorporations();
                            foreach ($cors as $v)
                            {
                                echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                    </div>

                    <label for="type" class="col-sm-2 control-label"  data-bind="visible:isShowProject">项目信息</label>
                    <div class="col-sm-4" data-bind="visible:isShowProject">
                        <select class="form-control input-sm" title="请选择项目信息"
                                data-bind="
                                optionsCaption:'请选择项目信息',
                                    optionsText: 'project_code',
                                    optionsValue: 'project_id',
                                    selectPickerOptions:projects,
                                    valueAllowUnset: true,
                                    selectpicker:project_id">

                        </select>
                    </div>
                </div>
                <h4 class="section-title">付款信息</h4>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">付款合同类型</label>
                    <div class="col-sm-4">
                        <select class="form-control input-sm" title="合同类型"
                                data-bind="value:sub_contract_type">
                            <option value='-1'>请选择付款合同类型</option>
                            <?php
                            foreach (Map::$v["contract_category"] as $k=>$v)
                            {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                    <label for="type" class="col-sm-2 control-label">付款合同编号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name= "data[sub_contract_code]" placeholder="付款合同编号" data-bind="value:sub_contract_code">
                    </div>
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
                        <input type="text" class="form-control" name= "data[account]" placeholder="银行帐号" data-bind="bankInput:account">
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
                <div class="form-group" data-bind="visible:isShowFactoring">
                    <label for="type" class="col-sm-2 control-label">是否对接保理</label>
                    <div class="col-sm-4">
                        <input type="checkbox"  name="obj[is_factoring]" data-bind="checked:is_factoring" />
                        选中为对接保理
                    </div>
                    <label for="type" class="col-sm-2 control-label">保理金额<span class="text-red fa fa-asterisk" data-bind="visible:is_factoring"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name= "data[amount_factoring]" placeholder="项目编号" data-bind="money:amount_factoring">
                    </div>
                </div>
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
        view.initProjects(<?php echo empty($projects)?"[]":json_encode($projects) ?>);
        ko.applyBindings(view);
    });


</script>