<?php
$buttons = [];
$buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText', 'id' => 'saveButton','class_abbr'=>'action-default-base']];
$buttons[] = ['text' => '提交', 'attr' => ['data-bind' => 'click:submit, html:submitBtnText', 'id' => 'submitButton']];
$this->loadHeaderWithNewUI([], $buttons, '/invoice/');
?>
<input type='hidden' name='obj[invoice_id]' data-bind="value:invoice_id"/>
<input type='hidden' name='obj[apply_id]' data-bind="value:apply_id"/>
<input type='hidden' name='obj[type]' data-bind="value:type"/>
<input type='hidden' name='obj[type_sub]' data-bind="value:type_sub"/>
<section class="content">
    <form role="form" id="mainForm">
        <div class="z-card">
            <h3 class="z-card-header">
                <?php echo $this->map['invoice_output_type'][$data['type_sub']] ?>
            </h3>
            <div class="z-card-body">

                <ul class="item-com">
                    <li>
                        <label>交易主体：</label>
                        <p class="form-control-static"><?php echo $data['corporation_name'] ?></p>
                    </li>
                    <li>
                        <label>货款合同类型：</label>
                        <p class="form-control-static"><?php echo $this->map['goods_contract_type'][$data["contract_type"]] ?></p>
                    </li>
                    <li>
                        <label>货款合同编号：</label>
                        <p>
                            <a href="/contract/detail/?id=<?php echo $data["contract_id"] ?>&t=1"
                               target="_blank"><?php echo $data["contract_code"] ?></a>
                        </p>
                    </li>
                    <li>
                        <label>项目编号：</label>
                        <p>
                            <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1"
                               target="_blank"><?php echo $data["project_code"] ?></a>
                        </p>
                    </li>
                    <li>
                        <label>发票合同类型：</label>
                        <p class="form-control-static"><?php echo $this->map['contract_category'][$data["invoice_contract_type"]] ?></p>
                    </li>
                    <li>
                        <label>发票合同编号：</label>
                        <p class="form-control-static"><?php echo $data["invoice_contract_code"] ?></p>
                    </li>
                    <li>
                        <label>发票公司名称：</label>
                        <p><?php echo $data["company_name"] ?></p>
                    </li>
                    <li>
                        <label>纳税人识别号：</label>
                        <p><?php echo $data["tax_code"] ?></p>
                    </li>
                    <li>
                        <label>税票类型：</label>
                        <p><?php echo $this->map['output_invoice_type'][$data["invoice_type"]] ?></p>
                    </li>
                    <li>
                        <label>地址：</label>
                        <p><?php echo $data['address'] ?></p>
                    </li>
                    <li>
                        <label>电话：</label>
                        <p><?php echo $data[phone] ?></p>
                    </li>
                    <li>
                        <label>开户行：</label>
                        <p><?php echo $data['bank_name'] ?></p>
                    </li>
                    <li>
                        <label>银行账户：</label>
                        <p class="form-control-static"><?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $data['bank_account']) ?></p>
                    </li>
                </ul>
            </div>
        </div>

        <div class="z-card">
            <h3 class="z-card-header">
                发票信息
            </h3>
            <div class="z-card-body">
                <label>发票明细</label>
                <?php
                if (Utility::isNotEmpty($invoiceDetail)) {
                    ?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="width:120px;text-align:center"><?php if ($data['type_sub'] == 1) echo '品名'; else echo '费用名称'; ?></th>
                            <?php if ($data['type_sub'] == 1) { ?>
                                <th style="width:120px;text-align:center">数量</th>
                                <th style="width:80px;text-align:center">单位</th>
                                <th style="width:120px;text-align:center">单价</th>
                            <?php } ?>
                            <th style="width:80px;text-align:center">税率</th>
                            <th style="width:120px;text-align:center">金额(元)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($invoiceDetail as $v) { ?>
                            <tr>
                                <td><?php echo $v['goods_name'] . $v['invoice_name'] ?></td>
                                <?php if ($data['type_sub'] == 1) { ?>
                                    <td><?php echo $v["quantity"] ?></td>
                                    <td><?php echo $this->map["goods_unit"][$v["unit"]]["name"] ?></td>
                                    <td>
                                        ￥ <?php echo number_format($v['price'] / 100, 2) ?>
                                    </td>
                                <?php } ?>
                                <td><?php echo $v['rate'] * 100 ?>%</td>
                                <td>
                                    ￥ <?php echo number_format($v["amount"] / 100, 2) ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <?php if (bccomp($data['exchange_rate'], 0) == 0) { ?>
                            <tr>
                                <td></td>
                                <?php if ($data['type_sub'] == 1) { ?>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                <?php } ?>
                                <td style="text-align: right;"></td>
                                <td style="text-align: right;">
                                    合计：￥ <?php echo number_format($data['total_amount'] / 100, 2) ?></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td ></td>
                                <?php if ($data['type_sub'] == 1) { ?>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                <?php } ?>
                                <td  style="text-align: right;vertical-align: middle;"></td>
                                <td style="text-align: right;vertical-align: middle;">
                                    合计：<span style="color:#FF6E34;">￥ <?php echo number_format($data['total_amount'] / 100, 2) ?></span></td>
                            </tr>
                            <tr>
                                <td ></td>
                                <?php if ($data['type_sub'] == 1) { ?>
                                    <td ></td>
                                    <td ></td>
                                    <td ></td>
                                <?php } ?>
                                <td  style="text-align: right;vertical-align: middle;"></td>
                                <td style="text-align: right;vertical-align: middle;">
                                    合计：<span style="color:#FF6E34;">$ <?php echo number_format($data['dollar_amount'] / 100, 2) ?><span></td>
                            </tr>
                        <?php } ?>
                        </tfoot>
                    </table>
                <?php }
                ?>
            </div>
        </div>
        <?php if (Utility::isNotEmpty($plans)) { ?>
            <div class="z-card">
                <h3 class="z-card-header">
                    <?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划
                </h3>
                <div class="z-card-body">
                    <label><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划明细</label>
                    <div class="form-group">
                        <table class="table">
                            <thead>
                            <tr>
                                <th style="width:100px;">计划<?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款日期
                                </th>
                                <th style="width:140px;"><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款类别
                                </th>
                                <th style="width:80px;">币种</th>
                                <th style="width:120px;">
                                    计划<?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款金额
                                </th>
                                <th style="width:120px;">
                                    已<?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额
                                </th>
                                <th style="width:120px;">
                                    未<?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额
                                </th>
                                <th style="width:120px;"><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($plans as $v) { ?>
                                <tr>
                                    <td><?php echo $v['pay_date'] ?></td>
                                    <td title="<?php echo $v['expense_desc'] ?>"><?php echo $v['expense_desc'] ?></td>
                                    <td><?php echo $this->map['currency'][$v["currency"]]["name"] ?></td>
                                    <td>
                                        <?php echo number_format($v['pay_amount'] / 100, 2) ?>
                                    </td>
                                    <td><?php echo number_format($v['amount_invoice'] / 100, 2) ?></td>
                                    <td>
                                        <?php echo number_format(($v["pay_amount"] - $v["amount_invoice"]) / 100, 2) ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($v['amount'] / 100, 2) ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (Utility::isNotEmpty($attachments)) { ?>

                        <ul class="item-com upload-list item-com-1">
                            <li>
                                <label><p>附件：</p></label>
                                <div>
                                    <?php
                                    foreach ($attachments as $key => $value) {
                                        echo '<p class="file-item">';
                                        echo "<a href='/inputInvoice/getFile/?id=" . $value["id"] . "&fileName=" . $value['name'] . "'  target='_blank'>" . $value['name'] . "</a>";
                                        echo '</p>';
                                    }
                                    ?>
                                </div>
                            </li>
                        </ul>
                    <?php } ?>

                    <div class="flex-grid form-group">
                        <label class="col col-count-1 field flex-grid">
                            <p style="width: 120px">备注：</p>
                            <div class="input-group">
                                <?php echo $data['apply_remark'] ?>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (Utility::isNotEmpty($invoices)) { ?>
            <div class="z-card">
                <h3 class="z-card-header">
                    历史开票信息
                </h3>
                <div class="z-card-body">
                    <label>开票明细</label>
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="width:200px;"><?php if ($data['type_sub'] == 1) echo '品名'; else echo '费用名称'; ?></th>
                            <?php if ($data['type_sub'] == 1) { ?>
                                <th style="width:200px;">数量</th>
                            <?php } ?>
                            <th style="width:200px;">实际开票金额</th>
                            <th style="width:200px;">开票日期</th>
                            <th style="width:120px;">开票数量</th>
                            <th>备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($invoices as $key => $invoice) { ?>
                            <tr <?php if ($key == $data['invoice_id']) echo 'class="bg-yellow color-palette"' ?>>
                                <td><?php echo $invoice['detail'][0]['goods_name'] . $invoice['detail'][0]['invoice_name'] ?></td>
                                <?php if ($data['type_sub'] == 1) { ?>
                                    <td><?php echo $invoice['detail'][0]["quantity"] . $this->map['goods_unit'][$invoice['detail'][0]["unit"]]['name'] ?></td>
                                <?php } ?>
                                <td>
                                    ￥ <?php echo number_format($invoice['detail'][0]["amount"] / 100, 2) ?>
                                </td>
                                <td><?php echo $invoice['detail'][0]['invoice_date'] ?></td>
                                <td style="vertical-align: middle;border-left: 1px solid #dcdcdc;"
                                    rowspan="<?php echo count($invoice['detail'])+1 ?>"><?php echo $invoice['invoice_num'] ?>
                                    &nbsp;张
                                </td>
                                <td style="vertical-align: middle;border-left: 1px solid #dcdcdc;"
                                    rowspan="<?php echo count($invoice['detail'])+1 ?>"><?php echo $invoice['remark'] ?></td>
                            </tr>
                            <?php
                            if (count($invoice['detail']) > 1) {
                                unset($invoice['detail'][0]);
                                foreach ($invoice['detail'] as $v) {
                                    ?>
                                    <tr <?php if ($key == $data['invoice_id']) echo 'class="bg-yellow color-palette"' ?>>
                                        <td><?php echo $v['goods_name'] . $v['invoice_name'] ?></td>
                                        <?php if ($data['type_sub'] == 1) { ?>
                                            <td><?php echo $v["quantity"]. $this->map['goods_unit'][$v["unit"]]['name'] ?></td>
                                        <?php } ?>
                                        <td>
                                            ￥ <?php echo number_format($v["amount"] / 100, 2) ?>
                                        </td>
                                        <td><?php echo $v['invoice_date'] ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td></td>
                            <?php if ($data['type_sub'] == 1) { ?>
                                <td></td>
                            <?php } ?>
                            <td></td>
                            <td>
                                合计：<span style="color:#FF6E34;">￥ <?php echo number_format($data['total_invoice_amount'] / 100, 2) ?></span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>

        <div class="z-card">
            <h3 class="z-card-header">
                开票信息
            </h3>
            <div class="z-card-body">
                <ul class="item-com">
                    <li>
                        <label>剩余开票金额：</label>
                        <p class="form-control-static">￥ <span data-bind="moneyText:blanace_amount"></span></p>
                    </li>
                </ul>
                <label class="must-fill">开票明细</label>
                <div class="form-group">
                    <!-- ko component: {
                                         name: "invoice",
                                         params: {
                                                     contract_id: contract_id,
                                                     project_id: project_id,
                                                     apply_id: apply_id,
                                                     type_sub: type_sub,
                                                     units: units,
                                                     allGoods: allGoods,
                                                     goodsItems: goodsItems,
                                                     items: invoiceItems
                                                     }
                                     } -->
                    <!-- /ko -->
                    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_invoiceDetail.php"; ?>
                </div>


                <div class="flex-grid form-group">
                    <label class="col col-count-3 field">
                        <p class="form-cell-title must-fill">开票数量</p>
                        <div class="input-group">
                            <input type="text" class="form-control" id="invoice_num" name="obj[invoice_num]"
                                   placeholder="开票数量" data-bind="value:invoice_num">
                            <span class="input-group-addon">张</span>
                        </div>
                    </label>
                    <label class="col col-count-3 field">
                        <p class="form-cell-title">已开票数量</p>
                        <div class="input-group">
                            <input disabled type="text" class="form-control"  data-bind="value:num">
                            <span class="input-group-addon">&nbsp;张</span>
                        </div>
                    </label>


                </div>
                <div class="flex-grid form-group">
                    <label class="col col-count-1 field">
                        <p class="form-cell-title flex-grid">备注</p>
                        <textarea class="form-control" id="remark" name="obj[remark]" rows="3" placeholder="备注"
                                  data-bind="value:remark"></textarea>
                    </label>
                </div>
            </div>
        </div>
    </form>
</section>
<script>
    var view;
    var upStatus = 0;
    var count = 0;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        view.formatUnits(<?php echo json_encode($this->map["goods_unit"]); ?>);
        view.formatAllGoods(<?php echo json_encode($allGoods) ?>);
        view.formatInvoiceItems(<?php echo json_encode($invoiceItems) ?>);
        // view.formatGoodsItems(<?php echo json_encode($goodsItems) ?>);
        ko.applyBindings(view);
    });

    function ViewModel(option) {
        var defaults = {
            apply_id: 0,
            corporation_id: "",
            contract_id: "",
            project_id: "",
            type: "",
            type_sub: "",
            invoice_date: "",
            invoice_num: "",
            num: 0,
            remark: "",
            amount_paid: 0.0,
            invoice_id: 0
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.invoice_id = ko.observable(o.invoice_id);
        self.apply_id = ko.observable(o.apply_id);
        self.corporation_id = ko.observable(o.corporation_id);
        self.contract_id = ko.observable(o.contract_id);
        self.project_id = ko.observable(o.project_id);
        self.type = ko.observable(o.type);
        self.type_sub = ko.observable(o.type_sub);
        self.invoice_num = ko.observable(o.invoice_num).extend({positiveNumber: {params: true, message: '请填写发票数量'}});
        self.num = ko.observable(o.num);
        self.remark = ko.observable(o.remark);
        self.total_amount = ko.observable(o.total_amount);
        self.amount_paid = ko.observable(o.amount_paid);
        self.blanace_amount = ko.computed(function (v) {
            return (parseFloat(self.total_amount()) - parseFloat(self.amount_paid())).toFixed(0);
        });

        self.units = ko.observableArray();
        self.formatUnits = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i in data) {
                self.units.push(data[i]);
            }
        }

        self.allGoods = ko.observableArray();
        self.formatAllGoods = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i in data) {
                self.allGoods().push(data[i]);
            }
        };

        /*self.goodsItems = ko.observableArray();
        self.formatGoodsItems = function (data) {
            if (data == null || data == undefined)
                return;
            console.log(data);
            self.goodsItems().push(data);
        };*/
        self.goodsItems = <?php echo json_encode($goodsItems) ?>;

        self.invoiceItems = ko.observableArray();
        self.formatInvoiceItems = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i in data) {
                // data[i]['goodsItems'] = self.goodsItems();
                data[i]['goodsItems'] = self.goodsItems;
                var obj = new Invoice(data[i]);
                self.invoiceItems().push(obj);
            }
        };


        self.isSave = ko.observable(1);
        self.actionState = ko.observable(0);
        self.saveBtnText = ko.observable("保存");
        self.submitBtnText = ko.observable("提交");
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        //保存
        self.save = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
            self.isSave(1);

            self.pass();
        }

        self.submit = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            self.isSave(0);
            inc.vueConfirm({
                content: "您确定要提交当前开票信息，该操作不可逆？",
                onConfirm: function () {
                    self.pass();
                }
            })

        }

        self.pass = function () {

            // console.log(self.doneItems());
            var filter = ["saveBtnText", "submitBtnText", "isValid", "allGoods", "formatInvoiceItems"];

            var invoice_amount = 0.0;
            if (self.invoiceItems().length > 0) {
                for (var item in self.invoiceItems()) {
                    if (self.invoiceItems()[item].amount() == 0) {
                        inc.vueAlert({content: "开票明细中第" + (parseInt(item) + 1) + "实际开票金额为空！"});
                        return;
                    }
                    invoice_amount += parseFloat(self.invoiceItems()[item].amount());

                }

                if (parseFloat(self.blanace_amount()) < parseFloat(invoice_amount)) {
                    inc.vueAlert({content: "开票总金额不得大于剩余发票总金额！"});
                    return;
                }

            } else {
                inc.vueAlert({content: "请添加开票明细！"});
                return;
            }


            var formData = {"data": inc.getPostData(self, filter)};

            if (self.actionState() == 1)
                return;
            if (self.isSave() == 1)
                self.saveBtnText("保存中" + inc.loadingIco);
            else
                self.submitBtnText("提交中" + inc.loadingIco);

            // console.log(formData);
            // return;

            self.actionState(1);
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    if (json.state == 0) {
                        inc.vueMessage({
                            message: "操作成功"
                        });
                        location.href = "/<?php echo $this->getId() ?>/detail/?id=" + json.data;
                    } else {
                        inc.vueAlert(json.data);
                    }
                    self.saveBtnText("保存");
                    self.submitBtnText("提交");
                },
                error: function (data) {
                    self.saveBtnText("保存");
                    self.submitBtnText("提交");
                    self.actionState(0);
                    inc.vueAlert("操作失败！" + data.responseText);
                }
            });
        }

        self.back = function () {
            history.back();
        }
    }
</script>