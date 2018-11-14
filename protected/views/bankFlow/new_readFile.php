<label>
    导入结果：
</label>
<table class="data-table dataTable stripe hover nowrap table-fixed">
    <thead>
    <tr>
        <th>
            校验结果
        </th>
        <th>
            银行流水编号
        </th>
        <th>
            银行帐号
        </th>
        <th>
            收款银行
        </th>
        <th>
            交易主体
        </th>
        <th>
            银行账户名
        </th>
        <th>
            付款公司
        </th>
        <th>
            付款银行
        </th>
        <th>
            收款时间
        </th>
        <th>
            币种
        </th>
        <th>
            汇率
        </th>
        <th>
            收款金额
        </th>
        <th>
            备注
        </th>
    </tr>
    </thead>
    <?php foreach ($returnArray as $value): ?>
        <tr class="<?php echo ($value['has_error'] == 1) ? 'text-red' : '' ?>">
            <td>
                <?php if (is_array($value['error_message'])): ?>
                    <?php foreach ($value['error_message'] as $message): ?>
                        <p><?php echo $message ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php echo $value['银行流水编号']; ?>
            </td>
            <td>
                <?php echo $value['银行帐号']; ?>
            </td>
            <td>
                <?php echo $value['收款银行']; ?>
            </td>
            <td>
                <?php echo $value['交易主体']; ?>
            </td>
            <td>
                <?php echo $value['银行账户名']; ?>
            </td>
            <td>
                <?php echo $value['付款公司']; ?>
            </td>
            <td>
                <?php echo $value['付款银行']; ?>
            </td>
            <td>
                <?php echo $value['收款时间']; ?>
            </td>
            <td>
                <?php echo $value['币种']; ?>
            </td>
            <td>
                <?php echo $value['汇率']; ?>
            </td>
            <td class="no-ellipsis">
                <?php echo number_format($value['收款金额'], 2); ?>
            </td>
            <td>
                <?php echo $value['备注']; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<script>
    $(function () {
        page.initDatatables('', {columns: 0})
    });
</script>