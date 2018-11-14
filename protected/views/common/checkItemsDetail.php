
<?php
if(!empty($checkLog->extra) && is_array($checkLog->extra->items))
{
    foreach ($checkLog->extra->items as $key=>$item)
    {
        ?>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label"><?php echo $item["name"] ?></label>
            <div class="col-sm-10">
                <?php echo $item["displayValue"] ?>
            </div>
        </div>
        <?php
    }
}
?>
