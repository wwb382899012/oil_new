<div class="box">
    <div class="box-body form-horizontal">
        <div class="box-header with-border">
            <h4 class="box-title"><?php echo $this->map['invoice_output_type'][$data['type_sub']] ?></h4>
            <div class="pull-right box-tools">
                <?php
                 if ($data['status'] == Invoice::STATUS_SAVED) { ?>
                    <button type="button" class="btn btn-sm btn-primary" onclick="edit(<?php echo $data['apply_id'] ?>)">修改</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo $data['invoice_id'] ?>)">提交</button>&nbsp;
                 <?php } ?>
                <button type="button"  class="btn btn-default btn-sm history-back" onclick="back()">返回</button>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">交易主体</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data['corporation_name'] ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">货款合同类型</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map['goods_contract_type'][$data["contract_type"]] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">货款合同编号</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a href="/businessConfirm/detail/?id=<?php echo $data["contract_id"] ?>&t=1" target="_blank"><?php echo $data["contract_code"] ?></a>
                </p>
            </div>
            <label for="type" class="col-sm-2 control-label">项目编号</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_code"] ?></a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">发票合同类型</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map['contract_category'][$data["invoice_contract_type"]] ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">发票合同编号</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["invoice_contract_code"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">公司名称</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["company_name"] ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">纳税人识别号</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["tax_code"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">税票类型</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map['output_invoice_type'][$data["invoice_type"]] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">地址</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data['address'] ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">电话</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data[phone] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">开户行</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data['bank_name'] ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">银行账户</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $data['bank_account']) ?></p>
            </div>
        </div>
        <div class="box-header with-border">
        </div>
        <h4 class="box-title">发票信息</h4>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">发票明细</label>
            <div class="col-sm-10">
                <?php
                if(Utility::isNotEmpty($invoiceDetail))
                {?>
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                        <tr>
                            <th style="width:120px;text-align:center"><?php if($data['type_sub']==1) echo '品名'; else echo '费用名称'; ?></th>
                            <?php if($data['type_sub']==1){ ?>
                            <th style="width:120px;text-align:center">数量</th>
                            <th style="width:80px;text-align:center">单位</th>
                            <th style="width:120px;text-align:center">单价</th>
                            <?php } ?>
                            <th style="width:80px;text-align:center">税率</th>
                            <th style="width:120px;text-align:center">金额(元)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($invoiceDetail as $v){ ?>
                            <tr>
                                <td style="text-align:center"><?php echo $v['goods_name'].$v['invoice_name'] ?></td>
                                <?php if($data['type_sub']==1){ ?>
                                <td style="text-align:right"><?php echo $v["quantity"] ?></td>
                                <td style="text-align:center"><?php echo $this->map["goods_unit"][$v["unit"]]["name"] ?></td>
                                <td style="text-align:right">
                                    ￥ <?php echo number_format($v['price']/100, 2) ?>
                                </td>
                                <?php } ?>
                                <td style="text-align:center"><?php echo $v['rate']*100 ?>%</td>
                                <td style="text-align:right">
                                    ￥ <?php echo number_format($v["amount"]/100, 2) ?>
                                </td>
                            </tr>
                        <?php  } ?>
                        </tbody>
                        <tfoot>
                            <?php if(bccomp($data['exchange_rate'],0)==1){ ?>
                            <tr>
                                <td style="text-align: center;">合计</td>
                                <?php if($data['type_sub']==1){ ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <?php } ?>
                                <td></td>
                                <td style="text-align: right;">￥ <?php echo number_format($data['total_amount']/100 ,2) ?></td>
                            </tr>
                            <?php }else{ ?>
                            <tr>
                                <td rowspan="2" style="text-align: center;vertical-align: middle;">合计</td>
                                <?php if($data['type_sub']==1){ ?>
                                <td rowspan="2"></td>
                                <td rowspan="2"></td>
                                <td rowspan="2"></td>
                                <?php } ?>
                                <td rowspan="2"></td>
                                <td style="text-align: right;vertical-align: middle;">￥ <?php echo number_format($data['total_amount']/100, 2) ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;vertical-align: middle;">$ <?php echo number_format($data['dollar_amount']/100, 2) ?></td>
                            </tr>
                            <?php } ?>
                        </tfoot>
                    </table>
                <?php  }
                ?>
            </div>
        </div>
        <?php if(Utility::isNotEmpty($plans)){ ?>
        <div class="form-group"></div>
        <div class="box-header with-border">
        </div>
        <h4 class="box-title"><?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划</h4>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label"><?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划明细</label>
            <div class="col-sm-10">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th style="width:100px;text-align:center"><?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款日期</th>
                        <th style="width:140px;text-align:center"><?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款类别</th>
                        <th style="width:80px;text-align:center">币种</th>
                        <th style="width:120px;text-align:center">计划<?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款金额</th>
                        <th style="width:120px;text-align:center">已<?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额</th>
                        <th style="width:120px;text-align:center">未<?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额</th>
                        <th style="width:120px;text-align:center"><?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($plans as $v){ ?>
                        <tr>
                            <td style="text-align:center"><?php echo $v['pay_date'] ?></td>
                            <td style="text-align:left"><?php echo $this->map['pay_type'][$v["expense_type"]]["name"] ?></td>
                            <td style="text-align:center"><?php echo $this->map['currency'][$v["currency"]]["name"] ?></td>
                            <td style="text-align:right">
                                <?php echo number_format($v['pay_amount']/100, 2) ?>
                            </td>
                            <td style="text-align:right"><?php echo number_format($v['amount_invoice']/100, 2) ?></td>
                            <td style="text-align:right">
                                <?php echo number_format(($v["pay_amount"] - $v["amount_invoice"])/100,2) ?>
                            </td>
                            <td style="text-align:right">
                                <?php echo number_format($v['amount']/100, 2) ?>
                            </td>
                        </tr>
                    <?php  } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } ?>
        <?php if(Utility::isNotEmpty($attachments)){ ?>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">附件</label>
            <div class="col-sm-10">
                    <?php 
                        foreach ($attachments as $key => $value) {
                            echo '<p class="form-control-static">';
                            echo "<a href='/inputInvoice/getFile/?id=" . $value["id"] . "&fileName=" . $value['name'] . "'  target='_blank' class='btn btn-primary btn-xs'>点击查看</a>";
                            echo '</p>';
                        }
                    ?>
            </div>
        </div>
        <?php } ?>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data['apply_remark'] ?></p>
            </div>
        </div>
        <?php if(Utility::isNotEmpty($invoices)) {?>
        <div class="box-header with-border">
        </div>
        <h4 class="box-title">历史开票信息</h4>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">开票明细</label>
            <div class="col-sm-10">
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                        <tr>
                            <th style="width:120px;text-align:center"><?php if($data['type_sub']==1) echo '品名'; else echo '费用名称'; ?></th>
                            <?php if($data['type_sub']==1){ ?>
                            <th style="width:100px;text-align:center">数量</th>
                            <?php } ?>
                            <th style="width:140px;text-align:center">实际开票金额</th>
                            <th style="width:100px;text-align:center">开票日期</th>
                            <th style="width:80px;text-align:center">开票数量</th>
                            <th style="text-align:center">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($invoices as $key => $invoice){ ?>
                            <tr <?php if($key==$data['invoice_id']) echo 'class="bg-yellow color-palette"' ?>>
                                <td style="text-align:center"><?php echo $invoice['detail'][0]['goods_name'].$invoice['detail'][0]['invoice_name'] ?></td>
                                <?php if($data['type_sub']==1){ ?>
                                  <td style="text-align:right"><?php echo $invoice['detail'][0]["quantity"] ?></td>
                                <?php } ?>
                                <td style="text-align:right">
                                    ￥ <?php echo number_format($invoice['detail'][0]["amount"]/100, 2) ?>
                                </td>
                                <td style="text-align:center"><?php echo $invoice['detail'][0]['invoice_date'] ?></td>
                                <td style="text-align:left;vertical-align: middle;" rowspan="<?php echo count($invoice['detail']) ?>"><?php echo $invoice['invoice_num'] ?>&nbsp;张</td>
                                <td style="text-align:left;vertical-align: middle;" rowspan="<?php echo count($invoice['detail']) ?>"><?php echo $invoice['remark'] ?></td>
                            </tr>
                            <?php 
                            if(count($invoice['detail'])>1) {
                                unset($invoice['detail'][0]);
                                foreach ($invoice['detail'] as $v) {
                            ?>
                            <tr <?php if($key==$data['invoice_id']) echo 'class="bg-yellow color-palette"' ?>>
                                <td style="text-align:center"><?php echo $v['goods_name'].$v['invoice_name'] ?></td>
                                <?php if($data['type_sub']==1){ ?>
                                  <td style="text-align:right"><?php echo $v["quantity"].$this->map['goods_unit'][$v['unit']]['name'] ?></td>
                                <?php } ?>
                                <td style="text-align:right">
                                    ￥ <?php echo number_format($v["amount"]/100, 2) ?>
                                </td>
                                <td style="text-align:center"><?php echo $v['invoice_date'] ?>
                                </td>
                            </tr>
                        <?php 
                                }
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="text-align: center;">合计</td>
                                <?php if($data['type_sub']==1){ ?>
                                <td></td>
                                <?php } ?>                                
                                <td style="text-align: right;">￥ <?php echo number_format($data['total_invoice_amount']/100 ,2) ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
            </div>
        </div>
        <?php } ?>
        <div class="box-header with-border">
        </div>
        <h4 class="box-title">开票信息</h4>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">开票明细</label>
            <div class="col-sm-10">
                <?php if(Utility::isNotEmpty($invoiceItems)) {?>
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                        <tr>
                            <th style="width:120px;text-align:center"><?php if($data['type_sub']==1) echo '品名'; else echo '费用名称'; ?></th>
                            <?php if($data['type_sub']==1){ ?>
                            <th style="width:100px;text-align:center">数量</th>
                            <?php } ?>
                            <th style="width:140px;text-align:center">实际开票金额</th>
                            <th style="width:100px;text-align:center">开票日期</th>
                            <th style="width:80px;text-align:center">开票数量</th>
                            <th style="text-align:center">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($invoiceItems as $key => $invoice){ ?>
                            <tr>
                                <td style="text-align:center"><?php echo $invoice['detail'][0]['goods_name'].$invoice['detail'][0]['invoice_name'] ?></td>
                                <?php if($data['type_sub']==1){ ?>
                                  <td style="text-align:right"><?php echo $invoice['detail'][0]["quantity"].$this->map['goods_unit'][$invoice['detail'][0]["unit"]]['name'] ?></td>
                                <?php } ?>
                                <td style="text-align:right">
                                    ￥ <?php echo number_format($invoice['detail'][0]["amount"]/100, 2) ?>
                                </td>
                                <td style="text-align:center"><?php echo $invoice['detail'][0]['invoice_date'] ?></td>
                                <td style="text-align:left;vertical-align: middle;" rowspan="<?php echo count($invoice['detail']) ?>"><?php echo $invoice['invoice_num'] ?>&nbsp;张</td>
                                <td style="text-align:left;vertical-align: middle;" rowspan="<?php echo count($invoice['detail']) ?>"><?php echo $invoice['remark'] ?></td>
                            </tr>
                            <?php 
                            if(count($invoice['detail'])>1) {
                                unset($invoice['detail'][0]);
                                foreach ($invoice['detail'] as $v) {
                            ?>
                            <tr>
                                <td style="text-align:center"><?php echo $v['goods_name'].$v['invoice_name'] ?></td>
                                <?php if($data['type_sub']==1){ ?>
                                  <td style="text-align:right"><?php echo $v["quantity"].$this->map['goods_unit'][$v['unit']]['name'] ?></td>
                                <?php } ?>
                                <td style="text-align:right">
                                    ￥ <?php echo number_format($v["amount"]/100, 2) ?>
                                </td>
                                <td style="text-align:center"><?php echo $v['invoice_date'] ?>
                                </td>
                            </tr>
                        <?php 
                                }
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="text-align: center;">合计</td>
                                <?php if($data['type_sub']==1){ ?>
                                <td></td>
                                <?php } ?>                                
                                <td style="text-align: right;">￥ <?php echo number_format($data['invoice_amount']/100 ,2) ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>
            </div>
        </div>
        <?php
        $checkLogs=FlowService::getCheckLog($data['invoice_id'],16);
        if(Utility::isNotEmpty($checkLogs))
            $this->renderPartial("/common/checkLogList",array("checkLogs"=>$checkLogs)); 
        ?>
    </div>
    <div class="box-footer">
        <?php
        if ($data['status'] == InvoiceApplication::STATUS_SAVED) { ?>
            <button type="button" class="btn btn-sm btn-primary" onclick="edit(<?php echo $data['apply_id'] ?>)">修改</button>&nbsp;
            <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo $data['invoice_id'] ?>)">提交</button>&nbsp;
        <?php } ?>
        <?php if(!$this->isExternal){ ?>
            <button type="button"  class="btn btn-sm btn-default" onclick="back()">返回</button>
        <?php } ?>
    </div>
</div>

<script>
    function back()
    {
        location.href="/<?php echo $this->getId() ?>/";
    }

    function edit(apply_id) {
        location.href = "/<?php echo $this->getId() ?>/edit?id=" + apply_id;
    }

    function submit(invoice_id) {
        layer.confirm("您确定要提交当前发票申请信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
            var formData = "id=" + invoice_id;
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/submit",
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg(json.data, {icon: 6, time: 1000}, function () {
                            location.reload();
                        });
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
            layer.close(index);
        });
    }

</script>
