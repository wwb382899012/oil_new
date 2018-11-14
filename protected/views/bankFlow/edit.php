<?php 
?>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">银行流水录入</h3>
        </div><!--end box box-header-->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="form-group">
                <label class="col-sm-2 control-label">银行流水编号<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="code" name= "obj[code]" placeholder="银行流水编号" data-bind="value:code" >
                </div>
                <label class="col-sm-2 control-label">交易主体<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-4">
                    <select class="form-control selectpicker" title="交易主体" id="ownership" name="obj[ownership]" data-live-search="true" data-bind="options:corporations,optionsText:'name',optionsCaption: '交易主体',value: corporation_id, optionsValue:'corporation_id',valueAllowUnset: true">
                    </select>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label">收款银行<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-10">
                    <select class="form-control" title="收款银行" id="ownership" name="obj[ownership]" data-bind="options:selectedAccounts(),optionsText:function(item) {
                       return item.bank_name + '--' + item.account_no
                   },optionsCaption: '收款银行',value: account, optionsValue:'account_id',valueAllowUnset: true">
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">银行账户名<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="account" name= "obj[account_name]" placeholder="银行账户名" data-bind="value:account_name">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">付款公司<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-4">
                    <input class="form-control" data-bind="value: pay_partner" />
                </div>
                <label class="col-sm-2 control-label">收款时间<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="receive_date" name= "obj[receive_date]" placeholder="收款时间" data-bind="date:receive_date">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">付款银行<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="pay_bank" name= "obj[pay_bank]" placeholder="付款银行" data-bind="value:pay_bank" >
                </div>
                <label class="col-sm-2 control-label">收款金额<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="amount" name= "obj[amount]" placeholder="收款金额" data-bind="money:amount">
                        <span class="input-group-addon" data-bind="moneyChineseText:amount"></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">币种<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-4">
                    <select class="form-control" title="币种" id="currency" name="obj[currency]" data-bind="options:currencies,optionsText:'name',optionsCaption: '币种',value: currency, optionsValue:'id',valueAllowUnset: true">
                    </select>
                </div>
                <label class="col-sm-2 control-label">汇率<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="code" name= "obj[exchange_rate]" placeholder="汇率" data-bind="value:exchange_rate">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-10">
                    <textarea type="text" class="form-control" id="remark" name= "obj[remark]" placeholder="备注" data-bind="value:remark" ></textarea>
                </div>
            </div>

            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save, html:saveBtnText"></button>
                        <button type="button" id="submitButton" class="btn btn-danger" data-bind="click:submit, html:submitBtnText"></button>
                        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                    </div>
                </div>
            </div>
        </form>
    </div><!--end box box-primary-->

</section><!--end content-->


<script>
    var view;
    $(function () {
        view=new ViewModel(
            <?php 
            $defaults = array(
                    'corporations'=>Corporation::getActiveCorporations(), 
                    'allAccounts'=>Account::getActiveAccounts(), 
                    'currencies'=>array_values($this->map['currency'])
                    );
            $data = array_merge($defaults, $bankFlow) ;
            echo json_encode($data);
            ?>);
        view.corporations = <?php echo json_encode(Corporation::getActiveCorporations());?>;
        ko.applyBindings(view);
        $("#start_date").datetimepicker({format:'yyyy-mm-dd',minView:'month'});
       // $("#bank_account").bankInput({min:1,max:50,deimiter:' '});
    });

    function ViewModel(option){
        var defaults={
            flow_id:'',
            code:"",
            name:"",
            corporation_id:"",
            corporations:[],
            account:'',
            allAccounts:[],
            account_name:'',
            pay_partner:'',
            pay_bank:'',
            amount:'',
            currency:'',
            currencies:[],
            receive_date:(new Date()).format(),
            exchange_rate:1,
            remark:"",

        };
        var o=$.extend(defaults,option);
        var self=this;
        self.flow_id = o.flow_id;
        self.code = ko.observable(o.code).extend({required:true});
        self.corporation_id = ko.observable(o.corporation_id).extend({required:true});
        self.corporations = o.corporations;
        self.account = ko.observable(o.account).extend({required:true});
        self.allAccounts = o.allAccounts;
        self.account_name = ko.observable(o.account_name).extend({required:true});
        self.pay_partner = ko.observable(o.pay_partner).extend({required:true});
        self.pay_bank = ko.observable(o.pay_bank).extend({required:true});
        self.amount = ko.observable(o.amount).extend({required:true});
        self.currency = ko.observable(o.currency).extend({required:true});
        self.currencies = o.currencies;
        self.receive_date = ko.observable(o.receive_date).extend({date: true, custom:{
            params: function (v) {
                var today = new Date();
                var target = new Date(v);
                var diff = target.getTime() - today.getTime();
                console.log(diff)
                diff = Math.ceil(diff / 86400000);
                console.log(diff)
                return (diff >= -365 && diff <= 2);
            },
            message: "需过去一年到未来两天之间"
        }});
        self.exchange_rate = ko.observable(o.exchange_rate);
        self.remark = ko.observable(o.remark);
        self.status = ko.observable(0);
        self.selectedAccounts = ko.computed(function() {
            var accounts = [];
            for (var i = self.allAccounts.length - 1; i >= 0; i--) {
                if(self.allAccounts[i].corporation_id == self.corporation_id()) {
                    accounts.push(self.allAccounts[i]);
                } 
            }
            return accounts;
        }, self);

        self.currency.subscribe(function(newValue) {
            if(newValue != 1) {
                self.exchange_rate(0);
            }
        });

        self.exchange_rate.extend({custom:{
                params: function (v) {
                    return !(self.currency() != 1 && v == 0);
                },
                message: "请填写汇率"
            }});

        self.getPostData = function () {
            return inc.getPostData(self, ["corporations", "selectedAccounts", "allAccounts", "currencies","isSubmit","submitBtnText","saveBtnText"]);
        }

        self.errors=ko.validation.group(self);

        self.isValid=function () {
            return self.errors().length===0;
        }

        self.submit=function () {
            self.status(1);
            self.post();
        }

        self.save=function () {
            self.status(0);
            self.post();
        }

        self.post=function() {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }
            var formData=self.getPostData();
            self.isSubmit(1);
            $.ajax({
                type:"POST",
                url:"/<?php echo $this->getId() ?>/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        layer.msg("保存成功", {icon: 6, time:1000},function() {
                            location.href="/<?php echo $this->getId() ?>/detail?id="+json.data;
                        });
                    }else{
                        layer.alert(json.data, {icon: 5});
                        self.submitBtnText("提交");
                        self.saveBtnText("保存");
                        self.isSubmit(0);
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error:function (data) {
                    layer.alert("保存失败！"+data.responseText, {icon: 5});
                    self.submitBtnText("提交");
                    self.saveBtnText("保存");
                    self.isSubmit(0);
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
        }

        self.back=function () {
            window.location.href="/<?php echo $this->getId() ?>/";
        }


        self.isSubmit = ko.observable(0);
        self.submitBtnText = ko.observable("提交");
        self.saveBtnText = ko.observable("保存");

    }
</script>