<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">公司主体详细信息</h3>
        <div class="pull-right box-tools">
            <button type="button"  class="btn btn-default btn-sm" onclick="edit()">修改</button>
        </div>
    </div><!--end box-header with-border-->
    <div class="box-body form-horizontal">
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">企业名称</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["name"] ?></p>
            </div>
        </div>
        <div class="form-group hide">
            <label for="type" class="col-sm-2 control-label">企业编码</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["code"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">统一信用代码</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["credit_code"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">法人代表</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["corporate"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">纳税识别号</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["tax_code"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">地址</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $data["address"] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">联系电话</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["phone"] ?></p>
            </div>
            <label class="col-sm-2 control-label">状态</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map["corporation_status"][$data["status"]] ?></p>
            </div>
        </div>
        <!-- <div class="form-group">
            <label for="type" class="col-sm-2 control-label">银行名称</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["bank_name"] ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">银行账号</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $data['bank_account']) ?></p>
            </div>
        </div> -->
        
        <div class="form-group">
            <label class="col-sm-2 control-label">所有制</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map["ownership"][$data["ownership"]] ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">成立日期</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["start_date"] ?></p>
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
    </div><!--end box-body form-horizontal-->
    <div class="box-footer">
        <?php if(!$this->isExternal){ ?>
            <button type="button"  class="btn btn-default" onclick="back()">返回</button>
        <?php } ?>
    </div><!--end box-footer-->
</div>
<script>
    function edit() {
        location.href="/corporation/edit/?t=<?php echo $this->isExternal ?>&id=<?php echo $data["corporation_id"] ?>"
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