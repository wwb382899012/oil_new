<!DOCTYPE html>
<html>
<?php include (ROOT_DIR.'/protected/views/layouts/new_header.php'); ?>
<body style="background-color: #ecf0f5">
<style>
    .is-fixed-bread {
        top: 0;
        left: 0;
    }
    #main-container {
        margin-top: calc(50px * 1);
    }
</style>
<div class="wrapper">
    <main class="main_body">
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
</body>
</html>
<script>
    $(function () {
        if(window.history.length<2)
            $(".history-back").hide();
    });
    // 借用element的部分控件
    var vue = new Vue({
        el: '#app-elment'
    })
</script>
