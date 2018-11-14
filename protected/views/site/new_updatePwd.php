<style>
  .update-pwd>li{
    width:70%;
    margin:0 auto;
  }
  .update-pwd>li>#saveButton{
    margin-top:20px;
  }
  #main-container{
      margin-top:50px;
  }
</style>
<script src="/js/md5.js"></script>
<div class="content-wrap">
  <div class="content-wrap-title">
      <div>
          <p>修改密码</p>
      </div>
  </div>
  <ul class="form-com update-pwd">
    <li>
      <label>原密码</label>
      <input type="password" class="form-control" data-bind="value:password"  maxLength="32" name="user_pwd" id="user_pwd"/>
    </li>
    <li>
      <label>新密码</label>
      <input type="password" class="form-control"   maxLength="16" name="user_pwd_1" id="user_pwd_1" data-bind="value:newPassword"/>
    </li>
    <li>
      <label>新密码</label>
      <input type="password" class="form-control"  name="user_pwd_2" id="user_pwd_2" data-bind="value:confirmPassword" />
    </li>
    <li>
      <label></label>
      <button id="saveButton"data-bind="click:save,html:buttonText">保存</button>
    </li>
  </ul>
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
                        inc.vueMessage("操作成功！");
                    }else{
						inc.vueAlert(json.data);
                    }
                },
                error:function (data) {
                    self.buttonText("保存");
					inc.vueAlert("保存失败："+data.responseText);
                }
            });
        }
    }


</script>


