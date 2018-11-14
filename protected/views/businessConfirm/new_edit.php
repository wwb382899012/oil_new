<link href="/newUI/css/business-confirm/edit.css" rel="stylesheet" type="text/css"/>
<style>
    .table {
        width: 100%;
    }

    .bootstrap-select.btn-group ul.dropdown-menu {
        max-height: 280px !important;
        overflow: auto;
    }
</style>
<?php
$menus = [
    ['text' => '项目管理'],
    ['text' => '商务列表', 'link' => '/businessConfirm/'],
    ['text' => '商务确认']
];
$buttons = [];
$buttons[] = ['text' => '驳回', 'attr' => ['data-bind' => 'click:startBack,visible:is_can_back()==1 && !isBack()', 'class_abbr' => 'action-default-base']];
$buttons[] = ['text' => '暂存', 'attr' => ['data-bind' => 'click:tempSave,html:tempSaveBtnText,visible:!isBack()', 'class_abbr' => 'action-default-base']];
$buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText,visible:!isBack()']];
$buttons[] = ['text' => '提交驳回', 'attr' => ['data-bind' => 'click:saveBack, html:saveBackBtnText,visible:isBack()', 'style' => 'display:none']];
$buttons[] = ['text' => '取消驳回', 'attr' => ['data-bind' => 'click:cancelBack,visible:isBack()', 'style' => 'display:none', 'class_abbr' => 'action-default-base']];
$this->loadHeaderWithNewUI($menus, $buttons, '/businessConfirm/');
?>

<input type='hidden' name='obj[project_type]' data-bind="value:project_type"/>
<input type='hidden' name='obj[project_id]' data-bind="value:project_id"/>
<input type='hidden' name='obj[project_status]' data-bind="value:project_status"/>
<input type='hidden' name='obj[contract_status]' data-bind="value:contract_status"/>
<input type='hidden' name='obj[buy_sell_type]' data-bind="value:buy_sell_type"/>
<input type='hidden' name='obj[buy_contract_id]' data-bind="value:buy_contract_id"/>
<input type='hidden' name='obj[sell_contract_id]' data-bind="value:sell_contract_id"/>


<div style="height:100%;display:flex;flex-direction: column;">
    <!-- 附件信息 -->
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>附件信息</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <div>
            <ul class="item-com file-info">
                <?php
                $itemHead = '<li>';
                $itemEnd = '</li>';
                $attachs = $this->map["project_launch_attachment_type"];
                if (Utility::isNotEmpty($attachs)) {
                    $index = 0;
                    foreach ($attachs as $key => $row) {
                        if ($index % 1 == 0) echo $itemHead;
                        ?>
                        <label><?php echo $row["name"] ?>：</label>
                        <div>
                            <ul>
                                <?php if (Utility::isNotEmpty($attachments[$key])) {
                                    foreach ($attachments[$key] as $val) {
                                        if (!empty($val['file_url'])) { ?>
                                            <li class="list-unstyled__upload-list">
                                                <a class="text-name-custom" target="_blank"
                                                   href="/project/getFile/?id=<?php echo $val['id'] ?>&fileName=<?php echo $val['name'] ?>"><?php echo $val['name'] ?></a>
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
                        if ($index % 1 != 0) echo $itemEnd;
                        ++$index;
                    }
                }
                ?>
            </ul>
        </div>
        <ul class="item-com file-info">
            <li style="width:100%;">
                <label>备注：</label>
                <p class="form-control-static"><?php echo $data['remark']; ?></p>
            </li>
        </ul>
        <div>
        </div>
    </div>
    <!-- 合同信息 -->
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>
                    合同信息
                    <a style="color:#3E8CF7!important;margin-left:10px;"
                       href="/project/detail/?id=<?php echo $data['project_id'] ?>&t=1"
                       target="_blank">项目编号：<?php echo $data['project_code'] ?></a>
                </p>

            </div>
        </div>
        <ul class="form-com">
            <li>
                <label>项目类型</label>
                <span><?php echo $data["project_type_desc"] ?></span>
            </li>
            <li>
                <label>购销信息</label>
                <span><?php echo $data['buy_sell_desc'] ?></span>
            </li>
            <li data-bind="visible: showBuyContractSelectType">
                <label>采购合同类型<i class="must-logo">*</i></label>
                <div class="ul-li-com">
                    <div class="ul-li-com">
                        <select class="selectpicker show-menu-arrow form-control " title="请选择采购合同类型" id="buy_category"
                                name="buy[category]"
                                data-bind="optionsCaption: '请选择采购合同类型',selectpicker:buy_category, valueAllowUnset: true">
                            <?php foreach ($this->map["buy_contract_type"] as $k => $v) {
                                echo "<option value=" . $k . ">" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
            </li>
            <li data-bind="visible: showBuyContractStaticType">
                <label>采购合同类型</label>
                <div>
                    <p style="border: 1px solid #dcdcdc;padding-left: 10px;border-radius: 3px;background-color: #f7f7f7;height: 32px;"
                       class="form-control-static">国内采购合同</p>
                    <input type="hidden" name="buy[category]" data-bind="value:buy_category"/>
                </div>
            </li>
            <li data-bind="visible: showSellContractStaticType">
                <label data-bind="visible:(isShowUpAndDown()||showOnlyDownPartner())">销售合同类型</label>
                <div>
                    <p style="border: 1px solid #dcdcdc;padding-left: 10px;border-radius: 3px;background-color: #f7f7f7;height: 32px;"
                       class="form-control-static">国内销售合同</p>
                    <input type="hidden" name="sell[category]" data-bind="value:sell_category"/>
                </div>
            </li>
            <li>
                <label>交易主体</label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择交易主体" id="corporation_id"
                        name="obj[corporation_id]"
                        data-live-search="true"
                        data-bind="optionsCaption: '请选择交易主体',selectpicker:corporation_id,valueAllowUnset: true,enable:false"
                        disabled>
                    <?php
                    $cors = UserService::getUserSelectedCorporations();
                    foreach ($cors as $v) {
                        echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible: showStorehouse">
                <label>仓库名称<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择仓库名称" id="storehouse_id"
                        name="obj[storehouse_id]"
                        data-live-search="true" data-bind="selectpicker: storehouse_id,valueAllowUnset: true">
                    <option value='0'>请选择仓库名称</option>
                    <?php
                    $users = Storehouse::getAllActiveStorehouse();
                    foreach ($users as $v) {
                        echo "<option value='" . $v["store_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible: !showOnlyDownPartner()">
                <label>上游合作方<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择上游合作方" id="up_partner_id"
                        name="buy[up_partner_id]"
                        data-live-search="true"
                        data-bind="optionsCaption: '请选择上游合作方',selectpicker: up_partner_id,valueAllowUnset: true">
                    <?php
                    $downPartners = PartnerService::getUpPartners();
                    foreach ($downPartners as $v) {
                        echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible: isShowAgent">
                <label>采购代理商 </label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择采购代理商" id="agent_id"
                        name="buy[agent_id]"
                        data-live-search="true"
                        data-bind="optionsCaption: '请选择采购代理商',selectpicker: agent_id,valueAllowUnset: true">
                    <option value=''>请选择采购代理商</option>
                    <?php
                    $downPartners = PartnerService::getAgentPartners();
                    foreach ($downPartners as $v) {
                        echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible: isShowAgent">
                <label>代理模式<i class="must-logo" data-bind="visible: showAgentDetail()">*</i> </label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择代理模式" id="agent_type"
                        name="buy[agent_type]"
                        data-bind="optionsCaption: '请选择代理模式',selectpicker:agent_type,valueAllowUnset: true">
                    <option value=''>请选择代理模式</option>
                    <?php foreach ($this->map["buy_agent_type"] as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible: !showOnlyUpPartner()">
                <label>下游合作方<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择下游合作方" id="down_partner_id"
                        name="sell[down_partner_id]" data-live-search="true"
                        data-bind="optionsCaption: '请选择下游合作方',selectpicker: down_partner_id,valueAllowUnset: true">
                    <?php
                    $downPartners = PartnerService::getDownPartners();
                    foreach ($downPartners as $v) {
                        echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible: !showOnlyDownPartner()">
                <label>采购币种<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择采购币种" id="purchase_currency"
                        name="buy[currency]"
                        data-bind="optionsCaption: '请选择采购币种',selectpicker:purchase_currency,valueAllowUnset: true">
                    <?php foreach ($this->map["currency_type"] as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible: !showOnlyDownPartner()">
                <label>即期汇率<i class="must-logo">*</i></label>
                <input type="text" class="form-control" id="buy_exchange_rate" name="buy[exchange_rate]"
                       placeholder="采购币种即期汇率" data-bind="value:buy_exchange_rate"/>
            </li>
            <li data-bind="visible: !showOnlyUpPartner()">
                <label>销售币种<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择销售币种" id="sell_currency"
                        name="sell[currency]"
                        data-bind="optionsCaption: '请选择销售币种',selectpicker:sell_currency,valueAllowUnset: true">
                    <?php foreach ($this->map["currency_type"] as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible: !showOnlyUpPartner()">
                <label>即期汇率<i class="must-logo">*</i></label>
                <input type="text" class="form-control" id="sell_exchange_rate" name="sell[exchange_rate]"
                       placeholder="销售币种即期汇率" data-bind="value:sell_exchange_rate"/>
            </li>
            <li data-bind="visible: !showOnlyDownPartner()">
                <label>采购价格方式<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择价格方式" id="buy_price_type"
                        name="obj[buy_price_type]"
                        data-bind="optionsCaption: '请选择采购价格方式',selectpicker:buy_price_type,valueAllowUnset: true">
                    <option value=''>请选择采购价格方式</option>
                    <?php foreach ($this->map["price_type"] as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible: !showOnlyUpPartner()">
                <label>销售价格方式<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择价格方式" id="sell_price_type"
                        name="obj[sell_price_type]"
                        data-bind="optionsCaption: '请选择销售价格方式',selectpicker:sell_price_type,valueAllowUnset: true">
                    <option value=''>请选择销售价格方式</option>
                    <?php foreach ($this->map["price_type"] as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                    } ?>
                </select>
            </li>
        </ul>
    </div>
    <!-- 交易明细 -->
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>交易明细</p>
            </div>
        </div>
        <!-- 采购 -->
        <div class="table-title" data-bind="visible: !showOnlyDownPartner()">
            <span>采购合同</span>
            <div>
                <label style="margin-right:10px;flex: 0 0 4em;">负责人<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择负责人" id="buy_manager_user_id"
                        data-live-search="true" name="buy[manager_user_id]"
                        data-bind="optionsCaption: '请选择负责人',selectpicker: buy_manager_user_id,valueAllowUnset: true">
                    <option value=''>请选择负责人</option>
                    <?php
                    $users = UserService::getProjectManageUsers();
                    foreach ($users as $v) {
                        echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="box-body  box-content-custom" data-bind="visible: !showOnlyDownPartner()">
            <!-- ko component: {
              name: "new_contract-goods",
              params: {
                    exchange_type:buy_type,
                    price_type:buy_price_type,
                    items:buyItems,
                    allGoods: allGoods,
                    units:units,
                    currencies:currencies,
                    exchange_rate:buy_exchange_rate,
                    currency:purchase_currency,
                    goods_can_edit:goods_can_edit,
                  }
            } -->
            <!-- /ko -->
        </div>
        <ul class="form-com" data-bind="visible: !showOnlyDownPartner() &&  buy_formula_status()">
            <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
                <label>采购计价公式<i class="must-logo">*</i></label>
                <textarea rows="3" class="form-control" id="buy_formula" name="buy[buy_formula]" placeholder="采购计价公式"
                          data-bind="value:buy_formula"></textarea>
            </li>
        </ul>
        <!-- 销售 -->
        <div class="table-title" style="margin-top: 10px;" data-bind="visible: !showOnlyUpPartner()">
            <span>销售合同</span>
            <div>
                <label style="margin-right:10px;flex: 0 0 4em;;">负责人<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择负责人" id="sell_manager_user_id"
                        data-live-search="true" name="sell[manager_user_id]"
                        data-bind="optionsCaption: '请选择负责人',value: sell_manager_user_id,valueAllowUnset: true"><!--  -->
                    <option value=''>请选择负责人</option>
                    <?php
                    $users = UserService::getProjectManageUsers();
                    foreach ($users as $v) {
                        echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="box-body  box-content-custom" data-bind="visible: !showOnlyUpPartner()">
            <!-- ko component: {
              name: "new_contract-goods",
              params: {
                    exchange_type:sell_type,
                    price_type:sell_price_type,
                    items:sellItems,
                    allGoods: allGoods,
                    units:units,
                    currencies:currencies,
                    exchange_rate:sell_exchange_rate,
                    currency:sell_currency
                  }
            } -->
            <!-- /ko -->
        </div>
        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_contractGoods.php"; ?>
        <ul class="form-com" data-bind="visible: !showOnlyUpPartner() &&  sell_formula_status()">
            <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
                <label>销售计价公式<i class="must-logo">*</i></label>
                <textarea rows="3" class="form-control" id="sell_formula" name="sell[sell_formula]" placeholder="销售计价公式"
                          data-bind="value:sell_formula"></textarea>
            </li>
        </ul>
    </div>
    <!-- 手续费明细 -->
    <div class="content-wrap" data-bind="visible:showAgentDetail">
        <div class="content-wrap-title">
            <div>
                <p>手续费明细</p>
            </div>
        </div>
        <ul class="table-com">
            <li>
                <span>品名</span>
                <span>计费方式<i class="must-logo">*</i></span>
                <span>计费单价<i class="must-logo">*</i></span>
                <span>计费单位<i class="must-logo">*</i></span>
                <span>代理手续费率<i class="must-logo">*</i></span>
                <span>代理手续费</span>
            </li>
            <!-- ko foreach: buyItems -->
            <li>
                <p>
                    <span data-bind="text:goods_name"></span>
                    <input hidden name='agent_goods_id' data-bind="value:goods_id"/>
                </p>
                <div>
                    <select class="  show-menu-arrow form-control" title="请选择计费方式" name="type"
                            data-bind="optionsCaption: '请选择计费方式',value:type,valueAllowUnset: true">
                        <?php foreach ($this->map["agent_fee_pay_type"] as $k => $v) {
                            echo "<option value='" . $k . "'>" . $v . "</option>";
                        } ?>
                    </select>
                </div>
                <div class="input-with-logo-left">
                    <span>¥</span>
                    <input type="text" class="form-control input-sm" name="agent_price" placeholder="计费单价"
                           data-bind="money:agent_price, disable:isRateAgentPriceDisable">
                </div>
                <select class=" show-menu-arrow form-control" title="请选择计费单位" name="agnet_unit"
                        data-bind="optionsCaption: '请选择计费单位',value:unit,valueAllowUnset: true, disable:isAgentunitDisable">
                    <option value=''>请选择计费单位</option>
                    <?php foreach ($this->map["goods_unit"] as $k => $v) {
                        echo "<option value='" . $v["id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
                <div class="input-with-logo-right">
                    <span>%</span>
                    <input type="text" class="form-control" name="fee_rate" placeholder="代理手续费率"
                           data-bind="percent:fee_rate, disable:isRateDisable">
                </div>
                <div>
                    <span>¥<span style="margin-left: 5px" data-bind="moneyText:agent_amount"></span><input hidden
                                                                                                           name='agent_amount'
                                                                                                           data-bind="value:agent_amount"/></span>
                </div>
            </li>
            <!-- /ko -->
        </ul>
    </div>
    <!-- 收付款明细 -->
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>收付款明细</p>
            </div>
        </div>
        <!-- 上游付款计划 -->
        <div data-bind="if: !showOnlyDownPartner()">
            <div class="table-title">
                <span>上游付款计划</span>
            </div>
            <div class="box-body  box-content-custom">
                <!-- ko component: {
                    name: "payments",
                    params: {
                        type:buy_type,
                        items:paymentItems,
                        paymentTypes:paymentTypes,
                        currencies:currencies,
                        exchange_rate:buy_exchange_rate
                      }
                  } -->
                <!-- /ko -->
            </div>
        </div>
        <!--  下游收款计划 -->
        <div data-bind="if: !showOnlyUpPartner()">
            <div class="table-title">
                <span>下游收款计划</span>
            </div>
            <div class="box-body box-content-custom">
                <!-- ko component: {
                    name: "payments",
                    params: {
                        type:sell_type,
                        items:proceedItems,
                        paymentTypes:proceedTypes,
                        currencies:currencies,
                        exchange_rate:sell_exchange_rate
                      }
                  } -->
                <!-- /ko -->
            </div>
        </div>

        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_paymentItems.php"; ?>
    </div>
    <!-- 上游交货日期与交票期限 -->
    <div class="content-wrap contract-item" style="margin-bottom:0 !important">
        <div class="content-wrap-title">
            <div>
                <p>最终交货/发货日期</p>
            </div>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body box-contract-extra">
                <div class="col-md-6 bd-right-2 contract-item_title" data-bind="visible: !showOnlyDownPartner()">
                    <div class="contract-type-container">
                        <div class="contract-type__circle">上游最终交货日期</div>
                    </div>
                    <div class="box-body box-body-custom">

                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                                最终交货日期<i class="must-logo">*</i>
                            </label>
                            <!-- 系统默认 -->
                            <span>
                                <div class="col-lg-6" data-bind="visible: upDeliveryTermDefault()">
                                <input type="text" class="form-control input-sm" id="up_delivery_term1" disabled
                                       name="buy[up_delivery_term]" placeholder="最终交货日期"
                                       data-bind="value:up_delivery_term">
                            </div>
                                <!-- 合同约定 -->
                            <div class="col-lg-6" data-bind="visible: !upDeliveryTermDefault()">
                                <input type="text" class="form-control input-sm date" id="up_delivery_term"
                                       name="buy[up_delivery_term]" placeholder="最终交货日期"
                                       data-bind="value:up_delivery_term">
                            </div>
                            <div class="col-lg-4">
                                <select class="form-control selectpicker show-menu-arrow" title="方式" id="purchase_currency"
                                        name="buy[delivery_mode]"
                                        data-bind="optionsCaption: '请选择',value:up_delivery_mode,valueAllowUnset: true">
                                    <?php foreach ($this->map["contract_delivery_mode"] as $k => $v) {
                                        echo "<option value='" . $k . "'>" . $v . "</option>";
                                    } ?>
                                </select>
                            </div>

                            </span>

                        </div>
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                                收票时间<i class="must-logo">*</i>

                            </label>
                            <div class="col-lg-10">
                                <div class="input-group  input-group-sm">
                                    <input type="text" class="form-control" id="up_days" name="buy[up_days]"
                                           placeholder="收票时间" data-bind="value:up_days">
                                    <span class="input-group-addon">天</span>

                                </div>
                                <p style="font-size: 8px;">（根据入库单日期倒推）</p>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="col-md-6 bd-right-2 contract-item_title" data-bind="visible: !showOnlyUpPartner()">
                    <div class="contract-type-container">
                        <div class="contract-type__circle contract-type__circle-right">下游最终发货日期</div>
                    </div>

                    <div class="box-body box-body-custom">
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                                最终发货日期<i class="must-logo">*</i>
                            </label>
                            <!-- 系统默认 -->
                            <span>
                                <div class="col-lg-6" data-bind="visible: downDeliveryTermDefault()">
                                <input type="text" class="form-control input-sm" id="down_delivery_term" disabled
                                       name="buy[down_delivery_term]" placeholder="最终发货日期"
                                       data-bind="value:down_delivery_term">
                            </div>
                                <!-- 合同约定 -->
                            <div class="col-lg-6" data-bind="visible: !downDeliveryTermDefault()">
                                <input type="text" class="form-control input-sm date" id="down_delivery_term"
                                       name="buy[down_delivery_term]" placeholder="最终发货日期"
                                       data-bind="value:down_delivery_term">
                            </div>
                            <div class="col-lg-4">
                                <select class="form-control selectpicker show-menu-arrow" title="方式" id="purchase_currency"
                                        name="buy[delivery_mode]"
                                        data-bind="optionsCaption: '请选择',value:down_delivery_mode,valueAllowUnset: true">
                                    <?php foreach ($this->map["contract_delivery_mode"] as $k => $v) {
                                        echo "<option value='" . $k . "'>" . $v . "</option>";
                                    } ?>
                                </select>
                            </div>
                            </span>
                        </div>
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                                开票时间<i class="must-logo">*</i>
                            </label>
                            <div class="col-lg-10">
                                <div class="input-group  input-group-sm">
                                    <input type="text" class="form-control" id="down_days" name="buy[down_days]"
                                           placeholder="开票时间" data-bind="value:down_days">
                                    <span class="input-group-addon">天</span>
                                </div>
                                <p style="font-size: 8px;">（根据出库单日期倒推）</p>
                            </div>
                        </div>

                    </div>


                </div>
            </div>

        </form>

    </div>
    <!-- 上游交货日期与交票期限 -->

    <!-- 合同条款 -->
    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_contractItems.php"; ?>
    <div class="content-wrap contract-item">
        <div class="content-wrap-title">
            <div>
                <p>合同条款</p>
            </div>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body box-contract-extra">
      <span data-bind="if:isShowUpAndDown()">
        <div class="col-md-6 bd-right-2 contract-item_title ">
          <div class="contract-type-container">
            <div class="contract-type__circle">上游合同条款</div>
          </div>
          <div class="box-body box-body-custom">
            <!-- ko component: {
                  name: 'new_contract-items',
                  params: {
                        type: buy_type,
                        category: buy_category,
                        config:upContractItems
                      }
                } -->
              <!-- /ko -->
          </div>
        </div>
        <div class="col-md-6">
          <div class="contract-type-container">
            <div class="contract-type__circle contract-type__circle-right">下游合同条款</div>
          </div>
          <div class="box-body box-body-custom">
            <!-- ko component: {
                  name: 'new_contract-items',
                  params: {
                        type: sell_type,
                        category: sell_category,
                        config:downContractItems
                      }
                } -->
              <!-- /ko -->
          </div>
        </div>
      </span>
                <span data-bind="if:!isShowUpAndDown()">
        <div class="col-md-6 bd-right-2">
          <div class="contract-type-container">
            <div class="contract-type__circle"><span data-bind="visible:showOnlyUpPartner">上游合同条款</span><span
                        data-bind="visible:showOnlyDownPartner">下游合同条款</span></div>
          </div>
          <div class="box-body box-body-custom">
            <!-- ko component: {
              name: 'new_contract-items',
                  params: {
                        type:  contract_type,
                        category: contract_category,
                        config:contractItems,
                        labelWidth:2,
                        controlWidth:10
                      }
                } -->
              <!-- /ko -->
          </div>
        </div>
      </span>

            </div>
        </form>
    </div>
    <!-- 驳回合同 -->
    <div class="content-wrap" data-bind="visible:isBack">
        <div class="content-wrap-title">
            <div>
                <p>驳回明细</p>
            </div>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <label for="type" class="control-label">驳回说明<i
                        class="must-logo">*</i></label>

            <div>
                        <textarea class="form-control" id="back_remark" name="obj[back_remark]" rows="3"
                                  placeholder="驳回说明" data-bind="value:back_remark"></textarea>
            </div>
        </form>
    </div>
    <!-- 驳回合同 -->
</div>

<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        view.formatGoods(<?php echo json_encode($goods) ?>);
        view.units = inc.objectToArray(<?php echo json_encode(array_values($this->map["goods_unit"])); ?>);
        view.formatCurrencies(<?php echo json_encode($this->map["currency"]); ?>);
        view.formatPaymentTypes(<?php echo json_encode($this->map["pay_type"]); ?>);
        view.formatProceedTypes(<?php echo json_encode($this->map["proceed_type"]); ?>);
        view.formatBuyItems(<?php echo json_encode($buyItems) ?>);
        view.formatSellItems(<?php echo json_encode($sellItems) ?>);
        view.formatPaymentItems(<?php echo json_encode($payments) ?>);
        view.formatProceedItems(<?php echo json_encode($proceeds) ?>);
        view.generatePayment();
        view.generateProceed();
        ko.applyBindings(view);
        $('.date').datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});

        var clipboard = new Clipboard('.copy-project-num');
        $('span.box-title__hiden').on('click', function (event) {
            var ele = $(this);
            $(ele).html('');
            var toggle1 = $(ele).parents("div.sub-container__box").find("div.box-content-custom:visible");
            var toggle2 = $(ele).parents("div.sub-container__box").find("div.form-horizontal:visible");
            var toggle3 = $(ele).parents("div.sub-container__box").find("div.box-body-overflow:visible");

            if (toggle1.length > 0 || toggle2.length > 0 || toggle3.length > 0) {
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
            project_id: 0,
            project_type: '',
            buy_type: 1,
            sell_type: 2,
            buy_category: 0,
            sell_category: 4,
            agent_id: 0,
            agent_type: '',
            buy_sell_type: 0,
            up_partner_id: 0,
            down_partner_id: 0,
            corporation_id: 0,
            price_type: 0,
            purchase_currency: '',
            sell_currency: '',
            manager_user_id: 0,
            plan_describe: '',
            storehouse_id: 0,
            sell_exchange_rate: 1,
            buy_exchange_rate: 1,
            buy_formula: '',
            sell_formula: '',
            sell_price_type: '',
            buy_price_type: '',
            buy_manager_user_id: 1,
            sell_manager_user_id: 1,
            remark: '',
            project_status: '',
            contract_status: '',
            buy_contract_id: 0,
            sell_contract_id: 0,
            is_can_back: 0,
            up_delivery_term: "<?php echo date("Y-m-d", strtotime("+" . ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM . " day")); ?>",
            down_delivery_term: "<?php echo date("Y-m-d", strtotime("+" . ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM . " day")); ?>",
            goods_can_edit:1
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.project_id = ko.observable(o.project_id);
        self.buy_contract_id = ko.observable(o.buy_contract_id);
        self.sell_contract_id = ko.observable(o.sell_contract_id);
        self.project_status = ko.observable(o.project_status);
        self.contract_status = ko.observable(o.contract_status);
        self.project_type = ko.observable(o.project_type);
        self.buy_sell_type = ko.observable(o.buy_sell_type);
        self.buy_type = ko.observable(o.buy_type);
        self.sell_type = ko.observable(o.sell_type);
        self.buy_category = ko.observable(o.buy_category);
        self.sell_category = ko.observable(o.sell_category);
        self.storehouse_id = ko.observable(o.storehouse_id);
        self.goods_can_edit = ko.observable(o.goods_can_edit);
        //仓库名称展示条件
        self.showStorehouse = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.projectTypeWarehouseReceipt) >= 0)
                return true;
            return false;
        }, self);

        //采购合同类型下拉展示条件
        self.showBuyContractSelectType = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.buyContractSelectType) >= 0 || (parseInt(self.project_type()) == config.projectTypeSelfImport && parseInt(self.buy_sell_type()) == config.firstBuyLastSale)) {
                if (self.buy_category() == 0)
                    self.buy_category(parseInt(config.buySaleContractTypeAgentImport));
                return true;
            }
            return false;
        }, self);
        //采购合同类型静态展示条件
        self.showBuyContractStaticType = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.buyContractStaticType) >= 0 || (parseInt(self.project_type()) == config.projectTypeSelfInternalTrade && parseInt(self.buy_sell_type()) == config.firstBuyLastSale)) {
                self.buy_category(config.buySaleContractTypeInternal);
                return true;
            }
            return false;
        }, self);

        //销售合同类型静态展示条件
        self.showSellContractStaticType = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.buyContractSelectType.concat(config.buyContractStaticType)) >= 0 || ($.inArray(parseInt(self.project_type()), config.projectTypeSelfSupport) >= 0 && parseInt(self.buy_sell_type()) == config.firstSaleLastBuy))
                return true;
            return false;
        }, self);

        //仅展示上游合作方条件
        self.showOnlyUpPartner = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.projectTypeSelfSupport) >= 0 && parseInt(self.buy_sell_type()) == config.firstBuyLastSale)
                return true;
            return false;
        }, self);


        //仅展示下游合作方条件
        self.showOnlyDownPartner = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.projectTypeSelfSupport) >= 0 && parseInt(self.buy_sell_type()) == config.firstSaleLastBuy)
                return true;
            return false;
        }, self);


        //代理商展示条件
        self.isShowAgent = ko.observable(false);
        if (($.inArray(parseInt(self.project_type()), config.buyContractSelectType) >= 0 || (parseInt(self.project_type()) == config.projectTypeSelfImport && parseInt(self.buy_sell_type()) == config.firstBuyLastSale)) && parseInt(self.buy_category()) == config.buySaleContractTypeAgentImport) {
            self.isShowAgent(true);
        }

        self.buy_category.subscribe(function (v) {
            if (parseInt(v) == config.buySaleContractTypeAgentImport)
                self.isShowAgent(true);
            else
                self.isShowAgent(false);
        });
        self.agent_id = ko.observable(o.agent_id);

        self.agent_type = ko.observable(o.agent_type).extend({
            custom: {
                params: function (v) {
                    if (self.isShowAgent() && self.agent_id() > 0) {
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

        self.agent_id.subscribe(function (v) {
            if (v == '')
                self.agent_type();
        });

        //代理商明细展示条件
        self.showAgentDetail = ko.computed(function () {
            if (self.isShowAgent() && self.agent_id() > 0)
                return true;
            return false;
        }, self);


        self.up_partner_id = ko.observable(o.up_partner_id).extend({
            custom: {
                params: function (v) {
                    self.msg = '请选择上游合作方';
                    if (!self.showOnlyDownPartner()) {
                        if (v > 0) {
                            if (!self.showOnlyUpPartner() && v == ko.unwrap(self.down_partner_id)) {
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
                    if (!self.showOnlyUpPartner()) {
                        if (v > 0) {
                            if (!self.showOnlyDownPartner() && v == ko.unwrap(self.up_partner_id)) {
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

        self.buy_price_type = ko.observable(o.buy_price_type).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyDownPartner()) {
                        if (v > 0)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "请选择采购价格方式"
            }
        });

        self.sell_price_type = ko.observable(o.sell_price_type).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyUpPartner()) {
                        if (v > 0)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "请选择销售价格方式"
            }
        });

        self.buy_manager_user_id = ko.observable(o.buy_manager_user_id).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyDownPartner()) {
                        if (v > 0)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "请选择采购项目负责人"
            }
        });
        self.sell_manager_user_id = ko.observable(o.sell_manager_user_id).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyUpPartner()) {
                        if (v > 0)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "请选择销售项目负责人"
            }
        });

        self.purchase_currency = ko.observable(o.purchase_currency);
        self.sell_currency = ko.observable(o.sell_currency);
        self.contractGoodsUnitConvert = ko.observable(o.contractGoodsUnitConvert);
        self.contractGoodsUnitConvertValue = ko.observable(o.contractGoodsUnitConvertValue);
        self.buy_exchange_rate = ko.observable(parseFloat(o.buy_exchange_rate)).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyDownPartner()) {
                        if (v != '')
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "采购币种即期汇率不得为空"
            }
        });
        self.sell_exchange_rate = ko.observable(parseFloat(o.sell_exchange_rate)).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyUpPartner()) {
                        if (v != '')
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "销售币种即期汇率不得为空"
            }
        });

        self.buy_formula = ko.observable(o.buy_formula).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyDownPartner()) {
                        if (v != '' || parseInt(self.buy_price_type()) == config.staticPrice)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "采购计价公式不得为空"
            }
        });

        self.sell_formula = ko.observable(o.sell_formula).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyUpPartner()) {
                        if (v != '' || parseInt(self.sell_price_type()) == config.staticPrice)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "销售计价公式不得为空"
            }
        });

        self.contract_default_delivery_term = ko.observable(o.contract_default_delivery_term);

        self.up_delivery_mode = ko.observable(o.up_delivery_mode);

        self.up_delivery_mode.subscribe(function (v) {
            if (v == 0) {
                self.up_delivery_term((new Date(new Date().getTime() + self.contract_default_delivery_term() * 24 * 60 * 60 * 1000)).Format("yyyy-MM-dd"));
            }
            else
                self.up_delivery_term('');
        });
        self.up_days = ko.observable(o.up_days).extend({
            custom: {
                params: function (v) {
                    if(!self.showOnlyDownPartner()) {
                        if (v == '' || v == null || parseInt(v) < 0 || !isPositiveInteger(v))
                            return false;
                        else
                            return true;
                    };
                    return true;
                },
                message: "请输入一个不小于0的数字"
            }
        });

        self.up_delivery_term = ko.observable(o.up_delivery_term).extend({
            custom: {
                params: function (v) {
                    if(!self.showOnlyDownPartner()) {
                        if (v == "" || v == null || v.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/) == null)
                            return false;
                        else
                            return true;
                    };
                    return true;
                },
                message: "请填写正确的日期"
            }
        });

        self.down_delivery_mode = ko.observable(o.down_delivery_mode);
        self.down_delivery_mode.subscribe(function (v) {
            if (v == 0) {
                self.down_delivery_term((new Date(new Date().getTime() + self.contract_default_delivery_term() * 24 * 60 * 60 * 1000)).Format("yyyy-MM-dd"));
            }
            else
                self.down_delivery_term('');
        });
        self.down_delivery_term = ko.observable(o.down_delivery_term).extend({
            custom: {
                params: function (v) {
                    if(!self.showOnlyUpPartner()) {
                        if (v == '' || v == null || v.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/) == null)
                            return false;
                        else
                            return true;
                    };
                    return true;
                },
                message: "请填写正确的日期"
            }
        });

        self.down_days = ko.observable(o.down_days).extend({
            custom: {
                params: function (v) {
                    if(!self.showOnlyUpPartner()) {
                        if (v == '' || v == null || parseInt(v) < 0 || !isPositiveInteger(v))
                            return false;
                        else
                            return true;
                    };
                    return true;

                },
                message: "请输入一个不小于0的数字"
            }
        });

        //最终交货日期：方式
        self.upDeliveryTermDefault = ko.computed(function () {
            if (self.up_delivery_mode() != 1)//系统默认
                return true;
            return false;
        }, self);
        //最终发货日期：方式
        self.downDeliveryTermDefault = ko.computed(function () {
            if (self.down_delivery_mode() == 0)
                return true;
            return false;
        }, self);

        self.buy_formula_status = ko.observable(false);
        self.sell_formula_status = ko.observable(false);

        if (self.buy_price_type() == config.tempPrice)
            self.buy_formula_status(true);
        if (self.sell_price_type() == config.tempPrice)
            self.sell_formula_status(true);
        self.buy_price_type.subscribe(function (v) {
            if (parseInt(v) == config.tempPrice)
                self.buy_formula_status(true);
            else
                self.buy_formula_status(false);
        });

        self.sell_price_type.subscribe(function (v) {
            if (parseInt(v) == config.tempPrice)
                self.sell_formula_status(true);
            else
                self.sell_formula_status(false);
        });

        self.fileUploadStatus = ko.observable();
        self.allGoods = ko.observableArray();
        self.buyItems = ko.observableArray();
        self.sellItems = ko.observableArray();
        self.paymentItems = ko.observableArray();
        self.proceedItems = ko.observableArray();
        self.units = [];
        self.currencies = ko.observableArray();
        self.paymentTypes = ko.observableArray();
        self.proceedTypes = ko.observableArray();

        self.formatGoods = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i = 0; i < data.length; i++) {
                self.allGoods().push(data[i]);
            }
        }

        self.formatBuyItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                data[i]['currencies'] = self.currencies;
                data[i]['allGoods'] = self.allGoods;
                data[i]['exchange_type'] = self.buy_type();
                if (data[i]['type'] == config.agentFeeCalculateByAmount) {
                    data[i]['isRateDisable'] = true;
                    data[i]['isRateAgentPriceDisable'] = false;
                    data[i]['isAgentunitDisable'] = false;
                } else if (data[i]['type'] == config.agentFeeCalculateByPrice) {
                    data[i]['isRateDisable'] = false;
                    data[i]['isRateAgentPriceDisable'] = true;
                    data[i]['isAgentunitDisable'] = true;
                }
                var obj = new ContractGoods(data[i]);
                self.buyItems().push(obj);
            }
        }

        self.formatSellItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                data[i]['currencies'] = self.currencies;
                data[i]['allGoods'] = self.allGoods;
                data[i]['exchange_type'] = self.sell_type();
                if (data[i]['type'] == config.agentFeeCalculateByAmount) {
                    data[i]['isRateDisable'] = true;
                    data[i]['isRateAgentPriceDisable'] = false;
                    data[i]['isAgentunitDisable'] = false;
                } else if (data[i]['type'] == config.agentFeeCalculateByPrice) {
                    data[i]['isRateDisable'] = false;
                    data[i]['isRateAgentPriceDisable'] = true;
                    data[i]['isAgentunitDisable'] = true;
                }

                var obj = new ContractGoods(data[i]);
                self.sellItems().push(obj);
            }
        }

        self.formatPaymentTypes = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                self.paymentTypes.push(data[i]);
            }
        }

        self.formatPaymentItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                data[i]['currencies'] = self.currencies();
                data[i]['paymentTypes'] = self.paymentTypes();
                var obj = new Payments(data[i]);
                self.paymentItems().push(obj);
            }
        }

        if (!self.showOnlyDownPartner()) {
            self.generatePayment = function () {
                if (self.paymentItems().length == 0) {
                    self.paymentItems.push(new Payments({
                        currencies: self.currencies(),
                        paymentTypes: self.paymentTypes(),
                        exchange_rate: self.buy_exchange_rate(),
                        type: self.buy_type()
                    }));
                }
            }
        } else {
            // self.paymentItems=ko.observable();
            self.generatePayment = function () {
            }
        }


        self.formatCurrencies = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                self.currencies.push(data[i]);
            }
        }


        self.formatProceedTypes = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                self.proceedTypes.push(data[i]);
            }
        }


        self.formatProceedItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                data[i]['currencies'] = self.currencies();
                data[i]['paymentTypes'] = self.proceedTypes();
                var obj = new Payments(data[i]);
                self.proceedItems().push(obj);
            }
        }

        if (!self.showOnlyUpPartner()) {
            self.generateProceed = function () {
                if (self.proceedItems().length == 0) {
                    self.proceedItems.push(new Payments({
                        currencies: self.currencies(),
                        paymentTypes: self.proceedTypes(),
                        exchange_rate: self.sell_exchange_rate(),
                        type: self.sell_type()
                    }));
                }
            }
        } else {
            // self.proceedItems=ko.observable();
            self.generateProceed = function () {
            }
        }


        self.isShowUpAndDown = ko.observable(true);
        self.contract_type = ko.observable();
        self.contract_category = ko.observable();

        /*self.contractItems = [];
        self.upContractItems = [];
        self.downContractItems = [];*/


        // self.contractItemsMap = <?php echo json_encode($contractConfig) ?>;
        if ($.inArray(parseInt(self.project_type()), config.projectTypeChannelBuy.concat(config.projectTypeWarehouseReceipt)) >= 0) {
            self.upContractItems = <?php echo json_encode($upContractConfig) ?>;
            self.downContractItems = <?php echo json_encode($downContractConfig) ?>;
        } else {
            self.contractItems = <?php echo json_encode($contractConfig) ?>;
            if ($.inArray(parseInt(self.project_type()), config.projectTypeSelfSupport) >= 0 && parseInt(self.buy_sell_type()) == config.firstBuyLastSale) {
                self.contract_type(self.buy_type());
                self.contract_category(self.buy_category());
            } else {
                self.contract_type(self.sell_type());
                self.contract_category(self.sell_category());
            }

            self.isShowUpAndDown(false);
        }

        self.calculateBuyAmountCurrency = function () {
            var total = 0;
            if (self.buyItems() != undefined && self.buyItems().length > 0) {
                ko.utils.arrayForEach(self.buyItems(), function (item) {
                    var value = parseFloat(item.amount());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        };

        self.calculateSellAmountCurrency = function () {
            var total = 0;
            if (self.sellItems() != undefined && self.sellItems().length > 0) {
                ko.utils.arrayForEach(self.sellItems(), function (item) {
                    var value = parseFloat(item.amount());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        };

        self.calculateBuyAmount = function () {
            var total = 0;
            if (self.buyItems() != undefined && self.buyItems().length > 0) {
                ko.utils.arrayForEach(self.buyItems(), function (item) {
                    var value = parseFloat(item.amount_cny());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        };

        self.calculateSellAmount = function () {
            var total = 0;
            if (self.sellItems() != undefined && self.sellItems().length > 0) {
                ko.utils.arrayForEach(self.sellItems(), function (item) {
                    var value = parseFloat(item.amount_cny());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        };

        self.calculatePaymentAmount = function () {
            var total = 0;
            if (self.paymentItems() != undefined && self.paymentItems().length > 0) {
                ko.utils.arrayForEach(self.paymentItems(), function (item) {
                    var value = parseFloat(item.amount_cny());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        }

        self.calculateProceedAmount = function () {
            var total = 0;
            if (self.proceedItems() != undefined && self.proceedItems().length > 0) {
                ko.utils.arrayForEach(self.proceedItems(), function (item) {
                    var value = parseFloat(item.amount_cny());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        }

        self.buy_amount = ko.computed(function () {
            self.calculateBuyAmountCurrency();
        }, self);

        self.buy_amount_cny = ko.computed(function () {
            self.calculateBuyAmount();
        }, self);

        self.sell_amount = ko.computed(function () {
            self.calculateSellAmountCurrency();
        }, self);

        self.sell_amount_cny = ko.computed(function () {
            self.calculateSellAmount();
        }, self);

        self.tradeItems = function (data) {
            if (Array.prototype.isPrototypeOf(data) && data.length > 0) {
                for (var k in data) {
                    delete data[k]['currencies'];
                    delete data[k]['currency_ico'];
                    delete data[k]['allGoods'];
                    delete data[k]['isRateDisable'];
                    delete data[k]['isRateAgentPriceDisable'];
                    delete data[k]['isAgentunitDisable'];
                    delete data[k]['calculateAgentAmount'];
                }
            }
        }

        self.planItems = function (data) {
            if (Array.prototype.isPrototypeOf(data) && data.length > 0) {
                for (var k in data) {
                    delete data[k]['currencies'];
                    delete data[k]['currency_ico'];
                    delete data[k]['paymentTypes'];
                    delete data[k]['showExpenseNameInput'];
                    delete data[k]['expense_width'];
                }
            }
        }

        self.contractData = function (data, type, category) {
            if (data != undefined && Object.keys(data).length > 0) {
                var extra = data[type][category].extraItems;
                delete data[self.buy_type()];
                delete data[self.sell_type()];
                /*var item = {};
                data[type] = [];
                for(var i in extra) {
                    for(var j in extra[i]){
                        if(j=='type' && extra[i][j]== 'koMultipleSelect' && Array.isArray(extra[i]['value'])) {
                            extra[i]['value'] = extra[i]['value'].join(',');
                        }
                        if(j!='name' && j!='value' && j!='key')
                            delete extra[i][j];
                    }
                }*/
                data[type] = koForm.getValues(extra);
            }
        }

        self.postData = function () {
            var data = inc.getPostData(self, ["isShowUpAndDown", "sell_formula_status", "buy_formula_status", "showAgentDetail", "isShowAgent", "showSellContractStaticType", "showBuyContractStaticType", "showBuyContractSelectType", "showOnlyDownPartner", "showOnlyUpPartner", "units", "currencies", "allGoods", "paymentTypes", "proceedTypes"]);
            self.tradeItems(data['buyItems']);
            self.tradeItems(data['sellItems']);
            self.planItems(data['paymentItems']);
            self.planItems(data['proceedItems']);
            self.contractData(data['upContractItems'], self.buy_type(), self.buy_category());
            self.contractData(data['downContractItems'], self.sell_type(), self.sell_category());
            self.contractData(data['contractItems'], self.contract_type(), self.contract_category());

            return data;
        }

        self.actionState = ko.observable(0);
        self.is_temp_save = ko.observable(0);
        self.tempSaveBtnText = ko.observable("暂存");
        self.saveBtnText = ko.observable("保存");

        self.is_can_back = ko.observable(o.is_can_back);
        self.isBack = ko.observable(false);
        self.back_remark = ko.observable("").extend({
            custom: {
                params: function (v) {
                    return (!self.isBack() || (v != null && v != ""))
                },
                message: "请填写驳回备注"
            }
        });


        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        //暂存
        self.tempSave = function () {
            self.is_temp_save(1);
            self.submit();
        }
        //保存
        self.save = function () {

            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
            /*if(self.fileUploadStatus()!=1){
                layer.alert("请上传项目预算表！", {icon: 2});
                return;
            }*/

            self.fileUploadStatus(1);

            if (self.buy_amount() == undefined) {
                self.buy_amount_cny = ko.observable(self.calculateBuyAmount());
            }

            if (self.buy_amount() == undefined) {
                self.buy_amount = ko.observable(self.calculateBuyAmountCurrency());
            }

            var temp1 = 0;
            var temp2 = 0;
            if (parseFloat(self.buy_amount_cny()) < parseFloat(self.calculatePaymentAmount())) {
                temp1 = 1;
            }

            if (self.sell_amount_cny() == undefined) {
                self.sell_amount_cny = ko.observable(self.calculateSellAmount());
            }

            if (self.sell_amount() == undefined) {
                self.sell_amount = ko.observable(self.calculateSellAmountCurrency());
            }

            if (parseFloat(self.sell_amount_cny()) < parseFloat(self.calculateProceedAmount())) {
                temp2 = 1;
            }
            if (temp1 == 1) {
                inc.vueConfirm({
                    content: "付款金额超过人民币采购总价,是否继续？",
                    onConfirm: function (index) {
                        if (temp2 == 1) {
                            inc.vueConfirm({
                                content: "收款金额超过人民币销售总价,是否继续？",
                                onConfirm: function (index) {
                                    self.submit();
                                }
                            });
                        } else {
                            self.submit();
                        }
                    }
                });
            } else if (temp2 == 1) {
                inc.vueConfirm({
                    content: "收款金额超过人民币销售总价,是否继续？",
                    onConfirm: function (index) {
                        self.submit();
                    }
                });
            } else {
                self.submit();
            }
        }

        self.submit = function () {
            var formData = {"data": self.postData()};
            if (self.actionState() == 1)
                return;
            if (self.is_temp_save() == 1)
                self.tempSaveBtnText("暂存中" + inc.loadingIco);
            else
                self.saveBtnText("保存中" + inc.loadingIco);

            self.actionState(1);
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if(json.data.buy_contract_id){
                            self.buy_contract_id(json.data.buy_contract_id);
                        }
                        if(json.data.sell_contract_id){
                            self.sell_contract_id(json.data.sell_contract_id);
                        }
                        var contractId=json.data.buy_contract_id?json.data.buy_contract_id:json.data.sell_contract_id;
						inc.vueMessage({
                            message: '操作成功',duration:500, onClose: function () {
                                if (json.extra == 1) {
									location.href = "/<?php echo $this->getId() ?>/edit/?id=" + contractId + "&project_id=<?php echo $data['project_id'] ?>";
								} else {
									location.href = "/<?php echo $this->getId() ?>/detail/?id=" + contractId;
								}
                            }
                        });
                    } else {
                        inc.vueAlert(json.data);
                    }
                    self.tempSaveBtnText("暂存");
                    self.saveBtnText("保存");
                    self.actionState(0);
                    self.is_temp_save(0);
                },
                error: function (data) {
                    self.tempSaveBtnText("暂存");
                    self.saveBtnText("保存");
                    self.actionState(0);
                    self.is_temp_save(0);
                    inc.vueAlert("保存失败！" + data.responseText);
                }
            });
            //setTimeout("self.actionState(0)", 1000);
        }

        self.back = function () {
            // history.back();
            location.href = "/<?php echo $this->getId()?>/";
        }


        self.cancelBack = function () {
            self.isBack(false);
        }
        self.startBack = function () {
            self.isBack(true);
        }

        self.saveBackBtnText = ko.observable("提交驳回");

        self.saveBack = function () {
            inc.vueAlert("请在页面底部填写驳回备注");
            if (!self.back_remark.isValid()) {
                self.back_remark.isModified(true);
                return;
            }

            var formData = {
                id: self.project_id(),
                remark: self.back_remark()
            };
            formData = {data: formData};
            if (self.actionState() == 1)
                return;
            self.saveBackBtnText("提交中。。。");
            self.actionState(1);
            $.ajax({
                type: "POST",
                url: '/<?php echo $this->getId() ?>/saveBack',
                data: formData,
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    self.saveBackBtnText("提交驳回");
                    if (json.state == 0) {
                        inc.vueMessage({
                            message: '操作成功',duration:500, onClose: function () {
                                location.href = "/<?php echo $this->getId() ?>/";
                            }
                        });
                    } else {
                        inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
                    self.saveBackBtnText("提交驳回");
                    self.actionState(0);
                    inc.vueAlert("操作失败：" + data.responseText);
                }
            });
        }

    }

    function isPositiveInteger(s){//是否为正整数
        var re = /^[0-9]+$/ ;
        return re.test(s)
    };
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