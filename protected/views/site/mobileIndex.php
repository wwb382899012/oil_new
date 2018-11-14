<div class="box box-default">
    <div class="box-header with-border">
        <i class="fa fa-fw fa-tasks"></i>
        <h3 class="box-title">我的待办</h3>
    </div>
    <div class="box-body no-padding">
        <ul class="nav nav-stacked">
            <?php
            if(!isset($tasks))
                $tasks=$this->getUserTasks();

            if(is_array($tasks) && count($tasks)>0)
            {
                $i = 1;
                foreach ($tasks as $v)
                {
                    $url = $v["action_url"];
                    $icon = "<i class=\"fa fa-fw fa-tasks\"></i>";
                    if (!empty($v["icon"]))
                        $icon = $v["icon"];
                    $icon = $i . ". ";
                    if ($v["user_id"] == 0)
                        $url = "/site/task?id=" . $v["task_id"];
                    $title = empty($v["title"]) ? $v["key_value"] : $v["title"];
                    echo "<li><a href='" . $url . "'>" . $icon . $v["action_name"] . " （" . $title . "） <small class='text-info'>" . $v["create_time"] . "</small></a></li>";
                    $i++;
                }
            }
            else
                echo "<li><a><i class=\"fa fa-fw fa-check-square-o\"></i><span>暂无待办</span></a></li>";
            ?>
        </ul>
    </div>
</div>
<script type="text/javascript">
    function is_weixin() { 
        var ua = window.navigator.userAgent.toLowerCase(); 
        if (ua.match(/MicroMessenger/i) == 'micromessenger') { 
            return true;
        } else { 
            return false;
        } 
    }
    window.onpopstate = function(event) {
        if (is_weixin() && event.state.page === 'weixin_index') {
            WeixinJSBridge.call('closeWindow');
        }
    }
    window.onload = function () {
        if (is_weixin()) {
            history.pushState({page : 'weixin_index'},'title','#weixin_index');
            history.pushState({page : '/site/index'},'title','');
        }
    }
</script>
