<ul class="item-com item-com-1 ul-contract-item">
    <?php 
    // $label_width = isset($label_width)?$label_width:4;
    // $control_width = 12 - $label_width;
    $label_width = 4;
    $control_width = 8;
    $extra = json_decode($extraValue->content, true);
    if($extra && is_array($extra))
    foreach($extra as $value):
        ?>
    <li>
        <label for="type"><?php echo $value['name']?>：</label>
        <span>
        <?php 
            echo $value["display_value"];
        ?>
        </span>
    </li>
    <?php endforeach;?>
</ul>