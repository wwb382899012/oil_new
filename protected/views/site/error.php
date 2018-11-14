<div class="site-error">

    <h1></h1>

    <div class="alert alert-danger">
        <h4><i class="icon fa fa-warning"></i> <?= "Error Code: ".$error["code"] ?></h4>

        <?php
        if($error["code"]=="403")
        {
            echo "你无权操作当前页面及功能！";
        }
        else
        {
            if (!Mod::app()->user->isGuest && Mod::app()->user->main_role_id==1)
                echo Mod::t("mod", $error["message"]);
            else
                echo "发生错误，请联系管理员！";
        }
            ?>

    </div>


</div>