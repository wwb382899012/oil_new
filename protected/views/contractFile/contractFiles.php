<table class="table table-hover">
    <thead>
    <tr>
        <th style="width:200px; text-align: left;">合同名称</th>
        <th style="width:200px; text-align: left;">我方合同编号</th>
        <th style="width:200px; text-align: left;">对方合同编号</th>
        <th style="width:150px; text-align: center;">最终合同</th>
        <th style="width:150px; text-align: center;">电子双签合同</th>
        <th style="width:150px; text-align: center;">纸质双签合同</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($contractFiles as $val) {
        ?>
        <tr>
            <td style="width:200px; text-align: left;">
                <?php
                echo Map::$v['contract_file_categories'][$contract->type][$val['category']]['name'];
                ?>
                &nbsp;&nbsp;&nbsp;
                <span class="text-red" style="font-size: 12px">
                                        <?php
                                        echo Map::$v['contract_standard_type'][$val['version_type']]['name'];
                                        ?>
                                </span>
            </td>
            <td style="width:150px; text-align: left;"><?php echo !empty($val['code']) ? $val['code'] : '无'; ?></td>
            <td style="width:150px; text-align: left;"><?php echo !empty($val['code_out']) ? $val['code_out'] : '无'; ?></td>
            <td style="width:150px; text-align: center;">
                <?php
                if (!empty($val['final_file_url'])) {
                    echo '<a target="_blank" class="btn btn-primary btn-xs" title="' . $val['final_file_name'] . '" href="/contractUpload/getFile/?id=' . $val['final_file_id'] . '&fileName='.$val['final_file_name'].'">点击查看</a>';
                } else {
                    echo '无';
                }
                ?>
            </td>
            <td style="width:150px; text-align: center;">
                <?php
                if (!empty($val['esign_file_url'])) {
                    echo '<a target="_blank" class="btn btn-primary btn-xs" title="' . $val['esign_file_name'] . '" href="/contractUpload/getFile/?id=' . $val['esign_file_id'] . '&fileName='.$val['esign_file_name'].'">点击查看</a>';
                } else {
                    echo '无';
                }
                ?>
            </td>
            <td style="width:150px; text-align: center;">
                <?php
                if (!empty($val['psign_file_url'])) {
                    echo '<a target="_blank" class="btn btn-primary btn-xs" title="' . $val['psign_file_name'] . '" href="/contractUpload/getFile/?id=' . $val['psign_file_id'] . '&fileName='.$val['psign_file_name'].'">点击查看</a>';
                } else {
                    echo '无';
                }
                ?>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>