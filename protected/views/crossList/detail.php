<section class="content">
    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/cross/contract.php"; ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title" style="text-align: center">
                <b>调货单&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo $data[0]['cross_code'] ?></span></span></b>
            </h3>
            <?php
            if ($this->checkIsCanSubmit($data[0]['status'])) { ?>
            <div class="pull-right box-tools">
                <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo $data[0]['cross_id'] ?>)">已完成借货</button>
            </div>
            <?php } ?>
            
        </div>
        <div class="box-body form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">销售合同编号</label>
                <div class="col-sm-2">
                    <p class="form-control-static"><?php echo $data[0]['contract_code'] ?></p>
                </div>
                <label class="col-sm-2 control-label">品名</label>
                <div class="col-sm-2">
                    <p class="form-control-static"><?php echo $data[0]['goods_name'] ?></p>
                </div>
                <label class="col-sm-2 control-label">调货日期</label>
                <div class="col-sm-2">
                    <p class="form-control-static"><?php echo $data[0]['cross_date'] ?></p>
                </div>
            </div>
            <?php
            if (Utility::isNotEmpty($data)) { ?>
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                <tr>
                    <th style="width:100px;text-align:center">采购合同编号</th>
                    <th style="width:160px;text-align:center">上游合作方</th>
                    <th style="width:120px;text-align:center">入库单编号</th>
                    <th style="width:160px;text-align:center">仓库</th>
                    <th style="width:80px;text-align:center">预计调货数量</th>
                    <th style="width:80px;text-align:center">实际配货数量</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $val) { ?>
                    <tr>
                        <td style="text-align:center"><?php echo $val['contract_code'] ?></td>
                        <td style="text-align:center"><?php echo $val['partner_name'] ?></td>
                        <td style="text-align:center"><?php echo $val['stock_code'] ?></td>
                        <td style="text-align:center"><?php echo $val['store_name'] ?></td>
                        <td style="text-align:right"><?php echo number_format($val['quantity'],2).$this->map['goods_unit'][$val['unit']]['name'] ?></td>
                        <td style="text-align:right"><?php echo number_format(($val['quantity_frozen']+$val['quantity_out']),2).$this->map['goods_unit'][$val['unit']]['name'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <div class="form-group"></div>
            <?php } ?>
            <div class="form-group">
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo $data[0]['remark'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">状态</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo $this->map['cross_list_status'][$data[0]['status']] ?></p>
                </div>
            </div>
        </div>
        
    </div>
</section>

<script>
    function back() {
        location.href="/<?php echo $this->getId() ?>/";
    }

    function submit(cross_id) {
        layer.confirm("您确定要完成当前调货单借货吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
            var formData = "id=" + cross_id;
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