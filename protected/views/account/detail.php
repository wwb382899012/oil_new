<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">银行帐户详细信息</h3>
        <div class="pull-right box-tools">
            <button type="button"  class="btn btn-primary btn-sm" onclick="edit()">修改</button>
        </div>
    </div><!--end box-header with-border-->
    <div class="box-body form-horizontal">
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">公司主体</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["account_name"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">银行名称</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["bank_name"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">银行账号</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $data['account_no']) ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">状态</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map["account_status"][$data["status"]] ?></p>
            </div>
        </div>
        <div class="form-group ">
            <label for="prd_type" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["remark"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">创建时间</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["create_time"] ?></p>
            </div>
            <label class="col-sm-2 control-label">更新时间</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["update_time"] ?></p>
            </div>
        </div>
    </div><!--end box-body form-horizontal-->
    <div class="box-footer">
        <?php if(!$this->isExternal){ ?>
            <button type="button"  class="btn btn-default" onclick="back()">返回</button>
        <?php } ?>
    </div><!--end box-footer-->
</div>
<script>
    function edit() {
        location.href="/account/edit/?t=<?php echo $this->isExternal ?>&id=<?php echo $data["account_id"] ?>"
    }
    
    function back() {
        <?php
            if(!empty($_GET["url"]))
                echo 'location.href="'.$this->getBackPageUrl().'";';
            else
                echo "history.back();";
        ?>
    }
</script>