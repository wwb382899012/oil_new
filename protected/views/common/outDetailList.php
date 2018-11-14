<style type="text/css">
    <!--
    .hide1{display:none;}
    -->
</style>

<?php if (is_array($outOrders) && count($outOrders)>0):?>
    <?php foreach ($outOrders as $key => $outOrder):?>
        <?php $this->renderPartial("/stockOut/partial/stockOutOrderInfo", array(
            'outOrder' => $outOrder,
            'isNotFirstItem' => ($key >= 1),
        ));?>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    $("div.box-header").each(function () {
        $(this).click(function () {
            $(this).next().toggle();
        });
    });
</script>

<?php $this->renderPartial("/stockOut/partial/commonJs");?>