<?php
$menus = [
    ['text' => '项目管理'],
    ['text' => '额度管理', 'link' => '/quota/'],
    ['text' => '修改']
];
$buttons = [];
$buttons[] = ['text' => '提交', 'attr' => ['data-bind' => 'click:save']];
$this->loadHeaderWithNewUI($menus, $buttons, '/quota/');
?>
<div class="el-container is-vertical">
    <?php
    if (empty($contract->relative)) {
        // 单边合同
        $upPartnerOnly = ($contract->type == ConstantMap::BUY_TYPE) || ($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY);
    }
    $this->renderPartial("/quota/new_editElement", array('contract' => $contract));
    ?>
    <!-- 进口渠道 -->
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>
        <span>
            <?php
            $typeDesc = $this->map["project_type"][$contract->project['type']];
            if (!empty($contract->project['base']['buy_sell_type'])) {
                $typeDesc .= '-' . $this->map["purchase_sale_order"][$contract->project['base']["buy_sell_type"]];
            }
            echo $typeDesc;
            ?>
        </span>
                    <span>
          <a href="/project/detail/?id=<?php echo $contract->project['project_id'] ?>&t=1"
             style="color:#3E8CF7!important;margin-left:10px;"
             target="_blank">项目编号：<?php echo $contract->project['project_code'] ?></a>
        </span>
                    <span onclick="copy()" data-clipboard-text="<?php echo $contract->project['project_code']; ?>"
                          style="color:#FF6E34;font-size:14px;margin-left:10px;cursor: pointer">复制</span>
                </p>
            </div>
        </div>
        <ul class="item-com">
            <li>
                <label>交易主体：</label>
                <span><?php echo $contract->corporation['name']; ?></span>
            </li>
            <?php if (!empty($contract->relative)) {

                $buy_contract = ($contract->type == ConstantMap::BUY_TYPE) ? $contract : $contract->relative;
                $sell_contract = ($contract->type == ConstantMap::SALE_TYPE) ? $contract : $contract->relative;
                ?>
                <li>
                    <label>采购合同类型：</label>
                    <span><?php echo $this->map["contract_config"][$buy_contract["type"]][$buy_contract['category']]["name"]; ?></span>
                </li>
                <li>
                    <label>销售合同类型：</label>
                    <span><?php echo $this->map["contract_config"][$sell_contract["type"]][$sell_contract['category']]["name"]; ?></span>
                </li>
                <li>
                    <label>合同状态：</label>
                    <span>
          <?php
          echo $this->map["contract_status"][$contract->status];
          ?>
      </span>
                </li>
                <?php if (!empty($buy_contract->agent)) { ?>
                    <li>
                        <label>采购代理商：</label>
                        <?php echo '<a href="/partner/detail/?id=' . $buy_contract->agent['partner_id'] . '&t=1" target="_blank">' . $buy_contract->agent['name'] . '</a>'; ?>
                    </li>
                    <li>
                        <label>代理模式：</label>
                        <span><?php echo $this->map['buy_agent_type'][$buy_contract['agent_type']]; ?></span>
                    </li>
                <?php } ?>
                <li>
                    <label>采购价格方式：</label>
                    <span><?php echo $this->map["price_type"][$buy_contract['price_type']]; ?></span>
                </li>
                <li>
                    <label>销售价格方式：</label>
                    <span><?php echo $this->map["price_type"][$sell_contract['price_type']]; ?></span>
                </li>
            <?php } else { ?>
                <li>
                    <label>购销信息：</label>
                    <span><?php echo $contract->getContractType(); ?></span>
                </li>
                <li>
                    <label>合同类型：</label>
                    <span>
                <?php echo $this->map["contract_config"][$contract["type"]][$contract['category']]["name"]; ?>
            </span>
                </li>
                <li>
                    <label>合同编号：</label>
                    <span><?php echo $contract['contract_code']; ?></span>
                </li>
                <li>
                    <label>合同状态：</label>
                    <span><?php echo $this->map["contract_status"][$contract->status]; ?></span>
                </li>
                <li>
                    <label>价格方式：</label>
                    <span><?php echo $this->map["price_type"][$contract['price_type']]; ?></span>
                </li>
                <?php if (!empty($contract->agent)) { ?>
                    <li>
                        <label>采购代理商：</label>
                        <span>
                <?php echo '<a href="/partner/detail/?id=' . $contract->agent['partner_id'] . '&t=1" target="_blank">' . $contract->agent['name'] . '</a>'; ?>
            </span>
                    </li>
                    <li>
                        <label>代理模式：</label>
                        <span><?php echo $this->map['buy_agent_type'][$contract['agent_type']]; ?></span>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>

    <form class="form-horizontal" role="form" id="mainForm">
        <?php
        if (!empty($contract->relative)) {
            $this->renderPartial("/common/new_contractChannelInfo", array('contract' => $contract));
            ?>
            <!-- 占用额度 -->
            <div class="content-wrap" style="margin-bottom:20px;">
                <div class="content-wrap-title">
                    <div>
                        <p>占用额度</p>
                    </div>
                </div>
                <div class="table-title">
                    <span>采购合同占用额度</span>
                    <!-- ko component: {
                                                 name: "quota-items",
                                                 params: {
                                                            quotas:upPartnerQuotas,
                                                            managers:upManagers
                                                        }
                                             } -->
                    <!-- /ko -->
                </div>

                <div class="table-title" style="margin-top:20px;">
                    <span>销售合同占用额度</span>
                    <!-- ko component: {
                                     name: "quota-items",
                                     params: {
                                                quotas:downPartnerQuotas,
                                                managers:downManagers
                                            }
                                 } -->
                    <!-- /ko -->
                </div>

            </div>
        <?php } else { ?>
            <?php
            if ($upPartnerOnly) {
                // 采购单边合同
                $this->renderPartial("/common/new_contractInfo", array('contract' => $contract));
                ?>
                <div class="content-wrap" style="margin-bottom:20px;">
                    <div class="content-wrap-title">
                        <div>
                            <p>占用额度</p>
                        </div>
                    </div>
                    <div class="table-title">
                        <span>采购合同占用额度</span>
                        <!-- ko component: {
                                         name: "quota-items",
                                         params: {
                                                    quotas:upPartnerQuotas,
                                                    managers:upManagers
                                                }
                                     } -->
                        <!-- /ko -->
                    </div>
                </div>
            <?php } ?>
            <?php
            if (!$upPartnerOnly) {
                // 销售单边合同
                $this->renderPartial("/common/new_contractInfo", array('contract' => $contract));
                ?>
                <div class="content-wrap" style="margin-bottom:20px;">
                    <div class="content-wrap-title">
                        <div>
                            <p>占用额度</p>
                        </div>
                    </div>
                    <div class="table-title">
                        <span>销售合同占用额度</span>
                        <!-- ko component: {
                                         name: "quota-items",
                                         params: {
                                                    quotas:downPartnerQuotas,
                                                    managers:downManagers
                                                }
                                     } -->
                        <!-- /ko -->
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </form>

</div>
<script type="text/javascript">
    var view;

    (function () {
        setTimeout(function () {
            $("section.content").trigger('resize');
        });
        var clipboard = new Clipboard('.copy-project-num');
        view = new ViewModel();
        view.upManagers = <?php echo json_encode($upManagers)?>;
        view.downManagers = <?php echo json_encode($downManagers)?>;
        ko.applyBindings(view, $("#content")[0]);
        view.contract_id =<?php echo $contract['contract_id'];?>;
        view.project_id =<?php echo $contract['project_id'];?>;
        view.is_main =<?php echo $contract['is_main'];?>;
    })();

    function ViewModel(option) {
        var defaults = {
            upPartnerQuotas: [],
            downPartnerQuotas: [],
            upManagers: [],
            downManagers: [],
            contract_id: '',
            project_id: '',
            is_main: '',
        }
        var o = $.extend(defaults, option);
        var self = this;
        self.upPartnerQuotas = ko.observableArray(o.upPartnerQuotas);
        self.downPartnerQuotas = ko.observableArray(o.downPartnerQuotas);
        self.contract_id = o.contract_id;
        self.project_id = o.project_id;
        self.upManagers = o.upManagers;
        self.downManagers = o.downManagers;
        self.errors = ko.validation.group(self);
        self.submitting = ko.observable(0);
        self.isValid = function () {
            return self.errors().length === 0;
        }

        self.save = function () {
            if (self.isValid() && self.submitting() == 0) {
                var upQuotaItems = self.getQuotasValue(self.upPartnerQuotas());
                var downQuotaItems = self.getQuotasValue(self.downPartnerQuotas());
                var confirmString = "提交额度占用，该操作不可逆？";
                if (upQuotaItems.length == 0 && downQuotaItems.length == 0) {
                    confirmString = "额度占用信息尚未填写,确认无额度占用信息，该操作不可逆？";
                }
                inc.vueConfirm({
                    content: "是否确认" + confirmString,
                    onConfirm: function () {
                        self.submitting(1);
                        $.ajax({
                            type: "POST",
                            url: "/quota/save",
                            data: {
                                contract_id: self.contract_id,
                                project_id: self.project_id,
                                is_main: self.is_main,
                                upQuotaItems: upQuotaItems,
                                downQuotaItems: downQuotaItems,
                            },
                            dataType: "json",
                            success: function (json) {
                                if (json.state == 0) {
                                    inc.vueMessage({duration: 500,message: json.data, onClose: function () {
                                            window.location.href = '/quota/';
                                        }
                                    });
                                } else {
                                    inc.vueAlert(json.data);
                                    self.submitting(0);
                                }
                            },
                            error: function (data) {
                                inc.vueAlert({content: "保存失败！" + data.responseText});
                                self.submitting(0);
                            }
                        });
                    }
                })
            } else {
                self.errors.showAllMessages();
            }
        }
        self.getQuotasValue = function (quotas) {
            var quotaItems = [];
            ko.utils.arrayForEach(quotas,function (item) {
                quotaItems.push(item.getValue());
            });
            /*$(quotas).each(function (ind, item) {
                quotaItems.push(item.getValue());
            });*/
            return quotaItems;
        }
    }

    $(document).delegate('span.box-title__hiden', 'click', function (event) {
        var ele = event.target;
        var toggle1 = $(ele).parents("div.sub-container__box").find("div.box-content-custom:visible");
        var toggle2 = $(ele).parents("div.sub-container__box").find("div.form-horizontal:visible");
        if (toggle1.length > 0 || toggle2.length > 0) {
            $(ele).parents("div.sub-container__box").find("div.box-content-custom").hide('slow');
            $(ele).parents("div.sub-container__box").find("table").hide('slow');
            $(ele).parents("div.sub-container__box").find("div.form-horizontal").hide('slow');
            var eleI = $('<i class="fa fa-angle-double-down"></i>');
            $(ele).html(' 展开');
            eleI.prependTo($(ele));
        } else {
            $(ele).parents("div.sub-container__box").find("div.box-content-custom").show('slow');
            $(ele).parents("div.sub-container__box").find("table").show('slow');
            $(ele).parents("div.sub-container__box").find("div.form-horizontal").show('slow');
            var eleI = $('<i class="fa fa-angle-double-up"></i>');
            $(ele).html(' 收起');
            eleI.prependTo($(ele));
        }
    });

    function copy() {
        inc.vueMessage('复制成功');
    }

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
</script>