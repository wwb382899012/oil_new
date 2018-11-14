<?php
$buttons = [];
if ($data['status'] == InvoiceApplication::STATUS_SAVED) {
    $buttons[] = ['text' => '修改', 'attr' => ['onclick' => "edit({$data['apply_id']})", 'class_abbr' => 'action-default-base']];
    $buttons[] = ['text' => '提交', 'attr' => ['onclick' => "submit({$data['apply_id']})"]];
}
$menus = [['text' => '发票管理'], ['text' => $this->moduleName, 'link' => $this->moduleUrl], ['text' => $this->pageTitle]];
$this->loadHeaderWithNewUI($menus, $buttons, $this->moduleUrl);
?>
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
            <a href="/contract/detail/?id=<?php echo $data["contract_id"] ?>&t=1"
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

        <?php if ($data['type'] == ConstantMap::OUTPUT_INVOICE_TYPE) { ?>
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
        <?php } ?>

    </ul>
</div>
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>发票信息</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <ul class="item-com">

        <?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) { ?>
            <li>
                <label>税票类型：</label>
                <span><?php echo $this->map['vat_invoice_type'][$data["invoice_type"]] ?></span>
            </li>
            <li>
                <label>汇率：</label>
                <span><?php echo $data["exchange_rate"] ?></span>
            </li>
            <li>
                <label>发票日期：</label>
                <span><?php echo $data["invoice_date"] ?></span>
            </li>
            <li>
                <label>发票数量：</label>
                <span><?php echo $data["num"] ?> 张</span>
            </li>
        <?php } ?>
    </ul>
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
                        <li class="li-add">
                            <p>
                                <span>合计：</span>
                                <span>￥ <?php echo number_format($data['total_amount'] / 100, 2) ?></span>
                            </p>
                        </li>
                        <li>
                            <p>
                                <span>合计：</span>
                                <span>$ <?php echo number_format($data['dollar_amount'] / 100, 2) ?></span>
                            </p>
                        </li>
                    <?php } ?>
                </ul>
            <?php }
            ?>
        </li>
    </ul>
</div>

<?php if (Utility::isNotEmpty($plans)) { ?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="form-com form-com-1">
            <li>
                <label><?php if ($data['type'] == ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划明细</label>
                <ul class="table-com">
                    <?php
                    $payTypeName = $data['type'] == ConstantMap::INPUT_INVOICE_TYPE ? '付' : '收';
                    $invoicedTypeName = $data['type'] == ConstantMap::INPUT_INVOICE_TYPE ? '收' : '开';
                    ?>
                    <li>
                        <span>预计<?php echo $payTypeName; ?>款日期</span>
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
    </div>
<?php } ?>

<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>附件信息</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
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

    <ul class="item-com">
        <li style="width: 100%">
            <label>备注:</label>
            <span><?php echo $data['remark'] ?></span>
        </li>
    </ul>
</div>
<?php
$checkLogs = FlowService::getCheckLog($data['apply_id'], 15);
if (Utility::isNotEmpty($checkLogs))
    $this->renderPartial("/common/new_checkLogList", array("checkLogs" => $checkLogs));
?>


<script>
    function back() {
        location.href = "/<?php echo $this->getId() ?>/";
    }

    function edit(apply_id) {
        location.href = "/<?php echo $this->getId() ?>/edit?id=" + apply_id;
    }

    function submit(apply_id) {
        inc.vueConfirm({
            content: "您确定要提交当前发票申请信息吗，该操作不可逆？",
            onConfirm: function () {
                var formData = "id=" + apply_id;
                $.ajax({
                    type: "POST",
                    url: "/<?php echo $this->getId() ?>/submit",
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            inc.vueMessage({
                                message: '操作成功'
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
