<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>中优油管家<?php echo(isset($this->menu_name))?'- '.$this->menu_name:'' ?></title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- bootstrap 3.3.5 -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- font Awesome 4.4.0 -->
    <link href="/plugins/font-awesome-4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.1 -->
    <link href="/plugins/ionicons-2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />

    <link href="/css/login_style.css" rel="stylesheet" type="text/css" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries  3.7.3 1.4.2   -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/js/html5shiv.min.js"></script>
    <script src="/js/respond.min.js"></script>
    <![endif]-->

    <script src="/js/jquery-2.1.4.min.js"></script>
    <!-- Bootstrap -->
    <script src="/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>


    <script src="/js/knockout/knockout-3.4.2.js" type="text/javascript"></script>
    <script src="/js/knockout/knockout.validation.min.js" type="text/javascript"></script>

    <script src="/js/knockout/knockout.extend.js" type="text/javascript"></script>
    <script src="/js/inc.js?key=20171130" type="text/javascript"></script>
    <script src="/js/login.js" type="text/javascript"></script>
    <script src="/js/jquery.backstretch.min.js" type="text/javascript"></script>
</head>
<body >
<div class="top-content">

    <div class="inner-bg">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2 text">
                    <h1><span class="logo"><strong>中智诚 </strong></span> ◇ 能源集团</h1>
                    <div class="description hide">
                        <p>
                            中优油管家
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 form-box">
                    <div class="form-top">
                        <div class="form-top-left">
                            <h3>中优油管家</h3>
                            <div class="alert alert-danger alert-dismissible" data-bind="visible:serverError,text:errorText" ></div>
                            <p>请输入用户名和密码登录系统:</p>
                        </div>
                        <div class="form-top-right">
                            <i class="fa fa-key"></i>
                        </div>
                    </div>
                    <div class="form-bottom">
                        <form role="form" action="" method="post" class="login-form">
                            <div class="form-group">
                                <label class="sr-only" for="form-username">用户名</label>
                                <input type="text" class="form-username form-control" id="username" autofocus name="username" placeholder="用户名" data-bind="textInput: username">
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="form-password">密码</label>
                                <input type="password" class="form-password form-control" id="password" name="password" placeholder="密码" data-bind="textInput: password">
                            </div>
                            <button type="button" class="btn" id="submitButton" data-bind="click: login,html:buttonText">登录</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script src="/js/md5.js"></script>
<script>
    var view;
    var backUrl="<?php echo $url ?>";
    $(function () {
        $.backstretch("/img/1.jpg");
        view = new LoginViewModel(backUrl);
        ko.applyBindings(view);
        $(window).keydown(function(event){
            if(event.keyCode==13||event.keyCode==32){
                $("#submitButton").click();
            }
            return true;
        });
    });


</script>

