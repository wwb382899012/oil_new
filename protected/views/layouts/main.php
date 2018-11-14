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
                    <?php
                    $tasks=$this->getUserTasks();
                    $n=count($tasks);
                    ?>
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-flag-o"></i>
                            <?php if($n>0) echo "<span class=\"label label-warning\">".$n."</span>"; ?>
                        </a>
                        <ul class="dropdown-menu top-task-container">
                            <li class="header">您有 <?php echo $n ?> 个待办</li>
                            <li>
                                <ul class="menu">
                                    <?php foreach ($tasks as $v)
                                    {
                                        $url=$v["action_url"];
                                        $icon="<i class=\"fa fa-fw fa-tasks\"></i>";
                                        if(!empty($v["icon"]))
                                            $icon=$v["icon"];
                                        if($v["user_id"]==0)
                                            $url="/site/task?id=".$v["task_id"];
                                        $title=$v["title"];
                                        if(empty($title))
                                            $title=$v["key_value"];
                                        //$title=$v["action_name"]." ".$v["key_value"];
                                        $title=$v["action_name"]."-".$title;
                                        echo "<li><a href='".$url."' title='".$title."'>".$icon.$title."</a></li>";

                                    } ?>

                                </ul>
                            </li>

                        </ul>
                    </li>
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

                    <?php
                    $addClassName="";
                    if($_SERVER['PHP_SELF']=="/project/add/")
                        $addClassName="active";
                   /* $weekArray = array('0'=>'星期日','1'=>'星期一','2'=>'星期二','3'=>'星期三','4'=>'星期四','5'=>'星期五','6'=>'星期六');
                    $weekDay = $weekArray[date('w')];*/
                    ?>
                    <?php if (UserService::checkActionRight("project","add")) {?>
                        <li class="dropdown notifications-menu <?php echo $addClassName ?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="glyphicon glyphicon-plus"></span> 发起项目
                            </a>
                            <ul class="dropdown-menu container">
                                <li class="header">点击下面的项目类型发起</li>
                                <li>

                                    <?php
                                    foreach (Map::$v["project_config"] as $v)
                                    {
                                        //echo '<div class="btn-group margin"><a href="/project/add/?type='.$v["id"].'" class="btn btn-default btn-lg ">'.$v["name"].'</a></div>';
                                        echo '<a href="/project/add/?type='.$v["id"].'" class="btn bg-red  btn-flat margin  btn-lg ">'.$v["name"].'</a>';
                                    }
                                    ?>

                                </li>
                            </ul>
                        </li>

                    <?php } ?>
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
            <form onsubmit="return false;" action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" id="top_search_key" class="form-control" placeholder="项目编号" onkeydown="page.keyDownSearch()">
                    <span class="input-group-btn">
                                <button class="btn btn-flat" type="button" onclick="page.showProject()"><i class="fa fa-search"></i></button>
                        </span>
                </div>
            </form>
            <ul class="sidebar-menu"  data-widget="tree" id="sidebar-menu-my-tasks">

            </ul>
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
                <li><a href="/site/index"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active"><a href="<?php echo $this->moduleUrl ?>"><?php echo $this->moduleName ?></a></li>
            </ol>
        </section>

        <section class="content" id="main-container">
            <?php echo $content; ?>
        </section>
    </div>
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.6.0
        </div>
        <strong>Copyright © 2017-2020 <a href="#">中优国聚能源科技有限公司科技部出品</a>.</strong> All rights reserved.
    </footer>
</div>
<script>
    $(function(){
		page.showMyTasks(1);
		page.setSelectedMenus("<?php echo $this->moduleParentIds ?>");
		if(window.history.length<2)
		$(".history-back").hide();
		page.initTableHeight();
		page.initDataTables();
		$(window).resize(function(){page.initTableHeight();});

		/*setInterval(function () {
			page.showMyTasks();
		}, 10000);*/
    });
</script>
</body>
</html>


















