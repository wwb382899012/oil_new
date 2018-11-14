<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<script src="/js/jquery.bankInput.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">基础信息</h3>
            <div class="pull-right box-tools">
                <button type="button" class="btn btn-default btn-sm" data-bind="click:back">返回</button>
            </div>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <?php 
                	$this->renderPartial("/pay/detailBody", array('apply'=>$model)); 
                    include "payInfo.php"
                ?>
                <div class="box-header with-border">
                </div>
                <h4 class="box-title">实际付款信息</h4>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">实付日期 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control date" id="pay_date" name="obj[pay_date]" placeholder="实付日期" data-bind="value:pay_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">实付金额 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                    	<div class="input-group">
            			    <span class="input-group-addon" data-bind="html:currency_ico"></span>
            			    <input type="text" class="form-control date" id="amount" name="obj[amount]" placeholder="实付金额" data-bind="money:amount">
            			</div>
            			<p class="form-control-static" data-bind="visible:isDisplay"><span></span>￥ <span data-bind="moneyText:amount_cny"></span></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">剩余金额</label>
                    <div class="col-sm-4"> 
                        <p class="form-control-static"> <?php echo $this->map['currency'][$data['currency']]['ico'].' '.number_format($data['balance_amount']/100, 2) ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">付款账户名</label>
                    <div class="col-sm-4">
                        <select class="form-control selectpicker" id="corporation_id" name="obj[corporation_id]" data-live-search="true" data-bind="optionsCaption: '请选择付款账户名',value:corporation_id,valueAllowUnset: true">
                            <option value=''>请选择付款账户名</option>
                            <?php
                            $cors = UserService::getUserSelectedCorporations();
                            foreach ($cors as $v) {
                                echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">实付银行</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="obj[account_id]"
                                data-bind="value: account_id,valueAllowUnset: true
                                 ,options:accounts
                                 ,optionsText: 'bank_name'
                                 ,optionsValue:'account_id'
                                 ,optionsCaption: '请选择实付银行'
                                    ">
                            <option value=''>请选择实付银行</option>

                        </select>
                    </div>
                    <label for="type" class="col-sm-2 control-label">银行账号</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="account" name="obj[account_id]"
                                data-bind="value: account_id,valueAllowUnset: true
                                 ,options:accounts
                                 ,optionsText: 'account_no'
                                 ,optionsValue:'account_id'
                                 ,optionsCaption: '请选择银行账号'
                                    ">
                            <option value=''>请选择银行账号</option>
                    
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">银行付款流水号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control date" id="payment_no" name="obj[payment_no]" placeholder="银行付款流水号" data-bind="value:payment_no">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">汇率 <span class="text-red fa fa-asterisk" data-bind="visible:isDisplay"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control date" id="exchange_rate" name="obj[exchange_rate]" placeholder="汇率" data-bind="value:exchange_rate,enable:isCanEditRate">
                    </div>
                </div>
                <div class="form-group">
                    <?php
                    $attachType = $this->map["payment_attachment_type"][11];
                    $attachments=AttachmentService::getAttachments(Attachment::C_PAYMENT,$data["payment_id"], 11);
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
                                         files:<?php echo json_encode($attachments[11]); ?>,
                                         fileParams: {
                                            id:<?php echo empty($data['payment_id'])?0:$data['payment_id'] ?>
                                         }
                                     }
                         } -->
                        <!-- /ko -->
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name="obj[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save, html:saveBtnText"></button>
                        <button type="button" id="submitButton" class="btn btn-danger" data-bind="click:submit, html:submitBtnText"></button>
                        <button type="button" class="btn btn-default history-back" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[payment_id]' data-bind="value:payment_id"/>
                        <input type='hidden' name='obj[apply_id]' data-bind="value:apply_id"/>
                        <input type='hidden' name='obj[currency]' data-bind="value:currency"/>
                        <input type='hidden' name='obj[balance_amount]' data-bind="value:balance_amount"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
	var view;
	var upStatus = 0;
	var count = 0;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
		view.formatAccounts(<?php echo json_encode($accounts) ?>);
		ko.applyBindings(view);
		$("#pay_date").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});
        $("#account").bankInput({min:1,max:50,deimiter:' '});
		
	});
	function ViewModel(option) {
		var defaults = {
			payment_id: 0,
			apply_id: "0",
			pay_date: "",
			amount: 0,
			account_id: "",
			corporation_id: "",
			exchange_rate: "",
			payment_no: "",
			remark: "",
			currency: 0,
			balance_amount: 0,
            currency_ico: "",
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.payment_id = ko.observable(o.payment_id);
		self.currency = ko.observable(o.currency);
        self.currency_ico = ko.observable(o.currency_ico);
		self.apply_id = ko.observable(o.apply_id);
		self.balance_amount = ko.observable(o.balance_amount);
		self.exchange_rate = ko.observable(o.exchange_rate).extend({
			custom: {
				params: function (v) {
					if (self.currency()==1 || (self.currency()==2 && v>0) ) {
						return true;
					}
					else
						return false;
				},
				message: "请填写汇率"
			}
		});
		
		self.payment_no = ko.observable(o.payment_no);
		self.pay_date = ko.observable(o.pay_date).extend({required: true});
		self.amount = ko.observable(o.amount).extend({
			money: true, custom: {
				params: function (v) {
					/*if (v>0 && 
						((self.currency()==1 && parseFloat(v)<=parseFloat(self.balance_amount())) || 
						(self.currency()==2 && 
						parseFloat(self.currnecy_amount())<=parseFloat(self.balance_amount())))) {
						return true;
					}*/
                    if(parseInt(v)>0 && parseInt(v)<=parseInt(self.balance_amount()))
                        return true;
					else
						return false;
				},
				message: "实付金额必须填写，且不能大于剩余金额！"
			}
		});

        self.amount_cny = ko.computed(function(v){
            if(self.exchange_rate()>0 && self.currency()==2)
                return (parseFloat(self.amount())*parseFloat(self.exchange_rate())).toFixed(0);
            else
                return self.amount();
        });
		
		self.account_id = ko.observable(o.account_id);
		self.corporation_id = ko.observable(o.corporation_id);
		self.remark = ko.observable(o.remark);

		self.corporation_id.subscribe(function (v) {
			self.account_id("");
			if(v>0)
				self.setAccounts();
			else
				self.accounts([]);
		});

        self.isCanEditRate = ko.computed(function(v){
            return self.currency()==2;
        });


		self.accounts = ko.observableArray();
		self.formatAccounts = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i in data) {
                self.accounts().push(data[i]);
            }
        };

		self.setAccounts=function () {
            self.accounts([]);
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/getAccounts",
                data: {corporation_id: self.corporation_id()},
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        self.accounts(json.data);
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("获取数据失败：" + data.responseText, {icon: 5});
                }
            });
        }
		

		self.isDisplay = ko.computed(function(v){
			return self.currency()==2;
		});


		self.fileUploadStatus=ko.observable();

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

            self.pass();
        }

        self.submit = function(){
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            self.isSave(0);

            layer.confirm("您确定要提交当前实付信息，改操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.pass();
                layer.close(index);
            });

        }

        self.pass = function () {
            // console.log(self.doneItems());
            var filter = ["isDisplay", "saveBtnText", "submitBtnText", "isValid", "accounts", "fileUploadStatus"];

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
                    } else if (json.state == -2) {
                        layer.confirm(json.data, {icon: 3, title: '提示', btn: ['确定']}, function () {
                            location.href = "/payconfirm/";
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

		self.back = function () {
			history.back();
		}
	}
</script>