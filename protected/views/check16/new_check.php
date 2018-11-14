<?php
$buttons = [];
if ($this->checkButtonStatus["pass"] == 1) {
    $buttons[] = ['text' => '通过', 'attr' => ['data-bind' => 'click:pass', 'id' => 'passButton']];
}
if ($this->checkButtonStatus["back"] == 1) {
    $buttons[] = ['text' => '驳回', 'attr' => ['data-bind' => 'click:checkBack', 'id' => 'checkBackButton', 'class_abbr' => 'action-default-base']];
}

if ($this->checkButtonStatus["reject"] == 1) {
    $buttons[] = ['text' => '拒绝', 'attr' => ['data-bind' => 'click:reject', 'id' => 'rejectButton', 'class_abbr' => 'action-default-base']];
}
$this->loadHeaderWithNewUI([], $buttons, true);
?>

<section class="content">
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p><?php echo $this->map[$data['title_map_name']][$data['type_sub']] ?></p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="item-com">
            <li>
                <label>交易主体：</label>
                <span><?php echo $data['corporation_name'] ?></span>
            </li>
            <li>
                <label>货款合同类型：</label>
                <span><?php echo $this->map['goods_contract_type'][$data["contract_type"]] ?></span>
            </li>
            <li>
                <label>货款合同编号：</label>
                <a href="/businessConfirm/detail/?id=<?php echo $data["contract_id"] ?>&t=1"
                   target="_blank"><?php echo $data["contract_code"] ?></a>
            </li>
            <li>
                <label>项目编号：</label>
                <span><a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1"
                         target="_blank"><?php echo $data["project_code"] ?></a></span>
            </li>
            <li>
                <label>发票合同类型：</label>
                <span><?php echo $this->map['contract_category'][$data["invoice_contract_type"]] ?></span>
            </li>
            <li>
                <label>发票合同编号：</label>
                <span><?php echo $data["invoice_contract_code"] ?></span>
            </li>
            <li>
                <label>发票公司名称：</label>
                <span><?php echo $data["company_name"] ?></span>
            </li>
            <li>
                <label>纳税人识别号：</label>
                <span><?php echo $data["tax_code"] ?></span>
            </li>

            <li>
                <label>税票类型：</label>
                <span><?php echo $this->map['output_invoice_type'][$data["invoice_type"]] ?></span>
            </li>
            <li>
                <label>地址：</label>
                <span><?php echo $data['address'] ?></span>
            </li>
            <li>
                <label>电话：</label>
                <span><?php echo $data[phone] ?></span>
            </li>
            <li>
                <label>开户行：</label>
                <span><?php echo $data['bank_name'] ?></span>
            </li>
            <li>
                <label>银行账号：</label>
                <span><?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $data['bank_account']) ?></span>
            </li>

        </ul>
    </div>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>发票信息</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="form-com form-com-1">
            <li>
                <label>发票明细</label>
                <?php
                if (Utility::isNotEmpty($invoiceDetail)) {
                    ?>
                    <ul class="table-com">
                        <li>
                            <span><?php if ($data['type_sub'] == 1) echo '品名'; else echo '费用名称'; ?></span>
                            <?php if ($data['type_sub'] == 1) { ?>
                                <span>数量</span>
                                <span>单位</span>
                                <span>单价</span>
                            <?php } ?>
                            <span>税率</span>
                            <span>金额<i class="must-logo"></i></span>
                        </li>
                        <?php foreach ($invoiceDetail as $v) { ?>
                            <li>
                                <span><?php echo $v['goods_name'] . $v['invoice_name'] ?></span>
                                <?php if ($data['type_sub'] == 1) { ?>
                                    <span><?php echo $v["quantity"] ?></span>
                                    <span><?php echo $this->map["goods_unit"][$v["unit"]]["name"] ?></span>
                                    <span>￥ <?php echo number_format($v['price'] / 100, 2) ?></span>
                                <?php } ?>
                                <span><?php echo $v['rate'] * 100 ?>%</span>
                                <span>￥ <?php echo number_format($v["amount"] / 100, 2) ?></span>
                            </li>
                        <?php } ?>
                        <?php if (bccomp($data['exchange_rate'], 0) == 1) { ?>
                            <li class="li-add">
                                <p>
                                    <span>合计：</span>
                                    <span>￥ <?php echo number_format($data['total_amount'] / 100, 2) ?></span>
                                </p>
                            </li>
                        <?php } else { ?>
                            <li>
                                <span></span>
                                <?php if ($data['type_sub'] == 1) { ?>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                <?php } ?>
                                <span>合计</span>
                                <span>￥ <?php echo number_format($data['total_amount'] / 100, 2) ?></span>
                            </li>
                            <li>
                                <span>$ <?php echo number_format($data['dollar_amount'] / 100, 2) ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                <?php }
                ?>
            </li>
        </ul>
    </div>

    <div class="content-wrap">

        <div class="content-wrap-title">
            <div>
                <p><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <?php if (Utility::isNotEmpty($plans)) { ?>
            <ul class="form-com form-com-1">
                <li>
                    <label><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划明细</label>
                    <ul class="table-com">
                        <?php
                        $payTypeName = $data['type'] == ConstantMap::INPUT_INVOICE_TYPE ? '付' : '收';
                        $invoicedTypeName = $data['type'] == ConstantMap::INPUT_INVOICE_TYPE ? '收' : '开';
                        ?>
                        <li>
                            <span><?php echo $payTypeName; ?>款日期</span>
                            <span><?php echo $payTypeName; ?>款类别</span>
                            <span>币种</span>
                            <span>计划<?php echo $payTypeName; ?>款金额</span>
                            <span>已<?php echo $invoicedTypeName; ?>票金额</span>
                            <span>未<?php echo $invoicedTypeName; ?>票金额</span>
                            <span><?php echo $invoicedTypeName; ?>票金额</span>
                        </li>
                        <?php foreach ($plans as $v) { ?>
                            <li>
                                <span><?php echo $v['pay_date'] ?></span>
                                <span title="<?php echo $v['expense_desc'] ?>"><?php echo $v['expense_desc'] ?></span>
                                <span><?php echo $this->map['currency'][$v["currency"]]["name"] ?></span>
                                <span><?php echo number_format($v['pay_amount'] / 100, 2) ?></span>
                                <span><?php echo number_format($v['amount_invoice'] / 100, 2) ?></span>
                                <span><?php echo number_format(($v["pay_amount"] - $v["amount_invoice"]) / 100, 2) ?></span>
                                <span><?php echo number_format($v['amount'] / 100, 2) ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
        <?php } ?>
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

        <ul class="item-com item-com-1">
            <li>
                <label>备注:</label>
                <span><?php echo $data['o_remark'] ?></span>
            </li>
        </ul>
    </div>


    <?php if (Utility::isNotEmpty($invoices)) { ?>
        <div class="content-wrap">

            <div class="content-wrap-title">
                <div>
                    <p>历史开票信息</p>
                    <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
                </div>
            </div>
            <ul class="form-com form-com-1">
                <li>
                    <label>开票明细</label>
                    <table class="table table-striped table-bordered table-condensed table-hover">
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
                                            <td><?php echo $v["quantity"] . $this->map['goods_unit'][$v['unit']]['name'] ?></td>
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
                </li>
            </ul>
        </div>

    <?php } ?>
    <div class="content-wrap">

        <div class="content-wrap-title">
            <div>
                <p>开票信息</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="form-com form-com-1">
            <li>
                <label>开票明细</label>
                <?php if (Utility::isNotEmpty($invoiceItems)) { ?>
                    <table class="table table-striped  table-condensed ">
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
                        <?php foreach ($invoiceItems as $key => $invoice) { ?>
                            <tr>
                                <td><?php echo $invoice['detail'][0]['goods_name'] . $invoice['detail'][0]['invoice_name'] ?></td>
                                <?php if ($data['type_sub'] == 1) { ?>
                                    <td><?php echo $invoice['detail'][0]["quantity"] . $this->map['goods_unit'][$invoice['detail'][0]['unit']]['name'] ?></td>
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
                                    <tr>
                                        <td><?php echo $v['goods_name'] . $v['invoice_name'] ?></td>
                                        <?php if ($data['type_sub'] == 1) { ?>
                                            <td><?php echo $v["quantity"] . $this->map['goods_unit'][$v['unit']]['name'] ?></td>
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
                                合计：<span style="color:#FF6E34;">￥ <?php echo number_format($data['invoice_amount'] / 100, 2) ?></span></td>

                        </tr>
                    </table>
                <?php } ?>
            </li>
        </ul>
    </div>

    <form role="form" id="mainForm">
        <div class="content-wrap" style="margin-bottom:20px;">
            <div class="content-wrap-title">
                <div>
                    审核信息
                </div>
            </div>
            <div class="flex-grid form-group">
                <label class="col col-count-1 field">
                    <p class="form-cell-title must-fill">审核意见:</p>
                    <textarea class="form-control" id="remark" name="obj[remark]" rows="3" placeholder="审核意见"
                              data-bind="value:remark"></textarea>
                </label>
            </div>

        </div>
        <input type='hidden' name='obj[check_id]' data-bind="value:check_id"/>
        <input type='hidden' name='obj[project_id]' data-bind="value:project_id"/>
    </form>


</section>

<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data)?>);
        ko.applyBindings(view);
    });

    function ViewModel(option) {
        var defaults = {
            project_id: 0,
            check_id: 0,
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.project_id = ko.observable(o.project_id);
        self.check_id = ko.observable(o.check_id);
        self.remark = ko.observable(o.remark).extend({required: true, maxLength: 512});
        self.actionState = ko.observable(0);

        self.status = ko.observable(o.status);
        self.errors = ko.validation.group(self, {deep: false});
        // self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.pass = function () {
			inc.vueConfirm({
				content: "您确定要通过当前信息的审核，该操作不可逆？", onConfirm: function () {
					self.status(1);
					self.save();
				}
            });
        }

        self.checkBack = function () {
			inc.vueConfirm({
				content: "您确定要驳回当前信息的审核，该操作不可逆？", onConfirm: function () {
					self.status(-1);
					self.save();
				}
            });
        }

        self.save = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
            var formData = $("#mainForm").serialize();
            formData += "&obj[checkStatus]=" + self.status();
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/save/',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if (document.referrer)
                            location.href = document.referrer;
                        else
                            location.href = "<?php echo $this->mainUrl ?>";
                    }
                    else {
						inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
					inc.vueAlert("保存失败！" + data.responseText);
                }
            });
        }
        self.back = function () {
            history.back();
        }
    }

</script>

