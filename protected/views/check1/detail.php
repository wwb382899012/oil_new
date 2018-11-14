<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">仓库详情</h3>
        </div><!--end box box-header-->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="tgitype" class="col-sm-2 control-label">仓库名称<span class="text-red">*</span></label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['name'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">所属公司<span class="text-red">*</span></label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['company_name'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">统一信用代码</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['credit_code'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="registration_code" class="col-sm-2 control-label">工商注册号</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['registration_code'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="corporate" class="col-sm-2 control-label">法定代表人</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['corporate'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="start_date" class="col-sm-2 control-label">成立日期</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['start_date'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="registration_address" class="col-sm-2 control-label">注册地址</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['registration_address'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="registration_authority" class="col-sm-2 control-label">登记机关</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['registration_authority'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="registered_capital" class="col-sm-2 control-label">注册资本（万元）</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['registered_capital'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_scope" class="col-sm-2 control-label">经营范围</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['business_scope'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="ownership" class="col-sm-2 control-label">企业所有制</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['ownership'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="runs_state" class="col-sm-2 control-label">经营状态</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $this->map["runs_state"][$storehouse['runs_state']];?></p>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label for="address" class="col-sm-2 control-label">仓库地址</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['address'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="capacity" class="col-sm-2 control-label">仓库面积</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $storehouse['capacity'];?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">仓库类型</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $this->map["storehouse_type"][$storehouse['type']];?></p>
                    </div>
                </div>
            </div><!--end box-border-->

            <div class="box-footer">
            </div>
        </form>
    </div><!--end box box-primary-->
</section><!--end content-->

<script type="text/javascript">
    var submit = function(store_id) {

    }
    var edit = function(store_id) {

    }
    var back = function(store_id) {

    }
</script>