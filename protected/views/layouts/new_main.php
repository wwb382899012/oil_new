<!DOCTYPE html>
<html>
<?php include (ROOT_DIR.'/protected/views/layouts/new_header.php'); ?>
<body class="sidebar-mini skin-red-light">
  <div class="wrapper">
    <aside class="main-sidebar">
      <section class="sidebar">
        <a class="logo-wrap" href="/site/index">
          <img src="/newUI/img/logo2-no-text-new.png" alt="">
          <span style="white-space:nowrap;">中优油管家</span>
        </a>
        <ul class="sidebar-menu tree"  data-widget="tree">
            <li class="<?php echo $this->mainActive ?>">
                <a href="/site/index" class="menu-to-index">
                    <i class="icon icon-zhuye"></i> <span>首页</span>
                </a>
            </li>
          <?php
            $treeData=SystemUser::getFormattedRightCodes($this->userId);
            $this->generateMenuWithNewUI($treeData["tree"]);
          ?>
        </ul>
      </section>
    </aside>
    <main class="main_body">
      <header id="app" class="is-fixed-nav" style="box-shadow: 0 1px 4px rgba(51, 51, 51, 0.15);z-index:1000;">
        <i class="icon icon-menu-unfold" style="cursor:pointer;"></i>
        <ul class="header-right-part">
          <!-- 搜索项目编码 -->
          <li>
            <input type="text" id="top_search_key" class="search form-control" placeholder="项目编号" onkeydown="page.keyDownSearchNewUI()">
            <i class="icon icon-search" onclick="page.showProjectNewUI()" style="cursor:pointer;"></i>
          </li>
          <!-- 待办 -->
          <li>
            <div class="dropdown action-more action-more--adjust common-dropdown div-todo">
              <?php
                $tasks=TaskService::getUserActions(Utility::getNowUserId());
                $n = 0;
                if (Utility::isNotEmpty($tasks)) {
                    foreach ($tasks as $task) {
                        $n += $task['n'];
                    }
                }
                ?>
              <p id="p_ing" data-toggle="dropdown" style="cursor:pointer;position:relative;margin-right:10px;">
                <i class="icon icon-xiaoxi"></i>
                <?php
                  if($n==0)
                    echo '';
                  else if($n<=10) 
                    echo "<span class=\"label label-warning msg-notice\" style=\"width: 16px;border-radius: 50%;\">".$n."</span>";
                  else if($n<=99) 
                    echo "<span class=\"label label-warning msg-notice\">".$n."</span>";
                  else
                    echo "<span class=\"label label-warning msg-notice\">··</span>";
                 ?>
              </p>
              <!-- 待办 -->
              <ul class="dropdown-menu" aria-labelledby="drop1" id="sidebar-menu-my-tasks-newUI" style="left:unset;right:0;width:238px;max-height: 282px;overflow: auto;">
                  <!--<div>
                  <li class="header" style="border-bottom:1px solid #dcdcdc;line-height:40px;">您有 <?php /*echo $n */?> 个待办</li>
                  <li style="height:300px;overflow:auto;">
                    <ul class="menu">
                        <?php /*foreach ($tasks as $v)
                        {
                          $url=$v["action_url"];
                          $icon="<i style='display: inline-block;width: 4px;height: 4px;border-radius: 50%;background-color: #999;margin-right: 10px;vertical-align: middle;'></i>";
                          // if(!empty($v["icon"]))
                              // $icon=$v["icon"];
                          if($v["user_id"]==0)
                              $url="/site/task?id=".$v["task_id"];
                          $title=$v["title"];
                          if(empty($title))
                              $title=$v["key_value"];
                          //$title=$v["action_name"]." ".$v["key_value"];
                          $title=$v["action_name"]."-".$title;
                          echo "<li><a href='".$url."' title='".$title."'>".$icon.$title."</a></li>";
                        }
                        */?>
                    </ul>
                  </li>
                </div>-->
              </ul>
            </div>
          </li>

          <!-- 修改密码和退出登录 -->
          <li>
            <div class="dropdown action-more action-more--adjust common-dropdown operation">
              <p  data-toggle="dropdown" slot="reference">
                <img class="point" src="/newUI/img/default_header.png" alt="">
                <span style="cursor:pointer;color:#666!important;"><?php echo  Mod::app()->user->name ?><i class="icon icon-shouqizhankai" style="color:#999 !important;"></i></span>
              </p>
              <ul class="dropdown-menu" aria-labelledby="drop1" style="left:unset;right:10px;">
                  <a href="/site/updatePwd" style="display: block;line-height: 34px;padding-left: 20px;">修改密码</a>
                  <a href="/site/logout" style="display: block;line-height: 34px;padding-left: 20px;">退出</a>
              </ul>
            </div>
          </li>
          <!-- 选择角色 -->
          <li>
            <div class="dropdown action-more action-more--adjust common-dropdown role-slt-wrap"  style="margin-left:25px;">
              <a data-toggle="dropdown" href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:#666 !important;">
                  <?php
                  $roles=UserService::getUserRoles();
                  $roleId=UserService::getNowUserMainRoleId();
                  echo $roles[$roleId]["role_name"]; ?>
                  <i class="icon icon-shouqizhankai" style="color:#999 !important;"></i>
              </a>
                <ul class="dropdown-menu" aria-labelledby="drop1" style="left:unset;right:0;width:180px; max-height: 282px;overflow: auto;">
                    <li>
                        <ul class="menu">
                            <?php
                            if(is_array($roles))
                            {
                                foreach ($roles as $v)
                                {
                                    echo '<li style="line-height:34px;height:34px;padding-left:20px;cursor:pointer;">
                                <span title="'.$v["role_name"].'" style="display:inline-block;width:100%;height:100%;white-space: nowrap;text-overflow: ellipsis;overflow: hidden;" onclick="page.setMainRoleNewUI('.$v["role_id"].')">'.$v["role_name"].'</span>
                                </li>';
                                }
                            }
                            ?>
                        </ul>
                    </li>

                </ul>
            </div>
          </li>
        </ul>
      </header>
      <div class="content-wrapper">
        <section id="main-container" style="width:100%;">
            <div class="el-container is-vertical">
                <?php echo $content; ?>
            </div>
        </section>
    </div>
    </main>
<!--      勿删， 引入vue需要该元素作为锚点-->
      <div id="app-elment"></div>
  </div>
  <script>
    $(function(){
      page.showMyTasksWithNewUI();
      page.setSelectedMenus("<?php echo $this->moduleParentIds ?>");
      setTimeout(() => {
        $('body,.wrapper').css({'min-height':window.innerHeight+'px'})
      }, 100);
    });
    setTimeout(function() {
        $('.selectpicker').selectpicker({
            size: 8
        });
    })
    page.autoscroll()
    // 借用element的部分控件
    var vue = new Vue({
        el: '#app-elment'
    })
</script>
</body>
</html>
