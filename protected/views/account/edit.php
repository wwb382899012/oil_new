<script src="/js/jquery.bankInput.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div><!--end box box-header-->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">公司主体</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="corporation_id" name="obj[corporation_id]" placeholder="公司主体" data-bind="value:corporation_id,valueAllowUnset: true">
                            <option value=''>请选择公司主体</option>
                            <?php
                            $cors=Corporation::getActiveCorporations();
                            foreach($cors as $v)
                            {
                                echo "<option value='".$v["corporation_id"]."'>".$v["name"]."</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">银行名称</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="bank_name" name= "obj[bank_name]" placeholder="银行名称" data-bind="value:bank_name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">银行账号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="account_no" name= "obj[account_no]" placeholder="银行账号" data-bind="value:account_no">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">状态</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="status" name="obj[status]" data-bind="value:status">
                            <?php foreach($this->map["account_status"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div><!--end box-border-->
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">保存</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[account_id]' data-bind="value:account_id" />
                    </div>
                </div>
            </div>
        </form>
    </div><!--end box box-primary-->
</section><!--end content-->

<script>
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
        $("#account_no").bankInput({min:1,max:50,deimiter:' '});
    });

    function ViewModel(option){
        var defaults={
            account_id:"",
            corporation_id:"",
            account_no:"",
            bank_name:"",
            status:1,
            remark:""
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.account_id=ko.observable(o.account_id);
        self.corporation_id=ko.observable(o.corporation_id).extend({isNullVal:true});
        self.account_no=ko.observable(o.account_no).extend({required:true});
        self.bank_name=ko.observable(o.bank_name).extend({required:true});
        self.status=ko.observable(o.status);
        self.remark=ko.observable(o.remark);
        self.errors=ko.validation.group(self);
        self.isValid=function () {
            return self.errors().length===0;
        }

        self.save=function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }
            var formData=$("#mainForm").serialize();
            $.ajax({
                type:"POST",
                url:"/account/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        if(document.referrer){
                            location.href=document.referrer;
                        }else{
                            location.href="/account/";
                        }
                    }else{
                        alertModel(json.data);
                    }
                },
                error:function (data) {
                    alertModel("保存失败！"+data.responseText);
                }
            });
        }
        
        self.back=function () {
            history.back();
        }
    }
</script>