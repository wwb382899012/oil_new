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
			echo '<li><a href="#tab2" data-toggle="tab">附件信息</a></li>';
		}
		if (count($logData['rows']) > 0) {
			echo '<li ><a href="#tab3" data-toggle="tab">操作日志</a></li>';
		}
		?>
        <li class="pull-right">
            <button type="button" class="btn btn-default" onclick="back()">返回</button>
        </li>
		<?php if ($this->checkIsCanEdit($data['status']) && UserService::checkActionRight($this->rightCode, "edit")) { ?>
            <li class="pull-right">
                <button type="button" class="btn btn-primary" onclick="edit()">调整</button>
            </li>
		<?php } ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">查看个人额度信息</h3>
                </div>
                <div class="box-body form-horizontal">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">活（定）期存款</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo '￥ ' . number_format($data["bank_amount"]/1000000, 2) ?>万元</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="credit_code" class="col-sm-2 control-label">加油宝理财投资</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo '￥ ' . number_format($data["jyb_amount"]/1000000, 2) ?>万元</p>
                        </div>
                    </div>

					<?php
					$other_map = $this->map['user_credit_other_json'];
					if (is_array($other_map) && count($other_map) > 0) {
						foreach ($other_map as $key => $row) {
							?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $row['label'] ?>
                                    </label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><?php echo '￥ ' . number_format($data['other_json'][$row['key']]/1000000, 2) ?>万元</p>
                                </div>
                            </div>

							<?php
						}
					}
					?>

                    <div class="form-group">
                        <label for="credit_amount" class="col-sm-2 control-label">确认额度 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo '￥ ' . number_format($data['credit_amount']/1000000, 2) ?>万元</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="start_time" class="col-sm-2 control-label">生效日期</label>
                        <div class="col-sm-4">
                            <p class="form-control-static">
                                <?php
                                if($data['start_time'] != '0000-00-00 00:00:00') {
	                                echo date("Y-m-d", strtotime($data['start_time']));
                                } else {
                                    echo '';
                                }
                                ?></p>
                        </div>
                        <label for="end_time" class="col-sm-2 control-label">失效日期</label>
                        <div class="col-sm-4">
                            <p class="form-control-static">
                                <?php
                                if($data['end_time'] != '0000-00-00 00:00:00') {
	                                echo date("Y-m-d", strtotime($data['end_time']));
                                } else {
	                                echo '';
                                }
                                ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="remark" class="col-sm-2 control-label">备注</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data['remark'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
					<?php if (!$this->isExternal) { ?>
                        <button type="button" class="btn btn-default" onclick="back()">返回</button>
					<?php } ?>
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
						//$attachments = SystemUser::getUserAttachments($obj["user_id"], $attachmentType);
						$attachments = SystemUser::getUserAttachments($obj["user_id"], $this->attachmentType);
					}
					$attachmentTypeKey = "user_credit_attachment_type";
					$this->showAttachmentsEditMulti($data["user_id"], $data, $attachmentTypeKey, $attachments);
					?>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="tab3">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">操作日志</h3>
                </div><!--end box-header-->
                <div class="box-body no-padding">
				    <?php
				    $table_array = array(
					    array('key' => 'create_user_name', 'type' => '', 'style' => 'width:60px;text-align:center;', 'text' => '操作人'),
					    array('key' => 'create_time', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '时间'),
					    array('key' => 'remark', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '操作'),
					    array('key' => 'operation_content', 'type' => '', 'style' => 'width:200px;text-align:center;', 'text' => '操作行为'),
				    );
				    $this->show_table($table_array, $_data_['logData'], "", "min-width:900px;");
				    ?>
                </div><!--end box-body no-padding-->
            </div><!--end box-->
        </div><!--end tab3-->
    </div>
</div>

<script>
	//修改
	function edit() {
		location.href = "/userCredit/edit?user_id=<?php echo $data['user_id'] ?>&credit_id=<?php echo $data['credit_id'] ?>";
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