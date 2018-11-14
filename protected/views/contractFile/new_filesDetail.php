<?php
foreach ($contract as $row) {
    if (Utility::isNotEmpty($row['files'])) {
        ?>
        <div class="in-table-wrapper">
            <div class="flex-grid form-group align-between">
                <label class="field flex-grid emphasis">
                <span class="line-h--text cell-title colon text-link " style="color: #3e8cf7;">
                    <?php
                    $map = array(1 => '采购合同', 2 => '销售合同');
                    echo $map[$row['type']];
                    ?>
                </span>
                    <span class="form-control-static line-h--text">
                        <?php
                        echo '<a class="text-link" href="/contract/detail?id=' . $row['contract_id'] . '&t=1" title="合同详情" target="_blank">' . $row['contract_code'] . '</a> &nbsp;&nbsp;';
                        ?>
                    </span>
                </label>
                <label class="field flex-grid emphasis">
                    <span class="form-control-static line-h--text">
                        <?php
                        echo '<a class="text-link" href="/partner/detail/?id='.$row['partner_id']. '&t=1" title="'.$row['partner_name'].'" target="_blank">'.$row['partner_name'].'</a>';
                        ?>
                    </span>
                </label>
                <label class="field flex-grid emphasis">
                    <?php
                    echo '<span title="'.$row['amount'].'">'.$row['amount'].'</span>';
                    ?>
                </label>
                <label class="field flex-grid emphasis">
                    <?php
                    echo '<span title="'.$row['goods'].'">'.$row['goods'].'</span>';
                    ?>
                </label>
            </div>


            <table class="table table-fixed">
                <tbody>
                <?php
                foreach ($row['files'] as $val) {
                    ?>
                    <tr>
                        <td style="width: calc(137px - 12px)">
                            <?php echo Map::$v['contract_file_categories'][$row['type']][$val['category']]['name']; ?>
                        </td>
                        <td style="width:80px">
                            <?php echo Map::$v['contract_standard_type'][$val['version_type']]['name']; ?>
                        </td>
                        <td style="width:210px;"><?php echo !empty($val['code']) ? $val['code'] : '无'; ?></td>
                        <td style="width:210px; "><?php echo !empty($val['code_out']) ? $val['code_out'] : '无'; ?></td>
                        <td style="width:73px; "><?php echo Map::$v['contract_upload_status'][$val['status']] ?></td>
                        <td style="width:90px; ">
                            <?php
                            if (!empty($val['file_url'])) {
                                echo '<a target="_blank" class="z-btn-action" title="' . $val['name'] . '" href="/' . $this->getId() . '/getFile/?id=' . $val['file_id'] . '&fileName=' . $val['name'] . '">查看</a>';
                            } else {
                                echo '无';
                            }
                            ?>
                        </td>
                        <td style="width:110px;">
                            <?php
                            if (!empty($val['esign_file_url'])) {
                                echo '<a target="_blank" class="z-btn-action" title="' . $val['esign_file_name'] . '" href="/' . $this->getId() . '/getFile/?id=' . $val['esign_file_id'] . '&fileName=' . $val['esign_file_name'] . '">查看</a>';
                            } else {
                                echo '无';
                            }
                            ?>
                        </td>
                        <td style="width: calc(122px - 12px);">
                            <?php
                            if (!empty($val['psign_file_url'])) {
                                echo '<a target="_blank" class="z-btn-action" title="' . $val['psign_file_name'] . '" href="/' . $this->getId() . '/getFile/?id=' . $val['psign_file_id'] . '&fileName=' . $val['psign_file_name'] . '">查看</a>';
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
        </div>
        <?php
    }
} ?>