<?php
if (empty($contract->relative)) {
    // 单边合同
    $upPartnerOnly = ($contract->type == ConstantMap::BUY_TYPE) || ($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY);
}
$this->renderPartial("/quota/new_editElement", array('contract' => $contract));
?>
<div id="modalBody" class="ajax_box">
    <div class="modal-header">
        <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">×</span></a>
        <h4 class="modal-title">占用额度</h4>
    </div>
    <div class="modal-body">
        <form class="form-horizontal" role="form" id="childForm">

            <?php
            if (!empty($contract->relative)) :
                ?>
                <h4 style="margin: 10px 0;">
                    采购合同占用额度
                </h4>
                <div class="form-group">
                    <div class="col-sm-12">
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

                <h4 style="margin: 10px 0;">
                    销售合同占用额度
                </h4>
                <div class="form-group">
                    <div class="col-sm-12">
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

            <?php else: ?>
                <?php
                if ($upPartnerOnly) :
                    // 采购单边合同
                    ?>
                    <h4 style="margin: 10px 0;">
                        采购合同占用额度
                    </h4>
                    <div class="form-group">
                        <div class="col-sm-12">
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
                <?php endif; ?>
                <?php
                if (!$upPartnerOnly) :
                    // 销售单边合同
                    ?>
                    <h4 style="margin: 10px 0;">
                        销售合同占用额度
                    </h4>
                    <div class="form-group">
                        <div class="col-sm-12">
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
                <?php endif; ?>
            <?php endif; ?>

        </form>
    </div>
    <div class="modal-footer">
        <a href="javascript:void(0)" class="oil-btn" data-bind="click:save">确定</a>
        <a href="javascript:void(0)" class="z-btn-action w-base" data-dismiss="modal">关闭</a>
    </div>
</div>
<script type="text/javascript">
    var subView;

    (function () {
        subView = new ViewModel();
        subView.upManagers = <?php echo json_encode($upManagers)?>;
        subView.downManagers = <?php echo json_encode($downManagers)?>;
        ko.applyBindings(subView, $("#modalBody")[0]);
        subView.contract_id =<?php echo $contract['contract_id'];?>;
        subView.project_id =<?php echo $contract['project_id'];?>;
        subView.is_main =<?php echo $contract['is_main'];?>;
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
            console.log(self.isValid());
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
                                    inc.vueMessage({
                                        duration: 500, message: json.data, onClose: function () {
                                            $("#modalBody").parents("div.modal").modal('hide');
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
            $(quotas).each(function (ind, item) {
                quotaItems.push(item.getValue());
            });
            return quotaItems;
        }
    }

</script>

