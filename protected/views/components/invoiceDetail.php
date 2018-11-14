<template id='component-template-invoice' >
    <table class="table table-hover">
        <thead>
        <tr>
            <th style="width:170px;"><span data-bind="visible:isDisplayGoods">品名</span><span data-bind="visible:!isDisplayGoods()">费用名称</span> <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:120px; text-align: left;" data-bind="visible:isDisplayGoods">数量 <span class="text-red fa fa-asterisk"></span></th>
            <!-- <th style="width:120px; text-align: left;" data-bind="visible:isDisplayGoods">单位 <span class="text-red fa fa-asterisk"></span></th> -->
            <th style="width:180px; text-align: left;">实际开票金额 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:120px; text-align: left;">开票日期 <span class="text-red fa fa-asterisk"></span></th>
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
                        units: $parent.units,
                        parentItems:$parent.items,
                        allGoods: $parent.allGoods,
                        goodsItems: $parent.goodsItems
                        }
        } -->
        <!-- /ko -->
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align: left;">合计</td>
                <!-- <td data-bind="visible:isDisplayGoods">&nbsp;</td> -->
                <td data-bind="visible:isDisplayGoods">&nbsp;</td>
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
        <td data-bind="visible:isDisplay">
            <div class="input-group">
                <input type="text" class="form-control input-sm" name= "c[quantity]" placeholder="数量" data-bind="value:quantity">
                <span class="input-group-addon" data-bind="html:unit_format"></span>
            </div>
            <span hidden data-bind="html:unit"></span>
            <span hidden data-bind="html:price"></span>
        </td>
        <!-- <td data-bind="visible:isDisplay"><input type="text" class="form-control input-sm" name= "c[quantity]" placeholder="数量" data-bind="value:quantity"></td>
        <td data-bind="visible:isDisplay">
            <select class="form-control input-sm" title="单位"  name="c[unit]"
                    data-bind="
                    optionsCaption:'请选择单位',
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:$parent.units,
                    value:unit,valueAllowUnset: true">
            </select>
        </td> -->
        <td data-bind="visible:!isDisplay()">
            <input type="text" class="form-control input-sm" name="c[invoice_name]" placeholder="费用名称" data-bind="value:invoice_name">
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-addon" >￥ </span>
                <input type="text" class="form-control input-sm" name="c[amount]" placeholder="金额" data-bind="money:amount,enable:!isDisplay()">
            </div>
        </td>
        <td>
            <input type="text" class="form-control input-sm date" placeholder="开票日期" data-bind="date:invoice_date">
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
        self.type_sub=params.type_sub;
        self.units=params.units;
        self.allGoods=params.allGoods;
        self.goodsItems=params.goodsItems;
        self.items=params.items;
        
        self.add=function()
        {
            var obj=new Invoice({
                allGoods:self.allGoods,
                goodsItems:self.goodsItems,
                project_id:self.project_id(),
                type_sub:self.type_sub(),
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

        self.isDisplayGoods=ko.computed(function () {
            return ko.unwrap(self.type_sub())==1;
        },self);
    }

    function Invoice(option)
    {
        var defaults={
            detail_id:0,
            goods_id:0,
            price:0.0,
            quantity:"",
            unit: 1,
            unit_format: "",
            rate: 0.0,
            amount:0,
            invoice_date:(new Date()).format(),
            project_id:0,
            contract_id:0,
            type_sub:0,
            invoice_name: ""
        };
        var o=$.extend(defaults,option);
        var self=this;

        self.goodsItems=[];
        if(o.goodsItems)
            self.goodsItems=ko.unwrap(o.goodsItems);

        self.detail_id=ko.observable(o.detail_id);
        self.type_sub=ko.observable(o.type_sub);
        self.invoice_date=ko.observable(o.invoice_date).extend({required:{params:true, message:"请填写开票日期"}});
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
        self.price=ko.computed(function () {
            if(self.goods_id()>0)
                return self.goodsItems[self.goods_id()]['price'];
            return 0.0;
        },self);
        /*self.unit=ko.observable(o.unit).extend({
        custom:{
            params: function (v) {
                if(ko.unwrap(self.type_sub())==2 || (ko.unwrap(self.type_sub())==1 && ko.unwrap(v)>0))
                    return true;
                else
                    return false;
            },
            message: "请选择单位"
        }});*/
        // self.unit=ko.observable(o.unit);
        // self.unit_format=ko.observable(o.unit_format);
        self.unit=ko.computed(function () {
            if(self.goods_id()>0)
                return self.goodsItems[self.goods_id()]['unit'];
            return 0;
        },self);
        self.unit_format=ko.computed(function () {
            if(self.goods_id()>0)
                return self.goodsItems[self.goods_id()]['unit_format'];
            return '';
        },self);
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
        self.project_id=ko.observable(o.project_id);
        self.contract_id=ko.observable(o.contract_id);
        // self.rate=ko.observable(o.rate);
        self.rate = ko.computed(function () {
            if(self.goods_id()>0)
                return self.goodsItems[self.goods_id()]['rate'];
            return 0.0;
        },self);
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
        self.amount=ko.observable(o.amount).extend({positiveNumber:{params:true, message:"请填写实际开票金额"}});

        /*self.unit_format=ko.computed(function () {
            return self.units[self.unit()]["name"];
        },self);*/
        self.quantity.subscribe(function(v){
            if(parseFloat(v)>0 && self.type_sub()==1){
                self.amount((parseFloat(self.price())*parseFloat(self.quantity())).toFixed(0));
            }
        });

        self.isDisplay=ko.computed(function () {
            return ko.unwrap(self.type_sub())==1;
        },self);
    }

    function invoiceItemComponent(params)
    {
        var self=this;
        self.units=params.units;
        self.allGoods=params.allGoods;
        self.goodsItems=params.goodsItems;
        self.model=params.model;

        self.del=function(data)
        {
            if(params.parentItems)
                params.parentItems.remove(data);
        }


    }



</script>
