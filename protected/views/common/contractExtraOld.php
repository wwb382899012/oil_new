<?php 
$label_width = isset($label_width)?$label_width:4;
$control_width = 12 - $label_width;
$extra = json_decode($extraValue->content, true);
if($extra && is_array($extra))
foreach($extra as $value):
	?>
<div class="form-group">
    <label for="type" class="col-sm-<?php echo $label_width;?> control-label"><?php echo $value['name']?></label>
    <div class="col-sm-<?php echo $control_width;?>">
        <p class="form-control-static">
        <?php 
        	echo $value["display_value"];
        ?>
        </p>
    </div>
</div>
<?php endforeach;?>