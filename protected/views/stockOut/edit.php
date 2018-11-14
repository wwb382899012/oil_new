<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">

<?php
    $this->renderPartial("/deliveryOrder/partial/deliveryOrderInfo", array('deliveryOrder' => $deliveryOrder,'isShowBackButton'=>true));
    $this->renderPartial("/common/outDetailList", array('outOrders'=>$outOrders,'isShowEditButton'=>false));
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            本次出库单信息
        </h3>
    </div>

    <div class="box-body">
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="form-group">
                <label class="col-sm-2 control-label">出库日期<span class="text-red fa fa-asterisk"></span></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control input-sm date" placeholder="出库日期" data-bind="date:out_date">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">本次出库</label>
                <div class="col-sm-4">
                    <select class="form-control" data-bind="value:store_id">
                        <?php foreach($data['stores'] as $store_id => $store_name):?>
                        <option value="<?php echo $store_id;?>"><?php echo $store_name;?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <?php
                $attachType = $this->map["stock_delivery_attachment"][ConstantMap::STOCK_OUT_ATTACH_TYPE];
                $attachments=AttachmentService::getAttachments(Attachment::C_STOCK_OUT,$data['out_order_id'],ConstantMap::STOCK_OUT_ATTACH_TYPE);
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
                                     files:<?php echo json_encode($attachments[ConstantMap::STOCK_OUT_ATTACH_TYPE]); ?>,
                                     fileParams: {
                                        id:<?php echo empty($data['out_order_id'])?0:$data['out_order_id'] ?>
                                     }
                                 }
                     } -->
                    <!-- /ko -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">仓库出库明细</label>
                <div class="col-sm-10">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    销售合同编号
                                </th>
                                <th>
                                    品名
                                </th>
                                <th>
                                    配货入库单号
                                </th>
                                <th>
                                    配货数量
                                </th>
                                <th>
                                    出库
                                </th>
                                <th>
                                    本次出库数量
                                </th>
                            </tr>
                        </thead>
                        <tbody data-bind="foreach:details">
                            <tr data-bind="visible:$data.store_id == $parent.store_id()">
                                <td>
                                    <p class='form-control-static' data-bind='text:$data.contract_code'></p>
                                </td>
                                <td>
                                    <p class='form-control-static' data-bind='text:$data.goods_name'></p>
                                </td>
                                <td>
                                    <p class='form-control-static' data-bind='text:$data.stock_in_code'></p>
                                </td>
                                <td>
                                    <p class='form-control-static' data-bind='text:$data.quantity_str'></p>
                                </td>
                                <td>
                                    <p class='form-control-static' data-bind='text:$data.store_name'></p>
                                </td>
                                <td>
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                           <input class='form-control' data-bind='value:$data.quantity'>
                                           <span class="input-group-addon" data-bind='text:$data.unit_store'></span>
                                        </div>
                                        <!-- <input type="text" class="form-control" id="year_income" name= "obj[year_income]" placeholder="医院年收入" data-bind="value:year_income">(单位：万元) -->
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-10">
                    <textarea type="text" class="form-control" rows="3" placeholder="备注" data-bind="value:remark" ></textarea>
                </div>
            </div>
        </form>
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
</div>

</section>
<script>
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode(
			array(
				'order_id'=>$deliveryOrder->order_id,
				'store_id'=>$data['store_id'],
				'stores'=>$data['stores'],
				'details'=>$data['storeGoods'],
				'out_order_id'=>$data['out_order_id'],
				'remark'=>$data['remark']
				)
			) ?>);
		ko.applyBindings(view);

	});
	function ViewModel(option) {
		var defaults = {
			order_id:0,
			out_date:(new Date()).format(),
			store_id:0,
			details:[],
			stores:[],
			remark:'',
			out_order_id:0
		};
		var o = $.extend(defaults, option);
		var self = this;

		self.order_id = ko.observable(o.order_id);
		self.out_date = ko.observable(o.out_date).extend({date: true});
		self.store_id = ko.observable(o.store_id);
		// self.details = ko.observable(o.details);
		self.store_id.subscribe(function(newValue) {
		}, this);
		self.details = ko.observableArray();
		for(var ind in o.details) {
			self.details.push(new StockOutDetail(o.details[ind]));
		}
		self.remark = ko.observable(o.remark);
		self.out_order_id = o.out_order_id;
		self.fileUploadStatus = ko.observable();
		self.submitBtnText = ko.observable("提交");
		self.saveBtnText = ko.observable("保存");

		self.fileUploadStatus=ko.observable();

		self.isSubmit = ko.observable(0);

		self.errors = ko.validation.group(self);
		self.isValid = function () {
			var details = self.details();
			for (var i = details.length - 1; i >= 0; i--) {
                //TODO: 暂时屏蔽
				if(false && details[i].quantity() > details[i].quantity_default * 1.1){
					layer.alert("出库数量不能超过配货数量10%");
					return false;
				}
			}
			return self.errors().length === 0;
		};

		self.sendSaveSubmitAjax = function () {
			if (self.actionState == 1)
				return;
			self.actionState = 1;
			var formData = {"data": self.getPostData()};

			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/save',
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						layer.msg('操作成功', {icon: 6, time: 1000}, function () {
						    console.log("self.isSubmit():",self.isSubmit());
							if (self.isSubmit() == 10) {
                                if (document.referrer === '') {
                                    location.href = "/stockOutList/";
                                } else {
                                    history.back();
                                }
							} else {
								location.href = "/stockOutList/view/?id=" + json.data;
							}
						});
					} else {
						self.actionState = 0;
						self.submitBtnText("提交");
						self.saveBtnText("保存");
						self.isSubmit(0);
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					self.actionState = 0;
					self.submitBtnText("提交");
					self.saveBtnText("保存");
					self.isSubmit(0);
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});
		};

		self.getPostData = function() {
			var data = {};
			data.order_id = self.order_id();
			data.store_id = self.store_id();
			data.out_date = self.out_date();
			data.remark = self.remark();
			data.status = self.isSubmit();
			data.out_order_id = self.out_order_id;
			var items = [], details = self.details();
			ko.utils.arrayForEach(self.details(),function(item,index){
			   if(item.quantity()>0 && item.store_id==self.store_id())
			       items.push(item.getValue());
            });
			/*for(var ind in details) {
				if(details[ind].store_id == data.store_id) {
					var item = {
						detail_id:details[ind].detail_id(),
						quantity:details[ind].quantity(),
						stock_id:details[ind].stock_id,
						stock_detail_id:details[ind].stock_detail_id,
					}
					items.push(item);
				}
			}*/
			data.items = items;
			return data;
		}

		self.submit = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			layer.confirm("您确定要提交出库单信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
				self.isSubmit(10);
				self.submitBtnText("提交中" + inc.loadingIco);
				self.sendSaveSubmitAjax();
			})
		};

		self.save = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			self.isSubmit(0);
			self.saveBtnText("保存中" + inc.loadingIco);
			self.sendSaveSubmitAjax();
		};

		self.back = function () {
			history.back();
		}
	}

	function StockOutDetail(option) {
		var o = {
			contract_code:"",
			contract_id:0,
			detail_id:0,
			goods_id:0,
			goods_name:"",
			quantity:0,
			quantity_str:"",
			stock_in_code:"",
			stock_in_id:0,
			store_id:0,
			stock_id:0,
			store_name:"",
			unit:0,
			stock_out_detail_id:0,
			quantity_saved:0,
            quantity_stock:0,
			cross_detail_id:0,
			stock_detail_id:0,
		}
		o=$.extend(o, option);
		//o.quantity_default = o.quantity;
		var self = this;
		self.contract_code=ko.observable(o.contract_code);
		self.contract_id=ko.observable(o.contract_id);
		self.detail_id=ko.observable(o.detail_id);
		self.goods_id=ko.observable(o.goods_id);
		self.goods_name=ko.observable(o.goods_name);
		self.quantity_str=ko.observable(o.quantity_str);
		self.stock_in_code=ko.observable(o.stock_in_code);
		self.stock_in_id=ko.observable(o.stock_in_id);
		self.store_name=ko.observable(o.store_name);
		self.unit=ko.observable(o.unit);
		self.unit_store=ko.observable(o.unit_store);

		self.quantity_default=o.quantity_stock;
        self.store_id=o.store_id;
        self.stock_id=o.stock_id;
		self.cross_detail_id=o.cross_detail_id;
		self.stock_detail_id = o.stock_detail_id;

        self.quantity=ko.observable(o.quantity).extend({number:true});

		self.getValue=function () {
            return {
                detail_id:self.detail_id(),
                quantity:self.quantity(),
                stock_id:self.stock_id,
                stock_detail_id:self.stock_detail_id
            };
        }

	}
</script>