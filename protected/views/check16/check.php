<section class="content">
    <div class="box box-primary">
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
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
                    <label for="type" class="col-sm-2 control-label">发票公司名称</label>
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
                        <p class="form-control-static"><?php echo $data['o_remark'] ?></p>
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
                                          <td style="text-align:right"><?php echo $invoice['detail'][0]["quantity"].$this->map['goods_unit'][$invoice['detail'][0]['unit']]['name'] ?></td>
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
                <hr/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">审核意见 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="审核意见" data-bind="value:remark"></textarea>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="col-sm-offset-2 col-sm-10">
                        <?php if($this->checkButtonStatus["pass"]==1){ ?>
                            <button type="button" id="passButton" class="btn btn-success" data-bind="click:pass">通过</button>
                        <?php } ?>
                        <?php if($this->checkButtonStatus["back"]==1){ ?>
                            <button type="button" id="checkBackButton" class="btn btn-danger" data-bind="click:checkBack">驳回</button>
                        <?php } ?>
                        <?php if($this->checkButtonStatus["reject"]==1){ ?>
                            <button type="button" id="rejectButton" class="btn btn-danger" data-bind="click:reject">拒绝</button>
                        <?php } ?>
            
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[check_id]' data-bind="value:check_id" />
                        <input type='hidden' name='obj[project_id]' data-bind="value:project_id" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    var view;
    $(function(){
        view=new ViewModel(<?php echo json_encode($data)?>);
        ko.applyBindings(view);
    });
    function ViewModel(option)
    {
        var defaults = {
            project_id:0,
            check_id:0,
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self=this;
        self.project_id=ko.observable(o.project_id);
        self.check_id=ko.observable(o.check_id);
        self.remark=ko.observable(o.remark).extend({required:true,maxLength:512});
        self.actionState = ko.observable(0);

        self.status = ko.observable(o.status);
        self.errors = ko.validation.group(self,{deep: false});
        // self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.pass=function(){
            layer.confirm("您确定要通过当前信息的审核，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.status(1);
                self.save();
                layer.close(index);
            });
        }

        self.checkBack=function(){
            layer.confirm("您确定要驳回当前信息的审核，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.status(-1);
                self.save();
                layer.close(index);
            });
        }

        self.save=function(){
            if(!self.isValid())
            {
                self.errors.showAllMessages();
                return;
            }
            var formData=$("#mainForm").serialize();
            formData+="&obj[checkStatus]="+self.status();
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/save/',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if(document.referrer)
                            location.href=document.referrer;
                        else
                            location.href="<?php echo $this->mainUrl ?>";
                    }
                    else {
                        alertModel(json.data);
                    }
                },
                error:function (data) {
                    alertModel("保存失败！"+data.responseText);
                }
            });
        }
        self.back=function(){
            history.back();
        }
    }

</script>

