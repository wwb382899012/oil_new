var settlementConfigs={
    tariffTax:3,
    adjust_type_add:1,
    currency:{
        cny:1,
        default:{"id":1,"name":"人民币","ico":"￥"}
    },
    settle_type:{
        bill:1,//提单或发货单
        contract:2//合同
    },
    units:[],
    getUnit:function(unit)
    {
        if(unit==null)
            return "";
        if(this.units[unit])
            return this.units[unit]["name"];
        //return this.units;
    },
    currencies:[],
    getCurrencyIco : function(currency)
    {
        if(currency==null)
            return "";
        if(this.currencies[currency])
            return this.currencies[currency]["ico"];
    },
    tax:{
        excise :1,//消费税
        tariff:3//关税
    },
    taxRate:{
        2:0.16
    }
};


function SettlementViewModel(params) {
    var self=this;
    //console.log(params);
    self.settlement=new Settlement(params);

    // self.controllerName=controller_name;
    self.controllerName = settlementConfigs.controllerName;

    self.actionState=ko.observable(0);
    self.saveBtnText=ko.observable("保存");
    self.tempSaveBtnText=ko.observable("暂存");

    self.errors=ko.validation.group(self);
    self.isValid=function () {
        return self.errors().length===0;
    }

    self.back = function () {
        /*if( document.referrer === '')
            location.href="/"+self.controllerName+"/";
        else
            history.back();*/
        location.href="/"+self.controllerName+"/";
    }



    self.getPostData=function () {
        return {"settlement":inc.toJSON(ko.toJS(self.settlement))};
    }

    //暂存
    self.tempSave = function () {
        self.settlement.save_status(1);
        self.tempSaveBtnText("暂存中" + inc.loadingIco);
        self.submit();
    }
    //保存
    self.save = function() {
        if (!self.isValid()) {
            self.errors.showAllMessages();
            return;
        }
        self.settlement.save_status(2);
        self.saveBtnText("保存中" + inc.loadingIco);
        self.submit();
    }
    self.submit=function ()
    {
       /* if(!self.isValid()){
            self.errors.showAllMessages();
            return;
        }*/

        if(self.actionState()==1)
            return;
        self.actionState(1);

        var formData=self.getPostData();
        //console.log(formData);return;
        $.ajax({
            type:"POST",
            url:"/"+self.controllerName+"/save",
            data:formData,
            dataType:"json",
            success:function (json) {
                self.actionState(0);
                self.tempSaveBtnText("暂存");
                self.saveBtnText("保存");
                if(json.state==0){
                    // inc.vueMessage('操作成功');
                    inc.vueMessage({
                        message: '操作成功',duration:500, onClose: function () {
                            if(self.settlement.save_status() == 2) {
                                location.href = '/'+self.controllerName+'/detail?id='+self.settlement.bill_id();
                            }
                        }
                    });
                    
                }else{
					inc.vueAlert(json.data);
                }
            },
            error:function (data) {
                self.tempSaveBtnText("暂存");
                self.saveBtnText("保存");
                self.actionState(0);
				inc.vueAlert("保存失败："+data.responseText);
                self.actionState(0);
            }
        });
    }
}

/**
 * 结算单
 * @param params
 */
function Settlement(params){
    var defaults={
        "settle_id": 0,
        "contract_id": 0,
        "bill_id": 0,
        "bill_code":"",
        "project_id": 0,
        "partner_id": 0,
        "corporation_id": 0,
        "settle_date": inc.getNowDate(),
        "status": 0,
        "settle_currency": settlementConfigs.currency.default,
        "settle_type": 1,
        "goods_amount": 0,
        "other_amount": 0,
        "save_status": 0,
        "remark":""
    };
    params=inc.clearNullProperty(params);
    var o=$.extend(defaults,params);
    //console.log(o);
    var self=this;

    self.settle_id=ko.observable(o.settle_id);
    self.settle_date=ko.observable(o.settle_date).extend({required:true});
    self.bill_id=ko.observable(o.bill_id);
    self.bill_code=ko.observable(o.bill_code);
    self.status=ko.observable(o.status);
    self.settle_currency=ko.observable(o.settle_currency.id);
    self.settle_type=ko.observable(o.settle_type);
    self.remark=ko.observable(o.remark);
    self.save_status=ko.observable(o.save_status);
    /*self.goods_amount=ko.observable(o.goods_amount);
    self.other_amount=ko.observable(o.other_amount);*/

    //货款
    self.goodsItems=ko.observableArray();

    self.formatGoodsItems = function (data) {
        if (data == null || data == undefined)
            return;
        for (var i in data) {
            var obj = new GoodsSettlement(data[i]);
            self.goodsItems.push(obj);
        }
    }

    //非货款
    self.otherExpenseItems=ko.observableArray();

    self.formatOtherExpenseItems = function (data) {
        if (data == null || data == undefined)
            return;
        for (var i in data) {
            var obj = new OtherExpense(data[i]);
            self.otherExpenseItems.push(obj);
        }
    }

    self.goods_amount=ko.computed(function () {
        var amount=0;
       if((self.settle_type()==1 && params.lading_bills!=null) || (self.settle_type()==3 && params.delivery_orders!=null)){
            amount =parseInt(params.goods_amount);
        }else{
            ko.utils.arrayForEach(self.goodsItems(),function (item) {
                amount+=Math.round(parseFloat(item.amount_cny()));
            });
        }

        return amount;
    },self);


    self.other_amount=ko.computed(function () {
        var amount=0;
        ko.utils.arrayForEach(self.otherExpenseItems(),function (item) {
            amount+=Math.round(parseFloat(item.amount_cny()));
        });
        return amount;
    },self);

    self.amount=ko.computed(function () {
        return Math.round(self.goods_amount()+self.other_amount());
    },self);

    //提单或发货单
    self.bills=[];

    self.otherExpenseSubjects=ko.observable(settlementConfigs.otherExpenseSubjects);

    self.addOtherExpense=function()
    {
        self.otherExpenseItems.push(new OtherExpense({subjects:self.otherExpenseSubjects}));
    }
    self.removeOtherExpense=function(item)
    {
        self.otherExpenseItems.remove(item);

        var subjects=self.otherExpenseSubjects();
        if(subjects[item.subject_id()] != undefined){
            subjects[item.subject_id()]["status"]=0;
            self.otherExpenseSubjects(subjects);
        }
    }

    //可选的币种信息
    self.currencies=inc.objectToArray(settlementConfigs.currencies);

    self.init=function (data) {
        if(data==null)
            return;
        if(data.hasOwnProperty("settlementGoods") && data.settlementGoods!=null)
        {
            ko.utils.arrayForEach(data.settlementGoods,function (item) {
                item.bill_id = self.bill_id();
                item.bill_code = self.bill_code();
                item.currency = self.settle_currency();
                item.settle_type = self.settle_type();
                var g=new GoodsSettlement(item);
                self.goodsItems.push(g);
            });
        }

        var subjects;
        if(data.hasOwnProperty("other_expense") && data.other_expense!=null)
        {
            subjects=self.otherExpenseSubjects();
            ko.utils.arrayForEach(data.other_expense,function (item) {
                var g=new OtherExpense({
                    subjects:self.otherExpenseSubjects
                },item);
                if(g.subject_id()>0)
                    subjects[g.subject_id()]["status"]=1;
                self.otherExpenseItems.push(g);
            });
            self.otherExpenseSubjects(subjects);
        }
    }
    self.init(params);


    self.errors=ko.validation.group(self);
    self.isValid=function () {
        return self.errors().length===0;
    }
    
}

/**
 * 商品结算信息
 * @param option
 */
function GoodsSettlement(params){
    var defaults={
        "bill_id":0,
        "bill_code":"",
        "item_id":0,
        "goods_id":0,
        "goods_name":"",
        "quantity":{
            quantity:0,unit:1
        },
        "price":0,
        "amount":0,
        "bill_quantity":{
            quantity:0,unit:1
        },
        "bill_quantity_sub":{
            quantity:0,unit:1
        },
        "price_cny":0,
        "amount_cny":0,
        "currency":settlementConfigs.currency.default,
        "hasDetail":false,
        "exchange_rate":1,
        "settle_type":1,
        "settleFiles":null,
        "goodsOtherFiles":null,
        remark:""
    };
    params=inc.clearNullProperty(params);

    var o=$.extend(defaults,params);

    if(params.hasOwnProperty("in_quantity") && params.in_quantity){
        o.bill_quantity=params.in_quantity;
        if(params.hasOwnProperty("in_quantity_sub") && params.in_quantity_sub.unit
            && params.in_quantity.unit!=params.in_quantity_sub.unit )
            o.bill_quantity_sub = params.in_quantity_sub;
        else
            o.bill_quantity_sub = o.bill_quantity;
    }else if(params.out_quantity) {
        o.bill_quantity = params.out_quantity;
        if(params.hasOwnProperty("out_quantity_sub") && params.out_quantity_sub.unit
            && params.out_quantity.unit!=params.out_quantity_sub.unit )
            o.bill_quantity_sub = params.out_quantity_sub;
        else
            o.bill_quantity_sub = o.bill_quantity;
    }

    var self=this;

    self.bill_id=ko.observable(o.bill_id);
    self.item_id=ko.observable(o.item_id);
    self.bill_code=ko.observable(o.bill_code);
    self.goods_id=ko.observable(o.goods_id);
    self.goods_name=ko.observable(o.goods_name);
    self.quantity=ko.observable(parseFloat(o.quantity.quantity).round(4)).extend({ numeric: 4,notNegative:true });
    self.unit=ko.observable(o.quantity.unit);
    self.unit_name=ko.observable(settlementConfigs.getUnit(o.quantity.unit));
    self.unit_name_sub=ko.observable(settlementConfigs.getUnit(o.bill_quantity_sub.unit));
    self.price=ko.observable(parseInt(o.price)).extend({money:true,intN0:true});
    self.amount=ko.observable(parseInt(o.amount)).extend({money:true,intN0:true});
    self.bill_quantity=ko.observable(parseFloat(o.bill_quantity.quantity).round(4)).extend({ numeric: 4 });
    self.bill_quantity_sub=ko.observable(parseFloat(o.bill_quantity_sub.quantity).round(4)).extend({ numeric: 4 });
    //self.bill_quantity_sub=ko.observable(o.bill_quantity_sub.quantity);
    self.currency=ko.observable(o.currency);
    self.settle_type = ko.observable(o.settle_type);
    //self.exchange_rate=ko.observable(o.exchange_rate).extend({required:true,decimalLength:6});
    self.price_cny=ko.observable(parseInt(o.price_cny)).extend({money:true,intN0:true});
    self.amount_cny=ko.observable(parseInt(o.amount_cny)).extend({money:true,intN0:true});
    self.remark=ko.observable(o.remark);

    self.settleFiles=ko.observableArray(inc.arrayClone(o.settleFiles));
    self.goodsOtherFiles=ko.observableArray(o.goodsOtherFiles);
    self.settlementFileStatus=ko.observable(0);
    self.goodsOtherFileStatus=ko.observable(0);
    /*console.log(o.settleFiles);
    console.log(self.settleFiles());*/

    self.isShowQuantitySub = ko.computed(function () {
        return o.bill_quantity.unit!=o.bill_quantity_sub.unit;
    },self);

    self.hasDetail=ko.observable(o.hasDetail);
    self.currencyIco=ko.computed(function () {
        return settlementConfigs.getCurrencyIco(self.currency());
    },self);

    self.displayIn = ko.computed(function () {
        return self.settle_type()==1 || self.settle_type()==2;
    },self);

    var detailParams={currency:self.currency()};
    if(params!=null && params.hasOwnProperty("settlementGoodsDetail"))
        detailParams=params["settlementGoodsDetail"];
    if(detailParams.currency==null)
        detailParams.currency=self.currency();
    self.detail=new GoodsSettlementDetail({
        quantity:self.quantity,
        amount:self.amount,
        currency:self.currency,
        hasDetail:self.hasDetail
    },detailParams);

    var typeDesc = "入库";
    if(self.settle_type()==3 || self.settle_type()==4){
        typeDesc = "出库";
    }

    self.quantity_loss=ko.computed(function () {
        return Math.round((self.bill_quantity()-self.quantity())*10000)/10000;
    },self);

    self.exchange_rate=ko.computed(function () {
        if(self.currency()==settlementConfigs.currency.cny)
            return 1;
        if(self.amount()>0)
            return (self.amount_cny()/self.amount()).round(6);
        else
            return 1;
    },self);

    /*self.price_cny=ko.computed(function () {
        return Math.round(self.amount_cny()/self.quantity());
    },self);*/

    if(self.quantity()<=0)
        self.quantity(self.bill_quantity());

    self.quantity.subscribeChanged(function (newVal, oldVal){
        if(newVal>0){
            // self.price((self.amount()/newVal).round());
            var newP = (parseFloat(self.amount()).round(0) / parseFloat(newVal).round(4)).round(0);
            self.price(newP);
            if(self.currency()!=settlementConfigs.currency.cny)
                self.price_cny((parseFloat(self.amount_cny()).round(0) / parseFloat(newVal).round(4)).round(0));
                // self.price_cny((self.amount_cny()/newVal).round());
        }else{
            self.price(0);
            self.amount(0);
            self.price_cny(0);
            self.amount_cny(0);
        }

        if(self.billItemsAllQuantity()==newVal)
            return;

        var n=self.billItems().length;
        if(n==1)
            self.billItems()[0].quantity(newVal);
        else {
            var q = parseFloat(newVal) - parseFloat(oldVal);
            if (q != 0) {
                var q1 = 0;

                for (var i = 0; i < n; i++) {
                    var item = self.billItems()[i];
                    q1 = parseFloat(item.quantity()).round(4) + q;
                    if (q1 < 0) {
                        q = q1;
                        item.quantity(0);
                    }
                    else {
                        item.quantity(q1);
                        break;
                    }
                }
                /*ko.utils.arrayForEach(self.billItems(),function (item) {
                    q1=parseFloat(item.quantity()).round(4)+q;
                    if(q1<0)
                    {
                        q=q1;
                        item.quantity(0);
                    }
                    else
                    {
                        item.quantity(q1);
                        return;
                    }
                });*/
            }
        }

        if((Math.abs(self.bill_quantity()-newVal)*10000/(newVal*10000))*100>1){
			inc.vueAlert(typeDesc + "损耗比例超出1%，请确认");
        }
    },self);

    self.amount.subscribe(function (v) {
        if(self.quantity()>0)
        {
            v=parseFloat(v).round(0);
            var newAmount=(parseFloat(self.price()).round(0)*parseFloat(self.quantity()).round(4)).round(0);
            if(v!=newAmount)
            {
                var p=Math.round(parseFloat(v)/self.quantity());
                if(p!=parseInt(self.price()))
                {
                    self.price(p);
                }
            }
        }

        if(self.currency()==settlementConfigs.currency.cny)
        {
            self.amount_cny(v);
        }
    });

    self.price.subscribe(function (v) {
        if(self.quantity()>0) {
            v = parseFloat(v).round(0);
            var newP = (parseFloat(self.amount()).round(0) / parseFloat(self.quantity()).round(4)).round(0);
            if (v != newP) {
                var amount = Math.round(parseFloat(v) * self.quantity());
                if (amount != self.amount())
                    self.amount(amount);
            }
        }

    });

    self.price_cny.subscribe(function (v) {
        if(self.quantity()>0) {
            v = parseFloat(v).round(0);
            var newP = (parseFloat(self.amount_cny()).round(0) / parseFloat(self.quantity()).round(4)).round(0);
            if (v != newP) {
                var amount = Math.round(parseFloat(v) * self.quantity());
                if (amount != self.amount_cny())
                    self.amount_cny(amount);
            }
        }

    });


    self.amount_cny.subscribe(function (v) {
        if(self.quantity()>0)
        {
            v=parseFloat(v).round(0);
            var newAmount=(parseFloat(self.price_cny()).round(0)*parseFloat(self.quantity()).round(4)).round(0);
            if(v!=newAmount)
            {
                var p=Math.round(parseFloat(v)/self.quantity());
                if(p!=parseInt(self.price_cny()))
                {
                    self.price_cny(p);
                }
            }
        }

        /*if(self.currency()==settlementConfigs.currency.cny){
            self.amount(v);
        }*/
    });
    
    self.detail.amount_actual.subscribe(function (v) {
        if(self.hasDetail()){
            if(v!=self.amount_cny())
                self.amount_cny(v);
            if(self.currency() == settlementConfigs.currency.cny)
                self.amount(v);
        }
    });

    //是否显示人民币结算相关金额
    self.cnyVisible=ko.computed(function () {
        return self.currency()!=settlementConfigs.currency.cny;
    },self);
    self.isShowDetail=ko.observable(false);

    //增加明细信息
    self.addDetail=function () {
        self.hasDetail(true);
        self.showDetail();
    }
    //删除明细
    self.trashDetail=function () {
		inc.vueConfirm({
			content: "您确定取消录入明细操作吗？" +
            "<p class='text-red'>注意：取消之后，计算明细区域录入的内容清空，" +
            "非明细部分内容恢复至最近结算操作数据；若取消请点击确定，" +
            "不取消请点击关闭，关闭明细数据不会发生变化。</p>",
            title: '取消明细录入确认',
            onConfirm: function () {
				self.hasDetail(false);
				self.hideDetail();
			}
		});
    }
    //显示明细
    self.showDetail=function () {
        self.isShowDetail(true);
    }
    self.hideDetail=function () {
        self.isShowDetail(false);
    }
    //明细录入是否显示
    self.detailVisible=ko.computed(function () {
        return self.hasDetail() && self.isShowDetail();
    },self);

    self.toggleDetail=function()
    {
        if(self.hasDetail())
        {
            if(self.isShowDetail())
                self.hideDetail();
            else
                self.showDetail();
        }
    }

    self.isShowLockDetailBtn =ko.computed(function () {
        return self.settle_type()==1;
    },self);
    self.displayLockPriceDetail=function () {
        $.ajax({
            data: {
                batch_id:self.bill_id(),
                goods_id:self.goods_id()
            },
            url:"/stockBatchSettlement/ajaxGetBuyLockList",
            method:'post',
            success:function(res) {
                $("#buy_lock_dialog_body").html(res);
                $("#buy_lock_dialog").modal("show");
            },
            error:function(res) {
				inc.vueAlert("操作失败！" + res.responseText);
            }
        });
    }

    /**
     * 提单或发货单结算明细项
     */
    self.billItems=ko.observableArray();

    /*self.billItems.subscribe(function (v) {
        console.log(v);
    });*/

    self.billItemsAllQuantity=ko.computed(function () {
        var q=0;
        ko.utils.arrayForEach(self.billItems(),function (item) {
            q+=parseFloat(item.quantity()).round(4);
        });
        return parseFloat(q).round(4);
    },self);

    self.diffQuantity=ko.computed(function () {
        var d = self.quantity()-self.billItemsAllQuantity();
        return parseFloat(d).round(4);
    },self);

    self.quantity.extend({custom:{
            params: function (v) {
                return self.settle_type()==1 || self.settle_type()==3 || parseFloat(v).round(4)==self.billItemsAllQuantity();
            },
            message: "合计结算数量不等于明细的结算数量之和"
        }});

    self.init=function (data) {
    	
    	if(data.hasOwnProperty("lading_items") && data.lading_items.length>0){
    		
    		ko.utils.arrayForEach(data.lading_items,function (item) {
                var g=new BillGoodsSettlement({
                    quantity:self.quantity,
                    exchange_rate:self.exchange_rate,
                    price:self.price,
                    price_cny:self.price_cny,
                    items:self.billItems,
                    settle_type:self.settle_type,
                    settle_amount:self.amount,
                    settle_amount_cny:self.amount_cny
                },item);
                self.billItems.push(g);
            });
    	}else{
    		
    		ko.utils.arrayForEach(data.order_items,function (item) {
                var g=new BillGoodsSettlement({
                    quantity:self.quantity,
                    exchange_rate:self.exchange_rate,
                    price:self.price,
                    price_cny:self.price_cny,
                    items:self.billItems,
                    settle_type:self.settle_type,
                    settle_amount:self.amount,
                    settle_amount_cny:self.amount_cny
                },item);
                self.billItems.push(g);
                
            });
    	}

        if(self.billItems().length===1)
        {
            self.billItems()[0].quantity.subscribe(function (v) {
                self.quantity(v);
            });
            self.billItems()[0].amount.subscribe(function (v) {
                self.amount(v);
            });
            self.billItems()[0].amount_cny.subscribe(function (v) {
                self.amount_cny(v);
            });
        }
       
    }

    self.errors=ko.validation.group(self);
    self.isValid=function () {
        return self.errors().length===0;
    }

    self.init(params);
}

/**
 * 提单或发货单商品明细
 * @param observables {quantity,exchange_rate,price,price_cny,items}
 * @param option
 * @constructor
 */
function BillGoodsSettlement(observables,option){
    var defaults={
        "bill_id":0,
        "bill_code":"",
        "quantity":{ quantity:0,unit:1},
        "price":0,
        "amount":0,
        "bill_quantity":{ quantity:0,unit:1},
        "bill_quantity_sub":{ quantity:0,unit:1},
        "price_cny":0,
        "amount_cny":0,
        "currency":settlementConfigs.currency.default,
        "exchange_rate":1
    };
    option=inc.clearNullProperty(option);
    var o=$.extend(defaults,option);
    var self=this;

    if(option.hasOwnProperty("in_quantity") && option.in_quantity){
        o.bill_quantity=option.in_quantity;
        if(option.hasOwnProperty("in_quantity_sub") && option.in_quantity_sub.unit
            && option.in_quantity.unit!=option.in_quantity_sub.unit )
            o.bill_quantity_sub = option.in_quantity_sub;
        else
            o.bill_quantity_sub = o.bill_quantity;
    }
    else if(option.out_quantity){
        o.bill_quantity=option.out_quantity;
        if(option.hasOwnProperty("out_quantity_sub") && option.out_quantity_sub.unit
            && option.out_quantity.unit!=option.out_quantity_sub.unit )
            o.bill_quantity_sub = option.out_quantity_sub;
        else
            o.bill_quantity_sub = o.bill_quantity;
    }

    if(option.hasOwnProperty("batch_code") && option.batch_code)
        o.bill_code=option.batch_code;
    else
        o.bill_code=option.delivery_code;

    self.allQuantity=observables.quantity;
    self.settle_type=observables.settle_type;
    //self.currency=currencyObservable;
    self.exchange_rate=observables.exchange_rate;
    self.price=observables.price;
    self.price_cny=observables.price_cny;
    self.settle_amount = observables.settle_amount;
    self.settle_amount_cny = observables.settle_amount_cny;

    self.allItems=observables.items;

    self.bill_id=ko.observable(o.bill_id);
    self.bill_code=ko.observable(o.bill_code);
    var controller = 'stockIn';
    if(self.settle_type()==3 || self.settle_type()==4)
        controller = 'deliveryOrder';
    self.url = ko.observable("<a href='/"+controller+"/detail/?id="+self.bill_id()+"&t=1' target='_blank'>"+self.bill_code()+"</a>");

    self.quantity=ko.observable(parseFloat(o.quantity.quantity).round(4)).extend({ numeric: 4,notNegative:true });
    //self.amount=ko.observable(o.amount);
    self.bill_quantity=ko.observable(parseFloat(o.bill_quantity.quantity).round(4));
    self.bill_quantity_sub=ko.observable(parseFloat(o.bill_quantity_sub.quantity).round(4)).extend({ numeric: 4});
    self.unit=ko.observable(o.quantity.unit);
    self.unit_name=ko.observable(settlementConfigs.getUnit(o.quantity.unit));
    self.unit_name_sub=ko.observable(settlementConfigs.getUnit(o.bill_quantity_sub.unit));

    self.isShowQuantitySub = ko.computed(function () {
        return  o.bill_quantity.unit!=o.bill_quantity_sub.unit;
    },self);

    if(self.quantity()<=0)
        self.quantity(self.bill_quantity());

    /*self.quantity.subscribeChanged(function (newV,oldV) {
        var q=newV-oldV;
        if(q==0)
            return;
        ko.units.arrayForEach(self.allItems(),function(item,index){

        });
        self.allItems.notifySubscribers(v);
    },self);*/

    self.quantity_loss=ko.computed(function () {
        return Math.round((self.bill_quantity()-self.quantity())*10000)/10000;
    },self);
    self.amount=ko.computed(function () {
        if(self.allItems().length==1){
            return self.settle_amount();
        }else{
            var newA = (parseFloat(self.price()).round(0)*parseFloat(self.quantity()).round(4)).round(0);
            return Math.round(newA);
        }
        
    },self);
    self.amount_cny=ko.computed(function () {
        if(self.allItems().length==1){
            return self.settle_amount_cny();
        }else{
            var newA = (parseFloat(self.price_cny()).round(0)*parseFloat(self.quantity()).round(4)).round(0);
            return Math.round(newA);//self.amount()*self.exchange_rate()
        }
        
    },self);


    self.errors=ko.validation.group(self);
    self.isValid=function () {
        return self.errors().length===0;
    }

}

/**
 * 商品结算明细
 * @param observables {quantityObservable,amount,currency}
 * @param option
 * @constructor
 */
function GoodsSettlementDetail(observables,option){
    var defaults={
        "currency":settlementConfigs.currency.default,
        "price_goods":0,
        "amount_currency":0,
        "exchange_rate":1,
        "amount_goods":0,
        "exchange_rate_tax":1,
        "amount_goods_tax":0,
        "adjust_type":0,
        "amount_adjust":0,
        "reason_adjust":"",
        "quantity":1,
        "amount":0,
        "amount_actual":0,
        "price":0,
        "price_actual":0,
        "remark":""
    };
    option=inc.clearNullProperty(option);
    var o=$.extend(defaults,option);
    var self=this;
    if(observables.quantity)
        self.quantity=observables.quantity;
    self.settleAmount=observables.amount;
    self.settleCurrency=observables.currency;
    self.hasDetail=observables.hasDetail;
    
    self.currency=ko.observable(o.currency.id).extend({
        custom: {
            params: function (v) {
                if (!self.hasDetail() || v > 0 )
                    return true;
                else
                    return false;
            },
            message: "货款计价币种不能为空"
        }
    });


    self.currencyIco=ko.computed(function () {
        return settlementConfigs.getCurrencyIco(self.currency());
    },self);

    self.price_goods=ko.observable(parseInt(o.price_goods)).extend({numeric:0,intN0:true});
    self.amount_currency=ko.observable(parseInt(o.amount_currency)).extend({numeric:0,intN0:true});
    self.exchange_rate=ko.observable().extend({numeric:6});
    if(o.exchange_rate)
        self.exchange_rate(parseFloat(o.exchange_rate));
    self.amount_goods=ko.observable(parseInt(o.amount_goods)).extend({numeric:0,intN0:true});
    self.exchange_rate_tax=ko.observable(parseFloat(o.exchange_rate_tax)).extend({numeric:6});
    self.amount_goods_tax=ko.observable(parseFloat(o.amount_goods_tax)).extend({numeric:0,intN0:true});

    self.amount_adjust=ko.observable(parseInt(o.amount_adjust)).extend({numeric:0,intN0:true});
    self.adjust_type=ko.observable(o.adjust_type).extend({custom:{
        params: function (v) {
            if(self.amount_adjust()!=0 && v<=0)
            {
                return false;
            }
            return true;
        },
        message: "调整方式不得为空"
    }});
    self.reason_adjust=ko.observable(o.reason_adjust).extend({custom:{
            params: function (v) {
                if(self.amount_adjust()!=0 && v.trim().length<=0)
                {
                    return false;
                }
                return true;
            },
            message: "调整原因不得为空"
        }});
    self.reasonAdjustIsRequired=ko.computed(function () {
        var v=self.amount_adjust();
        return (!isNaN(v) && parseInt(v)!==0);

    },self);

    self.quantity_actual=ko.observable(parseFloat(o.quantity_actual));

    self.amount_actual=ko.observable(parseInt(o.amount_actual)).extend({numeric:0,intN0:true});
    self.price_actual=ko.observable(parseInt(o.price_actual)).extend({numeric:0,intN0:true});

    self.remark=ko.observable(o.remark);

    self.isCNY=ko.computed(function () {
        return self.currency()==settlementConfigs.currency.cny
    });

    if(self.isCNY())
        self.exchange_rate(1);


    self.currency.subscribe(function (v) {
        if(v==settlementConfigs.currency.cny)
            self.exchange_rate(1);
    });


    self.amount_currency.subscribe(function (v) {
        v=v.round();
        if(self.isCNY())
            self.amount_goods(v);
        else
        {
            var amount=Math.round(v*self.exchange_rate());
            self.amount_goods(amount);
        }
        amount=Math.round(parseFloat(v)*self.exchange_rate_tax());
        self.amount_goods_tax(amount);
        if(self.settleCurrency()!=settlementConfigs.currency.cny && self.currency()!=settlementConfigs.currency.cny)
            self.settleAmount(v);
    });

    self.exchange_rate.subscribe(function (v) {

        if(self.amount_currency()==0)
            return;
        v=parseFloat(v).round(6);
        var newV=(parseFloat(self.amount_goods()).round(0)/parseFloat(self.amount_currency()).round(0)).round(6);
        if(v!=newV) {
            var amount=(self.amount_currency()*v).round();
            if(amount!=self.amount_goods())
                self.amount_goods(amount);
        }
    },self);
    self.exchange_rate_tax.subscribe(function (v) {
        if(self.amount_currency()==0)
            return;
        v=parseFloat(v).round(6);
        var newV=(self.amount_goods_tax().round(0)/self.amount_currency().round(0)).round(6);
        if(v!=newV) {
            var amount = (self.amount_currency() * v).round();
            if (amount != self.amount_goods_tax())
                self.amount_goods_tax(amount);
        }
    },self);
    self.amount_goods.subscribe(function (v) {
        v=parseFloat(v).round(0);
        if(self.quantity()>0) {
            var p = Math.round(v / self.quantity());
            if (p != self.price_goods())
                self.price_goods(p);

            if (self.amount_currency() == 0 || self.isCNY())
                return;
            var newV = (parseFloat(self.amount_currency()).round(0) * parseFloat(self.exchange_rate()).round(6)).round(0);
            if (v != newV) {
                var rate = (v / self.amount_currency()).round(6);
                if (rate != self.exchange_rate())
                    self.exchange_rate(rate);
            }
        }
    });
    self.amount_goods_tax.subscribe(function (v) {
        if(self.quantity()>0) {
            if (self.amount_currency() == 0)
                return;
            v = v.round(0);
            var newV = self.amount_currency().round(0) * self.exchange_rate_tax().round();
            if (v != newV) {
                var rate = (v / self.amount_currency()).round(6);
                if (rate != self.exchange_rate_tax())
                    self.exchange_rate_tax(rate);
            }
        }

    });

    self.price_goods.subscribe(function(v){
        if(self.quantity()>0)
        {
            var newV=(self.amount_goods()/self.quantity().round(4)).round();
            if(v!=newV)
            {
                newV=(self.price_goods()*self.quantity()).round();
                if(newV!=self.amount_goods())
                    self.amount_goods(newV);
            }
        }
    });

    self.quantity.subscribe(function (v) {
        if(v>0) {
            if (self.price_goods() > 0) {
                var newV = (self.amount_goods() / self.price_goods()).round(4);
                if (v != newV) {
                    newV = (self.amount_goods() / v).round();
                    if (newV != self.price_goods())
                        self.price_goods(newV);
                }
            }
        }else{
            self.amount_goods(0);
            self.amount_actual(0);
            self.price_actual(0);
            self.price_goods(0);
            self.amount_goods_tax(0);
        }

    });

    self.currencyIsCanChange=ko.computed(function () {
        if(self.settleCurrency()==settlementConfigs.currency.cny)
            return true;
        else{
            self.currency(2);
            return false;
        }

    },self);

    //税款明细项
    self.taxItems=ko.observableArray();

    //其他费用明细项
    self.otherExpenseItems=ko.observableArray();

    self.taxAmount=ko.computed(function () {
        var amount=0;
        ko.utils.arrayForEach(self.taxItems(),function (item) {
            amount+=Math.round(parseFloat(item.amount()));
        });
        return amount;
    },self);

    self.otherAmount=ko.computed(function () {
        var amount=0;
        ko.utils.arrayForEach(self.otherExpenseItems(),function (item) {
            amount+=Math.round(parseFloat(item.amount()));
        });
        return amount;
    },self);

    self.amount=ko.computed(function () {
        if(self.quantity()>0) {
            var amount = self.amount_goods() + self.taxAmount() + self.otherAmount();
            amount = parseInt(amount);
            if (self.adjust_type() == settlementConfigs.adjust_type_add)
                amount = amount + parseInt(self.amount_adjust());
            else
                amount = amount - parseInt(self.amount_adjust());
            return Math.round(amount);
        }else{
            return 0;
        }
    },self);

    self.price=ko.computed(function () {
        if(self.quantity()>0)
            return Math.round(self.amount()/self.quantity());
        else
            return 0;
    });


    self.amount.subscribe(function (newV) {
        self.amount_actual(newV);
    });

    self.price.subscribe(function (v) {
        self.price_actual(v);
    });

    self.amount_actual.subscribe(function (v) {
        if(self.quantity()>0) {
            // var newV=(parseFloat(self.price_actual()).round(0)*parseFloat(self.quantity()).round(4)).round(0);
            var newV = (self.price_actual() * self.quantity()).round();
            if (v != newV) {
                newV = (v / self.quantity()).round();
                if (newV != self.price_actual())
                    self.price_actual(newV);
            }
        }
    });

    self.price_actual.subscribe(function (v) {
        if(self.quantity()>0) {
            var newV = (self.amount_actual() / self.quantity()).round();
            if (v != newV) {
                newV = (v * self.quantity()).round();
                if (newV != self.amount_actual())
                    self.amount_actual(newV);

                if(self.settleCurrency()==settlementConfigs.currency.cny)
                    self.settleAmount(newV);
            }
        }
    });

    //可选的币种信息
    self.currencies=inc.objectToArray(settlementConfigs.currencies);

    //关税
    self.tariffAmount=ko.computed(function () {
        var amount=0;
        ko.utils.arrayForEach(self.taxItems(),function (item) {
            if(item.subject_id()==settlementConfigs.tariffTax)
            {
                amount=item.amount();
                return amount;
            }
        });
        return amount;
    },self);

    self.addTax=function()
    {
        self.taxItems.push(new GoodsTaxItem({
            quantity:self.quantity,
            amount:self.amount_goods_tax,
            tariffAmount:self.tariffAmount,
            subjects:self.taxSubjects,
            hasDetail:self.hasDetail
        }, { quantity:self.quantity() }));
    }
    self.removeTax=function(item)
    {
        self.taxItems.remove(item);

        var subjects=self.taxSubjects();
        if(subjects[item.subject_id()] != undefined){
            subjects[item.subject_id()]["status"]=0;
            self.taxSubjects(subjects);
        }
    }
    self.addOtherExpense=function()
    {
        self.otherExpenseItems.push(new GoodsOtherExpenseItem({
            quantity:self.quantity,
            subjects:self.goodsOtherSubjects,
            hasDetail:self.hasDetail
        }, { quantity:self.quantity() }));
    }
    self.removeOtherExpense=function(item)
    {
        self.otherExpenseItems.remove(item);

        var subjects=self.goodsOtherSubjects();
        if(subjects[item.subject_id()] != undefined){
            subjects[item.subject_id()]["status"]=0;
            self.goodsOtherSubjects(subjects);
        }
    }

    self.taxSubjects=ko.observable(JSON.parse(JSON.stringify(settlementConfigs.taxSubjects)));

    self.goodsOtherSubjects=ko.observable(JSON.parse(JSON.stringify(settlementConfigs.goodsOtherSubjects)));


    self.errors=ko.validation.group(self);
    self.isValid=function () {
        return self.errors().length===0;
    }

    self.init=function (data) {
        if(data==null)
            return;
        var subjects;
        if(data.hasOwnProperty("tax_detail_item") && data.tax_detail_item!=null)
        {
            subjects=self.taxSubjects();
            ko.utils.arrayForEach(data.tax_detail_item,function (item) {
                var g=new GoodsTaxItem({
                    amount:self.amount_goods_tax,
                    tariffAmount:self.tariffAmount,
                    quantity:self.quantity,
                    subjects:self.taxSubjects,
                    hasDetail:self.hasDetail
                },item);
                if(g.subject_id()>0)
                    subjects[g.subject_id()]["status"]=1;
                self.taxItems.push(g);
            });
            self.taxSubjects(subjects);
        }

        if(data.hasOwnProperty("other_detail_item") && data.other_detail_item!=null)
        {
            subjects=self.goodsOtherSubjects();
            ko.utils.arrayForEach(data.other_detail_item,function (item) {
                var g=new GoodsOtherExpenseItem({
                    quantity:self.quantity,
                    subjects:self.goodsOtherSubjects,
                    hasDetail:self.hasDetail
                },item);
                if(g.subject_id()>0)
                    subjects[g.subject_id()]["status"]=1;
                self.otherExpenseItems.push(g);
            });
            self.goodsOtherSubjects(subjects);
        }
    }
    self.init(option);

}


/**
 * 货款费用其他费用明细项
 * @param observables {quantityObservable,}
 * @param option
 * @constructor
 */
function GoodsOtherExpenseItem(observables,option){
    var defaults={
        "subject_list":{"id":0,"name":""},
        "amount":0,
        "quantity":0,
        "price":0,
        "remark":""

    };
    option=inc.clearNullProperty(option);
    var o=$.extend(defaults,option);
    var self=this;
    self.quantity=observables.quantity;
    self.allSubjects=observables.subjects;
    self.hasDetail=observables.hasDetail;


    self.subject_id=ko.observable(o.subject_list.id).extend({
        custom: {
            params: function (v) {
                if (!self.hasDetail() || v > 0)
                    return true;
                else
                    return false;
            },
            message: "其他费用科目不能为空"
        }
    });
    self.amount=ko.observable(parseInt(o.amount)).extend({numeric:0,intN0:true});
    self.price=ko.observable(parseInt(o.price)).extend({numeric:0,intN0:true});
    self.remark=ko.observable(o.remark);

    self.amount.subscribe(function (v) {
        if(self.quantity()>0)
        {
            var newV = (self.price()*self.quantity()).round();
            if(v!==newV)
            {
                var p=Math.round(parseFloat(v)/self.quantity());
                if(p!=self.price())
                    self.price(p);
            }
        }
    });
    self.price.subscribe(function (v) {
        if(self.quantity()>0)
        {
            var newV = (self.amount()/self.quantity()).round();
            if(v!==newV)
            {
                var amount=Math.round(parseFloat(v)*self.quantity());
                if(amount!=self.amount())
                    self.amount(amount);
            }
        }
    });
    self.quantity.subscribe(function (v) {
        if(self.quantity()>0)
        {
            var newV = (self.amount()/self.price()).round(4);
            if(v!==newV)
            {
                newV=Math.round(parseFloat(self.amount())/self.quantity());
                if(newV!==self.price())
                    self.price(newV);
            }
        }else{
            self.amount(0);
            self.price(0);
        }
    });

    self.otherExpenseSubjects=ko.computed(function () {
        return inc.objectToArray(settlementConfigs.goodsOtherSubjects);
    },self);

    self.subject_id.subscribeChanged(function (newVal, oldVal){
        var s=self.allSubjects();
        if(oldVal)
            s[oldVal].status=0;
        if(newVal)
            s[newVal].status=1;
        self.allSubjects(s);
    });
    self.subjects=ko.computed(function () {
        var s=inc.objectToArray(self.allSubjects());
        return ko.utils.arrayFilter(s, function (item) {
            return (item.id == self.subject_id() || !item.hasOwnProperty("status") || item.status == 0)
        });

    },self);

    self.errors=ko.validation.group(self);
    self.isValid=function () {
        return self.errors().length===0;
    }

}

/**
 * 货款费用税款明细项
 * @param  observables { amountObservable,tariffAmountObservable,quantityObservable,subjects}
 *      amountObservable  计税总金额对象
 *      tariffAmountObservable    关税金额对象
 *      quantityObservable    结算数量对象
 * @param option
 * @constructor
 */
function GoodsTaxItem(observables,option){
    var defaults={
        "subject_list":{"id":0,"name":""},
        "rate":0,
        "amount":0,
        "quantity":0,
        "price":0,
        "remark":""
    };
    option=inc.clearNullProperty(option);
    var o=$.extend(defaults,option);
    var self=this;
    self.goodsAmount=observables.amount;
    self.tariffAmount=observables.tariffAmount;
    self.quantity=observables.quantity;
    self.allSubjects=observables.subjects;
    self.hasDetail=observables.hasDetail;

    self.subject_id=ko.observable(o.subject_list.id).extend({
        custom: {
            params: function (v) {
                if (!self.hasDetail() || v > 0)
                    return true;
                else
                    return false;
            },
            message: "税收名目不能为空"
        }
    });
    self.rate=ko.observable(o.rate).extend({numeric:2});
    self.amount=ko.observable(parseInt(o.amount)).extend({numeric:0,intN0:true,intN0:true});
    self.price=ko.observable(parseInt(o.price)).extend({numeric:0,intN0:true});
    self.remark=ko.observable(o.remark);

    self.allAmount=ko.computed(function(){
       if(self.subject_id()!=settlementConfigs.tariffTax)
           return self.goodsAmount()+self.tariffAmount();
       else
           return self.goodsAmount();
    },self);

    self.amount.subscribe(function (v) {
        if(parseInt(self.allAmount())===0)
        {
            self.amount(0);
            self.price(0);
            return;
        }

        if(self.quantity()>0)
        {
            var p=Math.round(parseFloat(v)/self.quantity());
            if(p!=self.price())
                self.price(p);
        }
        if(parseInt(self.allAmount())!==0) {
            var newV = self.computeTaxAmount(self.allAmount(), self.rate());
            if (v != newV) {
                var rate = self.computeTaxRate(self.allAmount(), v);
                if (rate != self.rate())
                    self.rate(rate);
            }
        }
    });
    self.rate.subscribe(function (v) {
        var newV=self.computeTaxRate(self.allAmount(),self.amount());
        if(v!=newV)
        {
            var amount=self.computeTaxAmount(self.allAmount(),v);
            if(amount!=self.amount())
                self.amount(amount);
        }
    });

    /**
     * 计算税金
     * @returns {number}
     */
    self.computeTaxAmount=function(allAmount,rate)
    {
        var amount=0;
        if(self.subject_id()==settlementConfigs.tax.excise)
        {
            if(rate==1)
                amount=0;
            else
                amount=Math.round(allAmount/(1-rate)*rate);
        }
        else
            amount=Math.round(allAmount*rate);
        return amount;
    }
    /**
     * 计算税率
     * @param allAmount
     * @param taxAmount
     * @returns {number}
     */
    self.computeTaxRate=function (allAmount,taxAmount) {
        var rate=0;
        if(self.subject_id()==settlementConfigs.tax.excise)
        {
            rate=(taxAmount/(allAmount+taxAmount)).round(2);
        }
        else
            rate=(taxAmount/allAmount).round(2);
        return rate;
    }

    self.allAmount.subscribe(function (v) {
        // var amount=Math.round(self.rate()*v);
        var amount = self.computeTaxAmount(v, self.rate());
        if(amount!=self.amount())
            self.amount(amount);
    });

    self.quantity.subscribe(function (v) {
        if(self.quantity()>0)
        {
            var p=Math.round(parseFloat(self.amount())/self.quantity());
            if(p!=self.price())
                self.price(p);
        }else{
            self.price(0);
            self.amount(0);
        }
    });

    self.subject_id.subscribeChanged(function (newVal, oldVal){
        /*self.allSubjects()[oldVal].status=0;
        self.allSubjects()[newVal].status=1;*/
        var s=self.allSubjects();
        if(oldVal)
            s[oldVal].status=0;
        if(newVal)
            s[newVal].status=1;
        self.allSubjects(s);

        //如果有初始化的税率，设置为初始化税率
        if(settlementConfigs.taxRate.hasOwnProperty(newVal))
            self.rate(settlementConfigs.taxRate[newVal]);

    });
    self.subjects=ko.computed(function () {
        var s=inc.objectToArray(self.allSubjects());
        return ko.utils.arrayFilter(s, function (item) {
            return (item.id == self.subject_id() || !item.hasOwnProperty("status") || item.status == 0)
        });

    },self);

    self.errors=ko.validation.group(self);
    self.isValid=function () {
        return self.errors().length===0;
    }

}


/**
 * 非货款费用
 * @param observables {subjects}
 * @param option
 * @constructor
 */
function OtherExpense(observables,option){
    var defaults={
        "detail_id": 0,
        "fee":{"id":0,"name":""},
        "currency":  settlementConfigs.currencies[settlementConfigs.currency.cny],
        "amount": 0,
        "amount_cny": 0,
        "exchange_rate": 1,
        "remark":"",
        "otherFiles":null
    };
    option=inc.clearNullProperty(option);
    var o=$.extend(defaults,option);
    var self=this;

    self.allSubjects=observables.subjects;

    var fee = 0;
    if(o.hasOwnProperty("fee") && o.fee!=null)
        fee = o.fee.id;
    var currency = 0;
    if(o.hasOwnProperty("currency") && o.currency!=null)
        currency = o.currency.id;

    self.detail_id=ko.observable(o.detail_id);
    self.subject_id=ko.observable(fee).extend({
        custom: {
            params: function (v) {
                if (v > 0)
                    return true;
                else
                    return false;
            },
            message: "非货款科目不能为空"
        }
    });
    self.currency=ko.observable(currency);
    self.amount=ko.observable(parseInt(o.amount)).extend({moneyNZ:true,intN0:true});
    self.exchange_rate=ko.observable(parseFloat(o.exchange_rate)).extend({numeric:6});
    self.amount_cny=ko.observable(parseInt(o.amount_cny)).extend({money:true,intN0:true});
    self.remark=ko.observable(o.remark);
    self.otherFiles=ko.observableArray(o.otherFiles);
    self.otherFileStatus=ko.observable(0);

    self.currency.subscribe(function (v) {
        if(v==settlementConfigs.currency.cny)
            self.exchange_rate(1);
    },self);

    self.currencyIco=ko.computed(function () {
        return settlementConfigs.getCurrencyIco(self.currency());
    },self);

    self.amount_cny=ko.computed(function () {
        return (self.amount()*self.exchange_rate()).round();
    },self);

    self.cnyIsVisible=ko.computed(function () {
        return self.currency()!=settlementConfigs.currency.cny;
    },self);

    self.subject_id.subscribeChanged(function (newVal, oldVal){
        var s=self.allSubjects();
        if(oldVal)
            s[oldVal].status=0;
        if(newVal)
            s[newVal].status=1;
        self.allSubjects(s);
    });
    self.subjects=ko.computed(function () {
        var s=inc.objectToArray(self.allSubjects());
        return ko.utils.arrayFilter(s, function (item) {
            return (item.id == self.subject_id() || !item.hasOwnProperty("status") || item.status == 0)
        });

    },self);

    self.errors=ko.validation.group(self);
    self.isValid=function () {
        return self.errors().length===0;
    }
}

