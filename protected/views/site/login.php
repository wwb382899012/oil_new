<!DOCTYPE html>
<html>
<?php include (ROOT_DIR.'/protected/views/layouts/header.php'); ?>
<body class="login-page">
<div class="login-box">
    <div class="login-logo">
        <a>石油项目系统V2</a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">系统登录</p>
        <div class="alert alert-danger alert-dismissible" data-bind="visible:serverError,text:errorText" >

        </div>
        <form id="loginForm" method="post">
            <div class="form-group has-feedback">
                <input type="text" class="form-control" id="username" autofocus name="username" placeholder="用户名" data-bind="textInput: username">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" id="password" name="password" placeholder="密码" data-bind="textInput: password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button type="button" class="btn bg-olive btn-block" id="submitButton" data-bind="click: login,html:buttonText">登录</button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<script src="/js/md5.js"></script>
<script>
    var view;
    var backUrl="<?php echo $url ?>";
    $(function () {
        view = new LoginViewModel(backUrl);
        ko.applyBindings(view);
        $(window).keydown(function(event){
            if(event.keyCode==13||event.keyCode==32){
                $("#submitButton").click();
            }
            return true;
        });
    });

    function LoginViewModel(backUrl)
    {
        var self = this;
        self.username = ko.observable().extend({ required: true });
        self.password = ko.observable().extend({ required: true });
        self.buttonText = ko.observable("登录");
        self.actionState = ko.observable(0);

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
                self.username($("#username").val());
                self.password($("#password").val());
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

</script>

