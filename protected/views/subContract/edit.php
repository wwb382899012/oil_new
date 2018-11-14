<link href="/css/businessconfirm.css?key=20180112" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/resize.js"></script>
<link href="/css/style/projectdetail.css?key=20180112" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/clipboard.js"></script>

<section >
    <section class="content-header">
        <div class="content-header__des">
            <?php echo empty($this->pageTitle)?$this->moduleName:$this->pageTitle ?>
        </div>
    </section>

    <section class="content sub-container">
        <!-- 项目附件信息 -->
        <div class="box box-primary sub-container__box padding-bottom-10">
            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;附件信息</h3>
                <span class="box-title__hiden">
                  <i class="fa fa-angle-double-up"></i> 收起</span>
            </div>

            <?php
            $itemHead = '<div class="box-body box-body-custom box-content-custom" >
                                <div class="form-group form-group-custom  form-group-custom-upload">
                                    <div class="form-group">';
            $itemEnd = '</div>
                                </div>
                            </div>';
            $attachs = $this->map["project_launch_attachment_type"];
            if (Utility::isNotEmpty($attachs)) {
                $index = 0;
                foreach ($attachs as $key => $row) {
                    if ($index % 2 == 0) echo $itemHead;
                    ?>
                    <label class="col-sm-2 control-label">
                        <span class="upload-title upload-title-custom"><?php echo $row["name"] ?>：</span>
                    </label>
                    <div class="col-sm-10">
                        <ul class="list-unstyled list-unstyled-custom">
                            <?php if (Utility::isNotEmpty($attachments[$key])) {
                                foreach ($attachments[$key] as $val) {
                                    if (!empty($val['file_url'])) { ?>
                                        <li class="list-unstyled__upload-list">
                                            <a class="text-name-custom" target="_blank"  href="/project/getFile/?id=<?php echo $val['id'] ?>&fileName=<?php echo $val['name'] ?>"><?php echo $val['name'] ?></a>
                                        </li>
                                        <?php
                                    } else {
                                        echo '无';
                                    }
                                }
                            } else {
                                echo '<p class="form-control-static">无</p>';
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                    if ($index % 2 != 0) echo $itemEnd;
                    ++$index;
                }
            }
            ?>
            <div class="box-body box-body-custom box-content-custom">
                <div class="form-group form-group-custom  form-group-custom-upload">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <span class="upload-title upload-title-custom">备注：</span>
                        </label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data['remark'];?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 项目附件信息 -->

        <!-- 合同信息 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;合同信息 (
                    <span class="project-num">
                        <a href="/project/detail/?id=<?php echo $project['project_id'] ?>&t=1" target="_blank">项目编号 : <?php echo $project["project_code"] ?></a></span> )
                </h3>
            </div>
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="box-body box-body-custom">
                    <?php
                    $prefix = $data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY ? '采购' : '销售';
                    $contractTypeMapKey = $data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY ? 'buy_sub_contract_type' : 'sale_sub_contract_type';
                    ?>
                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-2 col-xl-1 control-label control-label-custom">
                          <span class="label-custom__span-red">*</span><?php echo $prefix; ?>合同类型 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择<?php echo $prefix; ?>合同类型" id="type" name="obj[type]" data-bind="value:category,valueAllowUnset: true">
                                <?php foreach ($this->map[$contractTypeMapKey] as $k => $v) {
                                    echo "<option value='" . $k . "'>" . $v . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-2 col-xl-1 control-label control-label-custom">
                          <span class="label-custom__span-red">*</span>交易主体 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择交易主体" id="corporation_id" name="obj[corporation_id]" data-live-search="true" data-bind="optionsCaption: '请选择交易主体',value:corporation_id,valueAllowUnset: true" disabled>
                                <?php
                                $cors = UserService::getUserSelectedCorporations();
                                foreach ($cors as $v) {
                                    echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group form-group-custom form-group ">
                        <?php $categoryPrefix = $data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY ? '上游' : '下游';
                        $title = $categoryPrefix . '合作方 ：' ?>
                        <label for="type" class="col-lg-2 col-xl-1 control-label control-label-custom">
                          <span class="label-custom__span-red">*</span><?php echo $title; ?>
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择<?php echo $title ?>" id="partner_id" name="obj[partner_id]" data-live-search="true" data-bind="optionsCaption: '请选择<?php echo $title ?>',value: partner_id,valueAllowUnset: true">
                                <?php
                                $partners = PartnerService::getUpPartners();
                                if ($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_SALE) {
                                    $partners = PartnerService::getDownPartners();
                                }
                                foreach ($partners as $v) {
                                    echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group form-group-custom form-group " data-bind="visible:isShowAgent">
                        <label for="type" class="col-lg-2 col-xl-1 control-label control-label-custom">
                        采购代理商 ：</label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择采购代理商" id="agent_id" name="obj[agent_id]" data-live-search="true" data-bind="optionsCaption: '请选择采购代理商',value: agent_id,valueAllowUnset: true">
                                <?php
                                $upPartners = PartnerService::getAgentPartners();
                                foreach ($upPartners as $v) {
                                    echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                                } ?>
                            </select>
                        </div>
                        <label for="type" class="col-lg-2 col-xl-1 control-label control-label-custom control-label-custom-right"><span data-bind="html:agentTypeReqSpan"></span>代理模式 ：</label>
                        <div class="col-sm-4">
                            <select class="form-control" title="请选择代理模式" id="agent_type" name="obj[agent_type]" data-bind="optionsCaption: '请选择代理模式',value: agent_type,valueAllowUnset: true">
                                <option value=''>请选择代理模式</option>
                                <?php foreach ($this->map["buy_agent_type"] as $k => $v) {
                                    echo "<option value='" . $k . "'>" . $v . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-2 col-xl-1 control-label control-label-custom">
                        <span class="label-custom__span-red">*</span><?php echo $prefix; ?>币种 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control" title="请选择<?php echo $title ?>" id="currency" name="obj[currency]" data-bind="optionsCaption: '请选择<?php echo $title ?>',value: currency,valueAllowUnset: true">
                                <?php foreach ($this->map["currency_type"] as $k => $v) {
                                    echo "<option value='" . $k . "'>" . $v . "</option>";
                                } ?>
                            </select>
                        </div>
                        <label for="type" class="col-lg-2 col-xl-1 control-label control-label-custom control-label-custom-right">
                        <span class="label-custom__span-red">*</span>即期汇率 ：
                        </label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="corporate" name="data[exchange_rate]" placeholder="即期汇率" data-bind="value:exchange_rate">
                        </div>
                    </div>

                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-2 col-xl-1 control-label control-label-custom">
                        <span class="label-custom__span-red">*</span>价格方式 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择价格方式" id="price_type" name="obj[price_type]" data-bind="optionsCaption: '请选择价格方式',value:price_type,valueAllowUnset: true">
                                <option value=''>请选择价格方式</option>
                                <?php foreach ($this->map["price_type"] as $k => $v) {
                                    echo "<option value='" . $k . "'>" . $v . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
    
                </div>
            </form>
        </div>
        <!-- 合同信息 -->

        <!-- 交易明细 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;交易明细</h3>
            </div>
            <div class="box-header  box-content-custom">
                <span class="box-content__company-style">&nbsp;<?php echo $prefix; ?>合同</span>
                <div class="responser">
                    <label class="box-content__charger">
                      <span class="label-custom__span-red">*</span>负责人 ：
                    </label>
                    <select class="box-content__custom-select selectpicker" title="请选择负责人" data-live-search="true" id="manager_user_id" name="obj[manager_user_id]" data-bind="value: manager_user_id,valueAllowUnset: true">
                        <option value=''>请选择负责人</option>
                        <?php
                        $users = UserService::getProjectManageUsers();
                        foreach ($users as $v) {
                            echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
                        } ?>
                    </select>
                </div>
            </div>
                <!-- <span class="box-content__buy-amount">采购总价 : 24000.00万元</span> -->
            <div class="box-body  box-content-custom">
                <!-- ko component: {
                     name: "contract-goods",
                     params: {
                                 exchange_type:type,
                                 price_type:price_type,
                                 items:goodsItems,
                                 allGoods: allGoods,
                                 units:units,
                                 exchange_rate:exchange_rate,
                                 currencies:currencies,
                                 currency:currency
                                 }
                 } -->
                <!-- /ko -->
            </div>

            <span data-bind="visible:formula_status">
            <div class="box-body box-body-custom">
                <div class="line-dot"></div>
                <div class="box-body form-horizontal form-horizontal-custom">
                    <div class="form-group pd-bottom-0">
                        <label for="type" class="col-sm-2 control-label custom-width-10"><span data-bind="html:formulaReqSpan"></span><?php echo $prefix; ?>计价公式</label>
                        <div class="col-sm-8">
                            <textarea rows="3" class="form-control" id="formula" name="data[formula]" placeholder="<?php echo $prefix; ?>计价公式" data-bind="value:formula"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            </span>
            <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/contractGoods.php"; ?>
        </div>
        <!-- 交易明细 -->

        <!-- 代理费明细 -->
        <div class="box box-primary sub-container__box" data-bind="visible:isShowAgent">
            <div class="box-header with-border box-content-title">
              <h3 class="box-title">&nbsp;&nbsp;&nbsp;手续费明细</h3>
            </div>
            <div class="box-header  box-content-custom">
              <span class="box-content__company-style">&nbsp;代理手续费</span>
            </div>
            <div class="box-body  box-content-custom">
                <table class="table table-hover table-hover-custom">
                    <thead>
                        <tr>
                            <th style="width:130px;text-align:center">品名</th>
                            <th style="width:150px;text-align:center"><span class="label-custom__span-red">*</span>计费方式</th>
                            <th style="width:150px;text-align:center"><span class="label-custom__span-red">*</span>计费单价</th>
                            <th style="width:100px;text-align:center"><span class="label-custom__span-red">*</span>计费单位</th>
                            <th style="width:130px;text-align:center"><span class="label-custom__span-red">*</span>代理手续费率</th>
                            <th style="text-align:center">代理手续费</th>
                        </tr>
                    </thead>
                    <tbody data-bind="foreach: goodsItems">
                        <tr>
                            <td style="text-align:center;vertical-align: middle!important;">
                                <span data-bind="text:goods_name"></span>
                            </td>
                            <td style="text-align:center">
                                <select class="form-control input-sm" title="请选择计费方式" name="agent_type" data-bind="value:type,valueAllowUnset: true">
                                    <?php foreach ($this->map["agent_fee_pay_type"] as $k => $v) {
                                        echo "<option value='" . $k . "'>" . $v . "</option>";
                                    } ?>
                                </select>
                            </td>
                            <td style="text-align:center">
                                <div class="input-group  input-group-sm">
                                    <span class="input-group-addon">￥</span>
                                    <input type="text" class="form-control input-sm" name="agent_price" placeholder="计费单价" data-bind="money:agent_price, disable:isRateAgentPriceDisable">
                                </div>
                            </td>
                            <td style="text-align:center">
                                <select class="form-control input-sm" title="请选择计费单位" name="agent_unit" data-bind="optionsCaption: '请选择计费单位',value:agent_unit,valueAllowUnset: true, disable:isAgentunitDisable">
                                    <option value=''>请选择计费单位</option>
                                    <?php foreach ($this->map["goods_unit"] as $k => $v) {
                                        echo "<option value='" . $v["id"] . "'>" . $v["name"] . "</option>";
                                    } ?>
                                </select>
                            </td>
                            <td style="">
                                <div class="input-group  input-group-sm">
                                    <input type="text" class="form-control" name="fee_rate" placeholder="代理手续费率" data-bind="percent:fee_rate, disable:isRateDisable">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="text-align:right;vertical-align: middle!important;">
                                ￥ <span data-bind="moneyText:agent_amount"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- 代理费明细 -->

        <!-- 收付款明细 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;收付款明细</h3>
            </div>
            <div class="box-header  box-content-custom">
                <span class="box-content__company-style">
                    <?php
                    if ($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY) {
                        echo '&nbsp;&nbsp;上游付款计划';
                    } else {
                        echo '&nbsp;下游收款计划';
                    }
                    ?>
                </span>
            </div>
            <div class="box-body  box-content-custom">
            <!-- ko component: {
                     name: "payments",
                     params: {
                                type:<?php echo $data['type'] ?>,
                                items:plans,
                                paymentTypes:payTypes,
                                currencies:currencies,
                                exchange_rate:exchange_rate
                            }
                 } -->
            <!-- /ko -->
            </div>
            <div class="line-dot"></div>
            <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/paymentItems.php"; ?>
        </div>
        <!-- 收付款明细 -->
        <!-- 上游交货日期与交票期限 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;最终交货/发货日期</h3>
            </div>
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="box-body box-contract-extra">
                    <div class="col-md-6 bd-right-2">
                        <div class="contract-type-container">
                            <div class="contract-type__circle"><?php if($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY): ?>上游<?php else: ?>下游<?php endif;?></div>
                        </div>
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-2 col-xl-2  control-label control-label-custom">
                                <span class="label-custom__span-red">*</span>最终<?php if($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY): ?>交货<?php else: ?>发货<?php endif;?>日期
                            </label>
                            <!-- 系统默认 -->
                            <div class="col-lg-6" data-bind="visible: deliveryTermDefault()">
                                <input type="text" class="form-control input-sm" id="delivery_term1" disabled name="buy[delivery_term]" placeholder="日期" data-bind="value:delivery_term">
                            </div>
                            <!-- 合同约定 -->
                            <div class="col-lg-6" data-bind="visible: !deliveryTermDefault()">
                                <input type="text" class="form-control input-sm date" id="delivery_term" name="buy[delivery_term]" placeholder="日期" data-bind="value:delivery_term">
                            </div>
                            <div class="col-lg-4">
                                <select class="form-control selectpicker" title="方式" id="purchase_currency" name="buy[delivery_mode]" data-bind="optionsCaption: '请选择',value:delivery_mode,valueAllowUnset: true">
                                    <?php foreach ($this->map["contract_delivery_mode"] as $k => $v) {
                                        echo "<option value='" . $k . "'>" . $v . "</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-2 col-xl-2  control-label control-label-custom">
                                <span class="label-custom__span-red">*</span><?php if($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY): ?>收票<?php else:?>开票<?php endif;?>时间
                            </label>
                            <div class="col-lg-10">
                                <div class="input-group  input-group-sm">
                                    <input type="text" class="form-control" id="days" name="buy[days]" placeholder="时间" data-bind="value:days">
                                    <span class="input-group-addon">天</span>
                                </div>
                                <p style="font-size: 8px;">（根据<?php if($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY): ?>入库单<?php else:?>出库单<?php endif;?>日期倒推）</p>
                            </div>
                        </div>

                    </div>

                </div>
            </form>

        </div>
        <!-- 上游交货日期与交票期限 -->
        <!-- 合同条款 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
              <h3 class="box-title">&nbsp;&nbsp;&nbsp;合同条款</h3>
            </div>
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="box-body box-contract-extra">
                    <div class="col-md-6 bd-right-2">
                        <div class="contract-type-container">
                          <div class="contract-type__circle">
                                <?php
                                if ($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY) {
                                    echo '上游';
                                } else {
                                    echo '下游';
                                }
                                ?>
                          </div>
                          <!-- <div class="contract-type__line"></div> -->
                        </div>
                        <div class="box-body box-body-custom">
                            <!-- ko component: {
                                        name: 'contract-items',
                                        params: {
                                                    type: type,
                                                    category: category,
                                                    config:contractItemsMap,
                                                    labelWidth:2,
                                                    controlWidth:10
                                                }
                                    } -->
                            <!-- /ko -->
                        </div>
                    </div>
                    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/contractItems.php";?>
                </div>
            </form>
        </div>
        <!-- 合同条款 -->
        
        <!-- 提交保存 -->
        <div class="box box-primary sub-container__box sub-container__fixed">
            <div class="box-body">
                <div class="form-group form-group-custom-btn">
                  <!-- 此处删除了类：col-sm-offset-2 col-sm-10  增加了类submit-btn-custom-->
                  <div class="btn-contain-custom">
                    <button type="button" class="btn btn-contain__submit" data-bind="click:tempSave, html:tempSaveBtnText"></button>
                    <button type="button" class="btn btn-contain__save" data-bind="click:save, html:saveBtnText"></button>
                    <button type="button" class="btn btn-contain__default history-back" data-bind="click:back">返回</button>
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
		view = new ViewModel(<?php echo json_encode($data) ?>);
		view.formatGoods(<?php echo json_encode($goods) ?>);
		view.units = inc.objectToArray(<?php echo json_encode(array_values($this->map["goods_unit"])); ?>);
		view.formatCurrencies(<?php echo json_encode($this->map["currency"]); ?>);
		view.formatPayTypes(<?php echo $data['type'] == ConstantMap::BUY_TYPE ? json_encode($this->map["pay_type"]) : json_encode($this->map["proceed_type"]); ?>);
		view.formatGoodsItems(<?php echo json_encode($goodsItem) ?>);
		view.formatPaymentPlans(<?php echo json_encode($payments) ?>);
		view.contractItemsMap =<?php echo !empty($extra) ? json_encode($extra) : json_encode($this->map["contract_config"]); ?>;
		view.initPlans();
		ko.applyBindings(view);
        $('.date').datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});

		var clipboard = new Clipboard('.copy-project-num');
		$('span.box-title__hiden').on('click',function(event) {
			var ele = $(this);
			$(ele).html('');
			var toggle1 = $(ele).parents("div.sub-container__box").find("div.box-content-custom:visible");
			var toggle2 = $(ele).parents("div.sub-container__box").find("div.form-horizontal:visible");
			var toggle3 = $(ele).parents("div.sub-container__box").find("div.box-body-overflow:visible");

			if(toggle1.length > 0 || toggle2.length > 0 || toggle3.length > 0) {
				$(ele).parents("div.sub-container__box").find("div.box-content-custom").hide('slow');
				$(ele).parents("div.sub-container__box").find("table").hide('slow');
				$(ele).parents("div.sub-container__box").find("div.form-horizontal").hide('slow');
				$(ele).parents("div.sub-container__box").find("div.box-body-overflow").hide('slow');
				$(ele).parents("div.sub-container__box").find("div.line-dot").hide('slow');

				var eleI = $('<i class="fa fa-angle-double-down"></i>');
				$(ele).html(' 展开');
				eleI.prependTo($(ele));
			} else {
				$(ele).parents("div.sub-container__box").find("div.box-content-custom").show('slow');
				$(ele).parents("div.sub-container__box").find("table").show('slow');
				$(ele).parents("div.sub-container__box").find("div.form-horizontal").show('slow');
				$(ele).parents("div.sub-container__box").find("div.box-body-overflow").show('slow');
				$(ele).parents("div.sub-container__box").find("div.line-dot").show('slow');


				var eleI = $('<i class="fa fa-angle-double-up"></i>');
				$(ele).html(' 收起');
				eleI.prependTo($(ele));

			}
		});
	});
	function ViewModel(option) {
		var defaults = {
			contract_id: 0,
			project_id: 0,
			type: 0,
			partner_id: 0,
			category: <?php echo Map::$v['project_business_type'][$project['type']]['default_contract_category'][$data['type']]; ?>,
			is_main: 0,
			contract_code: 0,
			contract_name: '',
			corporation_id: 0,
			currency: <?php echo Map::$v['project_business_type'][$project['type']]['default_currency'][$data['type']]; ?>,
			agent_id: 0,
			agent_type: 0,
			exchange_rate: 1,
			price_type: 0,
			formula: '',
			manager_user_id: 0,
            delivery_mode:0,
            delivery_term:"<?php echo date("Y-m-d",strtotime("+".ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM." day")); ?>",
            days:"<?php echo ConstantMap::CONTRACT_DEFAULT_DAYS; ?>"
		};
		var o = $.extend(defaults, option);
		var self = this;

		self.contract_id = ko.observable(o.contract_id);
		self.project_id = ko.observable(o.project_id);
		self.type = ko.observable(o.type);
		self.category = ko.observable(o.category).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择<?php echo $prefix; ?>合同类型"
			}
		});

		//购销顺序展示条件
		self.isShowAgent = ko.computed(function () {
			if (self.category() == config.buySaleContractTypeAgentImport)
				return true;
			return false;
		}, self);
		self.partner_id = ko.observable(o.partner_id).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择合作方"
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
		self.currency = ko.observable(o.currency).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择币种"
			}
		});

        self.contract_default_delivery_term = ko.observable(o.contract_default_delivery_term);

        self.delivery_mode = ko.observable(o.delivery_mode);

        self.delivery_mode.subscribe(function (v) {
            if(v==0) {
                self.delivery_term((new Date(new Date().getTime() + self.contract_default_delivery_term()*24*60*60*1000)).Format("yyyy-MM-dd"));
            }
            else
                self.delivery_term('');
        });

        self.delivery_term = ko.observable(o.delivery_term).extend({
            custom: {
                params: function (v) {
                    if (v=='' || v==null || v.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/)==null)
                        return false;
                    else
                        return true;
                },
                message: "请填写正确的最终<?php if($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY): ?>交货<?php else: ?>发货<?php endif; ?>日期"
            }
        });

        self.days = ko.observable(o.days).extend({
            custom: {
                params: function (v) {

                    if (v=='' || v==null || parseInt(v)<0 || isNaN(v))
                        return false;
                    else
                        return true;
                },
                message: "请输入一个不小于0的数字"
            }
        });
        //最终收货/发货日期：方式
        self.deliveryTermDefault = ko.computed(function () {
            console.log(self.delivery_mode());
            if (self.delivery_mode() != 1)//系统默认
                return true;
            return false;
        }, self);

        self.contractGoodsUnitConvert = ko.observable(o.contractGoodsUnitConvert);
        self.contractGoodsUnitConvertValue = ko.observable(o.contractGoodsUnitConvertValue);

		self.agent_id = ko.observable(o.agent_id);
		self.agent_type = ko.observable(o.agent_type).extend({
			custom: {
				params: function (v) {
					if (self.category() == config.buySaleContractTypeAgentImport && self.agent_id() > 0) {
						return v > 0;
					}
					return true;
				},
				message: "请选择代理模式"
			}
		});

		self.agentTypeReqSpan = ko.computed(function () {
			if (self.category() == config.buySaleContractTypeAgentImport && self.agent_id() > 0) {
				return '<span class="label-custom__span-red">*</span>';
			} else {
				return '';
			}
		}, self);

		self.agentFeeReqSpan = ko.computed(function () {
			if (self.category() == config.buySaleContractTypeAgentImport && self.agent_id() > 0) {
				return '<span class="label-custom__span-red">*</span>';
			} else {
				return '';
			}
		}, self);

        // o.exchange_rate = inc.toDecimal(o.exchange_rate, 0);
        self.exchange_rate = ko.observable(parseFloat(o.exchange_rate)).extend({required: true, positiveNumber: true});
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

		self.category.subscribe(function (v) {
			if(self.type() == 1) {
				if (v == config.buySaleContractTypeInternal) {
					self.currency(config.currencyRMB);
				} else {
					self.currency(config.currencyDollar);
				}
            }
		});

		self.contractItemsMap = {};
		self.formula = ko.observable(o.formula).extend({
			custom: {
				params: function (v) {
					if (self.price_type() == config.tempPrice) {
						return v != '';
					}
					return true;
				},
				message: "请填写计价公式"
			}
		});
		self.formulaReqSpan = ko.computed(function () {
			if (self.price_type() == config.tempPrice) {
				return '<span class="label-custom__span-red">*</span>';
			} else {
				return '';
			}
		}, self);

        self.formula_status = ko.computed(function(){
            return parseInt(self.price_type())==config.tempPrice;
        });

		self.manager_user_id = ko.observable(o.manager_user_id).extend({
			custom: {
				params: function (v) {
					if (v > 0)
						return true;
					else
						return false;
				},
				message: "请选择<?php echo $prefix; ?>合同负责人"
			}
		});
		self.tempSaveBtnText = ko.observable('暂存');
		self.saveBtnText = ko.observable('保存');
		self.actionState = 0;
		self.errors = ko.validation.group(self);
		self.isValid = function () {
			return self.errors().length === 0;
		};
		self.allGoods = ko.observableArray();
		self.goodsItems = ko.observableArray();

		self.plans = ko.observableArray();
		self.initPlans = function () {
			if (self.contract_id() == 0 && self.plans().length == 0) {
				self.plans.push(new Payments({
					currencies: self.currencies(),
					paymentTypes: self.payTypes(),
					exchange_rate: self.exchange_rate(),
					type: self.type()
				}));
			}
		}
		self.units = [];
		self.currencies = ko.observableArray();
		self.payTypes = ko.observableArray();

		self.formatGoods = function (data) {
			if (data == null || data == undefined)
				return;
			self.allGoods.removeAll();

			for (var i = 0; i < data.length; i++) {
				self.allGoods.push(data[i]);
			}
		};

		self.formatCurrencies = function (data) {
			if (data == null || data == undefined)
				return;

			for (var i in data) {
				self.currencies.push(data[i]);
			}
		};

		self.formatPayTypes = function (data) {
			if (data == null || data == undefined)
				return;

			for (var i in data) {
				self.payTypes.push(data[i]);
			}
		};

		self.formatGoodsItems = function (data) {
			if (data == null || data == undefined)
				return;
			for (var i in data) {
				data[i]['currencies'] = self.currencies;
				data[i]['allGoods'] = self.allGoods;
				var obj = new ContractGoods(data[i]);
				self.goodsItems().push(obj);
			}
		};

		self.formatPaymentPlans = function (data) {
			if (data == null || data == undefined)
				return;
			for (var i in data) {
				data[i]['currencies'] = self.currencies;
				data[i]['paymentTypes'] = self.payTypes;
				data[i]['exchange_rate'] = self.exchange_rate();
				data[i]['type'] = self.type();
				var obj = new Payments(data[i]);
				self.plans().push(obj);
			}
		}

		self.calculateGoodsAmountCurrency = function () {
			var total = 0;
			ko.utils.arrayForEach(self.goodsItems(), function (item) {
				var value = parseFloat(item.amount());
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(0);
		};

		self.calculateGoodsAmount = function () {
			var total = 0;
			ko.utils.arrayForEach(self.goodsItems(), function (item) {
				var value = parseFloat(item.amount_cny());
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(0);
		};

		self.calculateTotalPaymentAmount = function () {
			var total = 0;
			ko.utils.arrayForEach(self.plans(), function (item) {
				var value = parseFloat(item.amount_cny());
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(0);
		};

		self.calculateTotalgoodsAmount = function () {
			var total = 0;
			ko.utils.arrayForEach(self.goodsItems(), function (item) {
				var value = parseFloat(item.amount_cny());
				if (!isNaN(value)) {
					total += value;
				}
			});
			return total.toFixed(0);
		}

		self.amount = ko.computed(function () {
			self.calculateGoodsAmountCurrency();
		}, self);

		self.amount_cny = ko.computed(function () {
			self.calculateGoodsAmount();
		}, self);

		self.tempSaveOperate = ko.observable(0);
		self.getPostData = function () {
			self.contractGoods = [];
			if (Array.isArray(self.goodsItems()) && self.goodsItems().length > 0) {
				ko.utils.arrayForEach(self.goodsItems(), function (item, i) {
					self.contractGoods[i] = inc.getPostData(item, ["currencies", "currency_ico", "allGoods", "isRateDisable", "isRateAgentPriceDisable", "isAgentunitDisable", "calculateAgentAmount"]);
				});
			}

			self.extra = koForm.getValues(self.contractItemsMap[self.type()][self.category()].extraItems);
			/*var extra = self.contractItemsMap[self.type()][self.category()].extraItems;
			if (Array.isArray(extra) && extra.length > 0) {
				ko.utils.arrayForEach(extra, function (item, i) {
					self.extra.push({
						"key": item.key(),
						"name": item.name(),
						"value": $.isArray(item.value()) ? item.value().join(',') : item.value()
					});
				});
			}*/

			self.paymentPlans = [];
			if (Array.isArray(self.plans()) && self.plans().length > 0) {
				ko.utils.arrayForEach(self.plans(), function (item, i) {
					self.paymentPlans[i] = inc.getPostData(item, ["currencies", "currency_ico", "paymentTypes", "showExpenseNameInput", "expense_width"]);
				});
			}

			return inc.getPostData(self, ["isShowAgent", "units", "currencies", "allGoods", "payTypes", "tempSaveBtnText", "saveBtnText", "agentTypeReqSpan", "agentFeeReqSpan", "formulaReqSpan", "contractItemsMap", "goodsItems", "plans"]);
		}

		self.tempSave = function () {
			self.tempSaveBtnText("暂存中" + inc.loadingIco);
			self.tempSaveOperate(1);
			self.sendSaveAjax();
		};

		self.save = function () {
			if (!self.isValid()) {
				self.errors.showAllMessages();
				return;
			}
			if (self.amount_cny() == undefined) {
				self.amount_cny = ko.observable(self.calculateGoodsAmount());
			}

			if (self.amount() == undefined) {
				self.amount = ko.observable(self.calculateGoodsAmountCurrency());
			}

			var payTypeDesc = '付款';
			var processTypeDesc = '采购';
			if (self.type() == 2) {
				payTypeDesc = '收款';
				processTypeDesc = '销售';
			}
			if (parseFloat(self.calculateTotalgoodsAmount()) < parseFloat(self.calculateTotalPaymentAmount())) {
				layer.confirm(payTypeDesc + "金额：￥" + parseFloat(self.calculateTotalPaymentAmount() / 100) + " 超过人民币" + processTypeDesc + "总价：￥" + parseFloat(self.amount_cny() / 100) + ",是否继续？", {
					icon: 3,
					'title': '提示'
				}, function (index) {
					self.doSave();
					layer.close(index);
				})
			} else {
				self.doSave();
			}
		}

		self.doSave = function () {
			self.tempSaveOperate(0);
			self.saveBtnText("保存中" + inc.loadingIco);
			self.sendSaveAjax();
		}

		self.sendSaveAjax = function () {
			if (self.actionState == 1)
				return;
			self.actionState = 1;
			var formData = {"data": self.getPostData()};
			// console.log(formData);
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/save',
				data: formData,
				dataType: "json",
				success: function (json) {
					self.tempSaveBtnText("暂存");
					self.saveBtnText("保存");
					self.actionState = 0;
					if (json.state == 0) {
						layer.msg('操作成功！', {icon: 6, time: 1000}, function () {
							if (self.tempSaveOperate() == 0) {
								location.href = "/businessConfirm/detail/?id=" + json.data;
							} else {
								location.href = '/subContract/edit?id=' + json.data;
							}
						});
					} else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					self.tempSaveBtnText("暂存");
					self.saveBtnText("保存");
					self.actionState = 0;
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});
		};

		self.back = function () {
			history.back();
		}
	}

    Date.prototype.Format = function (fmt) {
        var o = {
            "M+": this.getMonth() + 1, //月份
            "d+": this.getDate(), //日
            "H+": this.getHours(), //小时
            "m+": this.getMinutes(), //分
            "s+": this.getSeconds(), //秒
            "q+": Math.floor((this.getMonth() + 3) / 3), //季度
            "S": this.getMilliseconds() //毫秒
        };
        if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o)
            if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    }
</script>
