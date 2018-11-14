<link href="/newUI/css/business-confirm/edit.css" rel="stylesheet" type="text/css"/>
<style>
    .table {
        width: 100%;
    }

    .bootstrap-select.btn-group ul.dropdown-menu {
        max-height: 272px !important;
        overflow: auto;
    }
</style>
<?php
$menus = [
    ['text' => '项目管理'],
    ['text' => '商务确认', 'link' => '/businessConfirm/'],
    ['text' => $this->pageTitle]
];
$buttons = [];
if (empty($_GET['t']) || $_GET['t'] != 1) {
    $buttons[] = ['text' => '暂存', 'attr' => ['data-bind' => 'click:tempSave, html:tempSaveBtnText', 'class_abbr' => 'action-default-base']];
    $buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText']];
}
$this->loadHeaderWithNewUI($menus, $buttons, '/businessConfirm/');
?>


<div style="height:100%;display:flex;flex-direction: column;">
    <!-- 项目附件信息 -->
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
                <li style="width:100%;">
                    <label>备注：</label>
                    <p class="form-control-static"><?php echo $project['remark']; ?></p>
                </li>
            </ul>
        </div>


    </div>
    <!-- 项目附件信息 -->

    <!-- 合同信息 -->
    <?php
    $prefix = $data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY ? '采购' : '销售';
    $contractTypeMapKey = $data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY ? 'buy_sub_contract_type' : 'sale_sub_contract_type';
    ?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>
                    合同信息
                    <a style="color:#3E8CF7!important;margin-left:10px;"
                       href="/project/detail/?id=<?php echo $project['project_id'] ?>&t=1"
                       target="_blank">项目编号：<?php echo $project['project_code'] ?></a>
                </p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="form-com">
            <li>
                <label><?php echo $prefix; ?>合同类型<i class="must-logo">*</i></label>
                <select data-live-search="true" class="selectpicker show-menu-arrow form-control "
                        title="请选择<?php echo $prefix; ?>合同类型"
                        id="type" name="obj[type]" data-bind="selectpicker:category,valueAllowUnset: true">
                    <?php foreach ($this->map[$contractTypeMapKey] as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                    } ?>
                </select>
            </li>
            <li>
                <label> 交易主体<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择交易主体" id="corporation_id"
                        name="obj[corporation_id]" data-live-search="true"
                        data-bind="optionsCaption: '请选择交易主体',selectpicker:corporation_id,valueAllowUnset: true,enable:false"
                        disabled>
                    <?php
                    $cors = UserService::getUserSelectedCorporations();
                    foreach ($cors as $v) {
                        echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </li>
            <li>
                <?php $categoryPrefix = $data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY ? '上游' : '下游';
                $title = $categoryPrefix . '合作方' ?>
                <label><?php echo $title; ?><i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择<?php echo $title ?>"
                        id="partner_id" name="obj[partner_id]" data-live-search="true"
                        data-bind="optionsCaption: '请选择<?php echo $title ?>',selectpicker: partner_id,valueAllowUnset: true">
                    <?php
                    $partners = PartnerService::getUpPartners();
                    if ($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_SALE) {
                        $partners = PartnerService::getDownPartners();
                    }
                    foreach ($partners as $v) {
                        echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible:isShowAgent">
                <label>采购代理商 ：</label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择采购代理商" id="agent_id"
                        name="obj[agent_id]" data-live-search="true"
                        data-bind="optionsCaption: '请选择采购代理商',selectpicker: agent_id,valueAllowUnset: true">
                    <?php
                    $upPartners = PartnerService::getAgentPartners();
                    foreach ($upPartners as $v) {
                        echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </li>
            <li data-bind="visible:isShowAgent">
                <label>代理模式<span data-bind="html:agentTypeReqSpan"></span></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择代理模式" id="agent_type"
                        name="obj[agent_type]"
                        data-bind="optionsCaption: '请选择代理模式',selectpicker: agent_type,valueAllowUnset: true">
                    <option value=''>请选择代理模式</option>
                    <?php foreach ($this->map["buy_agent_type"] as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                    } ?>
                </select>
            </li>
            <li>
                <label><?php echo $prefix; ?>币种 <i class="must-logo">*</i> </label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择<?php echo $title ?>" id="currency"
                        name="obj[currency]"
                        data-bind="optionsCaption: '请选择<?php echo $title ?>',selectpicker: currency,valueAllowUnset: true,enable:goods_can_edit()==1">
                    <?php foreach ($this->map["currency_type"] as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                    } ?>
                </select>
            </li>
            <li>
                <label>即期汇率<i class="must-logo">*</i></label>
                <input type="text" class="form-control" id="corporate" name="data[exchange_rate]"
                       placeholder="即期汇率" data-bind="value:exchange_rate">
            </li>
            <li>
                <label>价格方式 <i class="must-logo">*</i> </label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择价格方式" id="price_type"
                        name="obj[price_type]"
                        data-bind="optionsCaption: '请选择价格方式',selectpicker:price_type,valueAllowUnset: true">
                    <option value=''>请选择价格方式</option>
                    <?php foreach ($this->map["price_type"] as $k => $v) {
                        echo "<option value='" . $k . "'>" . $v . "</option>";
                    } ?>
                </select>
            </li>
        </ul>
    </div>

    <!-- 合同信息 -->

    <!-- 交易明细 -->
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>交易明细</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <!-- 采购 -->
        <div class="table-title">
            <span><?php echo $prefix; ?>合同</span>
            <div>
                <label style="margin-right:10px;flex: 0 0 4em;;">负责人<i class="must-logo">*</i></label>
                <select class="selectpicker show-menu-arrow form-control " title="请选择负责人" id="manager_user_id"
                        data-live-search="true" name="obj[manager_user_id]"
                        data-bind="optionsCaption: '请选择负责人',
                        selectpicker: manager_user_id,
                        valueAllowUnset: true,
                        ">
                    <option value=''>请选择负责人</option>
                    <?php
                    $users = UserService::getProjectManageUsers();
                    foreach ($users as $v) {
                        echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
                    } ?>
                </select>
            </div>
        </div>
        <div class="box-body  box-content-custom">
            <!-- ko component: {
                 name: "new_contract-goods",
                 params: {
                             exchange_type:type,
                             price_type:price_type,
                             items:goodsItems,
                             allGoods: allGoods,
                             units:units,
                             exchange_rate:exchange_rate,
                             currencies:currencies,
                             currency:currency,
                             goods_can_edit:goods_can_edit,
                             }
             } -->
            <!-- /ko -->
        </div>
        <ul class="form-com" data-bind="visible:formula_status">
            <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
                <label><?php echo $prefix; ?>计价公式<i class="must-logo">*</i></label>
                <textarea rows="3" class="form-control" id="formula" name="data[formula]"
                          placeholder="<?php echo $prefix; ?>计价公式" data-bind="value:formula"></textarea>
            </li>
        </ul>
        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_contractGoods.php"; ?>
    </div>

    <!-- 交易明细 -->

    <!-- 代理费明细 -->


    <div class="content-wrap" data-bind="visible:isShowAgent">
        <div class="content-wrap-title">
            <div>
                <p>手续费明细</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="table-com">
            <li>
                <span>品名</span>
                <span>计费方式<i class="must-logo">*</i>	</span>
                <span>计费单价<i class="must-logo">*</i>	</span>
                <span>计费单位<i class="must-logo">*</i></span>
                <span>代理手续费率<i class="must-logo">*</i>	</span>
                <span>代理手续费</span>
            </li>
            <!-- ko foreach: goodsItems -->
            <li>
                <p>
                    <span data-bind="text:goods_name"></span>
                    <!--                    <input hidden name='agent_goods_id' data-bind="value:goods_id"/>-->
                </p>
                <div>
                    <select class=" form-control " title="请选择计费方式" name="type"
                            data-bind="value:type,valueAllowUnset: true">
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
                <div>
                    <select class=" show-menu-arrow form-control " title="请选择计费单位" name="agnet_unit"
                            data-bind="optionsCaption: '请选择计费单位',value:agent_unit,valueAllowUnset: true, disable:isAgentunitDisable">
                        <?php foreach ($this->map["goods_unit"] as $k => $v) {
                            echo "<option value='" . $v["id"] . "'>" . $v["name"] . "</option>";
                        } ?>
                    </select>
                </div>
                <div class="input-with-logo-right">
                    <span>%</span>
                    <input type="text" class="form-control" name="fee_rate" placeholder="代理手续费率"
                           data-bind="percent:fee_rate, disable:isRateDisable">
                </div>
                <span>¥<span style="margin-left: 5px" data-bind="moneyText:agent_amount"></span><input hidden
                                                                                                       name='agent_amount'
                                                                                                       data-bind="moneyText:agent_amount"/></span>
            </li>
            <!-- /ko -->
        </ul>
    </div>

    <!-- 代理费明细 -->

    <!-- 收付款明细 -->

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>收付款明细</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <!-- 上游付款计划 -->
        <div>
            <div class="table-title">
                <span>
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
        </div>
        <!--  下游收款计划 -->

        <!--        TODO new_paymentItems -- new_paymentItems  -->
        <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_paymentItems.php"; ?>
    </div>

    <!-- 收付款明细 -->

    <!-- 上游交货日期与交票期限 -->
    <div class="content-wrap contract-item" style="margin-bottom:0 !important">
        <div class="content-wrap-title">
            <div>
                <p>最终交货/发货日期</p>
            </div>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body box-contract-extra">
                <div class="col-md-6 bd-right-2 contract-item_title">
                    <div class="contract-type-container">
                        <div class="contract-type__circle"><?php if ($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY): ?>上游<?php else: ?>下游<?php endif; ?>最终<?php if ($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY): ?>交货<?php else: ?>发货<?php endif; ?>日期
                        </div>
                    </div>
                    <div class="box-body box-body-custom">
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                                最终<?php if ($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY): ?>交货<?php else: ?>发货<?php endif; ?>日期<i class="must-logo">*</i>
                            </label>
                            <!-- 系统默认 -->
                            <span>
                                <div class="col-lg-6" data-bind="visible: deliveryTermDefault()">
                                <input type="text" class="form-control input-sm" id="delivery_term1" disabled
                                       name="buy[delivery_term]" placeholder="日期" data-bind="value:delivery_term">
                            </div>
                                <!-- 合同约定 -->
                            <div class="col-lg-6" data-bind="visible: !deliveryTermDefault()">
                                <input type="text" class="form-control input-sm date" id="delivery_term"
                                       name="buy[delivery_term]" placeholder="日期" data-bind="value:delivery_term">
                            </div>
                            <div class="col-lg-4">
                                <select class="form-control selectpicker show-menu-arrow" title="方式" id="purchase_currency"
                                        name="buy[delivery_mode]"
                                        data-bind="optionsCaption: '请选择',value:delivery_mode,valueAllowUnset: true">
                                    <?php foreach ($this->map["contract_delivery_mode"] as $k => $v) {
                                        echo "<option value='" . $k . "'>" . $v . "</option>";
                                    } ?>
                                </select>
                            </div>
                            </span>
                        </div>
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                                <?php if ($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY): ?>收票<?php else: ?>开票<?php endif; ?>时间<i class="must-logo">*</i>
                            </label>
                            <div class="col-lg-10">
                                <div class="input-group  input-group-sm">
                                    <input type="text" class="form-control" id="days" name="buy[days]" placeholder="时间"
                                           data-bind="value:days">
                                    <span class="input-group-addon">天</span>
                                </div>
                                <p style="font-size: 8px;">
                                    （根据<?php if ($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY): ?>入库单<?php else: ?>出库单<?php endif; ?>
                                    日期倒推）</p>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

        </form>

    </div>
    <!-- 上游交货日期与交票期限 -->


    <!-- 合同条款 -->
    <div class="content-wrap contract-item">
        <div class="content-wrap-title">
            <div>
                <p>合同条款</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body box-contract-extra">
                <div class="col-md-6 bd-right-2">
                    <div class="contract-type-container">
                        <div class="contract-type__circle">
                            <?php
                            if ($data['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY) {
                                echo '上游合同条款';
                            } else {
                                echo '下游合同条款';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="box-body box-body-custom">
                        <!-- ko component: {
                                    name: 'new_contract-items',
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
                <!--                TODO  contractItems  new_contractItems-->
                <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_contractItems.php"; ?>
            </div>
        </form>
    </div>

    <!-- 合同条款 -->

</div>

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
            days:"<?php echo ConstantMap::CONTRACT_DEFAULT_DAYS; ?>",
            goods_can_edit:1
        };
        var o = $.extend(defaults, option);
        var self = this;

        self.contract_id = ko.observable(o.contract_id);
        self.project_id = ko.observable(o.project_id);
        self.type = ko.observable(o.type);
        self.goods_can_edit = ko.observable(o.goods_can_edit);
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
        console.log(self.currency(),'nedd');
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

                    if (v=='' || v==null || parseInt(v)<0 || !isPositiveInteger(v))
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
                return '<i class="must-logo">*</i>';
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
                inc.vueConfirm({
                    content: payTypeDesc + "金额：￥" + parseFloat(self.calculateTotalPaymentAmount() / 100) + " 超过人民币" + processTypeDesc + "总价：￥" + parseFloat(self.amount_cny() / 100) + ",是否继续？",
                    onConfirm: function (index) {
                        self.doSave();
                    }
                });
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
                        
						self.contract_id(json.data);//重新赋值
						inc.vueMessage({
                            message: '操作成功！',duration:500, onClose: function () {
                                if (self.tempSaveOperate() == 0) {
									location.href = "/businessConfirm/detail/?id=" + json.data;
								} else {
									location.href = '/subContract/edit?id=' + json.data;
								}
                            }
                        });
                    } else {
                        inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
                    self.tempSaveBtnText("暂存");
                    self.saveBtnText("保存");
                    self.actionState = 0;
                    inc.vueAlert("操作失败！" + data.responseText);
                }
            });
        };

        self.back = function () {
            history.back();
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
