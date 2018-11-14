<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">基本信息</a></li>
		<?php
		if (count($logData['rows']) > 0) {
			echo '<li ><a href="#tab2" data-toggle="tab">操作日志</a></li>';
		}
		?>
        <li class="pull-right">
            <button type="button" class="btn btn-primary" onclick="edit()">修改</button>
            <button type="button" class="btn btn-default" onclick="back()">返回</button>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">查看合作方白名单详情</h3>
                </div>
                <div class="box-body form-horizontal">
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">企业名称（全称）<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["name"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">法定代表人</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["corporate"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">注册资本（万元）</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["registered_capital"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">成立日期</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["start_date"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">企业所有制</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <?php
                                $ownerships = Ownership::getOwnerships();
                                if(count($ownerships) > 0 ) {
	                                foreach ($ownerships as $key => $row) {
		                                if($data['ownership'] == $row['id']) {
			                                echo $row['name'];
			                                break;
		                                }
	                                }
                                } else {
                                    echo "";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">企业分级</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $this->map["partner_level"][$data["level"]] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">状态</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["partner_white_status"][$data["status"]] ?></p>
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
                    <h3 class="box-title">操作日志</h3>
                </div><!--end box-header-->
                <div class="box-body no-padding">
	                <?php
	                $table_array = array(
		                array('key' => 'create_user_name', 'type' => '', 'style' => 'width:60px;text-align:center;', 'text' => '操作人'),
		                array('key' => 'create_time', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '时间'),
		                array('key' => 'operation_content', 'type' => '', 'style' => 'width:200px;text-align:center;', 'text' => '操作行为')
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
		location.href = "/partnerWhite/edit/?t=<?php echo $this->isExternal ?>&id=<?php echo $data["id"] ?>"
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