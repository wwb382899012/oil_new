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
                <h4 class="section-title">止付信息</h4>
                <?php if(!empty($model->contract_id) && is_array($model->details) && count($model->details)>0){
                    ?>
                    <!-- <h4 class="section-title">相关付款计划</h4> -->
                    <div class="form-group">
                        <div class="col-sm-offset-1 col-sm-11">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th style="width:60px; text-align: left;">期数</th>
                                    <th style="width:100px; text-align: left;">类别</th>
                                    <th style="width:180px; text-align: left;">计划付款金额</th>
                                    <th style="width:150px; text-align: left;">已申请金额</th>
                                    <th style="width:150px; text-align: left;">未申请金额</th>
                                    <th style="width:150px; text-align: left;">本次付款金额</th>
                                    <th style="text-align: left;">实付金额 <span class="text-red fa fa-asterisk"></span></th>
                                </tr>
                                </thead>
                                <tbody data-bind="foreach:plans">
                                <tr>
                                    <td data-bind="text:payment.period"> </td>
                                    <td><span data-bind="text:expenseName"></span><span data-bind="visible: payment.expense_name">--</span><span data-bind="text:payment.expense_name"></span>
                                    </td>
                                    <td ><span data-bind="text:currency_ico"></span> <span data-bind="moneyText:payment.amount"></span> </td>
                                    <td ><span data-bind="text:currency_ico"></span> <span data-bind="moneyText:payment.amount_paid"></span> </td>
                                    <td ><span data-bind="text:currency_ico"></span> <span data-bind="moneyText:amount_balance"></span> </td>
                                    <td><span data-bind="text:currency_ico"></span> <span data-bind="moneyText:amount"></span> </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon" data-bind="text:currency_ico"></span>
                                            <input type="text" class="form-control input-sm"  placeholder="金额" data-bind="money:amount_paid">
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td>合计</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><span data-bind="text:currency_desc"></span> <span data-bind="moneyText:total_amount"></span> </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <?php
                } ?>
                <div class="form-group">
                    <?php
                    $attachType = $this->map["pay_stop_attachment_type"][21];
                    $attachments=AttachmentService::getAttachments(Attachment::C_PAYSTOP,$data["apply_id"], 21);
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
                                         files:<?php echo json_encode($attachments[21]); ?>,
                                         fileParams: {
                                            id:<?php echo empty($data['apply_id'])?0:$data['apply_id'] ?>
                                         }
                                     }
                         } -->
                        <!-- /ko -->
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">止付原因 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="stop_remark" name="obj[stop_remark]" rows="3" placeholder="止付原因" data-bind="value:stop_remark"></textarea>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <!-- <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save, html:saveBtnText"></button> -->
                        <button type="button" id="submitButton" class="btn btn-danger" data-bind="click:submit, html:submitBtnText"></button>
                        <button type="button" class="btn btn-default history-back" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[apply_id]' data-bind="value:apply_id"/>
                        <!-- <input type='hidden' name='obj[balance_amount]' data-bind="money:balance_amount"/> -->
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    var currencies=<?php echo json_encode($this->map["currency"]); ?>;
    var expenseNames=<?php echo json_encode($this->map["pay_type"]); ?>;
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
        view.formatPaymentPlans(<?php echo json_encode($payments) ?>);
		ko.applyBindings(view);
		
	});
	function ViewModel(option) {
		var defaults = {
			apply_id: "0",
            // balance_amount: 0.0,
			stop_remark: "",
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.apply_id = ko.observable(o.apply_id);
        // self.balance_amount = ko.observable(o.balance_amount);
		self.stop_remark = ko.observable(o.stop_remark).extend({required: true});

		self.fileUploadStatus=ko.observable();

        self.plans = ko.observableArray();
        self.formatPaymentPlans = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                var obj = new PaymentPlan(data[i]);
                self.plans.push(obj);
            }
        }

        self.currency_desc=ko.computed(function () {
            if(self.plans().length>=1 && currencies[self.plans()[0].payment.currency])
                return currencies[self.plans()[0].payment.currency]["ico"];
            else
                return "";
        },self);


        self.total_amount=ko.computed(function () {
            var total = 0;
            ko.utils.arrayForEach(self.plans(), function(item) {
                var value = parseFloat(item.amount_paid());
                if (!isNaN(value)) {
                    total += value;
                }
            });
            return total.toFixed(0);
        },self);

		// self.isSave = ko.observable(1);
        self.actionState = ko.observable(0);
        // self.saveBtnText = ko.observable("保存");
        self.submitBtnText = ko.observable("提交");
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        //保存
        /*self.save = function(){
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            self.pass();
        }*/

        self.submit = function(){
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            // self.isSave(0);

            layer.confirm("您确定要提交当前止付信息，改操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.pass();
                layer.close(index);
            });

        }

        self.pass = function () {
            // console.log(self.doneItems());
            var filter = ["saveBtnText", "submitBtnText", "isValid", "fileUploadStatus"];

            var formData = {"data": inc.getPostData(self,filter)};            

            if (self.actionState() == 1)
                return;
            self.submitBtnText("提交中" + inc.loadingIco);
            // if(self.isSave()==1)
            //     self.saveBtnText("保存中" + inc.loadingIco);
            // else
            //     self.submitBtnText("提交中" + inc.loadingIco);

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
                            location.href = "/<?php echo $this->getId() ?>/";
                            // location.href = "/<?php echo $this->getId() ?>/detail/?id=" + json.data;
                        });
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                    // self.saveBtnText("保存");
                    self.submitBtnText("提交");
                },
                error: function (data) {
                    // self.saveBtnText("保存");
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

    function PaymentPlan(option)
    {
        var defaults={
            plan_id:0,
            amount_paid:0,
            detail_id:0
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.plan_id=ko.observable(o.plan_id);
        self.detail_id=ko.observable(o.detail_id);
        self.amount = ko.observable(o.amount);
        self.payment=option.payment;
        self.amount_paid=ko.observable(o.amount_paid).extend({
                custom:{
                    params: function (v) {
                        return  parseFloat(v)<=parseFloat(self.amount()) && parseFloat(v)>=0;
                    },
                    message: "实付金额不能大于本次付款金额且不能小于0"
                }
            });
    
        /*if(self.payment.amount < self.payment.amount_paid){
            self.amount_balance=self.payment.amount-(self.payment.amount_paid-self.amount());  
        }else{
            self.amount_balance=self.payment.amount-self.payment.amount_paid;  
        }*/
        self.amount_balance=self.payment.amount-self.payment.amount_paid;
        
        self.payment.amount_paid=self.payment.amount_paid-self.amount();

    
        self.currency_ico=ko.computed(function () {
            if(currencies[self.payment.currency])
                return currencies[self.payment.currency]["ico"];
            else
                return "";
        },self);
        
        self.expenseName=ko.computed(function () {
            if(expenseNames[self.payment.expense_type])
                return expenseNames[self.payment.expense_type]["name"];
            else
                return "";
        },self);

        

    }
</script>