<section class="content">
    <?php include 'partial/stockNoticeInfo.php';?>
    <?php if (Utility::isNotEmpty($stockIns)):?>
        <?php foreach ($stockIns as $key => $stockIn):?>
            <div class="box">
                <div class="box-header with-border <?php if ($key >= 1){echo 'link';} ?>">
                    <h3 class="box-title" style="text-align: center">
                        <b><?php echo $stockIn->type == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE ? $stockIn->store->name : '虚拟库' ?>入库单&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo $stockIn['code'] ?></span></span></b>
                    </h3>
                    <?php if (StockInService::isCanEdit($stockIn['status'])): ?>
                        <div class="pull-right box-tools">
                            <button type="button" class="btn btn-sm btn-primary" onclick="edit(<?php echo $stockIn['stock_in_id'] ?>)">修改</button>&nbsp;
                            <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo $stockIn['stock_in_id'] ?>)">提交</button>&nbsp;
                        </div>
                    <?php endif; ?>
                </div>

                <div class="box-body <?php if ($key >= 1){echo 'hide1';}?> form-horizontal">
                    <div class="form-group">
                        <?php
                        $this->renderPartial("/components/attachmentsDropdown", array(
                                'id' => $stockIn['stock_in_id'],
                                'map_key'=>'stock_in_attachment_type',
                                'attach_type'=>ConstantMap::STOCK_IN_ATTACH_TYPE,
                                'attachment_type'=>Attachment::C_STOCK_IN,
                                'controller'=>'stockIn',
                            )
                        );
                        ?>
                        <label class="col-sm-2 control-label">入库日期</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $stockIn['entry_date'] ?></p>
                        </div>
                    </div>
                    <?php if (Utility::isNotEmpty($stockIn->details)): ?>
                        <div class="form-group">
                        <div class="col-sm-12">
                            <table class="table table-striped table-bordered table-condensed table-hover">
                                <thead>
                                <tr>
                                    <th style="width:120px;text-align:center">品名</th>
                                    <th style="width:80px;text-align:center">入库单数量</th>
                                    <th style="width:80px;text-align:center">换算比例</th>
                                    <th style="width:80px;text-align:center">备注</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($stockIn->details as $val):?>
                                    <tr>
                                        <td style="text-align:center"><?php echo $val->goods->name ?></td>
                                        <td style="text-align:center">
                                            <?php
                                            $amountInfo = Utility::numberFormatToDecimal($val["quantity"], 4) . Map::$v['goods_unit'][$val['unit']]['name'];
                                            if (!empty($val->sub) && !empty($val->sub->unit)) {
                                                $amountInfo .= '/' . Utility::numberFormatToDecimal($val->sub->quantity, 4) . Map::$v['goods_unit'][$val->sub->unit]['name'];
                                            }
                                            echo $amountInfo;
                                            ?>
                                        </td>
                                        <td style="text-align:center">
                                            <?php echo  (!empty($val->sub) && !empty($val->unit_rate)) ? $val['unit_rate'] : ''; ?>
                                        </td>
                                        <td style="text-align:center">
                                            <?php echo $val['remark']; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php
                            $status = $stockIn['status']==StockIn::STATUS_SETTLED ? StockIn::STATUS_PASS : $stockIn['status'];
                            $this->renderPartial("/common/stockInOrOutStatusInfo", array(
                                    'isCanShowStatus'=> StockInService::isCanShowStatus($status),
                                    'isInvalid' => StockInService::isInvalid($status),
                                    'statusName'=> Map::$v['stock_in_status'][$status],
                                    'remark' => $stockIn['remark'],
                                    'isCanShowAuditStatus' => StockInService::isCanShowAuditStatus($status),
                                    'isShowAuditRemark' => StockInService::isShowAuditRemark($status),
                                    'id' => $stockIn['stock_in_id'],
                                    'businessIds'=> FlowService::BUSINESS_STOCK_IN_CHECK,
                                )
                            );
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach;?>
    <?php endif;?>
</section>

<script>
	$("div.link").each(function () {
		$(this).click(function () {
			$(this).next().toggle();
		});
	});

	function back() {
        <?php
        if (!empty($_GET["url"])) {
            echo 'location.href="' . $this->getBackPageUrl() . '";';
        } else {
            echo "history.back();";
        }
        ?>
	}

	function edit(stock_in_id) {
		location.href = "/<?php echo $this->getId() ?>/edit?id=" + stock_in_id;
	}

	function submit(stock_in_id) {
		layer.confirm("您确定要提交当前入库单信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
			var formData = "id=" + stock_in_id;
			$.ajax({
				type: "POST",
				url: "/<?php echo $this->getId() ?>/submit",
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						layer.msg(json.data, {icon: 6, time: 1000}, function () {
							location.reload();
						});
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
</script>