<?php
if (Utility::isNotEmpty($stockNotices)) {
foreach ($stockNotices as $key => $row) { ?>
<div class="box">
    <div class="box-header with-border link">
        <h3 class="box-title" style="text-align: center">
            <b><?php echo Map::$v['stock_notice_delivery_type'][$row['type']] . '入库通知单' ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <a target="_blank" href="/stockIn/detail/?id=<?php echo $row['batch_id']; ?>">
            <span style="font-size: 16px;">NO.<span><?php echo $row['code'] ?></span></span>
            </a>
            <?php if ($row['status'] > StockNotice::STATUS_SETTLE_SUBMIT) { ?>
            <span class="text-red">(已结算)</span>
            <?php } ?>
            </b>
        </h3>
        <?php
        if (0 && $row['status'] < StockNotice::STATUS_SUBMIT) { ?>
            <div class="pull-right box-tools">
                <button type="button" class="btn btn-sm btn-primary" onclick="edit(<?php echo $row['batch_id'] ?>)">修改</button>&nbsp;
                <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo $row['batch_id'] ?>)">提交</button>&nbsp;
            </div>
        <?php } ?>
    </div>
    <div class="box-body hide1 form-horizontal">
        <div class="form-group">
            <?php
            $this->renderPartial("/components/attachmentsDropdown", array(
                    'id' => $row['batch_id'],
                    'map_key'=>'stock_notice_attachment_type',
                    'attach_type'=>ConstantMap::STOCK_NOTICE_ATTACH_TYPE,
                    'attachment_type'=>Attachment::C_STOCK_NOTICE,
                    'controller'=>'stockNotice',
                )
            );
            ?>
            <label class="col-sm-2 control-label">入库通知单日期</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $row['batch_date'] ?></p>
            </div>
        </div>
        <?php
        if (Utility::isNotEmpty($row->details)) { ?>
        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th style="width:120px;text-align:center">品名</th>
                        <!--<th style="width:100px;text-align:center">规格</th>-->
                        <th style="width:80px;text-align:center">入库通知单数量</th>
                        <th style="width:80px;text-align:center">换算比例</th>
                        <th style="width:80px;text-align:center">仓库</th>
                        <th style="width:80px;text-align:center">备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($row->details as $val) { ?>
                        <tr>
                            <td style="text-align:center"><?php echo $val->goods->name ?></td>
                            <!--<td style="text-align:center"><?php /*echo !empty($val["goods_describe"]) ? $val["goods_describe"] : '无' */?></td>-->
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
                                <?php
                                if (!empty($val->sub) && !empty($val->unit_rate)) {
                                    echo $val['unit_rate'];
                                } else {
                                    echo '';
                                }
                                ?>
                            </td>
                            <td style="text-align:center"><?php echo $row['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE ? $val->store->name : '虚拟库' ?></td>
                            <td style="text-align:center">
                                <?php echo $val['remark']; ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="form-group">
                    <label class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $row['remark']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<?php }
} ?>
<script>
    $("div.link").each(function () {
        $(this).click(function () {
            $(this).next().toggle();
        });
    });

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
