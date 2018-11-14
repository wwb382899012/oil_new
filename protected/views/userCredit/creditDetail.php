<section class="content">
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">查看个人额度明细</h3>
		</div>
		<div class="box-body form-horizontal">
			<div class="box box-primary">
				<div class="form-group">
					<label for="type" class="col-sm-2 control-label">负责人编码</label>
					<div class="col-sm-4">
						<p class="form-control-static"><?php echo $userCreditData["user_id"] ?></p>
					</div>
					<label for="type" class="col-sm-2 control-label">负责人姓名</label>
					<div class="col-sm-4">
						<p class="form-control-static"><?php echo $userCreditData["name"] ?></p>
					</div>
				</div>
				<div class="form-group">
					<label for="type" class="col-sm-2 control-label">常规额度</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo "￥". number_format($userCreditData["credit_amount"]/1000000, 2) . "万元" ?></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">占用额度</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo "￥". number_format($userCreditData["use_amount"]/1000000, 2) . "万元" ?></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">剩余额度</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo "￥". number_format($userCreditData["balance_amount"]/1000000, 2) . "万元" ?></p>
                    </div>
				</div>
			</div>

            <?php
            $table_array = array(
	            array('key' => 'detail_id', 'type' => '', 'style' => 'width:60px;text-align:center;vertical-align:middle;', 'text' => '编号'),
	            array('key' => 'project_id', 'type' => '', 'style' => 'width:120px;text-align:center;vertical-align:middle;', 'text' => '项目编号'),
	            array('key' => 'project_id,project_name', 'type' => 'href', 'style' => 'text-align:left;vertical-align:middle;', 'text' => '项目名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
	            array('key' => 'user_id,user_name', 'type' => 'href', 'style' => 'width:100px;text-align:center;vertical-align:middle;', 'text' => '业务负责人','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/businessAssistant/detail/?user_id={1}&t=1&url=/userCredit/creditDetail/?user_id={1}" >{2}</a>'),
                array('key' => 'trade_type', 'type' => 'map_val', 'map_name'=>'trade_type', 'style' => 'width:80px;text-align:center;vertical-align:middle;', 'text' => '业务类型'),
                array('key' => 'actual_up_amount', 'type' => 'amount', 'style' => 'width:130px;text-align:right;vertical-align:middle;', 'text' => '采购价'),
                array('key' => 'actual_up_quantity', 'type' => '', 'style' => 'width:100px;text-align:right;vertical-align:middle;', 'text' => '采购数量'),
                array('key' => 'actual_down_quantity', 'type' => '', 'style' => 'width:100px;text-align:right;vertical-align:middle;', 'text' => '卖出数量'),
	            array('key' => 'used_amount', 'type' => 'amount',  'style' => 'width:130px;text-align:right;vertical-align:right;', 'text' => '占用额度'),
            );
            $this->show_table($table_array, $_data_['creditDetailData']['data'], "", "min-width:1030px;","table-bordered table-layout");
            ?>
		</div>
		<div class="box-footer">
			<?php if (!$this->isExternal) { ?>
				<button type="button" class="btn btn-default" onclick="back()">返回</button>
			<?php } ?>
		</div>
	</div>
</section>

<script>
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

