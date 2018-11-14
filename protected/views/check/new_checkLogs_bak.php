<ul class="table-com">
    <li>
        <span>#</span>
        <span>结果</span>
        <span>审核意见</span>
        <span>审核节点</span>
        <span>审核人</span>
        <span>审核时间</span>
    </li>
    <?php
    if (!empty($checkLogs)) {
        $k = 0;
        foreach ($checkLogs as $v) {
            $k++;
            ?>
            <li class=" popover-item">
                <span><?php echo $k ?>.</span>
                <span><?php echo $this->map["check_status"][$v["check_status"]] ?></span>
                <span style="position: relative;">
                   <?php echo $v["remark"] ?>
                    <?php
                    if (!empty($v->extra) && is_array($v->extra->items) && count($v->extra->items) > 0) {

                        ?>
                        <div class="popover-content">
                            <dl class="dl-horizontal check-extras">
                                <?php

                                foreach ($v->extra->items as $key => $item) {
                                    ?>
                                    <dt><?php echo $item["name"] ?></dt>
                                    <dd><?php echo $item["displayValue"] ?></dd>
                                    <?php
                                }

                                ?>
                            </dl>
                        </div>
                        <?php
                    }
                    ?>
                </span>
                <span><?php echo $v->checkNode["node_name"] ?></span>
                <span><?php echo $v->user["name"] ?></span>
                <span><?php echo $v["check_time"] ?></span>

            </li>
            <?php
        }
    }

    ?>
</ul>
<style>
    .popover-content {
        position: absolute;
        z-index: 99999;
        width: 500px;
        background-color: #ffffff;
        top: 25px;
        padding: 10px 20px 5px 0;
        border: 1px solid #ccc;
        display: none;
    }
</style>
<script>
    $(function () {
        $(".popover-item").mouseover(function () {
            $(this).find(".popover-content").show();
        }).mouseout(function () {
            $(this).find(".popover-content").hide();
        });
    });
</script>