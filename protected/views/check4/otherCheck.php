<script src="/plugins/pdf/pdf.js"></script>
<script src="/js/stickUp.min.js"></script>
<section class="content">
    <div id="pdf-show" class="stick-up">
        <div class="container-flow">
        <div class="row">
            <div class="col-sm-12">
                <div class="pdf-title">
                    <?php echo $contract["title"]?>
                    <!-- <a onclick="console.log($('#pdf-show').parent().width())"></a> -->
                </div>
                <iframe frameborder="0" scrolling="auto" width="100%" height="100%" src="/contractUpload/getPdf?id=<?php echo $contract["id"]?>"></iframe>
            </div>
        </div>
    </div>
    </div>
</section>
<script>
    jQuery(function($) {
        $(document).ready( function() {
            $('#pdf-show').stickUp();
            setSize();
            $("#pdf-show").parent().on("resize",setSize());
        });
    });

    function setSize()
    {
        var h=$(window).height();
        if(h>300)
            h=h-300;
        else
            h=h/2;

        $("#pdf-show").height(h).width("100%");
        //$("#pdf-show").height(h).width($("#pdf-show").parent().width()).parent().height(h+5);
        $("#pdf-show").find("iframe").height(h-15);
        $("#pdf-show").parent().height(h-45);
    }

</script>