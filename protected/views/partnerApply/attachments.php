<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">附件（注：*标注资料必须上传）</h3>
        </div>
        <div class="box-body">
			<?php
			if (empty($attachments)) {
				$attachments = PartnerApplyService::getAttachment($data["partner_id"]);
			}
			$attachmentTypeKey = "partner_apply_attachment_type";
			$this->showAttachmentsEditMulti($data["partner_id"], $data, $attachmentTypeKey, $attachments);
			?>
        </div>
        <div class="box-footer">
            <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">提交</button>
            <button type="button" id="backButton" class="btn btn-default" data-bind="click:back">返回</button>
            <input type='hidden' name='data[partner_id]' data-bind="value: partner_id"/>
        </div>
    </div>
</section>
<script>
	var requiredTypes = <?php
            //$auto_level = PartnerApplyService::getPartnerLevel($data);
            $level = !empty($data['custom_level']) ? $data['custom_level'] : (!empty($data['auto_level']) ? $data['auto_level'] : 0);
            $amountType = PartnerService::getAttachmentAmountType($data["business_type"], $level, $data['apply_amount']/10000/100);
            echo json_encode($this->map["partner_required_attachment_config"][$data["business_type"]][$level][$amountType]);
        ?>;
	if(requiredTypes != null && requiredTypes.length > 0){
		for (var i = 0; i < requiredTypes.length; i++) {
			$("td[data-type=" + requiredTypes[i] + "]").find(".file-title").append(" <span class='text-red fa fa-asterisk'></span>");
		}
    }

	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
		ko.applyBindings(view);

		function ViewModel(option) {
			var defaults = {
				partner_id: 0
			};
			var o = $.extend(defaults, option);

			var self = this;
			self.partner_id = ko.observable(o.partner_id);
			self.actionState = ko.observable(0);

			self.save = function () {
				if (self.actionState() == 1) {
					return;
				}
				self.actionState(1);
				var formData="data[partner_id]="+self.partner_id();
				$.ajax({
					type: "POST",
					url: "/partnerApply/save",
					data: formData,
					dataType: "json",
					success: function (json) {
						console.log(json);
						self.actionState(0);
						if (json.state == 0) {
							location.href = "/partnerApply/";
						} else {
							layer.alert(json.data, {icon: 5});
						}
					},
					error: function (data) {
						self.actionState(0);
						layer.alert("提交失败：" + data.responseText, {icon: 5});
					}
				});
			}

			self.back = function () {
				location.href = "/partnerApply/edit/?partner_id="+self.partner_id();
			}
		}
	});
</script>
