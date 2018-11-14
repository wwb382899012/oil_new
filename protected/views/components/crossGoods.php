<template id='component-template-cross-goods'>
    <table class="table table-hover">
        <thead>
        <tr>
            <th style="width:140px;">采购合同编号</th>
            <th style="width:170px; text-align: left;">上游合作方</th>
            <th style="width:170px; text-align: left;">入库单编号</th>
            <th style="width:170px; text-align: left;">仓库</th>
            <th style="width:110px; text-align: left;">可用库存数量</th>
            <th style="width:130px; text-align: left;">预计调货数量 <span class="text-red fa fa-asterisk"></span></th>
            <th style="text-align: left;">
                <button class="btn btn-success btn-xs" data-bind="click:add">新增</button>
            </th>
        </tr>
        </thead>
        <tbody data-bind="foreach:items">
        <!-- ko component: {
            name: "cross-goods-item",
            params: {
                        model: $data,
                        parentItems:$parent.items
                        }
        } -->
        <!-- /ko -->
        </tbody>
    </table>
    <div class="modal fade draggable-modal" id="crossModel" tabindex="-1" role="dialog" aria-labelledby="modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title">请添加调货信息</h4>
                </div>
                <div class="modal-body">
                    <div class="box box-primary">
                        <div class="box-body">
                            <form class="search-form">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="input-group-addon">采购合同编号</div>
                                                <input type="text" class="form-control" name="contractCode" id="contractCode"
                                                       placeholder="采购合同编号" value=""
                                                       data-bind="textInput:contractCode"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="input-group-addon">上游合作方</div>
                                                <input type="text" class="form-control" name="partnerName" id="partnerName"
                                                       placeholder="上游合作方" value=""
                                                       data-bind="textInput:partnerName"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="input-group-addon">入库单编号&emsp;</div>
                                                <input type="text" class="form-control" name="stockCode" id="stockCode"
                                                       placeholder="入库单编号" value=""
                                                       data-bind="textInput:stockCode"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="input-group-addon">仓库&emsp;&emsp;&emsp;</div>
                                                <select class="form-control selectpicker" data-live-search="true" title="请选择单位" id="storeId" name="storeId" data-bind="value:storeId,valueAllowUnset: true">
                                                    <option value='' >全部</option>
                                                    <?php
                                                    $storehouses = Storehouse::model()->findAllToArray("status=".Storehouse::STATUS_PASS);
                                                    foreach($storehouses as $v) {
                                                        echo "<option value='" . $v["store_id"] . "'>" . $v["name"]. "</option>";
                                                    }?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-1">
                                            <p class="form-control-static">
                                            	<button type="button" id="searchBtnText" class="btn btn-success btn-sm" placeholder="&nbsp查询&nbsp" data-bind="click:search,html:searchBtnText"></button>
                                            	<!-- <input type="submit" value="&nbsp查询&nbsp" class="btn btn-success btn-sm" data-bind=""> -->
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table id="crossInfo" class="table table-condensed table-hover table-bordered table-layout">
                                <thead>
                                <tr>
                                    <th style='width: 5%; text-align:center;'><input type="checkbox" id="selectAll" value="all" onclick="checkAll()" /></th>
                                    <th style='width: 17%; text-align:center;'>采购合同编号</th>
                                    <th style='text-align:center'>上游合作方</th>
                                    <th style='width: 20%; text-align:center;'>入库单编号</th>
                                    <th style='width: 10%; text-align:center;'>品名</th>
                                    <th style='width: 25%; text-align:center;'>仓库</th>
                                    
                                </tr>
                                </thead>

                                <tbody id="crossBody" data-bind="foreach: crossInfo">
                                <tr class="item">
                                    <td style='text-align:center;'><input type="checkbox" name="crossName" data-bind="value:stock_in_id" onclick="checkOne()" /></td>
                                    <td style='text-align:center;' class='contractCode' data-bind="text:contractCode,attr:{title:contractCode}"></td>
                                    <td style='text-align:left' class='partnerName' data-bind="text:partnerName,attr: {title: partnerName}"></td>
                                    <td style='text-align:center;' class='stockCode' data-bind="text:stockCode,attr:{title:stockCode}"></td>
                                    <td style='text-align:center;' class='goodsName' data-bind="text:goodsName,attr:{title:goodsName}"></td>
                                    <td style='text-align:left;' data-bind="text:storeName,attr:{title: storeName}"></td>
                                    <td data-bind="text:storeId" class='storeId' hidden></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmSubmitBtnText" class="btn btn-primary" placeholder="确认" data-bind="click:confirm,html:confirmSubmitBtnText"></button>
                	<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
</template>
<template id='component-template-cross-goods-item'>
    <tr data-bind="with:model">
        <td style="text-align: left;vertical-align: baseline!important;">
            <span data-bind="text:contract_code"></span>
            <span hidden data-bind="text:contract_id"></span>
            <span hidden data-bind="text:project_id"></span>
            <span hidden data-bind="text:goods_id"></span>
        </td>
        <td style="text-align: left;vertical-align: baseline!important;">
            <span data-bind="text:partner_name"></span>
        </td>
        <td style="text-align: left;vertical-align: baseline!important;">
            <span data-bind="text:stock_code"></span>
            <span hidden data-bind="text:stock_id"></span>
        </td>
        <td style="text-align: left;vertical-align: baseline!important;">
            <span data-bind="text:store_name"></span>
            <span hidden data-bind="text:store_id"></span>
        </td>
        <td style="text-align: right;vertical-align: baseline!important;">
            <span data-bind="text:quantity_format"></span>
            <span hidden data-bind="text:quantity_balance"></span>
        </td>
        <td>
            <div class="input-group">
                <input type="text" class="form-control input-sm" placeholder="预计调货数量" data-bind="value:quantity">
                <span class="input-group-addon" data-bind="text:unit_format"></span>
            </div>
            <span hidden data-bind="text:unit"></span>
        </td>
        <td>
            <button class="btn btn-danger btn-xs" data-bind="click:$parent.del">删除</button>
        </td>
    </tr>
</template>

<script>
	ko.components.register('cross-goods-item', {
		template: {element: 'component-template-cross-goods-item'},
		viewModel: crossGoodsItemComponent
	});

	ko.components.register('cross-goods', {
		template: {element: 'component-template-cross-goods'},
		viewModel: crossGoodsComponent
	});

	function crossGoodsComponent(params) {
		var self 	= this;
		self.items 	= params.items;
		self.corporation_id = params.corporation_id;
		self.contract_id 	= params.contract_id;
		self.goods_id 		= params.goods_id;
		self.project_id 	= params.project_id;
		self.contractCode 	= ko.observable();
		self.partnerName 	= ko.observable();
		self.stockCode 		= ko.observable();
		self.storeId 		= ko.observable();
		self.actionState 	= ko.observable(0);
		self.crossInfo 		= ko.observableArray();


		self.searchBtnText = ko.observable('&nbsp查询&nbsp');
		self.confirmSubmitBtnText = ko.observable('确认');

		//借货信息添加
        self.add = function () {
            if (self.actionState() == 1)
                return;

            self.actionState(1);
            $.ajax({
                type: "GET",
                url: "/<?php echo $this->getId() ?>/getCrossInfo",
                data: {corporation_id: self.corporation_id(), goods_id: self.goods_id(), project_id: self.project_id()},
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    if (json.state == 0) {
                    	var items = view.goodsItems();
                    	var ids = []
                    	if(items.length>0){
                    		for(var i in items){
                    			ids.push(items[i]['stock_in_id']());
                    		}
                    	}
                    	var crossItems = [];
                    	if(json.data.length>0){
                    		for(var j in json.data){
                    			var pos = $.inArray(json.data[j]['stock_in_id'], ids);
                    			if(pos>-1)
                    				delete json.data[j];
                    			else
                    				crossItems.push(json.data[j]);
                    		}
                    	}
                    	// console.log(crossItems);
                        self.crossInfo(crossItems);
                        $("#crossModel").modal({
                            backdrop: true,
                            keyboard: false,
                            show: true
                        });
                        if(crossItems.length>0)
                        	checkOne();
                        else
                        	document.getElementById("selectAll").checked = false; 
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    self.actionState(0);
                    layer.alert("增加失败：" + data.responseText, {icon: 5});
                }
            });
        }

        //查询
        self.search  = function(){
			var trs = $("#crossInfo > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                var td = $(this).children('td');
                /*console.log(td[6].innerHTML);
                console.log(self.storeId());*/
                // console.log(td);
                var allCells = td.each(function () {
                	var contractCode = '';
                	if(self.contractCode()!=undefined)
                    	contractCode = new RegExp(self.contractCode(), 'i');
                    var partnerName = '';
                	if(self.partnerName()!=undefined)
                    	partnerName = new RegExp(self.partnerName(), 'i');
                    var stockCode = '';
                	if(self.stockCode()!=undefined)
                    	stockCode = new RegExp(self.stockCode(), 'i');
                    var storeId = '';
                	if(self.storeId()!=undefined){
                    	storeId = self.storeId()
                	}

                    if ((contractCode=='' || contractCode.test(td[1].innerHTML))
                    	&& (partnerName=='' || partnerName.test(td[2].innerHTML))
                    	&& (stockCode=='' || stockCode.test(td[3].innerHTML))
                    	&& (storeId=='' || storeId==td[6].innerHTML)) {
                        found = true;
                        return false;
                    }
                });
                if (found) $(this).show(); else $(this).hide();
            });
		}

		//选中确认
		self.confirm = function(){
            var vals = "";
            $("input[name='crossName']").each(function(i){
                if($(this).prop("checked")==true){ 
                    vals += $(this).val()+",";//转换为逗号隔开的字符串
                }
            });
            var str = vals.substring(0,vals.length-1);
            if(str.length < 1){
                layer.alert("请选择要添加的列表项", {icon: 5});
                return;
            }

            if (self.actionState() == 1)
                return;

            self.actionState(1);
            $.ajax({
                type: "GET",
                url: "/<?php echo $this->getId() ?>/getCrossDetail",
                data: {id_str: str, goods_id: self.goods_id()},
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    if (json.state == 0) {
                        for(var i in json.data){
                        	var obj = new CrossGoods({
								stock_in_id: json.data[i].stock_in_id,
								stock_id: json.data[i].stock_id,
								store_id: json.data[i].store_id,
								contract_id: json.data[i].contract_id,
								project_id: json.data[i].project_id,
								goods_id: json.data[i].goods_id,
								contract_code: json.data[i].contract_code,
								partner_name: json.data[i].partner_name,
								stock_code: json.data[i].stock_code,
								store_name: json.data[i].store_name,
								quantity_format: json.data[i].quantity_format,
								unit_format: json.data[i].unit_format,
								quantity_balance: json.data[i].quantity_balance,
								unit: json.data[i].unit,
							});
							self.items.push(obj);
                        }

                        $('#crossModel').modal('hide');
            			// $('#crossModel').modal('hide');
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    self.actionState(0);
                    layer.alert("增加失败：" + data.responseText, {icon: 5});
                }
            });
            
            return;
		}

		/*self.isCanAdd = ko.computed(function () {
			return self.items().length < self.allGoods().length;
		}, self);*/
	}

	function checkAll() {  
	    //把所有参与选择的checkbox使用相同的name，这里为"crossName"  
	    var eles = document.getElementsByName("crossName");  
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
	    var eles = document.getElementsByName("crossName");  
	    for (var i = 0; i < eles.length; i++) {  
	        if (eles[i].checked != true) {  
	            isSelected = false;  
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


	function CrossGoods(option) {
		var defaults = {
			stock_in_id: 0,
			stock_id: 0,
			store_id: 0,
			contract_id: 0,
			project_id: 0,
			goods_id: 0,
			contract_code: '',
			partner_name: '',
			stock_code: '',
			store_name: '',
			quantity_balance: 0.0,
			quantity_format: '',
			quantity: 0.0,
			unit: 0,
			unit_format: '',
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.stock_in_id = ko.observable(o.stock_in_id);
		self.stock_id = ko.observable(o.stock_id);
		self.store_id = ko.observable(o.store_id);
		self.contract_code = ko.observable(o.contract_code);
		self.contract_id = ko.observable(o.contract_id);
		self.project_id = ko.observable(o.project_id);
		self.goods_id = ko.observable(o.goods_id);
		self.partner_name = ko.observable(o.partner_name);
		self.stock_code = ko.observable(o.stock_code);
		self.store_name = ko.observable(o.store_name);
		self.quantity_format = ko.observable(o.quantity_format);
		self.quantity_balance = ko.observable(o.quantity_balance);
		self.unit = ko.observable(o.unit);
		self.unit_format = ko.observable(o.unit_format);
		self.quantity = ko.observable(o.quantity).extend({positiveNumber: true,custom:{
            params: function (v) {
                return parseFloat(self.quantity_balance())>=parseFloat(v);
            },
            message: "预借数量不能超过可用库存"
        }});
	}


	function crossGoodsItemComponent(params) {
		var self = this;
		self.model = params.model;

		self.del = function (data) {
			if (params.parentItems) {
				params.parentItems.remove(data);
			}

		}
	}
</script>
