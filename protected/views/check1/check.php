<?php 
$checkDetail = CheckDetail::model()->findByPk($data['detail_id']);
$checkDetail = $checkDetail->getAttributes();
$storehouse = Storehouse::model()->findByPk($checkDetail['obj_id']);
$storehouse = $storehouse->getAttributes();
$this->renderPartial("/components/auditItems");?>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">仓库详情</h3>
        </div><!--end box box-header-->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="tgitype" class="col-sm-2 control-label">仓库ID<span class="text-red">*</span></label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $storehouse['store_id'];?></p>
                    </div>
                    <label for="tgitype" class="col-sm-2 control-label">仓库名称<span class="text-red">*</span></label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $storehouse['name'];?></p>
                    </div>
                </div>
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
                <hr>
                <!--以下是detail内容-->
                <audit-items params='configs: config, values:checkExtraLog'></audit-items>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">审核意见<span class="text-red">*</span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div><!--end box-border-->
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-success" data-bind="click:submit">通过</button>
                        <button type="button" id="saveButton" class="btn btn-danger" data-bind="click:rollback">驳回</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                    </div>
                </div>
            </div>
        </form>
    </div><!--end box box-primary-->
</section><!--end content-->

<script type="text/javascript">
    var view;
    $(function () {
        // 1.需要赋值的是check detail, 2. check extra log数据, 3. 对应的map
        view=new ViewModel(<?php echo json_encode(array('checkDetail'=>$checkDetail, 'checkLog'=>$checkLog, 'checkExtraLog'=>null, 'config'=>$this->map['storehouse_checkitems_config']));?>);
        ko.applyBindings(view, $("#mainForm")[0]);
    });

    function ViewModel(option){
        var defaults={
            checkDetail:null,
            config:null,
            checkExtraLog:{},
            remark : ''
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.config=ko.observableArray(o.config);
        self.checkExtraLog=ko.observable(o.checkExtraLog);
        self.remark=ko.observable(o.remark).extend({required:true,maxLength:512});
        self.errors=ko.validation.group(self);
		self.isValid=function () {
			return self.config.isValid() && self.errors().length === 0;
		}
        self.submit = function() {
            self.save(1);
        }
        self.rollback = function() {
            self.save(-1);
        }
        self.save=function (checkStatus) {
            if(self.isValid()) {
                var data ={
                    items : JSON.stringify(self.config.value()),
                    obj:{
                        remark : self.remark(),
                        detail_id : o.checkDetail.detail_id,
                        check_id : o.checkDetail.check_id,
                        checkStatus:checkStatus
                    }
                }
                $.ajax({
                    type:"POST",
                    url:"/<?php echo $this->getId() ?>/save",
                    data:data,
                    dataType:"json",
                    success:function (json) {
                        if(json.state==0){
                            layer.msg(json.data, {icon: 6, time:1000}, function() {
                                location.href="/<?php echo $this->getId() ?>/";
                            });
                        }else{
                            layer.alert(json.data);
                        }
                    },
                    error:function (data) {
                        layer.alert("保存失败！"+data.responseText);
                    }
                });
            } else {
				self.errors.showAllMessages();
				return;
            }
        }
        
        self.back=function () {
            window.location.href="/<?php echo $this->getId() ?>/";
        }
    }
</script>