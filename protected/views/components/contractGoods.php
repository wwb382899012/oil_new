<template id='component-template-contract-goods' >
    <table class="table table-hover table-hover-custom ">
        <thead>
            <tr>
              <th style="width:224px; "><span class="label-custom__span-red">*</span>品名</th>
              <th style="width:145px;"><span data-bind="visible:needTarget"><span class="label-custom__span-red">*</span></span>计价标的</th>
              <th style="width:145px;">溢短装比</span></th>
              <th style="width:109px; "><span class="label-custom__span-red">*</span>数量</th>
              <th style="width:110px;"><span class="label-custom__span-red">*</span>单位</th>
              <th style="width:110px;"><span class="label-custom__span-red">*</span>单位换算比</th>
              <th style="width:130px; "><span class="label-custom__span-red">*</span><span data-bind="visible:upExchange">采购</span><span data-bind="visible:!upExchange()">销售</span>单价</th>
              <th style="width:130px; "><span data-bind="visible:upExchange">采购</span><span data-bind="visible:!upExchange()">销售</span>总价</th>
              <th style="width:180px;"><span data-bind="visible:upExchange">采购</span><span data-bind="visible:!upExchange()">销售</span>人民币总价</th>
              <th style="width:90px; ">操作</th>
            </tr>
        </thead>
        <tbody data-bind="foreach:items">
        <!-- ko component: {
            name: "contract-goods-item",
            params: {
                        model: $data,
                        exchange_type:$parent.exchange_type,
                        price_type:$parent.price_type,
                        parentItems:$parent.items,
                        allGoods: $parent.allGoods,
                        units:$parent.units,
                        exchange_rate:$parent.exchange_rate,
                        currency:$parent.currency
                        }
        } -->
        <!-- /ko -->
        </tbody>
        <tfoot>
        <tr>
            <td style="text-align: center;">合计</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
            <td style="text-align: right;"><span data-bind="text:currency_ico"></span> <span data-bind="moneyText:amount"></span></td>
            <td style="text-align: right;">￥ <span data-bind="moneyText:amount_cny"></span></td>
            <td >&nbsp;</td>
        </tr>
        </tfoot>
    </table>
    <div class="add-btn-container">
      <button class="btn add-btn-custom" data-bind="click:add">+新增商品</button>
      <!-- <button class="btn  add-btn-custom" data-bind="click:add">+新增商品</button> -->
    </div>
</template>
<template id='component-template-contract-goods-item' >
    <tr data-bind="with:model">
        <td>
            <select class="form-control input-sm" title="请选择品名"  name="c[goods_id]"
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'goods_id',
                    options:$parent.allGoods,
                    selectpicker:{value:goods_id},
                    valueAllowUnset: true">
            </select>
        </td>
        <td>
            <input type="text" class="form-control input-sm" name= "c[refer_target]" placeholder="计价标的" data-bind="value:refer_target">
        </td>
        <td>
            <div class="input-group  input-group-sm">
                <input type="text" class="form-control"  name= "c[more_or_less_rate]" placeholder="溢短装比例" data-bind="percent:more_or_less_rate" >
                <span class="input-group-addon">%</span>
            </div>
        </td>
        <td><input type="text" class="form-control input-sm" name= "c[quantity]" placeholder="数量" data-bind="value:quantity"></td>
        <td>
            <select class="form-control input-sm" title="单位"  name="c[unit]"
                    data-bind="
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:$parent.units,
                    value:unit">
            </select>
        </td>
        <td style="text-align: right;vertical-align: baseline!important;">
           <!-- <span data-bind="text:unit_convert"></span>
            <input type="text" class="form-control input-sm" name="c[unit_convert_rate]" placeholder="单价" data-bind="value:unit_convert_rate">-->
            <div class="input-group" data-bind="visible: showUnitConvert">
                <span class="input-group-addon" data-bind="text:unit_convert"></span>
                <input type="text" class="form-control input-sm" name="" placeholder="" data-bind="value:unit_convert_rate">
            </div>
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-addon" data-bind="text:currency_ico"></span>
                <input type="text" class="form-control input-sm" name="c[price]" placeholder="单价" data-bind="money:price">
            </div>
        </td>

        <td style="text-align: right;vertical-align: baseline!important;">
            <span data-bind="text:currency_ico"></span>
            <span data-bind="moneyText:amount"></span>
        </td>
        <td style="text-align: right;vertical-align: baseline!important;">
            ￥ <span data-bind="moneyText:amount_cny"></span>
        </td>
        <td><button class="btn btn-xs delete-btn-custom" data-bind="click:$parent.del">删除</button></td>
    </tr>
</template>

<script>
    ko.components.register('contract-goods-item', {
        template: { element: 'component-template-contract-goods-item' },
        viewModel:contractGoodsItemComponent
    });

    ko.components.register('contract-goods', {
        template: { element: 'component-template-contract-goods' },
        viewModel:contractGoodsComponent
    });
    function contractGoodsComponent(params)
    {
        var self=this;
        self.exchange_type=params.exchange_type;
        self.price_type=params.price_type;
        self.allGoods=params.allGoods;
        self.units=params.units;
        self.currencies=params.currencies;
        self.items=params.items;
        self.currency=params.currency;
        self.exchange_rate=params.exchange_rate;

        self.needTarget=ko.computed(function () {
            return ko.unwrap(self.price_type)==2 && ko.unwrap(self.exchange_type)==1;
        },self);

        ko.utils.arrayForEach(self.items(),function (item) {
           item.refer_target.extend({
               custom:{
                   params: function (v) {
                       return !self.needTarget() || (v!=null && v!="");
                   },
                   message: "请填写计价标的"
               }
           });
        });

        self.add=function()
        {
            var obj=new ContractGoods({
                currencies:self.currencies,
                allGoods:self.allGoods,
                currency:self.currency(),
                exchange_rate:self.exchange_rate(),
                exchange_type:self.exchange_type(),
                price_type:self.price_type()
            });
            obj.refer_target.extend({
                custom:{
                    params: function (v) {
                        return !self.needTarget() || (v!=null && v!="");
                    },
                    message: "请填写计价标的"
                }
            });
            self.items.push(obj);
        }

        self.amount=ko.computed(function () {
            var total = 0;
            ko.utils.arrayForEach(self.items(), function(item) {
                var value = parseFloat(item.amount());
                if (!isNaN(value)) {
                    total += value;
                }
            });
            return total.toFixed(0);
        },self);


        self.amount_cny=ko.computed(function () {
            var total = 0;
            ko.utils.arrayForEach(self.items(), function(item) {
                var value = parseFloat(item.amount_cny());
                if (!isNaN(value)) {
                    total += value;
                }
            });
            return total.toFixed(0);
        },self);

        self.currency_ico=ko.computed(function () {
                return self.currencies()[self.currency() - 1]["ico"];
        },self);

        self.upExchange=ko.computed(function () {
            return ko.unwrap(self.exchange_type)==1;
        },self);


    }

    function ContractGoods(option)
    {
        var defaults={
        	detail_id:0,
            goods_id:0,
            price:0,
            quantity:0,
            amount:0,
            amount_cny:0,
            unit:0,
            more_or_less_rate:0,
            currency:1,
            exchange_rate:1,
            // goods_describe:"",
            refer_target:"",
            type:1,
            agent_price:0,
            agent_unit:0,
            fee_rate:0,
            agent_amount:0,
            agent_detail_id:0,
            exchange_type:0,
            unit_convert_rate:1.0000
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.detail_id=ko.observable(o.detail_id);
        self.agent_detail_id=ko.observable(o.agent_detail_id);
        self.goods_id=ko.observable(o.goods_id);
        self.type=ko.observable(o.type);
        self.price=ko.observable(o.price);
        self.refer_target=ko.observable(o.refer_target);
        self.more_or_less_rate=ko.observable(o.more_or_less_rate);
        self.quantity=ko.observable(o.quantity);
        self.unit=ko.observable(o.unit);
        self.unit.subscribe(function(v){
            if(v==view.contractGoodsUnitConvertValue())
                self.unit_convert_rate('1.0000');
        });
        self.currency=ko.observable(o.currency);
        self.exchange_rate=ko.observable(o.exchange_rate);
        self.fee_rate=ko.observable(o.fee_rate);
        self.exchange_type = ko.observable(o.exchange_type);

        self.amount=ko.computed(function () {
            return (parseFloat(self.price())*parseFloat(self.quantity())).toFixed(0);
        },self);
        self.amount_cny=ko.computed(function () {
            return (parseFloat(self.amount())*parseFloat(self.exchange_rate())).toFixed(0);
        },self);

        self.currencies=o.currencies;

        self.currency_ico=ko.computed(function () {
            return self.currencies()[self.currency() - 1]["ico"];
        },self);

        self.showUnitConvert = ko.computed(function () {
            if(self.unit()==view.contractGoodsUnitConvertValue())
                return false
            else
                return true;
        }, self);

        self.unit_convert = ko.computed(function () {
            for (var i in view.units){
                if(view.units[i].id == self.unit()) {
                    return view.units[i].name + '/' + view.contractGoodsUnitConvert();
                }
            }
        }, self);

        self.unit_convert_rate = ko.observable(o.unit_convert_rate).extend({
            custom: {
                params: function (v) {
                    if (self.showUnitConvert()) {
                        if(v==null || v==''|| v==0 || isNaN(v))
                            return false;
                    }
                    return true;
                },
                message: "请输入不小于0的数字"
            }
        });
        self.more_or_less_rate.extend({
            custom:{
                params: function (v) {
                    return v >= 0;
                },
                message: "溢短装比必须大于0"
            }
        });

        self.allGoods = o.allGoods;
        self.goods_name = ko.computed(function () {
            if(self.allGoods().length > 0) {
				for (var i in self.allGoods()) {
					if(self.allGoods()[i].goods_id == self.goods_id()){
						return self.allGoods()[i].name;
                    }
				}
            }
		},self);
        //代理费计算
        self.agent_price=ko.observable(o.agent_price);
        self.fee_rate=ko.observable(o.fee_rate);

        self.isRateDisable =ko.computed(function () {
            return self.type()==config.agentFeeCalculateByAmount;
        });
        self.isRateAgentPriceDisable = ko.computed(function () {
            return self.type()==config.agentFeeCalculateByPrice;
        });
        self.isAgentunitDisable = ko.computed(function () {
            return self.type()==config.agentFeeCalculateByPrice;
        });
        self.agent_unit = ko.computed(function () {
            return self.unit();
		},self);
        self.agent_amount = ko.computed(function () {
            if(self.type() == config.agentFeeCalculateByAmount) {
                return (parseFloat(self.agent_price())*parseFloat(self.quantity())).toFixed(0);
            } else if(self.type() == config.agentFeeCalculateByPrice) {
                return (parseFloat(self.fee_rate())*parseFloat(self.amount_cny())).toFixed(0);
            }
            else
                return 0;
        });
    }

    function contractGoodsItemComponent(params)
    {
        var self=this;
        self.allGoods=params.allGoods;
        self.units=params.units;
        self.model=params.model;
        self.model.currency(params.currency());
        self.model.exchange_rate(params.exchange_rate());

        params.currency.subscribe(function(v){
            self.model.currency(v);
        });

        params.exchange_rate.subscribe(function(v){
            self.model.exchange_rate(v);
        });

        self.del=function(data)
        {
            if(params.parentItems)
                params.parentItems.remove(data);
        }


    }



</script>
