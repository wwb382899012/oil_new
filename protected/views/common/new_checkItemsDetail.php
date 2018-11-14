
    <?php
    if (!empty($checkLog->extra) && is_array($checkLog->extra->items)) {
        foreach ($checkLog->extra->items as $key => $item) {
            ?>
            <li>
                <label style="width: unset">
                    <?php echo $item["name"] ?>：
                </label>
                <p>
                    <?php echo $item["displayValue"] ?>
                </p>
            </li>
            <?php
        }
    }
    ?>