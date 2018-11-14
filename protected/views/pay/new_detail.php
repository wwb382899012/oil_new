<?php
$menus=$this->getIndexMenuWithNewUI();

$menus[] = ['text' => '详情'];
$buttons = [];
if ($apply->isCanEdit()) {
    $buttons[] = ['text' => '提交', 'attr' => ['data-bind' => 'click:submitForCheck', 'id' => 'submitButton']];
    $buttons[] = ['text' => '修改', 'attr' => ['onclick' => 'edit()', 'id' => 'editButton', 'class_abbr' => 'action-default-base']];
}
$buttons[] = ['text' => '复制', 'attr' => ['onclick' => 'copy()', 'id' => 'copyButton','class_abbr'=>'action-default-base']];
$this->loadHeaderWithNewUI($menus, $buttons, '/pay/');
?>
<?php $this->renderPartial("/pay/new_detailBody", array('apply' => $apply)); ?>
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>审核记录</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <?php
    $checkLogs = FlowService::getCheckLogModel($apply->apply_id, FlowService::BUSINESS_PAY_APPLICATION);
    if (Utility::isNotEmpty($checkLogs))
        $this->renderPartial("/check/new_checkLogs", array("checkLogs" => $checkLogs)); ?>

</div>
<div class="modal fade draggable-modal" id="business-check-user-modal" tabindex="-1" role="dialog"
     aria-labelledby="modal" data-backdrop="static">
    <div class="modal-dialog modal-normal" role="document">
        <div class="modal-content">
            <div class="modal-header--flex">
                <h4 class="modal-title">业务主管审核人选择</h4>
                <a type="button" class="close" data-bind="click:hideModal" aria-label="Close"><span
                            aria-hidden="true">×</span></a>
            </div>
            <div class="modal-body">
                <form class="search-form">
                    <div class="flex-grid form-group">
                        <div class="o-row">
                            <div class="o-col-sm-12">
                                <div class="flex-grid children-gap--fixed first-line-align" style="flex-wrap: wrap;">

                                    <!-- ko foreach: businessDirectors -->

                                    <label class="o-control o-control--radio inline-flex" style="margin-left: 0 !important;margin-right: 20px;width: 150px">
                                        <input name="obj[check_user_validate]" type="radio" style="width: auto"
                                               data-bind="checkedValue:user_id,checked:$parent.check_user,value:user_id">
                                        <span style="margin-left: 10px;" data-bind="text:name"></span>
                                        <div class="o-control__indicator"></div>
                                    </label>

                                    <!-- /ko -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="o-row">
                        <input type="hidden" class="form-control" name="obj[check_user_validate]" placeholder="审核人" data-bind="value:check_user_validate" title="不得为空" >
                    </div>
                </form>
            </div>
            <div class="modal-footer flex-center">
                <a href="javascript: void 0" role="button" class="o-btn o-btn-primary"
                   data-bind="click:submitUserCheck">确定</a>
                <a href="javascript: void 0" role="button" class="o-btn o-btn-action w-base" data-bind="click:hideModal">关闭</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="/js/pages/pay.js?key=2018080301"></script>
<script>
    var currencies =<?php echo json_encode($this->map["currency"]); ?>;
    var expenseNames =<?php echo json_encode($this->map["pay_type"]); ?>;
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($applyInfo) ?>);
        view.initBusinessDirectors(<?php echo empty($business_directors) ? "[]" : json_encode($business_directors) ?>);
        ko.applyBindings(view);
    });

</script>
<script>
    function back() {
        location.href = "/pay/";
    }

    function edit() {
        location.href = "/pay/edit/?id=<?php echo $apply['apply_id'] ?>";
    }

	function copy() {
		inc.vueConfirm({
			content: "您确定要复制编号为“<?php echo $apply['apply_id'] ?>”的付款申请单吗，该操作不可逆？", onConfirm: function(index) {
				$.ajax({
					type: 'GET',
					url: '/pay/copy',
					data: {apply_id: <?php echo $apply['apply_id']; ?>},
					dataType: "json",
					success: function (json) {
						if (json.state == 0) {
							inc.vueMessage({
								message: "复制成功"
							});
							location.href = "/pay/edit/?id=" + json.data;
						}
						else {
							inc.vueAlert(json.data);
						}
					},
					error: function (data) {
						inc.vueAlert("复制失败！" + data.responseText);
					}
				});
			}
		});
	}

    //function submitForCheck() {
    //inc.vueConfirm({
    //	content: "您确定要提交当前信息进入审核吗，该操作不可逆？", onConfirm: function () {
    //		var formData = {id: <?php //echo $apply['apply_id'] ?>//};
    //		$.ajax({
    //			type: 'POST',
    //			url: '/pay/submit',
    //			data: formData,
    //			dataType: "json",
    //			success: function (json) {
    //				if (json.state == 0) {
    //					inc.vueMessage({
    //						message: "操作成功"
    //					});
    //                        location.reload();
    //				}
    //				else {
    //					inc.vueAlert(json.data);
    //				}
    //			},
    //			error: function (data) {
    //				inc.vueAlert("操作失败！" + data.responseText);
    //			}
    //		});
    //	}
    //    });
    //}
</script>