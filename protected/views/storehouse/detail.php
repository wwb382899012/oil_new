<?php
$contract = ProjectService::getContractDetailModel($storehouse['store_id']);
$checkLogs = FlowService::getCheckLog($storehouse['store_id'],"1");
?>
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#detail" data-toggle="tab">基本信息</a></li>
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
            <?php if(!$this->isExternal){ ?>
            <li class="pull-right"><button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button></li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="detail">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">仓库详情</h3>
                    </div><!--end box box-header-->
                    <div class="box-body">
                        <form class="form-horizontal" role="form" id="mainForm">
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
                                    <p class="form-control-static"><?php echo Ownership::getOwnershipNameById($storehouse['ownership']);?></p>
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
                        </form>
                    </div><!--end box-border-->

                    <div class="box-footer">
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <?php if($editable):?>
                                <button type="button" class="btn btn-danger" onclick="submit(<?php echo $storehouse['store_id']?>)">提交</button>
                                <a class="btn btn-primary" href="/storehouse/edit?store_id=<?php echo $storehouse['store_id']?>">修改</a>
                                <?php endif;?>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="back();">返回</a>
                                <input type='hidden' name='obj[store_id]' data-bind="value:store_id" />
                            </div>
                        </div>
                    </div>
                </div><!--end box box-primary-->

            </div><!--end tab1-->
            <div class="tab-pane" id="flow">
                <?php
                $this->renderPartial("/common/checkLogList", array('checkLogs'=>$checkLogs, 'map_name'=>'transection_check_status'));?>
            </div>
        </div>
    </div>
</section><!--end content-->
</section><!--end content-->

<script type="text/javascript">
    var back = function() {
        window.location.href = '/storehouse/';
    }
    var submit = function(store_id) {

        layer.confirm("是否确认提交仓库信息?", {icon: 3, title: '提示'}, function(){
            
            $.ajax({
                type:"POST",
                url:"/storehouse/submit",
                data: {
                    store_id:store_id
                },
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        layer.msg(json.data, {icon: 6, time:1000},function() {
                            back();
                        });
                    }else{
                        layer.alert(json.data);
                    }
                },
                error:function (data) {
                    layer.alert("提交失败！"+data.responseText);
                }
            });
        });
    }
</script>