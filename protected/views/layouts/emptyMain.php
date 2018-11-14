<!DOCTYPE html>
<html>
<?php include (ROOT_DIR.'/protected/views/layouts/header.php'); ?>
<body style="background-color: #ecf0f5">
<div class="wrapper">
    <section class="content" id="main-container">
        <?php echo $content; ?>
    </section>
</div>
<?php include (ROOT_DIR.'/protected/views/layouts/modal.php'); ?>

</body>
</html>
<script>
    $(function () {
        if(window.history.length<2)
            $(".history-back").hide();
        page.initTableHeight();
    });
</script>
