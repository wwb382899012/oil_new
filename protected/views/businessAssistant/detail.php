<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">基本信息</a></li>
		<?php
		if (count($attachments) <= 0) {
			echo '<li ><a href="#tab2" data-toggle="tab" class="text-red fa fa-warning" >&nbsp;扩展信息</a></li>';
		} else {
			echo '<li><a href="#tab2" data-toggle="tab">扩展信息</a></li>';
		}
		?>
        <li class="pull-right">
            <button type="button" class="btn btn-sm btn-primary" onclick="edit()">修改</button>&nbsp;
            <button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">业务负责人详细信息</h3>
                </div>
                <div class="box-body form-horizontal">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">姓名
                            <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["name"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">编码
                            <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["code"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sex" class="col-sm-2 control-label">性别 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $this->map['gender'][$data['sex']] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="id_code" class="col-sm-2 control-label">身份证号码
                            <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["id_code"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="col-sm-2 control-label">手机号码
                            <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["phone"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="col-sm-2 control-label">住址
                            <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["address"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-sm-2 control-label">状态
                            <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["user_status"][$data["status"]] ?></p>
                        </div>
                    </div>
                    <hr>

                    <div class="form-group">
                        <label for="contact_person" class="col-sm-2 control-label">紧急联系人</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["contact_person"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact_phone" class="col-sm-2 control-label">手机号码</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["contact_phone"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact_id_code" class="col-sm-2 control-label">身份证号码</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["contact_id_code"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remark" class="col-sm-2 control-label">备注</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["remark"] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="tab2">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">附件信息</h3>
                </div>
                <div class="box-body no-padding">
					<?php
					if (empty($attachments)) {
						$attachments = SystemUser::getUserAttachments($data['user_id'], $attachmentType);
					}
					$attachmentTypeKey = "user_extra_attachment_type";
					$this->showAttachmentsEditMulti($data["user_id"], $data, $attachmentTypeKey, $attachments);
					?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
	//修改
	function edit() {
		location.href = "/businessAssistant/edit?user_id=<?php echo $data['user_id'] ?>";
	}

	//返回
	function back() {
		<?php
		if (!empty($_GET["url"])) {
			echo 'location.href="' . $this->getBackPageUrl() . '";';
		} else {
			echo "history.back();";
		}
		?>
	}
</script>