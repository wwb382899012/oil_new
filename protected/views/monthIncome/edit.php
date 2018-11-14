<section class="content">
    <div class="box box-primary form-horizontal">
        <div class="box-header">
            <h3 class="box-title">烦请确认要作废的单据</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">单&emsp;&emsp;&emsp;号</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data['code'] ?></p>
                </div>
                <label for="type" class="col-sm-2 control-label">交易主体</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <a href="/corporation/detail/?id=<?php echo $data["corp_id"] ?>&t=1" target="_blank"><?php echo $data["corporation_name"] ?></a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">收入归属月</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["account_period"] ?></p>
                </div>
                <label for="type" class="col-sm-2 control-label">销售金额</label>
                <div class="col-sm-4">
                    <p class="form-control-static">￥ <?php echo number_format($data["sell_amount"]/100,2) ?></p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="saveButton" class="btn btn-danger" placeholder="确认" onclick="submit()">确认</button>
                    <button type="button"  class="btn btn-default" onclick="back()">返回</button>
                </div>
            </div>
        </div>
    </div>

</section>

<script>
    function back()
    {
        history.back();
    }

    function submit()
    {
        if(confirm("您确定要作废当前收入单据吗，该操作不可逆？")) {
            var formData = "id=<?php echo $data['statement_id'] ?>";
            $.ajax({
                type: 'POST',
                url: '/monthIncome/cancel',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.showNotice("操作成功");
                        location.href="/monthIncome/";
                    }
                    else {
                        alertModel(json.data);
                    }
                },
                error: function (data) {
                    alertModel("操作失败！" + data.responseText);
                }
            });
        }

    }

</script>