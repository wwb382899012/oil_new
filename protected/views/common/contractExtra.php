<?php 
// $label_width = isset($label_width)?$label_width:4;
// $control_width = 12 - $label_width;
$label_width = 4;
$control_width = 8;
$extra = json_decode($extraValue->content, true);
if($extra && is_array($extra))
foreach($extra as $value):
	?>
<div class="form-group form-group-custom form-group form-group-custom-half">
	<label for="type" class="col-lg-3 col-xl-2 control-label control-label-custom"><?php echo $value['name']?>：</label>
    <div class="col-sm-<?php echo $control_width;?>">
        <span class="contract-desc">
        <?php 
        	echo $value["display_value"];
        ?>
        </span>
    </div>
</div>
<?php endforeach;?>