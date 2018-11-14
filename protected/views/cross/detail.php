<section class="content">
    <?php 
    include "contract.php";
    
    if (Utility::isNotEmpty($crossOrder)) {
        $index=0;
        foreach ($crossOrder as $key => $row) {
            $index++;
            ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title" style="text-align: center">
                        <b>调货单&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo $row['cross_code'] ?></span></span></b>
                    </h3>
                    <?php
                    if (CrossOrderService::isCanEdit($row['status'] )) { ?>
                        <div class="pull-right box-tools">
                            <button type="button" class="btn btn-sm btn-primary" onclick="edit(<?php echo $contract['detail_id'] ?>)">修改</button>&nbsp;
                            <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo $key ?>)">提交</button>&nbsp;
                        </div>
                    <?php } ?>
                </div>
                <div class="box-body <?php if ($index > 1) echo 'hide1'; ?> form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">销售合同编号</label>
                        <div class="col-sm-2">
                            <p class="form-control-static"><?php echo $contract['contract_code'] ?></p>
                        </div>
                        <label class="col-sm-2 control-label">品名</label>
                        <div class="col-sm-2">
                            <p class="form-control-static"><?php echo $contract['goods_name'] ?></p>
                        </div>
                        <label class="col-sm-2 control-label">调货日期</label>
                        <div class="col-sm-2">
                            <p class="form-control-static"><?php echo $row['cross_date'] ?></p>
                        </div>
                    </div>
                    <?php
                    if (Utility::isNotEmpty($row['details'])) { ?>
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                        <tr>
                            <th style="width:100px;text-align:center">采购合同编号</th>
                            <th style="width:160px;text-align:center">上游合作方</th>
                            <th style="width:120px;text-align:center">入库单编号</th>
                            <th style="width:80px;text-align:center">仓库</th>
                            <th style="width:80px;text-align:center">预计调货数量</th>
                            <th style="width:160px;text-align:center">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($row['details'] as $val) { ?>
                            <tr>
                                <td style="text-align:center"><?php echo $val['contract_code'] ?></td>
                                <td style="text-align:left"><?php echo $val['partner_name'] ?></td>
                                <td style="text-align:left"><?php echo $val['stock_code'] ?></td>
                                <td style="text-align:left"><?php echo $val['store_name'] ?></td>
                                <td style="text-align:right"><?php echo number_format($val['quantity'],4).$this->map['goods_unit'][$val['unit']]['name'] ?></td>
                                <td style="text-align:left">
                                    <?php 
                                        if(!empty($val['d_remark'])){
                                            $rArr = json_decode($val['d_remark'], true);
                                            echo "原始预计调货数量：".number_format($rArr['origin'],4).$this->map['goods_unit'][$val['unit']]['name'];
                                        }else{
                                            echo '';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="form-group"></div>
                    <?php } ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">调货原因</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $row['remark'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">审核状态</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $this->map['cross_order_status'][$row['status']] ?></p>
                        </div>
                    </div>
                </div>
                
            </div>
    <?php 
        }
    } 
    ?>
</section>

<script>
    $("div.box-header").each(function () {
        $(this).click(function () {
            $(this).next().toggle();
        });
    });

    function back() {
        location.href="/cross/";
    }

    function edit(detail_id) {
        location.href = "/<?php echo $this->getId() ?>/edit?id=" + detail_id;
    }

    function submit(cross_id) {
        layer.confirm("您确定要提交当前调货单信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
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