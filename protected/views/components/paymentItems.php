<template id='component-template-payments'>
    <table class="table table-hover table-hover-custom">
        <thead>
        <tr>
            <th style="width:148px;">预计<span data-bind="visible:upPayPlan">付款</span><span data-bind="visible:!upPayPlan()">收款</span>日期</th>
            <th style="width:226px; "><span class="label-custom__span-red">*</span><span data-bind="visible:upPayPlan">付款</span><span data-bind="visible:!upPayPlan()">收款</span>类别</th>
            <th style="width:111px;"><span class="label-custom__span-red">*</span>币种</th>
            <th style="width:186px; "><span class="label-custom__span-red">*</span>金额</th>
            <th style="width:259px;">备注</th>
            <th style="width:104px; ">操作</th>
        </tr>
        </thead>
        <tbody data-bind="foreach:items">
        <tr>
            <td>
                <input type="text" class="form-control input-sm date" placeholder="预计付款日期" data-bind="date:pay_date">
            </td>
            <td>
                <div class="form-inline">
                    <select class="form-control input-sm" title="请选择付款类别"
                            data-bind="
                            style:{width:expense_width},
                        optionsCaption: '请选择类别',
                        optionsText: 'name',
                        optionsValue: 'id',
                        options:$parent.paymentTypes,
                        value:expense_type,
                        valueAllowUnset: true">
                    </select>
                    <input type="text" title="请输入付款类别" class="form-control input-sm" name="" placeholder="请输入付款类别" data-bind="style:{width:expense_width},visible:showExpenseNameInput,value:expense_name">
                </div>
            </td>
            <td>
                <select class="form-control input-sm" title="币种"
                        data-bind="
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:$parent.currencies,
                    value:currency">
                </select>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-addon" data-bind="text:currency_ico"></span>
                    <input type="text" class="form-control input-sm"  placeholder="金额" data-bind="money:amount">
                </div>
            </td>
            <td>
                <input type="text" class="form-control input-sm"  placeholder="备注" data-bind="value:remark">
            </td>
            <td><button class="btn btn-xs delete-btn-custom" data-bind="click:$parent.del, visible:$index() > 0">删除</button></td>
        </tr>
        </tbody>
    </table>
    <div class="add-btn-container">
      <button class="btn  add-btn-custom" data-bind="click:add">+新增计划</button>
    </div>
</template>

<script>
	ko.components.register('payments', {
		template: {element: 'component-template-payments'},
		viewModel: paymentsComponent
	});
	function paymentsComponent(params) 
    {
        var self=this;
        self.type=params.type;//上下游类别，1上游收款，2下游付款
        self.items=params.items;
        self.paymentTypes=params.paymentTypes;
        self.currencies=params.currencies;
		self.exchange_rate = params.exchange_rate;

        self.paymentTypesObj={};
        ko.utils.arrayForEach(ko.unwrap(self.paymentTypes), function(item) {
            self.paymentTypesObj[ko.unwrap(item.id)]=item;
        });

        self.add=function()
        {

            var obj=new Payments({
                currencies:self.currencies,
                paymentTypes:self.paymentTypesObj,
				exchange_rate: self.exchange_rate(),
                type:self.type
            });
            self.items.push(obj);
        }
        self.upPayPlan=ko.computed(function () {
            return ko.unwrap(self.type)==1;
        },self);



        self.del=function(data)
        {
            if(self.items)
                self.items.remove(data);
        }
    }

	function Payments(option) 
    {
        var defaults={
        	plan_id:0,
            pay_date:(new Date()).format(),
            expense_type:"",
            amount:0,
            currency:1,
            expense_name:"",
            type:"",
            // payment_term:0,
            remark:"",
			exchange_rate: 1,
        };
        var o=$.extend(defaults,option);
        var self=this;

        self.currencies=[];
        if(o.currencies)
            self.currencies=ko.unwrap(o.currencies);
        self.paymentTypes=[];
        if(o.paymentTypes)
            self.paymentTypes=ko.unwrap(o.paymentTypes);
        self.plan_id = ko.observable(o.plan_id);
        self.type = ko.observable(o.type);
        self.pay_date=ko.observable(o.pay_date);
        self.expense_type=ko.observable(o.expense_type).extend({
            custom:{
                params: function (v) {
                    return v>0;
                },
                message: "请选择付款类别"
            }
        });

        self.showExpenseNameInput = ko.computed(function () {
            var t=self.paymentTypes[self.expense_type()];
            for (var i = self.paymentTypes.length - 1; i >= 0; i--) {
                if(self.paymentTypes[i].id == self.expense_type()) {
                    t = self.paymentTypes[i];
                    break;
                }
            }
            return (t && t.hasOwnProperty("type") && t["type"]==="input");
        }, self);
        self.expense_name=ko.observable(o.expense_name).extend({
            custom:{
                params: function (v) {
                    return (!self.showExpenseNameInput() || (v!=null && v!="") );
                },
                message: "请填写其他类别名称"
            }
        });
        self.currency=ko.observable(o.currency);
        // self.payment_term=ko.observable(o.payment_term).extend({intN0:true});
        self.remark=ko.observable(o.remark);
		self.exchange_rate = ko.observable(o.exchange_rate);
        self.amount=ko.observable(o.amount);
		self.amount_cny = ko.computed(function () { //人民币金额
			if (self.currency() == config.currencyDollar) {
				return (parseFloat(self.amount()) * parseFloat(self.exchange_rate())).toFixed(0);
			} else {
				return self.amount();
            }
		}, self);

        self.currency_ico=ko.computed(function () {
			return self.currencies[self.currency() - 1]["ico"];
        },self);

        self.expense_width=ko.computed(function () {
            if(self.showExpenseNameInput())
                return "47%";
            else
                return "95%";
        },self);

    }
</script>
