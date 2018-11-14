<?php if(Utility::isNotEmpty($checkLogs)): ?>
    <div class="z-card">
        <div class="content-wrap-title content-title">
            <h3 class="z-card-header">
                <b>审核记录</b>
                <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i
                        class="icon icon-shouqizhankai"></i>
                </p>
            </h3>
        </div>
        <div class="z-card-body">
            <div class="flex-grid form-group">
                <table class="table table-custom">
                    <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>审核意见</th>
                        <th width="130">审核节点</th>
                        <th width="100">审核人</th>
                        <th width="100">结果</th>
                        <th width="170">审核时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $map_name = empty($map_name) ? "check_status" : $map_name;
                    $k = 0;
                    foreach($checkLogs as $v):
                        $k++;
                        ?>
                        <tr class="popover-item" data-placement="bottom" data-html="true"
                            data-title="function(){return $(this).find('.popover-content').html();}">
                            <td><?php echo $k; ?>.</td>
                            <td>
                                <?php echo $v["remark"]; ?>
                                <div class="popover-content hide"></div>
                            </td>
                            <td><?php echo $v["node_name"]; ?></td>
                            <td><?php echo $v["name"]; ?></td>
                            <td><?php echo $this->map[$map_name][$v["check_status"]]; ?></td>
                            <td><?php echo $v["check_time"]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>