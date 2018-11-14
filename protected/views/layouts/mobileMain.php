<!DOCTYPE html>
<html>
<?php include (ROOT_DIR.'/protected/views/layouts/header.php'); ?>

<body class="hold-transition sidebar-mini skin-red-light">
<div class="wrapper">
    <header class="main-header">
        <a href="/site/index" class="logo">
            <span class="logo-lg">中优油管家</span>
            <span class="logo-mini"><img src="/newUI/img/logo2-no-text.png" alt=""></span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li><a>中优油管家</a></li>
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-fw fa-users"></i> &nbsp;<?php
                            $roles=UserService::getUserRoles();
                            $roleId=UserService::getNowUserMainRoleId();
                            echo $roles[$roleId]["role_name"]; ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header"><i class="fa fa-fw fa-check-circle"></i> 请选择角色</li>
                            <li>
                                <ul class="menu">
                                    <?php

                                    if(is_array($roles))
                                    {
                                        foreach ($roles as $v)
                                        {
                                            echo '<li><a onclick="page.setMainRole('.$v["role_id"].')">'.$v["role_name"].'</a></li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-gears"></i>
                        </a>
                        <ul class="dropdown-menu" style="width: 100px;">
                            <li>
                                <ul class="menu">
                                    <li><a href="/site/updatePwd" style="width: 158px;padding-left: 25px;padding-right: 15px;padding-bottom: 5px;padding-top: 5px;"><i class="fa fa-edit" aria-hidden="true"></i> 修改密码</a></li>
                                    <li><a href="/site/logout" style="width: 158px;padding-left: 25px;padding-right: 15px;padding-bottom: 5px;padding-top: 5px;"><i class="fa fa-sign-out" aria-hidden="true"></i> 退出</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="/img/avatar5.png" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?php echo  Mod::app()->user->name ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <ul class="sidebar-menu" data-widget="tree">
                <li class="<?php echo $this->mainActive ?>">
                    <a href="/site/index">
                        <i class="fa fa-dashboard"></i> <span>首页</span>
                    </a>
                </li>
                <?php
                $treeData=SystemUser::getFormattedRightCodes($this->userId);
                $this->generateMenu($treeData["tree"]);
                ?>
            </ul>
        </section>
    </aside>
    <div class="content-wrapper">
        <section class="content-header">
            <h1><?php echo empty($this->pageTitle)?$this->moduleName:$this->pageTitle ?> &nbsp;</h1>
            <ol class="breadcrumb">
                <li><a href="/site/index"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active"><a href="<?php echo $this->moduleUrl ?>"><?php echo $this->moduleName ?></a></li>
            </ol>
        </section>
        <section class="content" id="main-container">
            <?php echo $content; ?>
        </section>
    </div>
</div>

<script>
    $(function(){
        page.setSelectedMenus("<?php echo $this->moduleParentIds ?>");
        if(window.history.length<2)
            $(".history-back").hide();
        page.initTableHeight();
        $(window).resize(function(){page.initTableHeight();});
    });

</script>

</body>
</html>


















