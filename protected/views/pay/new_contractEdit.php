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
                        <ul class="item-com">
                            <li>
                                <label for="type">交易主体：</label>
                                <div>
                                    <p>
                                        <a href="/corporation/detail/?id=<?php echo $contract->corporation_id ?>&t=1"
                                           target="_blank"><?php echo $contract->corporation->name ?></a>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <label for="type">合作方：</label>
                                <div>
                                    <p>
                                        <a href="/partner/detail/?id=<?php echo $contract->partner_id ?>&t=1"
                                           target="_blank"><?php echo $contract->partner->name ?></a>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <label for="type">项目编号：</label>
                                <div>
                                    <p>
                                        <?php echo $contract->project->project_code ?>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <label for="type">项目类型：</label>
                                <div>
                                    <p>
                                        <?php echo $this->map["project_type"][$contract->project->type] ?>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <label for="type">合同编号：</label>
                                <div>
                                    <p>
                                        <a href="/businessConfirm/detail/?id=<?php echo $contract->contract_id ?>&t=1"
                                           target="_blank"><?php echo $contract->contract_code ?></a>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <label for="type">合同类型：</label>
                                <div>
                                    <p>
                                        <?php echo $this->map["contract_config"][$contract["type"]][$contract['category']]["name"]; ?>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <label for="type">合同已实付金额：</label>
                                <div>
                                    <p>
                                        ￥<?php echo Utility::numberFormatFen2Yuan($actual_paid_amount) ?>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <label for="type" style="width: unset">付款单已实付金额：</label>
                                <div>
                                    <p>
                                        <span data-bind="text:currency_ico"></span><?php echo Utility::numberFormatFen2Yuan($data['amount_paid']) ?>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <label for="type" style="width: unset">付款单未实付金额：</label>
                                <div>
                                    <p>
                                        <span data-bind="text:currency_ico"></span><span
                                                data-bind="moneyText:amount"></span>
                                    </p>
                                </div>
                            </li>
                        </ul>

                    </div>
                    <div class="z-card-part divide">
                        <h3 class="z-body-header">付款计划</h3>
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width:60px;">选择</th>
                                <th style="width:60px;">期数</th>
                                <th style="width:120px;">类别</th>
                                <th style="width:160px;">计划付款金额</th>
                                <th style="width:160px;">已申请金额</th>
                                <th style="width:160px;">未申请金额</th>
                                <th style="width: 160px">本次付款金额</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:plans">
                            <tr>
                                <td>
                                    <input type="checkbox" style="width: auto"
                                           data-bind="checked:checked,enable:isCanSelected">
                                </td>
                                <td data-bind="text:payment.period"></td>
                                <td><span data-bind="text:expenseName"></span><span
                                            data-bind="visible: payment.expense_name">--</span><span
                                            data-bind="text:payment.expense_name"></span>
                                </td>
                                <td><span data-bind="text:currency_ico"></span> <span
                                            data-bind="moneyText:payment.amount"></span></td>
                                <td><span data-bind="text:currency_ico"></span> <span
                                            data-bind="moneyText:payment.amount_paid"></span></td>
                                <td><span data-bind="text:currency_ico"></span> <span
                                            data-bind="moneyText:amount_balance"></span></td>
                                <td>
                                    <div class="input-group">
                                            <span class="input-group-addon"
                                                  data-bind="text:$parent.currency_ico"></span>
                                        <input type="text" class="form-control" placeholder="金额"
                                               data-bind="money:amount">
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="z-card-part">
                        <h3 class="z-body-header">付款信息</h3>
                        <div class="flex-grid form-group">
                            <label for="type" class="col col-count-3 field">
                                <p class="form-cell-title must-fill">付款合同信息</p>
                                <select class="form-control selectpicker show-menu-arrow" title="付款合同信息"
                                        data-bind="value:sub_contract_id">
                                    <?php
                                    if (is_array($contract->filesBase)) {
                                        foreach ($contract->filesBase as $v) {
                                            echo "<option value='" . $v["file_id"] . "'>" . Map::$v['contract_file_categories'][$contract['type']][$v['category']]['name'] . " - " . $v["code"] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </label>
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">收款单位</p>
                                <input type="text" class="form-control" name="data[payee]" placeholder="收款单位"
                                       data-bind="value:payee">
                            </label>
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">收款账户名</p>
                                <input type="text" class="form-control" name="data[account_name]" placeholder="收款账户名"
                                       data-bind="value:account_name">
                            </label>
                        </div>

                        <div class="flex-grid form-group">
                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">开户银行</p>
                                <input type="text" class="form-control" name="data[bank]" placeholder="开户银行"
                                       data-bind="value:bank">
                            </label>

                            <label class="col col-count-3 field">
                                <p class="form-cell-title must-fill">银行帐号</p>
                                <input type="text" class="form-control" name="data[account]" placeholder="银行帐号"
                                       data-bind="value:account">
                            </label>
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
                        </div>

                        <div class="flex-grid form-group">
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
                                    <span class="input-group-addon ellipsis" style="max-width: 200px;" data-bind="moneyChineseText:amount, attr: {title: titleAmount}"></span>
                                </div>
                            </label>
                        </div>

                        <div class="flex-grid form-group" data-bind="visible:isShowFactoring">
                            <span class="col col-count-3 field">
                                <p class="form-cell-title">是否对接保理</p>
                                <span class="flex-grid">
                                   <label class="o-control o-control--radio inline-flex">
                                    <input name="obj[is_factoring]" type="radio" style="width: auto"
                                           data-bind="checked:is_factoring,checkedValue:true">
                                    <span style="margin-left: 10px;">是</span>
                                    <div class="o-control__indicator"></div>
                                </label>
                                <label class="o-control o-control--radio inline-flex" style=" margin-left: 20px">
                                    <input name="obj[is_factoring]" type="radio" style="width: auto"
                                           data-bind="checked:is_factoring,checkedValue:false">
                                    <span style="margin-left: 10px;">否</span>
                                    <div class="o-control__indicator"></div>
                                </label>
                               </span>
                            </span>
                            <label class="col col-count-3 field">
                                <p class="form-cell-title">保理金额<i class="must-fill"
                                                                     data-bind="visible:is_factoring"></i></p>
                                <div class="input-group">
                                    <span class="input-group-addon">￥</span>
                                    <input type="text" class="form-control" name="data[amount_factoring]"
                                           placeholder="保理金额"
                                           data-bind="money:amount_factoring">
                                    <span class="input-group-addon ellipsis" style="max-width: 200px;"
                                          data-bind="moneyChineseText:amount_factoring, attr: {title: titleAmountFactoring}"></span>
                                </div>
                            </label>

                        </div>
                        <?php
                        if (!empty($factor)) { ?>
                            <div class="flex-grid form-group" data-bind="visible:isShowFactoring">
                                <label class="col col-count-3 field">
                                    <p class="form-cell-title">保理对接编号</p>
                                    <?php echo $factor->contract_code ?>
                                </label>
                                <label class="col col-count-3 field">
                                    <p class="form-cell-title">资金对接编号</p>
                                    <?php echo $factor->contract_code_fund ?>
                                </label>
                            </div>
                        <?php } ?>
                        <div class="flex-grid form-group">
                            <div class="col col-count-1 field ">
                                <?php
                                $attachType = $this->map["pay_application_attachment_type"][1];
                                $attachments = AttachmentService::getAttachments(Attachment::C_PAY_APPLICATION, $data["apply_id"], 1);
                                ?>
                                <p class="form-cell-title w-fixed">
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
                            <label class="col col-count-1 field ">
                                <p class="form-cell-title w-fixed must-fill">付款原因</p>
                                <textarea class="form-control" id="remark" name="data[remark]" rows="3"
                                          placeholder="付款原因"
                                          data-bind="value:remark"></textarea>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script type="text/javascript" src="/js/pages/pay.js?key=20171130"></script>
<script>
    var currencies =<?php echo json_encode($this->map["currency"]); ?>;
    var expenseNames =<?php echo json_encode($this->map["pay_type"]); ?>;
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        view.formatPaymentPlans(<?php echo json_encode($payments) ?>);
        view.titleAmount = ko.computed(function() {
            return inc.moneyToChinese(view.amount() / 100)
        });
        view.titleAmountFactoring = ko.computed(function() {
            return inc.moneyToChinese(view.amount_factoring() / 100)
        });
        ko.applyBindings(view);
    });


</script>