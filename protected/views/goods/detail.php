<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">商品详细信息</h3>
        <div class="pull-right box-tools">
            <button type="button"  class="btn btn-default btn-sm" onclick="edit()">修改</button>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">商品名称</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["name"] ?></p>
            </div>
        </div>
        <div class="form-group hide">
            <label for="type" class="col-sm-2 control-label">商品编码</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["code"] ?></p>
            </div>
        </div>
        <div class="form-group">

            <label class="col-sm-2 control-label">单位</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php
                    echo $this->map["goods_unit"][$data["unit"]];
                    ?></p>
            </div>
        </div>
        <div class="form-group">

            <label class="col-sm-2 control-label">状态</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map["corporation_status"][$data["status"]] ?></p>
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
        <div class="form-group ">
            <label for="prd_type" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["remark"] ?></p>
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
    function edit() {
        location.href="/goods/edit/?t=<?php echo $this->isExternal ?>&id=<?php echo $data["goods_id"] ?>"
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