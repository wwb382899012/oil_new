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
        <div class="form-group">
            <label class="col-sm-2 control-label">项目信息</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $project["project_id"] ?> &emsp;<?php echo $project["project_name"] ?></p>
            </div>
        </div>

    </div>
    <div class="list-group">
        <?php
        if(is_array($applies))
        {
            foreach ($applies as $apply)
            {
        ?>
                <div class="list-group-item">
                    <p>申请时间：<?php echo $apply["create_time"] ?></p>
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                        <tr>
                            <th style="width:200px;text-align:center">业务员</th>
                            <th style="width:300px;text-align:left;">金额</th>
                            <th style="text-align:left;">状态</th>
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
                            <td style="">
                                <?php echo number_format($item["amount"]/1000000,2) ?> 万元
                            </td>
                            <td style="">
                                <?php echo $this->map["project_credit_apply_detail_status"][$item["status"]] ?>
                            </td>
                        </tr>
                            <?php
                        }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <?php
            }
        }
        ?>

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