<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">商品价格详细信息</h3>
        <div class="pull-right box-tools">
            <button type="button"  class="btn btn-primary btn-sm" onclick="edit()">修改</button>
            <?php if(!$this->isExternal){ ?>
                <button type="button"  class="btn btn-default btn-sm" onclick="back()">返回</button>
            <?php } ?>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">商品</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $model->goods->name?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">价格</label>
            <div class="col-sm-3">
                <p class="form-control-static">
                    <?php echo $this->map["currency"][$model['currency']]['ico'];?>
                    <?php echo number_format($model->price/100,2)  ?></p>
            </div>
            <label class="col-sm-2 control-label">单位</label>
            <div class="col-sm-3">
                <p class="form-control-static">
                    <?php echo $this->map["goods_unit"][$model['unit']]['name'];?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">日期</label>
            <div class="col-sm-3">
                <p class="form-control-static"><?php echo $model->price_date ?></p>
            </div>
            <label class="col-sm-2 control-label">来源</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $model->source ?></p>
            </div>
        </div>
        <div class="form-group ">
            <label for="prd_type" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $model["remark"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">创建时间</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $model["create_time"] ?></p>
            </div>
            <label class="col-sm-2 control-label">更新时间</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $model["update_time"] ?></p>
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
        location.href="/price/edit/?t=<?php echo $this->isExternal ?>&id=<?php echo $model["price_id"] ?>"
    }

    function back() {
        location.href="/price/";
    }
</script>