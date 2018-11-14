<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<!-- <script type="text/javascript" src="/js/coSelect.js"></script> -->
<!-- <script src="/js/jquery.bankInput.js"></script> -->
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo $this->map[$data['title_map_name']][$data['type_sub']] ?></h3>
            <div class="pull-right box-tools">
                <button type="button"  class="btn btn-default btn-sm" data-bind="click:back">返回</button>
            </div>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
				<div class="form-group">
                    <label for="type" class="col-sm-2 control-label">交易主体 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control selectpicker" title="请选择交易主体" id="corporation_id" name="obj[corporation_id]" data-live-search="true" data-bind="optionsCaption: '请选择交易主体',value:corporation_id,valueAllowUnset: true">
                            <?php
                            $cors = UserService::getUserSelectedCorporations();
                            foreach ($cors as $v) {
                                echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                        <!-- <select class="form-control" data-bind='options: corporationCategories, optionsText: "corporation_name", optionsValue: "corporation_id", optionsCaption: "请选择交易主体", value: corporation, valueAllowUnset: true'> </select> -->
                    </div>
                    <label for="type" class="col-sm-2 control-label">货款合同类型</label>
                    <div class="col-sm-4">
                        <?php 
                            if($data['type_sub']==ConstantMap::PAYMENT_GOODS_TYPE) {
                                if($data['type']==ConstantMap::INPUT_INVOICE_TYPE)
                                    echo '<p class="form-control-static">采购合同</p>';
                                else
                                    echo '<p class="form-control-static">销售合同</p>';
                        ?>
                        <?php }else{ ?>
                            <select class="form-control selectpicker" title="请选择货款合同类型" id="contract_type" name="obj[contract_type]" data-bind="optionsCaption: '请选择货款合同类型',value:contract_type,valueAllowUnset: true">
                                 <option value='0'>请选择货款合同类型</option>
                                 <?php
                                 foreach ($this->map['goods_contract_type'] as $k=>$v) {
                                     echo "<option value='" . $k . "'>" . $v . "</option>";
                                 } ?>
                             </select>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">货款合同编号 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplayContractAsterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control selectpicker" id="contract_id" name="obj[contract_id]" data-live-search="true"
                                data-bind="
                                options: contracts,
                                optionsText: 'contract_code',
                                optionsValue:'contract_id',
                                optionsCaption: '请选择货款合同编号',
                                value:contract_id,
                                valueAllowUnset: true, 
                                enable: isCanSelectContract">
                        </select>
                        <!-- <span data-bind="with: corporation">
                            <select class="form-control" data-bind='options: contracts, optionsText: "contract_code", optionsValue: "contract_id", optionsCaption: "请选择货款合同编号", value: $parent.contract'> </select>
                        </span> -->
                    </div>
                    <label for="type" class="col-sm-2 control-label">项目编号 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplayContractAsterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control selectpicker" id="project_id" name="obj[project_id]" data-live-search="true"
                                data-bind="
                                options: projects,
                                optionsText: 'project_code',
                                optionsCaption: '请选择项目编号',
                                optionsValue:'project_id',
                                value:project_id,
                                valueAllowUnset: true">
                                <option value=''>请选择项目编号</option>
                        </select>
                        <!-- <span data-bind="with: contract">
                            <select class="form-control selectpicker" data-bind='options: projects, optionsText: "project_code", optionsValue: "project_id", optionsCaption: "请选择项目编号", value: $parent.project, valueAllowUnset: true'> </select>
                        </span> -->
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">发票合同类型</label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择发票合同类型" id="invoice_contract_type" name="obj[invoice_contract_type]" data-bind="optionsCaption: '请选择发票合同类型',value:invoice_contract_type, valueAllowUnset: true,enable:isCanEditContractType"><!-- data-live-search="true"  -->
                            <option value=''>请选发票合同类型</option>
                            <?php
                            foreach ($this->map['contract_category'] as $k=>$v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                    <label for="type" class="col-sm-2 control-label">发票合同编号 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplayCodeAsterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="invoice_contract_code" name= "obj[invoice_contract_code]" placeholder="发票合同编号" data-bind="value:invoice_contract_code,enable:isCanEditCode">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">公司名称 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="company_name" name= "obj[company_name]" placeholder="公司名称" data-bind="value:company_name">
                    </div>
                    <label for="type" class="col-sm-2 control-label">纳税人识别号 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="tax_code" name= "obj[tax_code]" placeholder="纳税人识别号" data-bind="value:tax_code">
                    </div>
                </div>
                <div class="form-group" data-bind="visible:isDisplayInvoiceDetail">
                    <label for="type" class="col-sm-2 control-label">税票类型 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control selectpicker" title="请选择税票类型" id="invoice_type" name="obj[invoice_type]" data-bind="optionsCaption: '请选择税票类型',value:invoice_type,valueAllowUnset: true">
                           <option value="">请选择税票类型</option>
                            <?php
                            foreach ($this->map['output_invoice_type'] as $k=>$v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div data-bind="visible:isDisplayInvoiceDetail">
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">地址 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplayInvoiceAsterisk"></span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="address" name= "obj[address]" placeholder="地址" data-bind="value:address">
                        </div>
                        <label for="type" class="col-sm-2 control-label">电话 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplayInvoiceAsterisk"></span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="phone" name= "obj[phone]" placeholder="电话" data-bind="value:phone">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">开户行 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplayInvoiceAsterisk"></span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="bank_name" name= "obj[bank_name]" placeholder="开户行" data-bind="value:bank_name">
                        </div>
                        <label for="type" class="col-sm-2 control-label">银行账号 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplayInvoiceAsterisk"></span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="bank_account" name= "obj[bank_account]" placeholder="银行账号" data-bind="value:bank_account">
                        </div>
                    </div>
                </div>
                <div data-bind="visible:isDisplayInvoice">
                    <div class="box-header with-border">
                    </div>
                    <h4>发票信息<!-- &nbsp;<span type="button" class="btn btn-social-icon btn-tumblr btn-sm"><i class="fa fa-plus"></i></span> --></h4>
                    <div data-bind="visible:isDisplayInvoiceHead">
                        <div class="form-group">
                            <label for="type" class="col-sm-2 control-label">税票类型 <span class="text-red fa fa-asterisk"></span></label>
                            <div class="col-sm-4">
                                <select class="form-control selectpicker" title="请选择税票类型" id="invoice_type" name="obj[invoice_type]" data-bind="optionsCaption: '请选择税票类型',value:invoice_type">
                                    <option value="">请选择税票类型</option>
                                    <?php
                                    $mapName = 'vat_invoice_type';
                                    if($data['type_sub']==ConstantMap::PAYMENT_NOT_GOODS_TYPE && $data['type']==ConstantMap::INPUT_INVOICE_TYPE){
                                        $mapName = 'non_vat_invoice_type';
                                    }
                                    foreach ($this->map[$mapName] as $k=>$v) {
                                        echo "<option value='" . $k . "'>" . $v . "</option>";
                                    } ?>
                                </select>
                            </div>
                            <label for="type" class="col-sm-2 control-label">汇率 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplayCustomerAsterisk"></span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="exchange_rate" name= "obj[exchange_rate]" placeholder="汇率" data-bind="value:exchange_rate">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="type" class="col-sm-2 control-label">发票日期 <span class="text-red fa fa-asterisk"></span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="invoice_date" name= "obj[invoice_date]" placeholder="发票日期" data-bind="value:invoice_date">
                            </div>
                            <label for="type" class="col-sm-2 control-label">发票数量 <span class="text-red fa fa-asterisk"></span></label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="num" name= "obj[num]" placeholder="发票数量" data-bind="value:num">
                                    <span class="input-group-addon" >张</span>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">发票明细</label>
                    </div> -->
                    <div class="form-group">
                        <!-- <label class="col-sm-2 control-label">发票明细 <span class="text-red fa fa-asterisk"></span></label> -->
                        <div class="col-sm-offset-1 col-sm-11">
                                <!-- ko component: {
                                     name: "invoice",
                                     params: {
                                                 contract_id: contract_id,
                                                 project_id: project_id,
                                                 apply_id: apply_id,
                                                 type_sub: type_sub,
                                                 exchange_rate: exchange_rate,
                                                 allGoods: allGoods,
                                                 units: units,
                                                 rates: rates,
                                                 items: invoiceItems
                                                 }
                                 } -->
                                <!-- /ko -->
                        </div>
                        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/invoiceItems.php"; ?>
                    </div>
                </div>
                <div data-bind="visible:isDisplayPayPlan">
                    <div class="box-header with-border">
                    </div>
                    <h4><?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划</h4>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划明细 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplayPaymentAsterisk"></span></label>
                        <div class="col-sm-10">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th style='width: 40px; text-align:center;'><input type="checkbox" id="selectAll" value="all" onclick="checkAll()" /></th>
                                        <th style="width: 100px; text-align: center;">预计<?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款日期</th>
                                        <th style="width: 140px; text-align: center;"><?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款类别</th>
                                        <th style="width: 100px; text-align: center;">计划<?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款金额</th>
                                        <th style="width: 60px; text-align: center;">币种</th>
                                        <th style="width: 100px; text-align: center;">已<?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额</th>
                                        <th style="width: 100px; text-align: center;">未<?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额</th>
                                        <th style="width: 100px; text-align: center;"><?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '收'; else echo '开'; ?>票金额 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplayPaymentAsterisk"></span></th>
                                    </tr>
                                </thead>
                                <tbody id="payItems" data-bind="foreach:paymentItems">
                                    <tr class="item">
                                        <td style='text-align: center;'><input type="checkbox" name="payment" data-bind="value:plan_id,checked:isSelected" onclick="checkOne()" /></td>
                                        <td style="text-align: center;vertical-align: baseline!important;">
                                            <span data-bind="text:pay_date"></span>
                                        </td>
                                        <td style="text-align: left;vertical-align: baseline!important;">
                                            <span data-bind="text:expense_desc"></span>
                                        </td>
                                        <td style="text-align: right;vertical-align: baseline!important;">
                                            <span data-bind="text:currency_ico"></span>&nbsp;
                                            <span data-bind="moneyText:pay_amount"></span>
                                        </td>
                                        <td style="text-align: center;vertical-align: baseline!important;">
                                            <span data-bind="text:currency_desc"></span>
                                        </td>
                                        <td style="text-align: right;vertical-align: baseline!important;">
                                            <span data-bind="text:currency_ico"></span>&nbsp;
                                            <span data-bind="moneyText:amount_invoice"></span>
                                        </td>
                                        <td style="text-align: right;vertical-align: baseline!important;">
                                            <span data-bind="text:currency_ico"></span>&nbsp;
                                            <span data-bind="moneyText:balance_amount"></span>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon" data-bind="html:currency_ico"></span>
                                                <input type="text" class="form-control input-sm" placeholder="发票金额" data-bind="money:amount">

                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <!-- <tfoot>
                                    <tr>
                                        <td style="text-align: left;">合计</td>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td style="text-align: left;">
                                            <span data-bind="text:currency_ico"></span>&nbsp;
                                            <span data-bind="moneyText:total_amount"></span>
                                        </td>
                                    </tr>
                                </tfoot> -->
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <?php
                    $attachType = $this->map["invoice_application_attachment_type"][1];
                    $attachments=AttachmentService::getAttachments(Attachment::C_INVOICE_APPLY,$data["apply_id"],1);
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
        view.units(inc.objectToArray(<?php echo json_encode(array_values(Map::$v['goods_unit'])) ?>));
        view.formatRates(inc.objectToArray(<?php echo json_encode(array_values($this->map["goods_invoice_rate"])) ?>));
        view.formatAllGoods(<?php echo json_encode($allGoods) ?>);
        view.formatInvoiceItems(<?php echo json_encode($invoiceItems) ?>);
        view.formatPaymentItems(<?php echo json_encode($paymentItems) ?>);
        ko.applyBindings(view);
        $("#invoice_date").datetimepicker({format: 'yyyy-mm-dd',minView: 'month'});
        // $("#tax_code").bankInput({min:1,max:50,deimiter:' '});
        checkOne();
        if(!inc.isEmpty(view.apply_id()) && !inc.isEmpty(view.corporation_id())) {
			view.getCorpContracts();
			view.getCorpProjects();
        }
    });
    function ViewModel(option)
    {
        var defaults = {
            apply_id: 0,
            corporation_id: "",
            contract_id: "",
            contract_code: "",
            project_id: "",
            project_code: "",
            invoice_contract_type: "",
            invoice_contract_code: "",
            company_name: "",
            tax_code:"",
            type: "",
            type_sub: "",
            contract_type: '0',
            invoice_type: "",
            exchange_rate: "1.000000",
            invoice_date: "",
            num: "",
            address: "",
            phone: "",
            bank_name: "",
            bank_account: "",
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self=this;
        self.apply_id=ko.observable(o.apply_id);
        self.corporation_id=ko.observable(o.corporation_id).extend({required: {params: true, message: "请选择交易主体"}});
        self.contract_type=ko.observable(o.contract_type);
        self.contract_id=ko.observable(o.contract_id).extend({
        custom:{
            params: function (v) {
                if(self.contract_type()==0 || (self.contract_type()>0 && v!=""))
                    return true;
                else
                    return false;
            },
            message: "请选择货款合同编号"
        }});

        self.project_id=ko.observable(o.project_id).extend({
        custom:{
            params: function (v) {
                if(self.contract_type()==0 || (self.contract_type()>0 && v!=""))
                    return true;
                else
                    return false;
            },
            message: "请选择项目编号"
        }});
        self.invoice_contract_type=ko.observable(o.invoice_contract_type);
        self.invoice_contract_code=ko.observable(o.invoice_contract_code).extend({
        custom:{
            params: function (v) {
                if(self.invoice_contract_type()>0 && v=="")
                    return false;
                else
                    return true;
            },
            message: "请填写发票合同编号"
        }});
        self.company_name=ko.observable(o.company_name).extend({required: {params: true, message: "请填写发票公司名称"}});
        self.tax_code=ko.observable(o.tax_code).extend({required: {params: true, message: "请填写纳税人识别号"}});
        self.type=ko.observable(o.type);
        self.type_sub=ko.observable(o.type_sub);
        self.contract_type=ko.observable(o.contract_type);
        self.invoice_type=ko.observable(o.invoice_type).extend({required: {params: true, message: "请选择税票类型 "}});
        self.exchange_rate=ko.observable(o.exchange_rate).extend({
        custom:{
            params: function (v) {
                if (self.type() == 1 && (self.invoice_type() == 2 || self.invoice_type() == 4 ) && (v == null || v == "" || !$.isNumeric(v) || v <= 0 ))
                    return false;

                return true;
            },
            message: "请填写汇率"
        }});
        self.invoice_date=ko.observable(o.invoice_date).extend({
        custom:{
            params: function (v) {
                if(self.type()==2 || (self.type()==1 && v!=""))
                    return true;
                else
                    return false;
            },
            message: "请填写发票日期"
        }});//.extend({required:{params:true, message:'请填写发票日期'}});
        self.num=ko.observable(o.num).extend({
        custom:{
            params: function (v) {
                if(self.type()==2 || (self.type()==1 && v!=""))
                    return true;
                else
                    return false;
            },
            message: "请填写发票数量"
        }});//.extend({positiveNumber:{params:true, message:'请填写发票数量'}});

        self.address = ko.observable(o.address).extend({
        custom:{
            params: function (v) {
                if(self.type()==1 || (self.type()==2 && (self.invoice_type()==2 || v!="")))
                    return true;
                else
                    return false;
            },
            message: "请填写地址"
        }});
        self.phone = ko.observable(o.phone).extend({
        custom:{
            params: function (v) {
                if(self.type()==1 || (self.type()==2 && (self.invoice_type()==2 || v!="")))
                    return true;
                else
                    return false;
            },
            message: "请填写电话"
        }});
        self.bank_name = ko.observable(o.bank_name).extend({
        custom:{
            params: function (v) {
                if(self.type()==1 || (self.type()==2 && (self.invoice_type()==2 || v!="")))
                    return true;
                else
                    return false;
            },
            message: "请填写开户行"
        }});
        self.bank_account = ko.observable(o.bank_account).extend({
			custom:{
            params: function (v) {
				self.msg = '请填写银行账号';
                if(self.type()==1 || (self.type()==2 && (self.invoice_type()==2 || v!=""))) {
                	if(v.length <= 32) {
                		return true;
					} else {
						self.msg = '银行账号不能超过32位字符';
						return false;
					}
				}
                else
                    return false;
            },
            message: function () {
				return self.msg;
			}
        }});
        self.remark = ko.observable(o.remark);

        self.isDisplayInvoiceAsterisk=ko.computed(function() {
            return self.type()==2 && self.invoice_type()==1;
        });

        self.isDisplayInvoiceDetail=ko.computed(function() {
            return self.type()==2;
        });

        self.contracts = ko.observableArray();
        self.projects = ko.observableArray();
        self.corporation_id.subscribe(function (v) {
        	self.getCorpContracts();
        	self.getCorpProjects();
		});

		self.contract_type.subscribe(function (v) {
			self.getCorpContracts();
			self.getCorpProjects();
		});

        self.getCorpContracts = function () {
			$.ajax({
				type: 'POST',
				url: '/inputInvoice/getCorpContracts',
				data: {
					corp_id: self.corporation_id(),
					contract_type: self.contract_type()
				},
				dataType: 'json',
				success: function (json) {
					if (json.state == 0) {
						self.contracts(json.data);
						self.contract_id.isModified(false);
						$('#contract_id').selectpicker('refresh');
					} else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("合同获取失败！" + data.responseText, {icon: 5});
				}
			})
		};

		self.getCorpProjects = function () {
			$.ajax({
				type: 'POST',
				url: '/inputInvoice/getCorpProjects',
				data: {
					corp_id: self.corporation_id(),
					contract_type: self.contract_type(),
					contract_id: self.contract_id()
				},
				dataType: 'json',
				success: function (json) {
					if (json.state == 0) {
						self.projects(json.data);
						self.project_id.isModified(false);
						$('#project_id').selectpicker('refresh');
					} else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("项目获取失败！" + data.responseText, {icon: 5});
				}
			})
		}

        self.getCompany = function () {
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/getCompany',
                data: {
                    contract_id: self.contract_id(),
                },
                dataType: 'json',
                success: function (json) {
                    if (json.state == 0) {
                        self.company_name(json.data[0].partner_name);
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("项目获取失败！" + data.responseText, {icon: 5});
                }
            })
        }

        self.isCanSelectContract=ko.computed(function() {
            return self.contract_type()>0;
        });

        /*self.isDisplayInoviceContract=ko.computed(function() {
            return self.type_sub()==2;
        });*/

        self.isDisplayContractAsterisk=ko.computed(function() {
            return self.contract_type()>0;
        });

        self.isDisplayCodeAsterisk=ko.computed(function() {
            return self.invoice_contract_type()>0;
        });

        self.isDisplayCustomerAsterisk=ko.computed(function() {
            return self.invoice_type()==2 || self.invoice_type()==4;
        });

        self.isDisplayInvoice = ko.computed(function() {
            return (self.type_sub()==1 && self.contract_id()>0) || (self.type_sub()==2 && self.corporation_id()>0);
        }); 

        self.isDisplayInvoiceHead = ko.computed(function() {
            return self.isDisplayInvoice() && self.type()==1;
        }); 

        self.isDisplayPaymentAsterisk = ko.computed(function() {
            return self.type_sub()==1;
        });

         self.isCanEditContractType = ko.computed(function(){
            return self.type_sub()==2;
         })

        self.isCanEditCode=ko.observable(true);
        self.invoice_contract_type.subscribe(function(v){
            if(v>0 && self.contract_type()>0 && 
              ((v<4 && self.contract_type()==1) || 
              (v==4 && self.contract_type()==2))){
               var code = $('#contract_id').find("option:selected").text();
               self.invoice_contract_code(code); 
               self.isCanEditCode(false);
            }else{
                self.invoice_contract_code(""); 
                self.isCanEditCode(true);
            }
        });

        self.contract_id.subscribe(function(v){
            //self.project_id(0);
            if(!inc.isEmpty(v)) {
				self.updateData();
				self.getCorpProjects();
                if(self.type_sub()==1)
                    self.getCompany();
			} else {
            	self.invoice_contract_type(0);
                self.company_name('');
            }
            if(self.invoice_contract_type()>0 && self.contract_type()>0 &&
              ((self.invoice_contract_type()<4 && self.contract_type()==1) || 
              (self.invoice_contract_type()==4 && self.contract_type()==2))){
               var code = $('#contract_id').find("option:selected").text();
               self.invoice_contract_code(code); 
               self.isCanEditCode(false);
            }else{
                self.isCanEditCode(true);
                self.invoice_contract_code(""); 
            }
        });

        self.company_name.subscribe(function(v){
            if(!inc.isEmpty(v)){
                self.setCode();
                if(self.isDisplayInvoiceDetail()){
                    self.setCompanyDetail();
                }
            }else{
                self.tax_code("");
            }
        });

        self.fileUploadStatus=ko.observable();

        self.allGoods = ko.observableArray();
        self.formatAllGoods = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i in data) {
                self.allGoods.push(data[i]);
            }
        };

        self.setCompanyDetail=function () {
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/getCompanyDetail",
                data: {company_name: self.company_name()},
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if(json.data.length>0){
                            self.phone(json.data[0].phone);
                            self.address(json.data[0].address);
                            self.bank_account(json.data[0].bank_account);
                            self.bank_name(json.data[0].bank_name);
                        }
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("获取数据失败：" + data.responseText, {icon: 5});
                }
            });
        }

        self.setGoods=function () {
            self.allGoods.removeAll();
            self.invoiceItems.removeAll();
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/getGoods",
                data: {contract_id: self.contract_id(), type: self.contract_type()},
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        self.allGoods(json.data);
                        for(var i in json.data){
                            // json.data[i]['allGoods'] = self.allGoods();
                            // console.log(json.data[i]);
                            json.data[i]['rates'] = self.rates();
                            json.data[i]['type_sub'] = self.type_sub();
                            var obj = new Invoice(json.data[i]);
                            self.invoiceItems.push(obj);
                        }
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("获取数据失败：" + data.responseText, {icon: 5});
                }
            });
        }

        self.setContractType=function () {
            self.invoice_contract_type('');
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/getContractType",
                data: {contract_id: self.contract_id()},
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                    	// console.log(json.data);
                        self.invoice_contract_type(json.data);
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("获取数据失败：" + data.responseText, {icon: 5});
                }
            });
        }
        
        self.units = ko.observableArray();

        self.rates = ko.observableArray();
        self.formatRates = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i in data) {
                self.rates.push(data[i]);
            }
        }

        self.invoiceItems = ko.observableArray();
        self.formatInvoiceItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                data[i]['rates'] = self.rates;
                var obj = new Invoice(data[i]);
                self.invoiceItems.push(obj);
            }
        };

        self.paymentItems = ko.observableArray()
        self.formatPaymentItems = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i in data) {
                var obj = new Payment(data[i]);
                self.paymentItems.push(obj);
            }

        };

        self.setPayment=function () {
            self.paymentItems.removeAll();
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/getPayment",
                data: {contract_id: self.contract_id()},
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        // console.log(json.data.hasOwnProperty.length);
                        if(json.data.hasOwnProperty.length>0){
                            for(var i in json.data){
                                var obj = new Payment(json.data[i]);
                                self.paymentItems.push(obj);
                            }
                        }
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("获取数据失败：" + data.responseText, {icon: 5});
                }
            });
        }


        self.isDisplayPayPlan = ko.computed(function() {
            return self.paymentItems().length > 0;
        });

        self.updateData=function(){
            /*if(self.contract_id()>0){
                
            }*/
            if(self.type_sub()==1){
                self.setGoods();
                self.setContractType();
            }
            self.setPayment();
            checkOne();
        }

        self.setCode = function () {
            self.tax_code("");
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/getTaxCode",
                data: {company_name: self.company_name()},
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        self.tax_code(json.data);
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("获取数据失败：" + data.responseText, {icon: 5});
                }
            });
        }


        self.payment = ko.observableArray();
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

            layer.confirm("您确定要提交当前发票申请信息，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.pass();
                layer.close(index);
            });

        }

        self.pass = function () {
            if(self.contract_type()>0){
                var ids = [];
                $("input[name='payment']").each(function(i){
                    if($(this).prop("checked")==true){ 
                        ids.push($(this).val());//将选中的条目明细id添加到数组
                    }
                });

                if(self.type_sub()==1 && ids.length < 1 && self.paymentItems().length>0){
                    layer.alert("请选择<?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划项", {icon: 5});
                    return;
                }
            }
            
            // console.log(ids);
            var total_pay_amount = 0.0;
            var currency = 1;
            if(self.paymentItems().length>0){
                for(var i in self.paymentItems()){
                    var pos = $.inArray(self.paymentItems()[i].plan_id(), ids);
                    if(pos<0){
                        delete self.payment()[i];
                        continue;
                    }
                    /*console.log(self.paymentItems()[i].amount());
                    return;*/
                    if(self.paymentItems()[i].amount()==0){
                        layer.alert("<?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划明细中第"+(parseInt(i)+1)+"条发票金额为空！", {icon: 5});
                        return;
                    }

                    if(self.paymentItems()[i].currency()==1){
                        total_pay_amount += parseFloat(self.paymentItems()[i].amount());
                    }else{
                        currency = 2;
                        total_pay_amount += (parseFloat(self.paymentItems()[i].amount())*parseFloat(self.exchange_rate())).toFixed(0);
                    }

                    // total_pay_amount += parseFloat(self.paymentItems()[i].amount());
                    self.payment()[i] = self.paymentItems()[i];
                }
            }

            // console.log(self.doneItems());
            var filter = ["paymentItems", "saveBtnText", "submitBtnText","formatPaymentItems", "isValid",
            "allContracts", "allGoods", "allProjects","formatInvoiceItems","formatRates","formatUnits","isCanEditContractType",
            "contracts", "projects","rates","units","isCanEditCode","isCanSelectContract","isDisplayCodeAsterisk",
            "isDisplayContractAsterisk","isDisplayCustomerAsterisk","isDisplayInvoice","isDisplayPayPlan"];
            
            var invoice_amount = 0.0;
            if(self.invoiceItems().length>0){
                for(var item in self.invoiceItems()){
                   if(self.invoiceItems()[item].amount()==0){
                       layer.alert("发票明细中第"+(parseInt(item)+1)+"金额为空！", {icon: 5});
                       return;
                   }
                   /*if(self.exchange_rate()>0){
                        invoice_amount += parseFloat(self.invoiceItems()[item].amount())/parseFloat(self.exchange_rate());
                   }else{
                        invoice_amount += parseFloat(self.invoiceItems()[item].amount());
                   }*/
                   invoice_amount += parseFloat(self.invoiceItems()[item].amount());
                   
                }
                /*console.log(total_pay_amount);
                console.log(invoice_amount);*/
                if(total_pay_amount>0){
                    total_pay_amount = (parseFloat(total_pay_amount)).toFixed(0);
                    if(self.exchange_rate()>0 && currency==2){
                        invoice_amount = (((parseFloat(invoice_amount)/parseFloat(self.exchange_rate())).toFixed(0))*parseFloat(self.exchange_rate())).toFixed(0);
                    }else{
                        invoice_amount   = (parseFloat(invoice_amount)).toFixed(0);
                    }
                    
                    if(total_pay_amount != invoice_amount){
                        layer.alert("选中的<?php if($data['type']==ConstantMap::INPUT_INVOICE_TYPE) echo '付'; else echo '收'; ?>款计划项总金额与发票明细总金额不一致！", {icon: 5});
                        return;
                    }
                }
                   
            }else{
               layer.alert("请添加发票明细！", {icon: 5});
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


    function checkAll() {  
        //把所有参与选择的checkbox使用相同的name，这里为"payment"  
        var eles = document.getElementsByName("payment");  
        var i = 0;  
        // 如果是全选状态，则取消所有的选择  
        if (isSelectAll() == true) {  
            for ( i = 0; i < eles.length; i++) {  
                eles[i].checked = false;  
            }  
            document.getElementById("selectAll").checked = false;  
        } else {  
            // 否则选中每一个checkbox  
            for ( i = 0; i < eles.length; i++) {  
                eles[i].checked = true;  
            }  
        }  
    }  
    // 判断当前是否为全选状态  
    function isSelectAll() {  
        var isSelected = true;  
        var eles = document.getElementsByName("payment");
        if(eles.length==0){
            isSelected = false; 
        }else{
            for (var i = 0; i < eles.length; i++) {  
                if (eles[i].checked != true) {  
                    isSelected = false;  
                }  
            }
        }
          
        return isSelected;  
    }  
    // 选择任意一个非全选checkbox  
    function checkOne() {  
        if (isSelectAll()) {  
            document.getElementById("selectAll").checked = true;  
        } else {  
            document.getElementById("selectAll").checked = false;  
        }  
    }

    function Payment(option) {
        var defaults = {
            detail_id: 0,
            plan_id: 0,
            amount_invoice: 0.0,
            pay_date: "",
            pay_amount: "",
            expense_desc: "",
            project_id: 0,
            contract_id: 0,
            currency: 0,
            currency_desc: "",
            currency_ico:"",
            amount:""
        }
        var o = $.extend(defaults, option);
        var self = this;
        self.detail_id=ko.observable(o.detail_id);
        self.plan_id=ko.observable(o.plan_id);
        self.amount_invoice=ko.observable(o.amount_invoice);
        self.pay_date=ko.observable(o.pay_date);
        self.pay_amount=ko.observable(o.pay_amount);
        self.contract_id=ko.observable(o.contract_id);
        self.project_id=ko.observable(o.project_id);
        self.expense_desc=ko.observable(o.expense_desc);
        self.currency=ko.observable(o.currency);
        self.currency_desc=ko.observable(o.currency_desc);
        self.currency_ico=ko.observable(o.currency_ico);
        self.amount=ko.observable(o.amount);
        /*self.amount=ko.observable(o.amount).extend({
        custom:{
            params: function (v) {
                if(view.contract_type()==0 || (view.contract_type()>0 && v>0))
                    return true;
                else
                    return false;
            },
            message: "请填写发票金额"
        }});*/

        self.balance_amount = ko.computed(function(v){
            return (parseFloat(self.pay_amount()) - parseFloat(self.amount_invoice())).toFixed(0);
        });

        self.isSelected = ko.computed(function(v){
            return self.detail_id()>0;
        });


        /*self.total_amount=ko.computed(function () {
            var total = 0;
            ko.utils.arrayForEach(self.items(), function(item) {
                var value = parseFloat(item.amount());
                if (!isNaN(value)) {
                    total += value;
                }
            });
            return total.toFixed(0);
        },self);*/
    }

</script>