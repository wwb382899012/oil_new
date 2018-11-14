<script src="/js/md5.js"></script>
<div class="box box-primary" id="mainView">
    <div class="box-header">
        <h3 class="box-title">修改密码</h3>
    </div>
    <form class="form-horizontal" role="form" id="mainForm">
        <div class="box-body">
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">原密码</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" data-bind="value:password"  maxLength="32" name="user_pwd" id="user_pwd"/>
                </div>
            </div>
            <div class="form-group">
                <label for="account_name" class="col-sm-2 control-label">新密码</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control"   maxLength="16" name="user_pwd_1" id="user_pwd_1" data-bind="value:newPassword"/>
                </div>
            </div>
            <div class="form-group">
                <label for="account_number" class="col-sm-2 control-label">重复新密码</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control"  name="user_pwd_2" id="user_pwd_2" data-bind="value:confirmPassword" />
                </div>
            </div>

        </div><!-- /.box-body -->

        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save,html:buttonText">保存</button>
                </div>
            </div>

        </div>
    </form>
</div>



<script>
    var updatePWView;
    $(function(){
        updatePWView=new UpdatePasswordViewModel();
        ko.applyBindings(updatePWView,$("#mainView")[0]);
    });

    function UpdatePasswordViewModel(option)
    {
        var self=this;
        self.password=ko.observable().extend({minLength:2,maxLength:32,required:true});
        self.newPassword=ko.observable().extend({minLength:2,maxLength:32,required:true});
        self.confirmPassword=ko.observable().extend({minLength:2,maxLength:32,required:true,equal:self.newPassword});
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };
        self.buttonText=ko.observable("保存");

        self.save=function(){
            if(!self.isValid())
            {
                self.errors.showAllMessages();
                return;
            }
            var data={
                "password":md5(self.password()),
                "newPassword":md5(self.newPassword()),
                "confirmPassword":md5(self.confirmPassword())
            };

            var formData={data:data};
            self.buttonText("保存中 "+inc.loadingIco);
            $.ajax({
                type:"POST",
                url:"/site/updatePwd",
                data:formData,
                dataType:"json",
                success:function (json) {
                    self.buttonText("保存");
                    if(json.state==0){
                        inc.showNotice("操作成功！");
                    }else{
                        alert(json.data);
                    }
                },
                error:function (data) {
                    self.buttonText("保存");
                    alert("保存失败："+data.responseText);
                }
            });
        }
    }


</script>


