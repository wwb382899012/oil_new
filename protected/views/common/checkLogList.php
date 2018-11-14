<?php
?>
<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th style="width: 10px">#</th>
        <th>审核意见</th>
        <th style="width: 130px;">审核节点</th>
        <th style="width: 100px;">审核人</th>
        <th style="width: 100px;">结果</th>
        <th style="width: 150px;">审核时间</th>

    </tr>
    </thead>
    <tbody>
    <?php
    if(Utility::isNotEmpty($checkLogs))
    {
        $map_name=empty($map_name)?"check_status":$map_name;
        $k=0;
        foreach($checkLogs as $v)
        {
            $k++;
            ?>
            <tr class="<?php if($k==1){echo 'text-bold';}?> popover-item"  data-placement="bottom" data-html="true" data-title="function(){return $(this).find('.popover-content').html();}">
                <td><?php echo $k ?>.</td>
                <td>
                    <?php echo $v["remark"] ?>
                    <div class="popover-content hide">

                    </div>
                </td>
                <td><?php echo $v["node_name"] ?></td>
                <td><?php echo $v["name"] ?></td>
                <td><?php echo $this->map[$map_name][$v["check_status"]] ?></td>
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
        $(".popover-item").tooltip({title:function(){return $(this).find('.popover-content').html();}});
    });
</script>