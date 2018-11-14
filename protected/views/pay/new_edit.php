<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<link rel="stylesheet" href="/css/style/addnewproject.css">
<?php
$menus=$this->getIndexMenuWithNewUI();

$menus[] = ['text' => $this->pageTitle];
$buttons = [];
$buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:tempSave, html:buttonText', 'id' => 'saveButton', 'class_abbr' => 'action-default-base']];
$buttons[] = ['text' => '保存并提交', 'attr' => ['data-bind' => 'click:submit, html:submitButtonText', 'id' => 'submitButton']];
$this->loadHeaderWithNewUI($menus, $buttons, '/pay/');
?>
<input type='hidden' name='data[apply_id]' data-bind="value:apply_id"/>

<section class="el-container is-vertical">
    <div class="card-wrapper">
        <form role="form" id="mainForm">
            <div class="z-card">
                <div class="z-card-body">
                    <div class="z-card-part divide">
                        <h3 class="z-body-header">请在下面填写</h3>
                        <div class="flex-grid form-group">
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">交易主体</p>
                                <select class="form-control selectpicker show-menu-arrow" title="请选择交易主体"
                                        id="corporation_id" name="data[corporation_id]" data-live-search="true"
                                        data-bind="optionsCaption: '请选择交易主体',value:corporation_id,valueAllowUnset: true">
                                    <?php
                                    $cors = UserService::getUserSelectedCorporations();
                                    foreach ($cors as $v) {
                                        echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                                    } ?>
                                </select>
                            </label>
                            <label class="col col-count-3 field" data-bind="visible:isShowProject">
                                <p class="form-cell-title ">项目信息</p>
                                <select class="form-control selectpicker show-menu-arrow" title="请选择项目信息"
                                        data-bind="
                                optionsCaption:'请选择项目信息',
                                    optionsText: 'project_code',
                                    optionsValue: 'project_id',
                                    selectPickerOptions:projects,
                                    valueAllowUnset: true,
                                    selectpicker:project_id">

                                </select>
                            </label>
                        </div>
                    </div>


                    <div class="z-card-part">
                        <h3 class="z-body-header">付款信息</h3>

                        <div class="flex-grid form-group">
                            <label class="col col-count-2 field">
                                <p class="form-cell-title">付款合同类型</p>
                                <select data-live-search="true" class="form-control selectpicker show-menu-arrow"
                                        title="合同类型"
                                        data-bind="value:sub_contract_type">
                                    <option value='-1'>请选择付款合同类型</option>
                                    <?php
                                    foreach (Map::$v["contract_category"] as $k => $v) {
                                        echo "<option value='" . $k . "'>" . $v . "</option>";
                                    } ?>
                                </select>
                            </label>
                            <label class="col col-count-2 field">
                                <p class="form-cell-title">付款合同编号</p>
                                <input type="text" class="form-control" name="data[sub_contract_code]"
                                       placeholder="付款合同编号"
                                       data-bind="value:sub_contract_code">
                            </label>
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">收款单位</p>
                                <input type="text" class="form-control" name="data[payee]" placeholder="收款单位"
                                       data-bind="value:payee">
                            </label>
                        </div>

                        <div class="flex-grid form-group">
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">收款账户名</p>
                                <input type="text" class="form-control" name="data[account_name]" placeholder="收款账户名"
                                       data-bind="value:account_name">
                            </label>
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">开户银行</p>
                                <input type="text" class="form-control" name="data[bank]" placeholder="开户银行"
                                       data-bind="value:bank">
                            </label>
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">银行帐号</p>
                                <input type="text" class="form-control" name="data[account]" placeholder="银行帐号"
                                       data-bind="bankInput:account">
                            </label>
                        </div>

                        <div class="flex-grid form-group">
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">用途</p>
                                <select class="form-control selectpicker show-menu-arrow" title="请选择用途" id="subject"
                                        name="data[subject_id]"
                                        data-live-search="true"
                                        data-bind="optionsCaption: '请选择用途',value:subject_id,valueAllowUnset: true">
                                    <?php
                                    $subjects = SubjectService::getActiveSubjects();

                                    foreach ($subjects as $v) {
                                        echo "<option value='" . $v["subject_id"] . "'>" . $v["name"] . "</option>";
                                    } ?>
                                </select>
                            </label>
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">付款币种</p>
                                <select class="form-control selectpicker show-menu-arrow" title="请选择币种" id="currency"
                                        name="obj[currency]"
                                        data-bind="optionsCaption: '请选择币种',value: currency,valueAllowUnset: true">
                                    <?php foreach ($this->map["currency_type"] as $k => $v) {
                                        echo "<option value='" . $k . "'>" . $v . "</option>";
                                    } ?>
                                </select>
                            </label>
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">付款金额</p>
                                <div class="input-group">
                                    <span class="input-group-addon" data-bind="text:currency_ico"></span>
                                    <input type="text" class="form-control" placeholder="付款金额"
                                           data-bind="money:amount,enable:amountEnable">
                                    <span class="input-group-addon ellipsis" style="max-width: 200px;"
                                          data-bind="moneyChineseText:amount, attr: {title: titleAmount}"></span>
                                </div>
                            </label>
                        </div>

                        <div class="flex-grid form-group" data-bind="visible:isShowFactoring">
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">是否对接保理</p>
                                <input type="checkbox" name="obj[is_factoring]" data-bind="checked:is_factoring"/>
                                选中为对接保理
                            </label>
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">保理金额<span class="must-fill"
                                                                               data-bind="visible:is_factoring"></span>
                                </p>
                                <input type="text" class="form-control" name="data[amount_factoring]" placeholder="保理金额"
                                       data-bind="money:amount_factoring">
                            </label>
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">付款金额</p>
                                <div class="input-group">
                                    <span class="input-group-addon" data-bind="text:currency_ico"></span>
                                    <input type="text" class="form-control" placeholder="付款金额"
                                           data-bind="money:amount,enable:amountEnable">
                                    <span class="input-group-addon" data-bind="moneyChineseText:amount"></span>
                                </div>
                            </label>
                        </div>

                        <div class="flex-grid form-group">
                            <div class="col col-count-1 field">
                                <?php
                                $attachType = $this->map["pay_application_attachment_type"][1];
                                $attachments = AttachmentService::getAttachments(Attachment::C_PAY_APPLICATION, $data["apply_id"], 1);
                                ?>
                                <p class="form-cell-title w-fixed ">
                                    <?php echo $attachType["name"] ?>
                                </p>
                                <div class="form-group-custom-upload">

                                    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_uploadNew.php"; ?>
                                    <!-- ko component: {
                             name: "file-upload",
                             params: {
                                         status:fileUploadStatus,
                                         controller:"<?php echo $this->getId() ?>",
                                         fileConfig:<?php echo json_encode($attachType) ?>,
                                         files:<?php echo json_encode($attachments[1]); ?>,
                                         fileParams: {
                                            id:<?php echo empty($data['apply_id']) ? 0 : $data['apply_id'] ?>
                                         }
                                         }
                         } -->
                                    <!-- /ko -->
                                </div>
                            </div>
                        </div>
                        <div class="flex-grid">
                            <label class="col col-count-1 field">
                                <p class="form-cell-title w-fixed must-fill">付款原因</p>
                                <textarea class="form-control" id="remark" name="data[remark]" rows="3"
                                          placeholder="付款原因"
                                          data-bind="value:remark"></textarea>
                            </label>
                        </div>


                    </div>
        </form>
    </div>
</section>

<div class="modal fade draggable-modal" id="business-check-user-modal" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="modal">
    <div class="modal-dialog modal-normal" role="document">
        <div class="modal-content">
            <div class="modal-header--flex">
                <h4 class="modal-title">业务主管审核人选择</h4>
                <a type="button" class="close" data-bind="click:hideModal" aria-label="Close"><span
                            aria-hidden="true">×</span></a>
            </div>
            <div class="modal-body">
                <form class="search-form">
                    <div class="flex-grid form-group">
                        <div class="o-row">
                            <div class="o-col-sm-12">
                                <div class="flex-grid children-gap--fixed first-line-align" style="flex-wrap: wrap;">


                                    <!-- ko foreach: businessDirectors -->

                                    <label class="o-control o-control--radio inline-flex" style="margin-left: 0 !important;margin-right: 20px;width: 150px">
                                        <input name="obj[check_user_validate]" type="radio" style="width: auto"
                                               data-bind="checkedValue:user_id,checked:$parent.check_user,value:user_id">
                                        <span style="margin-left: 10px;" data-bind="text:name"></span>
                                        <div class="o-control__indicator"></div>
                                    </label>

                                    <!-- /ko -->

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="o-row">
                        <input type="hidden" class="form-control" name="obj[check_user_validate]" placeholder="审核人" data-bind="value:check_user_validate" title="不得为空" >
                    </div>
                </form>
            </div>
            <div class="modal-footer flex-center">
                <a href="javascript: void 0" role="button" class="o-btn o-btn-primary" data-bind="click:submitCheck">确定</a>
                <a href="javascript: void 0" role="button" class="o-btn o-btn-action w-base" data-bind="click:hideModal">关闭</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="/js/pages/pay.js?key=2018080301"></script>
<script>
    var currencies =<?php echo json_encode($this->map["currency"]); ?>;
    var expenseNames =<?php echo json_encode($this->map["pay_type"]); ?>;
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        view.initProjects(<?php echo empty($projects) ? "[]" : json_encode($projects) ?>);
        view.initBusinessDirectors(<?php echo empty($business_directors) ? "[]" : json_encode($business_directors) ?>);
        view.titleAmount = ko.computed(function () {
            return inc.moneyToChinese(view.amount() / 100)
        });
        ko.applyBindings(view);
    });


</script>