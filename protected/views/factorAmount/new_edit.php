<?php
$menus = [['text' => '保理对接款管理', 'link' => '/factorAmount/'], ['text' => $this->pageTitle]];
$buttons = [];
$buttons[] = ['text' => '提交', 'attr' => ['data-bind' => 'click:submit, html:submitBtnText', 'id' => 'submitButton']];
$this->loadHeaderWithNewUI($menus, $buttons, true);
?>
<section class="content">
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>付款信息</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="item-com">
            <li>
                <label>付款编号：</label>
                <p>
                    <a target="_blank"
                       href="/pay/detail?id=<?php echo $data->apply_id ?>"><?php echo $data->apply_id ?></a>
                </p>
            </li>
            <li>
                <label>项目编号：</label>
                <p class="form-control-static">
                    <a target="_blank"
                       href="/project/detail?id=<?php echo $data->project_id ?>"><?php echo $data->project->project_code ?></a>
                </p>
            </li>
            <li>
                <label>合同编号：</label>
                <p class="form-control-static">
                    <a target="_blank"
                       href="/contract/detail?id=<?php echo $data->contract_id ?>"><?php echo $data->contract->contract_code ?></a>
                </p>
            </li>
            <li>
                <label>付款申请金额：</label>
                <p class="form-control-static"><?php echo Map::$v['currency'][$data->payApply->currency]['ico'] . Utility::numberFormatFen2Yuan($data->payApply->amount) ?></p>
            </li>
            <li>
                <label>申请保理对接金额：</label>
                <p class="form-control-static"><?php echo Map::$v['currency'][$data->payApply->currency]['ico'] . Utility::numberFormatFen2Yuan($data->apply_amount) ?></p>
            </li>
            <li>
                <label>保理对接编号：</label>
                <p class="form-control-static"><?php echo $data->contract_code ?></p>
            </li>
            <li>
                <label>资金对接编号：</label>
                <p class="form-control-static"><?php echo $data->contract_code_fund ?></p>
            </li>
        </ul>
    </div>
    <div class="card-wrapper">
        <form  role="form" id="mainForm">
            <div class="z-card">
                <div class="z-card-part">
                    <h3 class="z-body-header">保理对接实际信息</h3>
                    <div class="flex-grid form-group">
                        <label class="col col-count-3 field">
                            <p class="form-cell-title must-fill">实际保理对接金额</p>
                            <div class="input-group">
                                <span class="input-group-addon">￥</span>
                                <input type="text" class="form-control" placeholder="实际保理对接金额" data-bind="money:amount">
                                <span class="input-group-addon" data-bind="moneyChineseText:amount"></span>
                            </div>
                        </label>
                        <label class="col col-count-3 field">
                            <p class="form-cell-title must-fill">年化利率</p>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="年化利率" data-bind="value:rate">
                                <span class="input-group-addon">%</span>
                            </div>
                        </label>
                        <label class="col col-count-3 field">
                            <p class="form-cell-title must-fill">实际放款时间</p>
                            <input type="text" class="form-control date" placeholder="实际放款时间"
                                   data-bind="date:actual_pay_date">
                        </label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($factor) ?>);
        ko.applyBindings(view);
    });

    function ViewModel(option) {
        var defaults = {
            factor_id: 0,
            pay_apply_amount: 0,
            amount: 0,
            rate: 1,
            actual_pay_date: '',
            status: 0,
            remark: ''
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.factor_id = ko.observable(o.factor_id);
        self.pay_apply_amount = ko.observable(o.pay_apply_amount);
        self.amount = ko.observable(o.amount).extend({
            custom: {
                params: function (v) {
                    self.msg = '不得为空';
                    if (v != '' && v != null) {
                        if (v > ko.unwrap(self.pay_apply_amount)) {
                            self.msg = '不得超过付款申请金额';
                            return false;
                        }
                        if (v < 0) {
                            self.msg = '不得为负';
                            return false;
                        }
                    } else {
                        return false;
                    }

                    return true;
                },
                message: function () {
                    return self.msg;
                }
            }
        });
        self.rate = ko.observable(o.rate).extend({required: true});
        self.actual_pay_date = ko.observable(o.actual_pay_date).extend({required: true});
        self.status = ko.observable(o.status);
        self.remark = ko.observable(o.remark);

        self.submitBtnText = ko.observable("提交");
        self.actionState = 0;
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.sendSubmitAjax = function () {
            if (self.actionState == 1)
                return;
            self.actionState = 1;
            var formData = {"data": inc.getPostData(self, ["submitBtnText", "msg"])};
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/submit',
                data: formData,
                dataType: "json",
                success: function (json) {
                    self.actionState = 0;
                    self.submitBtnText("提交");
                    if (json.state == 0) {
						inc.vueMessage({message: '操作成功', onClose: function () {
                            location.href = "/<?php echo $this->getId() ?>";
                        }});
                    } else if (json.state == -1) {
						inc.vueConfirm({
							content: json.data, onConfirm: function () {
								location.href = "/<?php echo $this->getId() ?>/index";
							}
                        });
                    } else {
                        inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
                    self.actionState = 0;
                    self.submitBtnText("提交");
                    inc.vueAlert("操作失败！" + data.responseText);
                }
            });
        };

        self.submit = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
			inc.vueConfirm({
				content: "您确定要提交当前保理对接款信息吗，该操作不可逆？", onConfirm: function () {
					self.status(3);
					self.submitBtnText("提交中" + inc.loadingIco);
					self.sendSubmitAjax();
				}
            })
        };

        self.back = function () {
            location.href = "/<?php echo $this->getId() ?>";
        }
    }
</script>