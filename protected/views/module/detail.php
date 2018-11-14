<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">系统模块详情</h3>
    </div>
    <div class="box-body  form-horizontal">
        <div class="form-group ">
            <label class="col-sm-2 control-label">名称</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["icon"].$data["name"] ?></p>
            </div>
            <label class="col-sm-2 control-label">权限码</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["code"] ?></p>
            </div>
        </div>
        <div class="form-group ">
            <label class="col-sm-2 control-label">所属系统</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map["system_id"][$data["system_id"]] ?></p>
            </div>
            <label class="col-sm-2 control-label">排序码</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["order_index"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">操作</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["actions"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">页面链接</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["page_url"] ?></p>
            </div>
        </div>
        <div class="form-group ">
            <label class="col-sm-2 control-label">是否公开</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map["module_is_public"][$data["is_public"]] ?></p>
            </div>
            <label class="col-sm-2 control-label">是否外部链接</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map["module_is_external"][$data["is_external"]] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">状态</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map["module_status"][$data["status"]] ?></p>
            </div>
            <label class="col-sm-2 control-label">更新时间</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["update_time"] ?></p>
            </div>
        </div>
        <div class="form-group ">
            <label class="col-sm-2 control-label">是否菜单</label>
            <div class="col-sm-2">
                <p class="form-control-static"><?php echo $this->map["module_is_menu"][$data["is_menu"]] ?></p>
            </div>
            <label for="prd_type" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-6">
                <p class="form-control-static"><?php echo $data["remark"] ?></p>
            </div>
        </div>

    </div>
    <div class="box-footer">
        <button type="button"  class="btn btn-default" onclick="back()">返回</button>
    </div>
</div>

<script>
    function back()
    {
        history.back();
        //location.href="/module/";
    }
</script>