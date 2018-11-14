<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<link href="/css/style/addnewproject.css?key=20180112" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="/js/resize.js"></script>
<section class="content-header">
    <div class="content-header__des">
        <?php echo empty($this->pageTitle)?$this->moduleName:$this->pageTitle ?>
    </div>
</section>
<section class=" main-container" id="main-container">
  <section class="content sub-container">
      <?php if ($data["status"] == Project::STATUS_BACK) {
          $backLog = ProjectBackLog::model()->find(array("condition" => "project_id=" . $data["project_id"], "order" => "id desc"));
          if (!empty($backLog)) {

              ?>
              <div class="form-group text-danger" id="detail-back-title" style="">
                  <span class="fa fa-info-circle"></span>&nbsp;驳回备注:
                  <span class="form-control-static"><?php echo $backLog["remark"] ?></span>
              </div>
          <?php }
      } ?>
    <div class="box box-primary sub-container__box">
      <div class="box-header with-border box-content-title">
        <h3 class="box-title">&nbsp;&nbsp;&nbsp;项目信息</h3>
      </div>
      <form class="form-horizontal" role="form" id="mainForm">
        <div class="box-body box-body-custom">

            <div class="form-group form-group-custom">
                <label for="type" class="col-sm-2 control-label control-label-custom">
                  <span class="label-custom__span-red">*</span>项目类型 ：
                </label>
                <div class="col-sm-4">
                  <select class="form-control form-control-custom" aria-placeholder="请选择" title="请选择项目类型" id="type" name="obj[type]" data-bind="value:type,valueAllowUnset: true">
                    <option value=''>请选择项目类型</option>
                    <?php foreach ($this->map["project_type"] as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                    } ?>
                </select>
                </div>
                <span data-bind="visible: showBuySaleOrder">
                    <label for="manager_user_id" class="col-sm-2 control-label control-label-custom control-label-custom-right">
                        <span class="label-custom__span-red">*</span>购销顺序 ：
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control form-control-custom" title="请选择购销顺序" id="buy_sell_type" name="obj[buy_sell_type]" data-bind="value:buy_sell_type,valueAllowUnset: true">
                            <option value='0'>请选择购销顺序</option>
                            <?php foreach ($this->map["purchase_sale_order"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </span>
            </div>

            <div class="form-group form-group-custom" data-bind="visible:(showUpPartner() || showDownPartner())">
                <span data-bind="visible: showUpPartner">
                    <label for="type" class="col-sm-2 control-label control-label-custom">
                      <span class="label-custom__span-red">*</span>上游合作方 ：
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control form-control-custom" title="请选择上游合作方" id="up_partner_id" name="obj[up_partner_id]" data-live-search="true" data-bind="selectpicker: up_partner_id,valueAllowUnset: true">
                            <option value='0'>请选择上游合作方</option>
                            <?php
                            $upPartners = PartnerService::getUpPartners();
                            foreach ($upPartners as $v) {
                                echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                    </div>
                </span>
                <span data-bind="visible: showDownPartner">
                    <span data-bind="visible: showUpPartner">
                        <label for="manager_user_id" class="col-sm-2 control-label control-label-custom control-label-custom-right">
                        <span class="label-custom__span-red">*</span>下游合作方 ：
                        </label>
                    </span>
                    <span data-bind="visible: showDownPartner && !showUpPartner()">
                        <label for="manager_user_id" class="col-sm-2 control-label control-label-custom">
                        <span class="label-custom__span-red">*</span>下游合作方 ：
                        </label>
                    </span>
                    
                    <div class="col-sm-4">
                        <select class="form-control form-control-custom" title="请选择下游合作方" id="down_partner_id" name="obj[down_partner_id]" data-live-search="true" data-bind="selectpicker: down_partner_id,valueAllowUnset: true">
                            <option value='0'>请选择下游合作方</option>
                            <?php
                            $downPartners = PartnerService::getDownPartners();
                            foreach ($downPartners as $v) {
                                echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                    </div>
                </span>
            </div>

            <div class="form-group form-group-custom" data-bind="visible: isShowAgent">
                <label for="type" class="col-sm-2 control-label control-label-custom">
                  <span class="label-custom__span-red">*</span>采购代理商 ：
                </label>
                <div class="col-sm-4">
                    <select class="form-control form-control-custom" title="请选择采购代理商" id="agent_id" name="obj[agent_id]" data-bind="selectpicker: agent_id,valueAllowUnset: true">
                        <?php
                        $upPartners = PartnerService::getAgentPartners();
                        foreach ($upPartners as $v) {
                            echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group form-group-custom">
                <label for="type" class="col-sm-2 control-label control-label-custom">
                  <span class="label-custom__span-red">*</span>交易主体 ：
                </label>
		<div class="col-sm-4">
		     <select class="form-control form-control-custom" title="请选择交易主体" id="corporation_id" name="obj[corpord]" data-live-search="true" data-bind="selectpicker:corporation_id,valueAllowUnset: true">
                        <option value='0'>请选择交易主体</option>
                        <?php
                        $cors = UserService::getUserSelectedCorporations();
                        foreach ($cors as $v) {
                            echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                        } ?>
                    </select>
                </div>
                <label for="manager_user_id" class="col-sm-2 control-label control-label-custom control-label-custom-right">
                    <span class="label-custom__span-red">*</span>项目负责人 ：
                </label>
                <div class="col-sm-4">
                    <select class="form-control form-control-custom" title="请选择项目负责人" id="manager_user_id" name="obj[manager_user_id]" data-live-search="true" data-bind="selectpicker: manager_user_id,valueAllowUnset: true">
                        <option value='0'>请选择项目负责人</option>
                        <?php
                        $users = UserService::getProjectManageUsers();
                        foreach ($users as $v) {
                            echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group form-group-custom">
                <label for="type" class="col-sm-2 control-label control-label-custom">
                  <span class="label-custom__span-red">*</span>价格方式 ：
                </label>
                <div class="col-sm-4">
                    <select class="form-control form-control-custom" title="请选择价格方式" id="price_type" name="obj[price_type]" data-bind="value:price_type,valueAllowUnset: true">
                        <option value='0'>请选择价格方式</option>
                        <?php foreach ($this->map["price_type"] as $k => $v) {
                            echo "<option value='" . $k . "'>" . $v . "</option>";
                        } ?>
                    </select>
                </div>
                <span data-bind="visible: showStorehouse">
                    <label for="manager_user_id" class="col-sm-2 control-label control-label-custom control-label-custom-right">
                        <span class="label-custom__span-red">*</span>仓库名称 ：
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control form-control-custom" title="请选择仓库名称" id="storehouse_id" name="obj[storehouse_id]" data-live-search="true" data-bind="selectpicker: storehouse_id,valueAllowUnset: true">
                            <option value='0'>请选择仓库名称</option>
                            <?php
                            $users = Storehouse::getAllActiveStorehouse();
                            foreach ($users as $v) {
                                echo "<option value='" . $v["store_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                    </div>
                </span>
            </div>

            <div class="form-group form-group-custom">
                <div data-bind="visible:showUpPartner()">
                    <label for="type" class="col-sm-2 control-label control-label-custom">
                      <span class="label-custom__span-red">*</span>采购币种 ：
                    </label>
                    <div class="col-sm-4">
                        <select class="form-control form-control-custom" title="请选择采购币种" id="purchase_currency" name="obj[purchase_currency]" data-bind="value:purchase_currency,valueAllowUnset: true">
                            <?php foreach ($this->map["currency_type"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div data-bind="visible:showDownPartner()">
                    <span data-bind="visible: showUpPartner">
                        <label for="manager_user_id" class="col-sm-2 control-label control-label-custom control-label-custom-right">
                        <span class="label-custom__span-red">*</span>销售币种 ：
                        </label>
                    </span>
                    <span data-bind="visible: showDownPartner && !showUpPartner()">
                        <label for="manager_user_id" class="col-sm-2 control-label control-label-custom">
                        <span class="label-custom__span-red">*</span>销售币种 ：
                        </label>
                    </span>
                    <div class="col-sm-4">
                        <select class="form-control form-control-custom" title="请选择销售币种" id="sell_currency" name="obj[sell_currency]" data-bind="value:sell_currency,valueAllowUnset: true">
                            <?php foreach ($this->map["currency_type"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
      </form>
    </div>
    <!-- 项目信息 -->

    <!-- 交易明细 -->
    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
            <h3 class="box-title">&nbsp;&nbsp;&nbsp;交易明细</h3>
        </div>
      

        <div class="box-header  box-content-custom">
            <!-- ko component: {
                 name: "project-goods",
                 params: {
                     allGoods: allGoods,
                     units: units,
                     currencies:currencies,
                     items: goodsItems,
                     purchase_currency: purchase_currency,
                     sell_currency: sell_currency,
                     type: type,
                     buy_sell_type: buy_sell_type,
                     up_partner_id: up_partner_id,
                     down_partner_id: down_partner_id
                     }
             } -->
            <!-- /ko -->
        </div>
        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/projectGoodsNew.php"; ?>
        <div style="height: 95px" class="form-group form-group-custom form-group-custom-height"  data-bind="visible: showAnalysisPlan">
            <label for="plan_describe" class="col-sm-2 control-label control-label-custom"><span class="label-custom__span-red">*</span>市场分析及<span data-bind="html:project_buy_sell_type"></span>计划：
              <span class="text-red fa fa-asterisk"></span>
            </label>
            <div class="col-sm-10" style="width: 84.3%">
              <textarea class="form-control" id="plan_describe" name="obj[plan_describe]" rows="3" placeholder="请输入内容" data-bind="value:plan_describe"></textarea>
            </div>
          </div>
        <div class="line-dot"></div>
        <div class="line-dot"></div>
        
    </div>
    <!-- 交易明细 -->


    <div class="box box-primary sub-container__box padding-bottom-10">
        <div style="margin-bottom: 10px" class="box-header with-border box-content-title">
            <h3 class="box-title">&nbsp;&nbsp;&nbsp;附件上传</h3>
        </div>
        <?php
            $attachTypes = $this->map['project_launch_attachment_type'];
        ?>
        <!-- ko component: {
                name: "multi-file-upload",
                params: {
                    controller:"<?php echo $this->getId() ?>",
                    attachTypes:<?php echo json_encode($attachTypes) ?>,
                    attachs:<?php echo json_encode($attachments); ?>,
                    fileParams: {
                        id: <?php echo $data['project_id'] ?>
                    }
                }
            } -->
        <!-- /ko -->
        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/multiUploadNew.php"; ?>
        <div class="box-body form-group form-group-custom  form-group-custom-upload">
            <div class="form-group">
                <label class="col-sm-2 control-label">
                  <span class="glyphicon glyphicon-remove text-red"></span> 
                  <span class="upload-star-box">
                  </span>
                  <span class="upload-title upload-title-custom">备注</span>
                  <span style="display: none;">
                    <span class="text-red fa fa-asterisk"></span>
                  </span>
                </label>
                <div class="col-sm-10">
                  <textarea class="form-control" id="remark" name="obj[remark]" rows="3" placeholder="请输入内容" data-bind="value:remark"></textarea>
                </div>
            </div>
        </div>
        
    </div>

    <!-- 提交保存 -->
    <div class="box box-primary sub-container__box sub-container__fixed">
      <div class="box-body ">
        <div class="form-group form-group-custom-btn">
          <!-- 此处删除了类：col-sm-offset-2 col-sm-10  增加了类submit-btn-custom-->
          <div class="btn-contain-custom">
            <button type="button" id="submitButton" class="btn btn-contain__submit" data-bind="click:submit, html:submitBtnText">提交</button>
            <button type="button" id="saveButton" class="btn btn-contain__default " data-bind="click:save, html:saveBtnText">保存</button>
            <button type="button" class="btn btn-contain__default " data-bind="click:back">返回</button>
          </div>
        </div>
      </div>
    </div>
    <!-- 提交保存 -->
  </section>
</section>
<script>
	var view;
	$(function () {

        <?php
        $transactions = Utility::isNotEmpty($transactions) ? $transactions : array();
        ?>
		view = new ViewModel(<?php echo json_encode($data) ?>);
		view.formatGoods(<?php echo json_encode($goods) ?>);
		view.units = inc.objectToArray(<?php echo json_encode(array_values(Map::$v['goods_unit'])) ?>);
		view.currencies =<?php echo json_encode(Map::$v["currency"]); ?>;
		view.formatGoodsItems(<?php echo json_encode($transactions) ?>);
		ko.applyBindings(view);
	});
	function ViewModel(option) {
		var defaults = {
			project_id: 0,
			type: '',
			buy_sell_type: 0,
			up_partner_id: 0,
			down_partner_id: 0,
			corporation_id: 0,
			price_type: 0,
			purchase_currency: 1,
			sell_currency: 1,
			manager_user_id: 0,
			plan_describe: '',
			storehouse_id: 0,
			agent_id: 0,
			remark: '',
            project_buy_sell_type: ''
		};
		var o = $.extend(defaults, option);
		var self = this;

		self.project_id = ko.observable(o.project_id);
		self.type = ko.observable(o.type).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择项目类型"
			}
		});

		//购销顺序展示条件
		self.showBuySaleOrder = ko.computed(function () {
			if ($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) >= 0)
				return true;
			return false;
		}, self);

		//市场分析及采购计划展示条件
		self.showAnalysisPlan = ko.computed(function () {
			if ($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) >= 0)
				return true;
			return false;
		}, self);

		//仓库名称展示条件
		self.showStorehouse = ko.computed(function () {
			if ($.inArray(parseInt(self.type()), config.projectTypeWarehouseReceipt) >= 0)
				return true;
			return false;
		}, self);

		self.buy_sell_type = ko.observable(o.buy_sell_type).extend({
			custom: {
				params: function (v) {
					if (self.showBuySaleOrder()) {
						if (v > 0)
							return true;
						else
							return false;
					}
					return true;
				},
				message: "请选择购销顺序"
			}
		});

		//上游合作方展示条件
		self.showUpPartner = ko.computed(function () {
			if ($.inArray(parseInt(self.type()), config.projectTypeChannelBuy.concat(config.projectTypeWarehouseReceipt)) >= 0 || ($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) >= 0 && parseInt(self.buy_sell_type()) == config.firstBuyLastSale))
				return true;
			return false;
		}, self);

		//下游合作方展示条件
		self.showDownPartner = ko.computed(function () {
			if ($.inArray(parseInt(self.type()), config.projectTypeChannelBuy.concat(config.projectTypeWarehouseReceipt)) >= 0 || ($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) >= 0 && parseInt(self.buy_sell_type()) == config.firstSaleLastBuy))
				return true;
			return false;
		}, self);

		//采购代理商展示条件
		self.isShowAgent = ko.computed(function () {
			if ($.inArray(parseInt(ko.unwrap(self.type)), config.buyContractSelectType) >= 0 || (ko.unwrap(self.type) == config.projectTypeSelfImport && ko.unwrap(self.buy_sell_type) == config.firstBuyLastSale))
				return true;
			return false;
		}, self);

		self.up_partner_id = ko.observable(o.up_partner_id).extend({
			custom: {
				params: function (v) {
					self.msg = '请选择上游合作方';
					if (self.showUpPartner()) {
						if (v > 0) {
							if (self.showDownPartner() && v == ko.unwrap(self.down_partner_id)) {
								self.msg = '上下游合作方不能重复'
								return false;
							} else {
								return true;
							}
						}
						else
							return false;
					}
					return true;
				},
				message: function () {
					return self.msg;
				}
			}
		});
		self.down_partner_id = ko.observable(o.down_partner_id).extend({
			custom: {
				params: function (v) {
					self.msg = '请选择下游合作方';
					if (self.showDownPartner()) {
						if (v > 0) {
							if (self.showUpPartner() && v == ko.unwrap(self.up_partner_id)) {
								self.msg = '上下游合作方不能重复'
								return false;
							} else {
								return true;
							}
						}
						else
							return false;
					}
					return true;
				},
				message: function () {
					return self.msg;
				}
			}
		});
		self.corporation_id = ko.observable(o.corporation_id).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择交易主体"
			}
		});
		self.agent_id = ko.observable(o.agent_id).extend({
			custom: {
				params: function (v) {
					if (self.isShowAgent()) {
						if (v > 0)
							return true;
						else
							return false;
					}
					return true;
				},
				message: "请选择采购代理商"
			}
		});
		self.price_type = ko.observable(o.price_type).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择价格方式"
			}
		});
		self.purchase_currency = ko.observable(o.purchase_currency).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择采购币种"
			}
		});
		self.sell_currency = ko.observable(o.sell_currency).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择销售币种"
			}
		});
		self.manager_user_id = ko.observable(o.manager_user_id).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择项目负责人"
			}
		});
		self.plan_describe = ko.observable(o.plan_describe).extend({
			custom: {
				params: function (v) {
					if (self.showAnalysisPlan()) {
						if (v == '')
							return false;
					}
					return true;
				},
				message: "不得为空"
			}
		});
		self.storehouse_id = ko.observable(o.storehouse_id).extend({
			custom: {
				params: function (v) {
					if (self.showStorehouse()) {
						if (v > 0)
							return true;
						else
							return false;
					}
					return true;
				},
				message: "请选择仓库名称"
			}
		});
		self.remark = ko.observable(o.remark);
        self.contractGoodsUnitConvert = ko.observable(o.contractGoodsUnitConvert);
        self.contractGoodsUnitConvertValue = ko.observable(o.contractGoodsUnitConvertValue);

		self.type.subscribe(function (v) {
			self.formatProjectParams();
			if ($.inArray(parseInt(v), config.projectTypeImport) >= 0) { //进口
				self.purchase_currency(2);
			} else {
				self.purchase_currency(1);
			}
		});

        self.project_buy_sell_type = ko.computed(function() {
            var projectBuySellTypes = <?php echo json_encode($this->map["project_buy_sell_type"]);?>;
            if (self.buy_sell_type() > 0) {
                return projectBuySellTypes[self.buy_sell_type()];
            }
            return "";
        });
        
		self.buy_sell_type.subscribe(function (v) {
			self.formatProjectParams(); 
		});

		self.formatProjectParams = function () {
			if ($.inArray(parseInt(self.type()), config.projectTypeSelfSupport) < 0) { //非自营
                /*self.up_partner_id(o.up_partner_id);
                 self.up_partner_id.isModified(false);
                 self.down_partner_id(o.down_partner_id);
                 self.down_partner_id.isModified(false);*/
				self.plan_describe('');
				self.plan_describe.isModified(false);
			} else { //自营
				if (parseInt(self.buy_sell_type()) == config.firstBuyLastSale) { //先采后销
                    /*self.up_partner_id(o.up_partner_id);
                     self.up_partner_id.isModified(false);*/
					self.down_partner_id(0);
					self.down_partner_id.isModified(false);
					if ($.inArray(parseInt(self.type()), config.projectTypeImport) >= 0) { //进口
						self.purchase_currency(2);
					} else {
						self.purchase_currency(1);
					}
				} else if (parseInt(self.buy_sell_type()) == config.firstSaleLastBuy) { //先销后采
					self.up_partner_id(0);
					self.up_partner_id.isModified(false);
                    /*self.down_partner_id(o.down_partner_id);
                     self.down_partner_id.isModified(false);*/
				}
                /*self.plan_describe(o.plan_describe);
                 self.plan_describe.isModified(false);*/
			}

			if ($.inArray(parseInt(self.type()), config.projectTypeWarehouseReceipt) < 0) { //非仓单质押
				self.storehouse_id(0);
				self.storehouse_id.isModified(false);
			}
            /*else {
             self.storehouse_id(o.storehouse_id);
             self.storehouse_id.isModified(false);
             }*/
		};

		self.allGoods = ko.observableArray();
		self.goodsItems = ko.observableArray();
		self.units = [];
		self.currencies = [];

		self.formatGoods = function (data) {
			if (data == null || data == undefined)
				return;

			for (var i = 0; i < data.length; i++) {
				self.allGoods().push(data[i]);
			}
		}

		self.formatGoodsItems = function (data) {
			if (data == null || data == undefined)
				return;

			for (var i in data) {
				data[i]['currencies'] = self.currencies;
				var obj = new ProjectGoods(data[i]);
				self.goodsItems().push(obj);
			}
		}

		self.saveBtnText = ko.observable("保存");
        self.submitBtnText = ko.observable("提交");
		self.actionState = 0;
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		};

		self.getPostData = function () {
			self.goodsDetail = [];
			if (Array.isArray(self.goodsItems()) && self.goodsItems().length > 0) {
				ko.utils.arrayForEach(self.goodsItems(), function (item, i) {
					self.goodsDetail[i] = inc.getPostData(item, ["currencies", "isShowPurchasePrice", "isShowSalePrice", "purchase_currency_ico", "sell_currency_ico"]);
				});
			}

			return inc.getPostData(self, ["goodsItems", "showBuySaleOrder", "showAnalysisPlan", "showStorehouse", "showDownPartner", "showUpPartner", "units", "currencies", "allGoods", "currencies", "purchase_currency_ico", "sell_currency_ico", "isShowPurchasePrice", "isShowSalePrice", "isShowAgent", "msg"]);
		};

		self.save = function () {
			if (!self.isValid()) {
			    self.errors.showAllMessages();
				return;
			}
			self.saveBtnText("保存中" + inc.loadingIco);
			var formData = {"data": self.getPostData()};
			if (self.actionState == 1) {
				return;
            }
			self.actionState = 1;
			// console.log(formData);
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/saveAdd',
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						location.href = "/<?php echo $this->getId() ?>/detail/?id=" + json.data;
					} else {
						self.actionState = 0;
						self.saveBtnText("保存");
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					self.actionState = 0;
					self.saveBtnText("保存");
					layer.alert("保存失败！" + data.responseText, {icon: 5});
				}
			});
		}
        self.submit = function() {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
            self.submitBtnText("提交中" + inc.loadingIco);
            var formData = {"data": self.getPostData()};
            if (self.actionState == 1) {
                return;
            }
            self.actionState = 1;
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/saveAdd',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        self.doSubmit(json.data);
                    } else {
                        self.actionState = 0;
                        self.submitBtnText("提交");
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    self.actionState = 0;
                    self.saveBtnText("提交");
                    layer.alert("提交失败！" + data.responseText, {icon: 5});
                }
            });
        }
        self.doSubmit = function(projectId) {
            var formData = "id="+projectId;
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/submit',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg(json.data, {icon: 6, time: 1000}, function () {
                            location.href = '/<?php echo $this->getId() ?>/';
                        });
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
        }

		self.back = function () {
			location.href = "/<?php echo $this->getId() ?>/";
		}
	}
</script>
