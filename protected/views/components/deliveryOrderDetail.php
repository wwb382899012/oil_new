<template id='component-template-delivery-order-goods'>
    <table class="table table-hover">
        <thead>
        <tr>
            <!--<th style="width:120px;">销售合同 <span class="text-red fa fa-asterisk"></span></th>-->
            <th style="width:120px; text-align: left;">品名 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:160px; text-align: left;">合同数量</th>
            <th style="width:120px; text-align: left;">已配货数量</th>
            <th style="width:120px; text-align: left;">已出库数量</th>
            <th style="width:140px; text-align: left;">本次发货数量 <span class="text-red fa fa-asterisk"></span></th>
            <!-- ko if:type() == 1 -->
            <th style="width:80px; text-align: left;">操作 <span class="text-red fa fa-asterisk"></span></th>
            <th style="width:330px; text-align: left;">配货明细 <span class="text-red fa fa-asterisk"></span></th>
            <!-- /ko -->
            <th style="text-align: left;">
                <button class="btn btn-success btn-xs" data-bind="visible:(($parent.allGoodsItems().length > 1)
                && ($parent.allGoodsItems().length > $parent.goodsItems().length)),click:addAllGoods">新增</button>
            </th>
        </tr>
        </thead>
        <tbody data-bind="foreach:items">
        <!-- ko component: {
            name: "delivery-order-goods-item",
            params: {
                        model: $data,
                        goodsChangedFunc:$parent.goodsChanged,
                        deleteGoodsItemFunc:$parent.deleteGoodsItemFunc,
                        allGoodsItems:$parent.allGoodsItems,
                        items: $parent.items,
                        type: $parent.type,
                        buyContracts: $parent.buyContracts
                        }
        } -->
        <!-- /ko -->
        </tbody>
    </table>

    <div class="modal fade draggable-modal" id="distributeListModal" tabindex="-1" role="dialog" aria-labelledby="modal">
        <div class="modal-dialog modal-lg" role="document" style="width: 1050px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title">请输入配货信息</h4>
                </div>
                <div class="modal-body">
                    <div class="box box-primary">
                        <div class="box-body">
                            <form class="search-form">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">采购合同编号</div>
                                                <input type="text" class="form-control input-sm" placeholder="采购合同编号" data-bind="textInput:buyContractCodeKeyWord"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">上游合作方</div>
                                                <input type="text" class="form-control input-sm" placeholder="上游合作方" data-bind="textInput:partnerKeyWord"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">配货来源</div>
                                                <input type="text" class="form-control input-sm" placeholder="配货来源" data-bind="textInput:resourceKeyWord"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">入库单编号</div>
                                                <input type="text" class="form-control input-sm" placeholder="入库单编号" data-bind="textInput:stockInCodeKeyWord"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">仓库名称</div>
                                                <input type="text" class="form-control input-sm" placeholder="仓库名称" data-bind="textInput:storeNameKeyWord"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        </form>
                        <table id="distributeList" class="table table-condensed table-hover table-bordered table-layout" data-bind="visible:buyContracts().length > 0">
                            <thead>
                            <tr>
                                <th style='width: 150px; text-align:center;'>采购合同</th>
                                <th style='width: 100px; text-align:center'>上游合作方</th>
                                <th style='width: 200px; text-align:center;'>配货来源</th>
                                <th style='width: 180px; text-align:center;'>入库单编号</th>
                                <th style='width: 100px; text-align:center;'>仓库</th>
                                <th style='width: 120px; text-align:center;'>可用库存数量</th>
                                <th style='text-align:center;'>配货数量</th>
                            </tr>
                            </thead>

                            <tbody id="buyContractBody" data-bind="foreach: buyContracts">
                            <tr class="item">
                                <td style='text-align:center;' data-bind="html:contract_code,attr:{title:contract_code}"></td>
                                <td style='text-align:center' data-bind="html:partner_name,attr:{title:partner_name}"></td>
                                <td style='text-align:center;' data-bind="html:resource"></td>
                                <td style='text-align:center;' data-bind="html:stock_in_code,attr:{title:stock_in_code}"></td>
                                <td style='text-align:center;' data-bind="html:store_name,attr:{title:store_name}"></td>
                                <td style='text-align:center;'>
                                    <span data-bind="html:quantity_balance"></span><span data-bind="html:unit_store_desc"></span>
                                </td>
                                <td style='text-align:center;'>
                                    <div class="input-group">
                                        <input type="text" class="form-control input-sm" placeholder="配货数量" data-bind="value:quantity">
                                        <span class="input-group-addon" data-bind="html:unit_store_desc"></span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" data-bind="click:confirm">确定</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    </div>
</template>
<template id='component-template-delivery-order-goods-item'>
    <tr data-bind="with:model">
        <!--<td><span data-bind="html:contract_code"></span></td>-->
        <td>
            <select size=1 data-bind="
                options: $parent.allValidgoods,
                optionsText: 'name',
                optionsValue: 'key',
                value: $parent.goods_id">
            </select>
        </td>
        <td>
            <span data-bind="html:contract_quantity"></span>
        </td>
        <td>
            <span data-bind="html:distributed_quantity"></span>
        </td>
        <td>
            <span data-bind="html:stock_out_quantity"></span>
        </td>
        <td>
            <div class="input-group">
                <input type="text" class="form-control input-sm" placeholder="本次发货数量" data-bind="value:quantity">
                <span class="input-group-addon" data-bind="html:unit_store_desc"></span>
            </div>
        </td>
        <!-- ko if:type() == 1 -->
        <td>
            <a data-bind="click:$parent.distributeGoods">配货</a>
        </td>
        <!--<td>
            <span data-bind="html:distribute_detail"></span>
        </td>-->
        <td data-bind="foreach: stock_delivery_detail">
            <span data-bind="html:distribute_detail"></span>
            <hr style="margin-top: 5px; margin-bottom: 5px;"/>
        </td>
        <!-- /ko -->
        <td>
            <button class="btn btn-danger btn-xs" data-bind="visible:($parent.allGoodsItems().length > 1),click:$parent.delete">删除</button>
        </td>
    </tr>
</template>

<script>
    ko.components.register('delivery-order-goods-item', {
        template: {element: 'component-template-delivery-order-goods-item'},
        viewModel: deliveryOrderDetailItemComponent
    });

    ko.components.register('delivery-order-goods', {
        template: {element: 'component-template-delivery-order-goods'},
        viewModel: deliveryOrderDetailComponent
    });
    function deliveryOrderDetailComponent(params) {
        var self = this;
        //所有商品明细
        self.allGoodsItems = params.allGoodsItems;
        //添加全部商品明细函数
        self.addAllGoods = params.addAllGoodsItemsFunc;
        //提供给子组件START
        self.deleteGoodsItemFunc = params.deleteGoodsItemFunc;
        self.goodsChanged = params.goodsChangedFunc; //商品被改变
        //提供给子组件END
        //
        self.items = params.items;
        self.stock_in_id = params.stock_in_id;
        self.corporation_id = params.corporation_id;
        self.partner_id = params.partner_id;
        self.type = params.type;
        self.buyContracts = ko.observableArray([]);

        /////////////////////////////////////////
        //清理查询条件
        self.buyContracts.subscribe(function () {
            self.contractCodeKeyWord("");
            self.projectCodeKeyWord("");
            self.goodsNameKeyWord("");
            self.buyContractCodeKeyWord("");
            self.partnerKeyWord("");
            self.resourceKeyWord("");
            self.stockInCodeKeyWord("");
            self.storeNameKeyWord("");
        });
        self.contractCodeKeyWord = ko.observable();
        self.projectCodeKeyWord = ko.observable();
        self.goodsNameKeyWord = ko.observable();
        self.buyContractCodeKeyWord = ko.observable();
        self.partnerKeyWord = ko.observable();
        self.resourceKeyWord = ko.observable();
        self.stockInCodeKeyWord = ko.observable();
        self.storeNameKeyWord = ko.observable();
        self.contractCodeKeyWord.subscribe(function () {
            self.contractCodeSearch();
        });
        self.projectCodeKeyWord.subscribe(function () {
            self.projectCodeSearch();
        });
        self.goodsNameKeyWord.subscribe(function () {
            self.goodsNameSearch();
        });
        self.buyContractCodeKeyWord.subscribe(function () {
            self.buyContractCodeSearch();
        });
        self.partnerKeyWord.subscribe(function () {
            self.partnerCodeSearch();
        });
        self.resourceKeyWord.subscribe(function () {
            self.resourceSearch();
        });
        self.stockInCodeKeyWord.subscribe(function () {
            self.stockInCodeSearch();
        });
        self.storeNameKeyWord.subscribe(function () {
            self.storeNameSearch();
        });
        self.contractCodeSearch = function () {
            var trs = $("#saleContracts > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                $(this).children('td').each(function () {
                    var regExpContractCode = new RegExp(self.contractCodeKeyWord(), 'i');
                    if (regExpContractCode.test($(this).text())) {
                        found = true;
                        return false;
                    }
                });
                if (found)
                    $(this).show();
                else
                    $(this).hide();
            });
        };

        self.projectCodeSearch = function () {
            var trs = $("#saleContracts > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                $(this).children('td').each(function () {
                    var regExpProjectCode = new RegExp(self.projectCodeKeyWord(), 'i');
                    if (regExpProjectCode.test($(this).text())) {
                        found = true;
                        return false;
                    }
                });
                if (found)
                    $(this).show();
                else
                    $(this).hide();
            });
        };

        self.goodsNameSearch = function () {
            var trs = $("#saleContracts > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                $(this).children('td').each(function () {
                    var regExpGoodsName = new RegExp(self.goodsNameKeyWord(), 'i');
                    if (regExpGoodsName.test($(this).text())) {
                        found = true;
                        return false;
                    }
                });
                if (found)
                    $(this).show();
                else
                    $(this).hide();
            });
        };

        self.buyContractCodeSearch = function () {
            var trs = $("#distributeList > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                $(this).children('td').each(function () {
                    var regExpContractCode = new RegExp(self.buyContractCodeKeyWord(), 'i');
                    if (regExpContractCode.test($(this).text())) {
                        found = true;
                        return false;
                    }
                });
                if (found)
                    $(this).show();
                else
                    $(this).hide();
            });
        };

        self.partnerCodeSearch = function () {
            var trs = $("#distributeList > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                $(this).children('td').each(function () {
                    var regExpContractCode = new RegExp(self.partnerKeyWord(), 'i');
                    if (regExpContractCode.test($(this).text())) {
                        found = true;
                        return false;
                    }
                });
                if (found)
                    $(this).show();
                else
                    $(this).hide();
            });
        };

        self.resourceSearch = function () {
            var trs = $("#distributeList > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                $(this).children('td').each(function () {
                    var regExpProjectCode = new RegExp(self.resourceKeyWord(), 'i');
                    if (regExpProjectCode.test($(this).text())) {
                        found = true;
                        return false;
                    }
                });
                if (found)
                    $(this).show();
                else
                    $(this).hide();
            });
        };

        self.stockInCodeSearch = function () {
            var trs = $("#distributeList > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                $(this).children('td').each(function () {
                    var regExpGoodsName = new RegExp(self.stockInCodeKeyWord(), 'i');
                    if (regExpGoodsName.test($(this).text())) {
                        found = true;
                        return false;
                    }
                });
                if (found)
                    $(this).show();
                else
                    $(this).hide();
            });
        };

        self.storeNameSearch = function () {
            var trs = $("#distributeList > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                $(this).children('td').each(function () {
                    var regExpGoodsName = new RegExp(self.storeNameKeyWord(), 'i');
                    if (regExpGoodsName.test($(this).text())) {
                        found = true;
                        return false;
                    }
                });
                if (found)
                    $(this).show();
                else
                    $(this).hide();
            });
        };

        self.confirm = function () {
            if (Array.isArray(self.items()) && self.items().length > 0) {
                ko.utils.arrayForEach(self.items(), function (item, i) {
                    var stock_delivery_detail = [];
                    if (item.contract_detail_id() == self.buyContracts()[0].contract_detail_id && Array.isArray(self.buyContracts()) && self.buyContracts().length > 0) {
                        ko.utils.arrayForEach(self.buyContracts(), function (val, index) {
                            val.quantity = ko.unwrap(val.quantity);
                            if (val.quantity > 0) {
                                val.unit_store_desc = ko.unwrap(item.unit_store_desc);
                                val.buy_contract_id = val.contract_id;
                                val.contract_id = val.sale_contract_id;
                                stock_delivery_detail.push(new StockDeliveryDetail(val));
                            }
                        })
                        self.items()[i].stock_delivery_detail(stock_delivery_detail);
                    }
                })
            }
        }
    }

    function deliveryOrderDetailItemComponent(params) {
        var self = this;
        self.items = params.items;
        self.model = params.model;
        self.model.type = params.type;
        //
        //监听商品名改变事件
        self.goods_id = ko.observable(params.model.goods_id());
        self.goodsChangedFunc = params.goodsChangedFunc;
        self.goods_id.subscribeChanged(function (newVal, oldVal) {
            self.goodsChangedFunc(newVal, oldVal);
        });
        //
        self.allGoodsItems = params.allGoodsItems;
        //构造商品名下拉框数据
        self.allValidgoods = ko.computed(function () {
            var selectedGoodsIds = [];
            ko.utils.arrayForEach(self.items(), function (item) {
                selectedGoodsIds.push(item.goods_id());
            });

            var goodsNameList = [];
            ko.utils.arrayForEach(self.allGoodsItems(), function (item) {
                if(self.model.goods_id() == item.goods_id){
                    goodsNameList.push({key:item.goods_id,name:item.goods_name});
                    return;
                }

                if(!selectedGoodsIds.includes(item.goods_id)){
                    goodsNameList.push({key:item.goods_id,name:item.goods_name});
                }
            });

            return goodsNameList;
        });

        params.type.subscribe(function (v) {
            self.model.type(v);
        });

        //删除操作
        self.delete = function (data) {
            params.deleteGoodsItemFunc(data);
        };

        self.distributeGoods = function (data) {
            self.model.quantity.isModified(true);
            if (!self.model.quantity.isValid()) {
                return;
            }

            $.ajax({
                type: "GET",
                url: "/deliveryOrder/getStockInBuyContracts",
                data: {
                    contract_detail_id: data.contract_detail_id(),
                },
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if (Array.isArray(json.data.buyContracts) && json.data.buyContracts.length > 0) {
                            ko.utils.arrayForEach(json.data.buyContracts, function (item, i) {
                                if (self.model.stock_delivery_detail().length > 0) {
                                    ko.utils.arrayForEach(self.model.stock_delivery_detail(), function (row, k) {
                                        if (row.buy_contract_id() == item.contract_id && row.goods_id() == item.goods_id) {
                                            if ((item.cross_detail_id == 0 && row.stock_id() == item.stock_id) || (item.cross_detail_id > 0 && row.cross_detail_id() == item.cross_detail_id)) {
                                                json.data.buyContracts[i].quantity = row.quantity();
                                            }
                                        }
                                    })
                                }
                                json.data.buyContracts[i].quantity = ko.observable(item.quantity).extend({
                                    custom: {
                                        params: function (v) {
                                            self.msg = '配货数量不能小于0';
                                            if (parseFloat(v) >= 0) {
                                                if (parseFloat(v) <= parseFloat(item.quantity_balance)) {
                                                    return true;
                                                } else {
                                                    self.msg = '配货数量不能大于可用库存';
                                                    return false;
                                                }
                                            } else {
                                                return false;
                                            }
                                        },
                                        message: function () {
                                            return self.msg;
                                        }
                                    }
                                });

                                json.data.buyContracts[i].sale_contract_id = data.contract_id();
                                json.data.buyContracts[i].sale_project_id = data.project_id();
                            });
                        }
                        params.buyContracts(json.data.buyContracts);
                        $("#distributeListModal").modal({
                            backdrop: true,
                            keyboard: false,
                            show: true
                        });
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("配货信息检索失败！" + data.responseText, {icon: 5});
                }
            })
        };
    }

    //配货明细
    function StockDeliveryDetail(option) {
        var defaults = {
            stock_detail_id: 0,
            project_id: 0,
            contract_id: 0,
            goods_id: 0,
            stock_id: 0,
            store_id: 0,
            type: 1,
            quantity: 0,
            cross_detail_id: 0,
            buy_contract_id: 0,
            stock_in_code: '',
            cross_code: '',
            unit_store_desc: ''
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.stock_detail_id = ko.observable(o.stock_detail_id);
        self.project_id = ko.observable(o.project_id);
        self.contract_id = ko.observable(o.contract_id);
        self.goods_id = ko.observable(o.goods_id);
        self.stock_id = ko.observable(o.stock_id);
        self.store_id = ko.observable(o.store_id);
        self.type = ko.observable(o.type);
        self.quantity = ko.observable(o.quantity);
        self.cross_detail_id = ko.observable(o.cross_detail_id);
        self.buy_contract_id = ko.observable(o.buy_contract_id);
        self.stock_in_code = ko.observable(o.stock_in_code);
        self.cross_code = ko.observable(o.cross_code);
        self.unit_store_desc = ko.observable(o.unit_store_desc);

        self.distribute_detail = ko.computed(function () {
            var detail = self.stock_in_code();
            if(self.cross_code() != '') {
                detail += '<br/>(' + self.cross_code() + ')';
            }
            detail += '：' + self.quantity() + self.unit_store_desc();
            return detail;
        })
    }
</script>