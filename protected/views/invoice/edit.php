<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo $this->map['invoice_output_type'][$data['type_sub']] ?></h3>
            <div class="pull-right box-tools">
                <button type="button"  class="btn btn-default btn-sm" data-bind="click:back">返回</button>
            </div>
        </div>
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
                                    <?php if(bccomp($data['exchange_rate'],0)==0){ ?>
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
                                          <td style="text-align:right"><?php echo $v["quantity"] ?></td>
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
                    <label for="type" class="col-sm-2 control-label">剩余开票金额</label>
                    <div class="col-sm-4">
                       <p class="form-control-static">￥ <span data-bind="moneyText:blanace_amount"></span></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">开票明细 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
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
                    </div>
                    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/invoiceDetail.php"; ?>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">开票数量 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="invoice_num" name= "obj[invoice_num]" placeholder="开票数量" data-bind="value:invoice_num">
                            <span class="input-group-addon" >张</span>
                        </div>
                    </div>
                    <label for="type" class="col-sm-2 control-label">已开票数量</label>
                    <div class="col-sm-4">
                       <p class="form-control-static"><span data-bind="html:num"></span><span>&nbsp;张</span></span></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save, html:saveBtnText"></button>
                        <button type="button" id="submitButton" class="btn btn-danger" data-bind="click:submit, html:submitBtnText"></button>
                        <button type="button" class="btn btn-default history-back" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[invoice_id]' data-bind="value:invoice_id" />
                        <input type='hidden' name='obj[apply_id]' data-bind="value:apply_id" />
                        <input type='hidden' name='obj[type]' data-bind="value:type" />
                        <input type='hidden' name='obj[type_sub]' data-bind="value:type_sub" />
                        <!-- <input type='hidden' name='obj[project_id]' data-bind="value:project_id" /> -->
                    </div>
                </div>
            </div>
        </form>
    </div>

</section>

<script>
    var view;
    var upStatus=0;
    var count=0;
    $(function(){
        view=new ViewModel(<?php echo json_encode($data) ?>);
        view.formatUnits(<?php echo json_encode($this->map["goods_unit"]); ?>);
        view.formatAllGoods(<?php echo json_encode($allGoods) ?>);
        view.formatInvoiceItems(<?php echo json_encode($invoiceItems) ?>);
        // view.formatGoodsItems(<?php echo json_encode($goodsItems) ?>);
        ko.applyBindings(view);
    });
    function ViewModel(option)
    {
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
            amount_paid:0.0,
            invoice_id: 0
        };
        var o = $.extend(defaults, option);
        var self=this;
        self.invoice_id=ko.observable(o.invoice_id);
        self.apply_id=ko.observable(o.apply_id);
        self.corporation_id=ko.observable(o.corporation_id);
        self.contract_id=ko.observable(o.contract_id);
        self.project_id=ko.observable(o.project_id);
        self.type=ko.observable(o.type);
        self.type_sub=ko.observable(o.type_sub);
        self.invoice_num=ko.observable(o.invoice_num).extend({positiveNumber:{params:true, message:'请填写发票数量'}});
        self.num=ko.observable(o.num);
        self.remark = ko.observable(o.remark);
        self.total_amount = ko.observable(o.total_amount);
        self.amount_paid = ko.observable(o.amount_paid);
        self.blanace_amount = ko.computed(function(v){
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
                data[i]['goodsItems']=self.goodsItems;
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
        self.save = function(){
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
            self.isSave(1);
            
            self.pass();
        }

        self.submit = function(){
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            self.isSave(0);

            layer.confirm("您确定要提交当前开票信息，改操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.pass();
                layer.close(index);
            });

        }

        self.pass = function () {
        
            // console.log(self.doneItems());
            var filter = ["saveBtnText", "submitBtnText","isValid","allGoods", "formatInvoiceItems" ];
            
            var invoice_amount = 0.0;
            if(self.invoiceItems().length>0){
                for(var item in self.invoiceItems()){
                   if(self.invoiceItems()[item].amount()==0){
                       layer.alert("开票明细中第"+(parseInt(item)+1)+"实际开票金额为空！", {icon: 5});
                       return;
                   }
                   invoice_amount += parseFloat(self.invoiceItems()[item].amount());
                   
                }

                if(parseFloat(self.blanace_amount()) < parseFloat(invoice_amount)){
                    layer.alert("开票总金额不得大于剩余发票总金额！", {icon: 5});
                    return;
                }
                   
            }else{
               layer.alert("请添加开票明细！", {icon: 5});
               return;
            }
            

            var formData = {"data": inc.getPostData(self,filter)};            

            if (self.actionState() == 1)
                return;
            if(self.isSave()==1)
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
                        layer.msg("操作成功", {icon: 6, time:1000}, function(){
                            location.href = "/<?php echo $this->getId() ?>/detail/?id=" + json.data;
                        });
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                    self.saveBtnText("保存");
                    self.submitBtnText("提交");
                },
                error: function (data) {
                    self.saveBtnText("保存");
                    self.submitBtnText("提交");
                    self.actionState(0);
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
        }

        self.back=function(){
            history.back();
        }
    }
</script>