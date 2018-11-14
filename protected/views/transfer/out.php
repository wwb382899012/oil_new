<?php if(Utility::isNotEmpty($outOrder)){ ?>
<div class="form-group">
    <label class="col-sm-2 control-label">调货出库明细</label>
    <div class="col-sm-10">
        <table class="table table-condensed table-hover table-bordered table-layout">
            <thead>
                <tr>
                    <th style="width: 140px; text-align: center;">出库单编号</th>
                    <th style="width: 160px; text-align: center;">入库单编号</th>
                    <th style="width: 60px; text-align: center;">品名</th>
                    <th style="width: 100px; text-align: center;">实际出库数量</th>
                    <th style="width: 100px; text-align: center;">已处理数量</th>
                    <th style="width: 180px; text-align: center;">调货处理详情</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($outOrder['detail'] as $key => $value) {?>
                <tr>
                    <td style="text-align: left;"><?php echo $value['out_code'] ?></td>
                    <td style="text-align: left;"><?php echo $value['stock_code'] ?></td>
                    <td style="text-align: center;"><?php echo $data['goods_name'] ?></td>
                    <td style="text-align: right;"><?php echo number_format($value['quantity_actual'], 4).$this->map['goods_unit'][$value['unit']]['name'] ?></td>
                    <td style="text-align: right;"><?php echo number_format($value['quantity_done'], 4).$this->map['goods_unit'][$value['unit']]['name'] ?></td>
                    <td style="text-align: left;"><?php echo !empty($value['done_desc']) ? $value['done_desc'] : '-' ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align: center;">合计</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td style="text-align: right;"><?php echo number_format($outOrder['out_total_quantity'], 2).$this->map['goods_unit'][$outOrder['unit']]['name'] ?></td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>                    
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php } ?>