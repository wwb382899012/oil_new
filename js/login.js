function LoginViewModel(backUrl)
{
    var self = this;
    // self.username = ko.observable().extend({ required: {params:true,message:"用户名不得为空"} });
    self.username = ko.observable().extend({
        custom: {
            params: function (v) {
                return !(v == null || v == undefined || v == "");
            },
            message: "用户名不得为空"
        }
    });
    // self.password = ko.observable().extend({ required: { params: true, message: "密码不得为空" }});
    self.buttonText = ko.observable("登录");
    self.actionState = ko.observable(0);

    self.password=ko.observable().extend({
        custom:{
            params:function (v) {
                if(!self.username.isValid())
                    return true;
                return !(v==null || v==undefined || v=="");
            },
            message:"密码不得为空"
        }
    });

    /*self.formValidate=ko.observable().extend({
        custom:{
            params:function (v) {
                return (self.username.isValid() && self.password.isValid());
            },
            message:""
        }
    });*/

    self.errorText=ko.observable();
    self.serverError=ko.observable(false);

    self.username.subscribe(function(){
        self.serverError(false);
    });
    self.password.subscribe(function(){
        self.serverError(false);
    });

    self.errors = ko.validation.group(self);

    self.isValid = function () {
        return self.errors().length === 0;
    };

    self.login = function () {
        if (!self.isValid()) {
            // self.username($("#username").val());
            // self.password($("#password").val());
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
        }

        var formData = {
            username:self.username(),
            password:md5(self.password())
        };
        formData = { "obj": formData };
        if (self.actionState() == 1)
            return;
        self.actionState(1);
        self.buttonText("登录中 <i class=\"fa fa-spinner fa-pulse fa-fw\" ></i>");

        $.ajax({
            type: 'POST',
            url: '/site/login',
            data: formData,
            dataType: "json",
            success: function (json) {
                self.buttonText("登录");
                self.actionState(0);
                if (json.state == 0) {
                    location.href=backUrl;
                }
                else {
                    self.errorText(json.data);
                    self.serverError(true);
                }
            },
            error:function (data) {
                self.actionState(0);
                self.buttonText("登录");
                alert("登录失败："+data.responseText);
            }
        });
    }

    self.back=function(){
        history.back();
    }
}
