<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<div class="box">
    <div class="box-body form-horizontal">
        <div class="box-header with-border">
            <h3 class="box-title">合作方详细信息</h3>
        </div>
        <div class="form-group">
            <label for="partner_id" class="col-sm-2 control-label">企业编号</label>
            <div class="col-sm-2">
                <p class="form-control-static">
                    <a href="/PartnerApply/detail/?partner_id=<?php echo $partner['partner_id'] ?>&t=1" target="_blank"><?php echo $partner["partner_id"] ?></a>
                </p>
            </div>

            <label for="partner_name" class="col-sm-2 control-label">企业名称</label>
            <div class="col-sm-6">
                <p class="form-control-static">
                    <a href="/PartnerApply/detail/?id=<?php echo $partner['partner_id'] ?>&t=1" target="_blank"><?php echo $partner["partner_name"] ?></a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">类别</label>
            <div class="col-sm-2">
                <p class="form-control-static"><?php echo PartnerApplyService::getPartnerType($partner["type"]) ?></p>
            </div>
            <label for="apply_amount" class="col-sm-2 control-label">拟申请额度</label>
            <div class="col-sm-2">
                <p class="form-control-static"><?php echo '￥ ' . number_format($partner["apply_amount"] / 1000000, 2) ?> 万元</p>
            </div>
            <label for="o_credit_amount" class="col-sm-2 control-label">拟授予额度</label>
            <div class="col-sm-2">
                <p class="form-control-static"><?php echo '￥ ' . number_format($partner["o_credit_amount"] / 1000000, 2) ?> 万元</p>
            </div>
        </div>
        <div class="form-group">
            <label for="auto_level" class="col-sm-2 control-label">系统评级</label>
            <div class="col-sm-2">
                <p class="form-control-static">
					<?php
					if (!empty($partner["auto_level"])) {
						echo $this->map['partner_level'][$partner["auto_level"]];
					} else {
						echo "暂无";
					}
					?>
                </p>
            </div>
            <label for="custom_level" class="col-sm-2 control-label">商务评级</label>
            <div class="col-sm-2">
                <p class="form-control-static">
					<?php
					if (!empty($partner["custom_level"])) {
						echo $this->map['partner_level'][$partner["custom_level"]];
					} else {
						echo "暂无";
					}
					?>
                </p>
            </div>

            <label for="level" class="col-sm-2 control-label">风控评级</label>
            <div class="col-sm-2">
                <p class="form-control-static">
					<?php
					if (!empty($partner["level"])) {
						echo $this->map['partner_level'][$partner["level"]];
					} else {
						echo "暂无";
					}
					?>
                </p>
            </div>
        </div>
    </div>
</div>
<?php
if (count($data) > 0) {
	foreach ($data as $i => $value) { ?>
        <div class="box">
            <div class="box-header with-border <?php if ($i >= 1)
				echo 'link' ?>">
                <h3 class="box-title">
                    <b><span class="text-red"><?php echo $value['start_time'] ?></span>&nbsp;现场风控信息</b>
                </h3>
            </div>
            <div class="box-body <?php if ($i >= 1)
				echo 'hide1' ?>">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab1<?php echo $i ?>" data-toggle="tab">基本信息</a></li>
						<?php
						if (count($attachments) <= 0) {
							echo '<li ><a href="#tab2' . $i . '" data-toggle="tab" class="text-red fa fa-warning" >&nbsp;扩展信息</a></li>';
						} else {
							echo '<li><a href="#tab2' . $i . '" data-toggle="tab">扩展信息</a></li>';
						}
						?>
                        <!--<li class="pull-right">
						</li>-->
						<?php if ($i == 0) { ?>
                            <div class="pull-right box-tools">
								<?php if ($this->checkIsCanEdit($value['status']) && UserService::checkActionRight($this->rightCode, "submit")) { ?>
                                    <!--            <li class="pull-right box-tools">-->
                                    <button type="button" class="btn btn-sm btn-primary" onclick="edit()">修改</button>&nbsp;
                                    <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo PartnerRisk::STATUS_RISK_SUBMIT ?>)">提交</button>
                                    <!--            </li>-->
								<?php } ?>
                                <button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button>
                            </div>
						<?php } ?>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1<?php echo $i ?>">
                            <div class="box">
                                <div class="box-body form-horizontal">
                                    <div class="form-group">
                                        <label for="start_time" class="col-sm-2 control-label">风控考察时间<span
                                                    class="text-red fa fa-asterisk"></span></label>
                                        <div class="col-sm-2">
                                            <p class="form-control-static"><?php echo $value['start_time'] ?></p>
                                        </div>
                                        <div class="col-sm-1 text-center" style="margin-top: 7px; margin-left: -30px">至</div>
                                        <div class="col-sm-2">
                                            <p class="form-control-static"><?php echo $value['end_time'] ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="type" class="col-sm-2 control-label">风控考察负责人<span
                                                    class="text-red fa fa-asterisk"></span></label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static"><?php echo UserService::getUsernameById($value['main_user_id']) ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="type" class="col-sm-2 control-label">风控考察成员<span
                                                    class="text-red fa fa-asterisk"></span></label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static">
												<?php
												$user_names = "";
												$user_ids_arr = explode(",", $value['user_ids']);
												foreach ($user_ids_arr as $row) {
													$user_names .= UserService::getUsernameById($row) . "; ";
												}
												echo $user_names;
												?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address" class="col-sm-2 control-label">风控考察地址<span
                                                    class="text-red fa fa-asterisk"></span></label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static"><?php echo $value['address'] ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="describe" class="col-sm-2 control-label">货物、资金、发票匹配情况</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static"><?php echo $value['describe'] ?></p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="credit_amount" class="col-sm-2 control-label">确认最终额度</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static"><?php echo '￥ ' . number_format($value["credit_amount"] / 1000000, 2) ?> 万元</p>
                                        </div>
                                    </div>
                                    <hr/>

									<?php
									$content_map = $this->map['partner_risk_content_info'];
									foreach ($content_map as $key => $row) {
										if (is_array($row)) {
											if (count($row) > 1) {
												foreach ($content_map[$key][$partner['business_type']] as $k => $v) {
													?>
                                                    <div class="form-group">
                                                        <label class="col-sm-12 control-label" style="text-align: left"><?php echo $k ?></label>
                                                    </div>
													<?php
													foreach ($v as $index => $d) {
														?>
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label"><?php echo $d['label'] ?></label>
                                                            <div class="col-sm-4">
                                                                <p class="form-control-static"><?php echo $value['content']->$d['key'] ?></p>
                                                            </div>
                                                        </div>
														<?php
													}
												}
											} else {
												foreach ($row as $k => $v) {
													?>
                                                    <div class="form-group">
                                                        <label class="col-sm-12 control-label" style="text-align: left"><?php echo $k ?></label>
                                                    </div>
													<?php
													foreach ($v as $index => $d) {
														?>
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label"><?php echo $d['label'] ?></label>
                                                            <div class="col-sm-4">
                                                                <p class="form-control-static"><?php echo $value['content']->$d['key'] ?></p>
                                                            </div>
                                                        </div>
														<?php
													}
												}
											}
											?>
                                            <hr/>
											<?php
										}
									}
									?>

                                    <div class="form-group">
                                        <label for="conclusion" class="col-sm-1 control-label">结论</label>
                                        <div class="col-sm-11">
                                            <p class="form-control-static"><?php echo $value['conclusion'] ?></p>
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

                        <div class="tab-pane" id="tab2<?php echo $i ?>">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">附件信息</h3>
                                </div>
                                <div class="box-body no-padding">
									<?php
									if (empty($attachments[$i])) {
										$attachments[$i] = PartnerRisk::getPartnerRiskAttachments($value["risk_id"]);
									}
									$attachmentTypeKey = "partner_risk_attachment_type";
									$this->showAttachmentsEditMulti($value["risk_id"], $value, $attachmentTypeKey, $attachments[$i]);
									?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
} else {
	echo '
        <section class="content-list" style="height: auto">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p>您好，当前没有现场风控数据。</p>
                </div>
            </div>
        </section>';
} ?>

<script>
	$("div.link").each(function () {
		$(this).click(function () {
			$(this).next().toggle();
		});
	});

	//修改
	function edit() {
		location.href = "/partnerRisk/edit?partner_id=<?php echo $value['partner_id'] ?>&risk_id=<?php echo $value['risk_id'] ?>";
	}

	//提交
	function submit(status) {
		layer.confirm("您确定要执行该操作吗？该操作不可逆！", {icon: 3, title: '提示'}, function(index){
			var formData = "partner_id=<?php echo $value['partner_id'] ?>&status=" + status;
			$.ajax({
				type: "POST",
				url: "/partnerRisk/submit",
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						inc.showNotice("操作成功");
						if (status == <?php echo PartnerRisk::STATUS_RISK_REJECT?>) {
							location.href = "/partnerRisk/";
						} else {
							location.href = "/partnerRisk/detail/?partner_id=<?php echo $value['partner_id'] ?>";
						}
					}
					else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});

            layer.close(index);
		});
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