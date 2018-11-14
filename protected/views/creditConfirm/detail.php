<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">项目额度占用申请详情</h3>
        <div class="pull-right box-tools">
            <?php if(!$this->isExternal){ ?>
                <button type="button"  class="btn btn-default" onclick="back()">返回</button>
            <?php } ?>
        </div>
    </div>
    <div class="box-body form-horizontal">

        <?php include ROOT_DIR.DIRECTORY_SEPARATOR."protected/views/project/detailBody.php" ?>

        <div class="form-group-title">额度占用信息</div>
        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th style="width:200px;text-align:center">业务员</th>
                        <th style="width:300px;text-align:right;">金额</th>
                        <th style="width:80px;text-align:center;">状态</th>
                        <th style="text-align:left;">确认备注</th>
                    </tr>
                    </thead>
                    <tbody >
                    <?php
                    if(is_array($apply["items"]))
                    {
                        foreach ($apply["items"] as $item)
                        {
                            ?>
                            <tr>
                                <td style="text-align:center">
                                    <?php echo UserService::getNameById($item["user_id"]) ?>
                                </td>
                                <td style="text-align:right;">
                                    <?php echo number_format($item["amount"]/1000000,2) ?> 万元
                                </td>
                                <td style="text-align:center;">
                                    <?php echo $this->map["project_credit_apply_detail_status"][$item["status"]] ?>
                                </td>
                                <td style="">
                                    <?php echo $item["remark"] ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">申请说明</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $apply["remark"] ?></p>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <?php if(!$this->isExternal){ ?>
            <button type="button"  class="btn btn-default" onclick="back()">返回</button>
        <?php } ?>
    </div>
</div>
<script>

    function back() {
        <?php
        if(!empty($_GET["url"]))
            echo 'location.href="'.$this->getBackPageUrl().'";';
        else
            echo "history.back();";
        ?>
    }
</script>