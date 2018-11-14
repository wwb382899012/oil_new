<style>
    #main-container {
        margin-top: 50px;
    }
</style>
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p><i class="icon icon-close-circle-fill" style="vertical-align: middle;color:#333;"></i> <?= "Error Code: ".$error["code"] ?></p>
        </div>
    </div>
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