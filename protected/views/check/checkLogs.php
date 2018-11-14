
<table class="table table-striped table-hover">
    <tbody>
    <tr>
        <th style="width: 10px">#</th>
        <th style="width: 80px;">结果</th>
        <th>审核意见</th>
        <th style="width: 130px;">审核节点</th>
        <th style="width: 100px;">审核人</th>
        <th style="width: 150px;">审核时间</th>

    </tr>
    <?php
    if(!empty($checkLogs))
    {
        $k=0;
        foreach($checkLogs as $v)
        {
            $k++;
            ?>
            <tr class=" popover-item">
                <td><?php echo $k ?>.</td>
                <td><?php echo $this->map["check_status"][$v["check_status"]] ?></td>
                <td style="position: relative;"><?php echo $v["remark"] ?>
                    <?php
                    if(!empty($v->extra) && is_array($v->extra->items) && count($v->extra->items)>0)
                    {

                    ?>
                    <div class="popover-content">
                        <dl class="dl-horizontal check-extras">
                            <?php

                                foreach ($v->extra->items as $key=>$item)
                                {
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
                </td>
                <td><?php echo $v->checkNode["node_name"] ?></td>
                <td><?php echo $v->user["name"] ?></td>

                <td><?php echo $v["check_time"] ?></td>

            </tr>
            <?php
        }
    }

    ?>

    </tbody>
</table>
<script>
    $(function () {
        $(".popover-item").mouseover(function () {
            $(this).find(".popover-content").show();
        }).mouseout(function () {
            $(this).find(".popover-content").hide();
        });
    });
</script>