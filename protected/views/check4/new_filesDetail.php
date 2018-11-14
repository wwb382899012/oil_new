
    <?php
    foreach ($contract as $k => $val) {
        ?>
    <div class="in-table-wrapper">
        <div class="flex-grid form-group align-between">
            <label class="field flex-grid emphasis">
                <span class="line-h--text cell-title text-link " style="color: #3e8cf7;">
                    <?php
                    $info = end($val);
                    echo $this->map["buy_sell_type"][$key] . "：<a class='text-link' href='/contract/detail?id=" . $info['contract_id'] . "&t=1' target='_blank'>" . $k . '</a>';
                    ?>
                </span>
            </label>
            <label class="field flex-grid emphasis">
                    <span class="form-control-static line-h--text">
                       <a class='text-link' href="/partner/detail/?id=<?php echo $info['partner_id'] ?>&t=1"
                          target="_blank"
                          title="<?php echo $info['partner_name'] ?>"><?php echo $info['partner_name'] ?></a>
                    </span>
            </label>
            <label class="field flex-grid emphasis">
                <span title="<?php echo $info['amount'] ?>"><?php echo $info['amount'] ?></span>
            </label>
            <label class="field flex-grid emphasis">
                <span title="<?php echo $info['goods'] ?>"><?php echo $info['goods'] ?></span>
            </label>
        </div>

        <table class="table table-fixed">
            <tbody>
            <?php
            foreach ($val as $v) {
                ?>
                <tr>
                    <td style="width: calc(132px - 12px);">
                        <?php
                        echo $this->map['contract_file_categories'][$key][$v['category']]['name'];
                        ?>
                    </td>
                    <td style="width: 80px;">
                        <?php
                        echo $this->map['contract_standard_type'][$v['version_type']]['name'];
                        ?>
                    </td>
                    <td style="width: 210px; "><?php echo !empty($v['code']) ? $v['code'] : '-'; ?></td>
                    <td style="width: 210px; "><?php echo !empty($v['code_out']) ? $v['code_out'] : '-'; ?></td>
                    <td style="width: 90px;  white-space: nowrap;">
                        <?php
                        if (!empty($v['file_id'])) {
                            echo '<a target="_blank" class="z-btn-action" title="' . $v['file_name'] . '" href="/contractUpload/getFile/?id=' . $v['file_id'] . '&fileName=' . $v['file_name'] . '">查看</a>';
                        } else {
                            echo '无';
                        }
                        ?>
                    </td>
                    <td
                        <?php
                        if (($v['file_status'] == ContractFile::STATUS_CHECKING && empty($v['status'])) || ($v['check_status'] == 1 && $v['count'] < 1 && empty($v['remark'])))
                            echo 'style="text-align: left; width: calc(190px - 12px);"';
                        else
                            echo 'style="text-align: left; width: calc(190px - 12px);"';
                        ?>
                    >
                        <?php
                        if ($v['file_status'] == ContractFile::STATUS_CHECKING && empty($v['status'])) {
                            echo "<a  href='/" . $this->getId() . "/check/?id=" . $v['obj_id'] . "&type=1' class='o-btn o-btn-action'>审核</a>";
                            if ($count >= 2)
                                echo '&emsp;<a href="javascript: void 0" class="o-btn o-btn-action" data-bind="click:function(){compareModal(' . $v["file_id"] . ', ' . $key . ', 2, \'' . $k . '-' . $this->map['contract_file_categories'][$key][$v['category']]['name'] . '\')}">对比审核</a>';
                        } else {
                            if ($v['check_status'] == 1) {
                                echo '通过';
                                if (is_array($v['content']) && count($v['content']) > 0) {
                                    foreach ($v['content'] as $content) {
                                        if (!empty($content['remark'])) {
                                            echo "<br/><span>●" . $content['name'] . "<br/>";
                                            echo "<span class='text-red'>备注：" . $content['remark'] . "</span><br/>";
                                        }
                                    }
                                }

                            } else {
                                if (is_array($v['content']) && count($v['content']) > 0) {
                                    echo "<span class='text-red'>" . $v['count'] . "项不通过：</span><br/>";
                                    foreach ($v['content'] as $content) {
                                        if (!empty($content['remark']) && empty($content['check_status'])) {
                                            echo "<span>●" . $content['name'] . "(不通过)<br/>";
                                            echo "<span class='text-red'>修改意见：" . $content['remark'] . "</span><br/>";
                                        } else if (!empty($content['remark'])) {
                                            echo "<span>●" . $content['name'] . "(通过)<br/>";
                                            echo "<span class='text-red'>备注：" . $content['remark'] . "</span><br/>";
                                        }
                                    }
                                }
                            }

                            if (!empty($v['remark'])) {
                                echo "<br/>备注：" . $v['remark'];
                            }

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
    ?>
