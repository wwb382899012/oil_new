<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>中优油管家</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" media="screen" href="/newUI/css/common/common.css" />
  <link rel="stylesheet" type="text/css" media="screen" href="/newUI/css/login.css" />
  <style>
    input:-webkit-autofill , textarea:-webkit-autofill, select:-webkit-autofill {
      -webkit-box-shadow : 0 0 0px 1000px white inset
    } 
    .validationMessage{
      margin-left:0;
    }
  </style>
</head>
<body>
  <main style="overflow:hidden;min-height: 630px;min-width: 1000px;">
    <div class="main-wrap">
      <img class="left-img" src="/newUI/img/login3.png" alt="">
      <div data-bind="validationOptions:{insertMessages:false}">
        <img src="/newUI/img/logo@2x.png" alt="logo" class="img-logo">
        <div class="account">
          <label>账号</label>
          <input class="account-ipt" type="text" autofocus="autofocus" data-bind="textInput: username" placeholder="请输入您的账号">
        </div> 
        <div class="psd">
          <label>密码</label>
          <input class="psd-ipt" type="password" data-bind="textInput: password" placeholder="请输入您的密码">
        </div>
        <p style="height:20px;">
          <span class="validationMessage">
              <span  data-bind="validationMessage: username"></span>
              <span  data-bind="validationMessage: password"></span>
          </span>
          <span style="color:#F00;" class="alert alert-danger alert-dismissible" data-bind="visible:serverError,text:errorText"></span>
        </p>
        <button class="btn-login" id="submitButton" data-bind="click: login,html:buttonText"></button>
      </div>
      <img class="img-right-top" src="/newUI/img/login1.png">
      <img class="img-left-bottom" src="/newUI/img/login2.png">
      <p class="copy-text">Copyright© 2017-2020 中优国聚能源科技有限公司科技部出品</p>
    </div>
  </main>
  <script src="/js/jquery-2.1.4.min.js"></script>
  <!-- Bootstrap -->
  <script src="/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="/js/knockout/knockout-3.4.2.js" type="text/javascript"></script>
  <script src="/js/knockout/knockout.validation.min.js" type="text/javascript"></script>
  <script src="/js/knockout/knockout.extend.js" type="text/javascript"></script>
  <script src="/js/inc.js?key=20171130" type="text/javascript"></script>
  <script src="/js/login.js" type="text/javascript"></script>
  <script src="/js/jquery.backstretch.min.js" type="text/javascript"></script>
  <script src="/js/md5.js"></script>
  <script>
      var view;
      var backUrl="<?php echo $url ?>";
      $(function () {
          view = new LoginViewModel(backUrl);
          ko.applyBindings(view);
          $('.account-ipt').blur(function(){
            $(this).val($(this).val().replace(/^\s+|\s+$/g,""))
          })
          $('.psd-ipt').keydown(function(event){
              if(event.keyCode==13){
                  $("#submitButton").click();
              }
              return true;
          });
      });


  </script>
</body>
</html>