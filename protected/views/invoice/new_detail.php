<?php
$buttons = [];
if ($data['status'] == InvoiceApplication::STATUS_SAVED) {
    $buttons[] = ['text' => '修改', 'attr' => ['onclick' => "edit({$data['apply_id']})"]];
    $buttons[] = ['text' => '提交', 'attr' => ['onclick' => "submit({$data['invoice_id']})"]];
}
$this->loadHeaderWithNewUI([], $buttons, '/invoice/');
?>
<section class="content">
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p><?php echo $this->map['invoice_output_type'][$data['type_sub']] ?></p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>

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
                <label>银行账号：</label>
                <p class="form-control-static"><?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $data['bank_account']) ?></p>
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
        <div class="z-card-body">
            <label>发票明细</label>
            <?php
            if (Utility::isNotEmpty($invoiceDetail)) {
                ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th style="width:120px;text-align:left"><?php if ($data['type_sub'] == 1) echo '品名'; else echo '费用名称'; ?></th>
                        <?php if ($data['type_sub'] == 1) { ?>
                            <th style="width:120px;text-align:left">数量</th>
                            <th style="width:80px;text-align:left">单位</th>
                            <th style="width:120px;text-align:left">单价</th>
                        <?php } ?>
                        <th style="width:80px;text-align:left">税率</th>
                        <th style="width:120px;text-align:left">金额(元)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($invoiceDetail as $v) { ?>
                        <tr>
                            <td style="text-align:left"><?php echo $v['goods_name'] . $v['invoice_name'] ?></td>
                            <?php if ($data['type_sub'] == 1) { ?>
                                <td style="text-align:left"><?php echo $v["quantity"] ?></td>
                                <td style="text-align:left"><?php echo $this->map["goods_unit"][$v["unit"]]["name"] ?></td>
                                <td style="text-align:left">
                                    ￥ <?php echo number_format($v['price'] / 100, 2) ?>
                                </td>
                            <?php } ?>
                            <td style="text-align:left"><?php echo $v['rate'] * 100 ?>%</td>
                            <td style="text-align:left">
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
                            <td style="text-align: center;"></td>
                            <td style="text-align: right;">
                                合计：<span style="color:#FF6E34;">￥ <?php echo number_format($data['total_amount'] / 100, 2) ?><span></td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td rowspan="1"></td>
                            <?php if ($data['type_sub'] == 1) { ?>
                                <td rowspan="1"></td>
                                <td rowspan="1"></td>
                                <td rowspan="1"></td>
                            <?php } ?>
                            <td rowspan="1" style="text-align: right;vertical-align: middle;"></td>
                            <td style="text-align: right;vertical-align: middle;">
                                合计：<span style="color:#FF6E34;">￥ <?php echo number_format($data['total_amount'] / 100, 2) ?></span></td>
                        </tr>
                        <tr>
                            <td rowspan="1"></td>
                            <?php if ($data['type_sub'] == 1) { ?>
                                <td rowspan="1"></td>
                                <td rowspan="1"></td>
                                <td rowspan="1"></td>
                            <?php } ?>
                            <td rowspan="1"></td>
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

        <div class="content-wrap">
            <div class="content-wrap-title">
                <div>
                    <p><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划</p>
                    <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
                </div>

            </div>

            <div class="z-card-body">
                <label><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划明细</label>
                <div class="form-group">
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="width:100px;text-align:left">计划<?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款日期
                            </th>
                            <th style="width:140px;text-align:left"><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款类别
                            </th>
                            <th style="width:80px;text-align:left">币种</th>
                            <th style="width:120px;text-align:left">计划<?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款金额
                            </th>
                            <th style="width:120px;text-align:left">
                                已<?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额
                            </th>
                            <th style="width:120px;text-align:left">
                                未<?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额
                            </th>
                            <th style="width:120px;text-align:left"><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($plans as $v) { ?>
                            <tr>
                                <td style="text-align:left"><?php echo $v['pay_date'] ?></td>
                                <td title="<?php echo $v['expense_desc'] ?>"><?php echo $v['expense_desc'] ?></td>
                                <td style="text-align:left"><?php echo $this->map['currency'][$v["currency"]]["name"] ?></td>
                                <td style="text-align:left">
                                    <?php echo number_format($v['pay_amount'] / 100, 2) ?>
                                </td>
                                <td style="text-align:left"><?php echo number_format($v['amount_invoice'] / 100, 2) ?></td>
                                <td style="text-align:left">
                                    <?php echo number_format(($v["pay_amount"] - $v["amount_invoice"]) / 100, 2) ?>
                                </td>
                                <td style="text-align:left">
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
                        <p style="width: 120px">备注</p>
                        <div class="input-group">
                            <?php echo $data['apply_remark'] ?>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (Utility::isNotEmpty($invoices)) { ?>
        <div class="content-wrap">
            <div class="content-wrap-title">
                <div>
                    <p>历史开票信息</p>
                    <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
                </div>
            </div>

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
                                        <td><?php echo $v["quantity"] . $this->map['goods_unit'][$v["unit"]]['name'] ?></td>
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

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>开票信息</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <div class="z-card-body">
            <label>开票明细</label>

            <?php if (Utility::isNotEmpty($invoiceItems)) { ?>
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
                    <?php foreach ($invoiceItems as $key => $invoice) { ?>
                        <tr>
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
                            合计:<span style="color:#FF6E34;">￥ <?php echo number_format($data['invoice_amount'] / 100, 2) ?></span></td>
                    </tr>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>

    <?php
    $checkLogs = FlowService::getCheckLog($data['invoice_id'], 16);
    if (Utility::isNotEmpty($checkLogs))
        $this->renderPartial("/common/new_checkLogList", array("checkLogs" => $checkLogs));
    ?>


</section>
<script>
    function back() {
        location.href = "/<?php echo $this->getId() ?>/";
    }

    function edit(apply_id) {
        location.href = "/<?php echo $this->getId() ?>/edit?id=" + apply_id;
    }

    function submit(invoice_id) {
        inc.vueConfirm({
            content: "您确定要提交当前发票申请信息吗，该操作不可逆？",
            onConfirm: function () {
                var formData = "id=" + invoice_id;
                $.ajax({
                    type: "POST",
                    url: "/<?php echo $this->getId() ?>/submit",
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            inc.vueMessage({
                                message: json.data
                            });
                            location.reload();
                        }
                        else {
                            inc.vueAlert(json.data);
                        }
                    },
                    error: function (data) {
                        inc.vueAlert("操作失败！" + data.responseText);
                    }
                });
            }
        })
    }

</script>
