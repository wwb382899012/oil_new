<template id='component-template-invoice' >
    <table class="table table-hover">
        <thead>
        <tr>
            <th style="width:170px;"><span data-bind="visible:isDisplayGoods">品名</span><span data-bind="visible:!isDisplayGoods()">费用名称</span> <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:120px; text-align: left;" data-bind="visible:isDisplayGoods">数量 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:120px; text-align: left;" data-bind="visible:isDisplayGoods">单位 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:120px; text-align: left;" data-bind="visible:isDisplayGoods">单价</th></span>
            <th style="width:240px; text-align: left;">税率 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:140px; text-align: left;">金额</th>
            <th style="text-align: left;"><button class="btn btn-success btn-xs" data-bind="click:add">新增</button></th>
        </tr>
        </thead>
        <tbody data-bind="foreach:items">
        <!-- ko component: {
            name: "invoice-item",
            params: {
                        model: $data,
                        project_id:$parent.project_id,
                        contract_id:$parent.contract_id,
                        exchange_rate:$parent.exchange_rate,
                        parentItems:$parent.items,
                        allGoods: $parent.allGoods,
                        rates: $parent.rates,
                        units: $parent.units
                        }
        } -->
        <!-- /ko -->
        </tbody>
        <tfoot>
            <tr data-bind="visible:isDisplayDollar">
                <td style="text-align: left;vertical-align: middle;" rowspan="2">合计</td>
                <td data-bind="visible:isDisplayGoods" rowspan="2">&nbsp;</td>
                <td data-bind="visible:isDisplayGoods" rowspan="2">&nbsp;</td>
                <td data-bind="visible:isDisplayGoods" rowspan="2">&nbsp;</td>
                <td rowspan="2">&nbsp;</td>
                <td style="text-align: left;">￥ &nbsp;<span data-bind="moneyText:total_amount"></span></td>
                <td rowspan="2">&nbsp;</td>
            </tr>
            <tr data-bind="visible:isDisplayDollar">
                <td style="text-align: left;">$&emsp;<span data-bind="moneyText:dollar_amount"></span></td>
            </tr>
            <tr data-bind="visible:!isDisplayDollar()">
                <td style="text-align: left;">合计</td>
                <td data-bind="visible:isDisplayGoods">&nbsp;</td>
                <td data-bind="visible:isDisplayGoods">&nbsp;</td>
                <td data-bind="visible:isDisplayGoods">&nbsp;</td>
                <td>&nbsp;</td>
                <td style="text-align: left;">￥ <span data-bind="moneyText:total_amount"></span></td>
                <td>&nbsp;</td>
            </tr>
        </tfoot>
    </table>
</template>
<template id='component-template-invoice-item' >
    <tr data-bind="with:model">
        <td data-bind="visible:isDisplay">
            <select class="form-control input-sm" title="请选择品名"  name="c[goods_id]"
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'goods_id',
                    options:$parent.allGoods,
                    selectpicker:{value:goods_id},
                    valueAllowUnset: true">
            </select>
        </td>
        <td data-bind="visible:isDisplay"><input type="text" class="form-control input-sm" name= "c[quantity]" placeholder="数量" data-bind="value:quantity"></td>
        <td data-bind="visible:isDisplay">
            <select class="form-control input-sm" title="单位"  name="c[unit]"
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:$parent.units,
                    value:unit,enable:false">
            </select>
        </td>
        <td style="text-align: left;" data-bind="visible:isDisplay">￥ <span data-bind="moneyText:price"></span></td>
        <td data-bind="visible:!isDisplay()">
            <input type="text" class="form-control input-sm" name="c[invoice_name]" placeholder="费用名称" data-bind="value:invoice_name">
        </td>
        <td>
            <div class="form-inline">
                <select class="form-control input-sm" title="请选择税率"
                        data-bind="
                        style:{width:rate_width},
                    optionsCaption: '请选择税率',
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:$parent.rates,
                    value:rate_type,
                    valueAllowUnset: true">
                </select>
                <div class="input-group" data-bind="style:{width:rate_width},visible:showRateDescInput">
                    <input type="text" title="请输入税率" class="form-control input-sm" name="" placeholder="请输入税率" data-bind="percent:rate">
                    <span class="input-group-addon" >%</span>
                </div>
            </div>
            <!-- <select class="form-control input-sm" title="税率"  name="c[rate]"
                    data-bind="
                    optionsCaption:'请选择税率',
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:$parent.rates,
                    value:rate,valueAllowUnset: true">
            </select>
            <div class="input-group">
                <input type="text" class="form-control input-sm" name="c[rate_desc]" placeholder="税率" data-bind="percent:rate">
                <span class="input-group-addon" >%</span>
            </div> -->
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-addon" >￥ </span>
                <input type="text" class="form-control input-sm" name="c[amount]" placeholder="金额" data-bind="money:amount">
            </div>
        </td>
        <td><button class="btn btn-danger btn-xs" data-bind="click:$parent.del">删除</button></td>
    </tr>
</template>

<script>
    ko.components.register('invoice-item', {
        template: { element: 'component-template-invoice-item' },
        viewModel:invoiceItemComponent
    });

    ko.components.register('invoice', {
        template: { element: 'component-template-invoice' },
        viewModel:invoiceComponent
    });
    function invoiceComponent(params)
    {
        var self=this;
        self.project_id=params.project_id;
        self.contract_id=params.contract_id;
        self.exchange_rate=params.exchange_rate;
        self.type_sub=params.type_sub;
        self.allGoods=params.allGoods;
        self.units=params.units;
        self.rates=params.rates;
        self.items=params.items;

        self.ratesObj={};
        ko.utils.arrayForEach(ko.unwrap(self.rates), function(item) {
            self.ratesObj[ko.unwrap(item.id)]=item;
        });
        
        self.add=function()
        {
            var obj=new Invoice({
                allGoods:self.allGoods,
                project_id:self.project_id(),
                exchange_rate:self.exchange_rate(),
                type_sub:self.type_sub(),
                rates:self.ratesObj,
                contract_id:self.contract_id()
            });
            self.items.push(obj);
        }
        self.total_amount=ko.computed(function () {
            var total = 0;
            ko.utils.arrayForEach(self.items(), function(item) {
                var value = parseFloat(item.amount());
                if (!isNaN(value)) {
                    total += value;
                }
            });
            return total.toFixed(0);
        },self);
        
        self.dollar_amount=ko.computed(function () {
            return (parseFloat(self.total_amount())/parseFloat(self.exchange_rate())).toFixed(0);
        },self);

        self.isDisplayGoods=ko.computed(function () {
            return ko.unwrap(self.type_sub())==1;
        },self);

        self.isDisplayDollar=ko.computed(function () {
            return (parseFloat(self.exchange_rate())>0 && parseFloat(self.exchange_rate())!=1);
        },self);

        // console.log(self.isDisplayGoods());


        /*self.currency_ico=ko.computed(function () {
                return self.currencies()[self.currency() - 1]["ico"];
        },self);

        self.upExchange=ko.computed(function () {
            return ko.unwrap(self.exchange_type)==1;
        },self);

        self.needTarget=ko.computed(function () {
            return ko.unwrap(self.price_type)==2 && ko.unwrap(self.exchange_type)==1;
        },self);*/
    }

    function Invoice(option)
    {
        var defaults={
            detail_id:0,
            goods_id:0,
            price:0,
            quantity:"",
            amount:0,
            unit:"",
            rate:"",
            rate_type: "",
            exchange_rate:"",
            project_id:0,
            contract_id:0,
            type_sub:0,
            invoice_name: ""
        };
        var o=$.extend(defaults,option);
        var self=this;

        self.rates=[];
        if(o.rates)
            self.rates=ko.unwrap(o.rates);

        self.detail_id=ko.observable(o.detail_id);
        self.type_sub=ko.observable(o.type_sub);
        self.goods_id=ko.observable(o.goods_id).extend({
        custom:{
            params: function (v) {
                if(ko.unwrap(self.type_sub())==2 || (ko.unwrap(self.type_sub())==1 && ko.unwrap(v)>0))
                    return true;
                else
                    return false;
            },
            message: "请选择品名"
        }});
        // self.price=ko.observable(o.price);
        self.quantity=ko.observable(o.quantity).extend({
        custom:{
            params: function (v) {
                if(ko.unwrap(self.type_sub())==2 || (ko.unwrap(self.type_sub())==1 && ko.unwrap(v)>0))
                    return true;
                else
                    return false;
            },
            message: "请填写数量"
        }});
        self.unit=ko.observable(o.unit).extend({
        custom:{
            params: function (v) {
                if(ko.unwrap(self.type_sub())==2 || (ko.unwrap(self.type_sub())==1 && ko.unwrap(v)>0))
                    return true;
                else
                    return false;
            },
            message: "请选择单位"
        }});
        self.project_id=ko.observable(o.project_id);
        self.rate_type=ko.observable(o.rate_type).extend({
            custom:{
                params: function (v) {
                    return v>0;
                },
                message: "请选择税率"
            }
        });
        // self.rate=ko.observable(o.rate).extend({required:{params:true, message:"请选择税率"}});
        self.exchange_rate=ko.observable(o.exchange_rate);
        self.contract_id=ko.observable(o.contract_id);
        self.invoice_name=ko.observable(o.invoice_name).extend({
        custom:{
            params: function (v) {
                if(ko.unwrap(self.type_sub())==1 || (ko.unwrap(self.type_sub())==2 && ko.unwrap(v)!=""))
                    return true;
                else
                    return false;
            },
            message: "请填写费用名称"
        }});
        self.amount=ko.observable(o.amount).extend({positiveNumber:{params:true, message:"请填写金额"}});

        self.price=ko.computed(function () {
            if(self.quantity()>0){
                return (parseFloat(self.amount())/parseFloat(self.quantity())).toFixed(0);
            }else{
                return 0;
            }
        },self);

        /*self.allGoods = o.allGoods;
        self.goods_name = ko.computed(function () {
            if(self.allGoods.length > 0) {
                for (var i in self.allGoods) {
                    if(self.allGoods[i].goods_id == self.goods_id()){
                        return self.allGoods[i].name;
                    }
                }
            }
        },self);*/

        self.isDisplay=ko.computed(function () {
            return ko.unwrap(self.type_sub())==1;
        },self);

        self.showRateDescInput = ko.computed(function () {
            var t=self.rates[self.rate_type()];
            for (var i = self.rates.length - 1; i >= 0; i--) {
                if(self.rates[i].id == self.rate_type()) {
                    t = self.rates[i];
                    break;
                }
            }
            return (t && t.hasOwnProperty("type") && t["type"]==="input");
        }, self);
        self.rate=ko.observable(o.rate).extend({
            custom:{
                params: function (v) {
                    return (!self.showRateDescInput() || (v!=null && v!="") );
                },
                message: "请填写税率"
            }
        });

        self.rate_width=ko.computed(function () {
            if(self.showRateDescInput())
                return "47%";
            else
                return "95%";
        },self);
    }

    function invoiceItemComponent(params)
    {
        var self=this;
        self.units=params.units;
        self.rates=params.rates;
        self.allGoods=params.allGoods;
        self.model=params.model;

        self.del=function(data)
        {
            if(params.parentItems)
                params.parentItems.remove(data);
        }


    }



</script>
